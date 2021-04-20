<?php
    require_once "functions.php";
    require_once "config.php";

    $conn = new mysqli(Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbName);
    $conn->set_charset("utf8");

    $all = false;

    if (isset($_GET['id'])) {
        if($_GET['id'] == 'all') $all = true;

        $correct = true;
        $roundId = $_GET['id'];
    } else {
        $correct = false;
    }

    $notHeldMultiplier = 0.5;
    $changeMultiplierDate = strtotime("2019-08-01");

    if ($correct) {
        $count = 0;
        if(!$all) {
            $roundRes = getRound($conn, $roundId);
            $count = count($roundRes);
        }

        if ($count == 1 || $all) {
            if(!$all) {
                $round = $roundRes[0];
            }
            $good = true;

            if(!$all) {
                $cmd = "SELECT * FROM (SELECT id, matchNumber, date, NULL AS name, compId, compName, pay, cards, homeTeam, homeGoals, awayGoals, awayTeam, role, yellowCards, redCardsFromSecondYellow, redCards, comment, held FROM matches INNER JOIN competitions ON matches.competitionId = competitions.compId "
                    ."UNION SELECT id, NULL AS matchNumber, date, name, NULL AS compId, NULL AS compName, pay, NULL AS cards, NULL AS homeTeam, NULL AS homeGoals, NULL AS awayGoals, NULL AS awayTeam, NULL AS role, NULL AS yellowCards, NULL AS redCardsFromSecondYellow, NULL AS redCards, NULL AS comment, NULL AS held FROM tournaments) "
                    ." A WHERE date > ? AND date < ? ORDER BY date ASC";

                $matchesStmt = $conn->prepare($cmd);
                $matchesStmt->bind_param("ss", $round['startDate'], $round['endDate']);
            } else {
                $cmd = "SELECT * FROM (SELECT id, matchNumber, date, NULL AS name, compId, compName, pay, cards, homeTeam, homeGoals, awayGoals, awayTeam, role, yellowCards, redCardsFromSecondYellow, redCards, comment, held FROM matches INNER JOIN competitions ON matches.competitionId = competitions.compId "
                    ."UNION SELECT id, NULL AS matchNumber, date, name, NULL AS compId, NULL AS compName, pay, NULL AS cards, NULL AS homeTeam, NULL AS homeGoals, NULL AS awayGoals, NULL AS awayTeam, NULL AS role, NULL AS yellowCards, NULL AS redCardsFromSecondYellow, NULL AS redCards, NULL AS comment, NULL AS held FROM tournaments) "
                    ." A ORDER BY date ASC";

                $matchesStmt = $conn->prepare($cmd);
            }
            $matchesStmt->execute();
            $matchesResult = $matchesStmt->get_result();
            $matches = $matchesResult->fetch_all(MYSQLI_ASSOC);

            $matchesByMonth = [];

            foreach ($matches as &$match) {
                    $t = strtotime($match['date']);
                    $matchMonth = monthNameNatural(strftime('%m', $t)) . " " . strftime('%Y', $t);

                    if (!isset($matchesByMonth[$matchMonth])) {
                        $matchesByMonth[$matchMonth] = [];
                        $matchesByMonth[$matchMonth]['month'] = $matchMonth;
                        $matchesByMonth[$matchMonth]['matches'] = [];
                    }

                    array_push($matchesByMonth[$matchMonth]['matches'], $match);

            }

            $monthSummary = [];

            $i = 0;
            foreach ($matchesByMonth as &$monthMatches) {
                $monthSummary[$i] = [];
                $monthSummary[$i]['name'] = $monthMatches['month'];
                $monthSummary[$i]['matches'] = 0;
                $monthSummary[$i]['asRef'] = 0;
                $monthSummary[$i]['yellowCards'] = 0;
                $monthSummary[$i]['redCardsFromSecondYellow'] = 0;
                $monthSummary[$i]['redCards'] = 0;
                $monthSummary[$i]['asAR'] = 0;
                $monthSummary[$i]['asFO'] = 0;
                $monthSummary[$i]['notHeld'] = 0;
                $monthSummary[$i]['held'] = 0;
                $monthSummary[$i]['tournaments'] = 0;
                $monthSummary[$i]['equivalent'] = 0;
                $monthSummary[$i]['costs'] = 0;
                $monthSummary[$i]['income'] = 0;
                $monthSummary[$i]['tax'] = 0;
                $monthSummary[$i]['total'] = 0;

                foreach ($monthMatches['matches'] as &$match) {
                    $t = strtotime($match['date']);
                    $date = date('l, d.m.Y H:i', $t);
                    $already = strtotime($date) < time();

                    if ($match['name'] == null) {
                        $monthSummary[$i]['matches']++;

                        if ($already) {
                            $equivalent = getPay($conn, $match['pay'], $match['role'], $match['date']);

                            if (!$match['held']) {
                                $monthSummary[$i]['notHeld']++;

                                if($t < $changeMultiplierDate) $notHeldMultiplier = 0.5;
                                else $notHeldMultiplier = 0.7;

                                $equivalent = round($equivalent * $notHeldMultiplier);
                            } else {
                                $monthSummary[$i]['held']++;
                            }

                            $costs = round($equivalent * 0.2);
                            $income = $equivalent - $costs;

                            $taxValue = getTaxValue($conn, $match['date']);

                            $tax = round($income * $taxValue);
                            $total = $equivalent - $tax;

                            $monthSummary[$i]['equivalent'] += $equivalent;
                            $monthSummary[$i]['costs'] += $costs;
                            $monthSummary[$i]['income'] += $income;
                            $monthSummary[$i]['tax'] += $tax;
                            $monthSummary[$i]['total'] += $total;

                            switch ($match['role']) {
                                case 'R':
                                    $monthSummary[$i]['asRef']++;

                                    if($match['cards']) {
                                        $monthSummary[$i]['yellowCards'] += $match['yellowCards'];
                                        $monthSummary[$i]['redCardsFromSecondYellow'] += $match['redCardsFromSecondYellow'];
                                        $monthSummary[$i]['redCards'] += $match['redCards'];
                                    }

                                    break;
                                case 'AR':
                                    $monthSummary[$i]['asAR']++;
                                    break;
                                case 'FO':
                                    $monthSummary[$i]['asFO']++;
                                    break;
                            }
                        }
                    } else {
                        $monthSummary[$i]['tournaments']++;

                        $monthSummary[$i]['total'] += $match['pay'];
                    }
                }

                $i++;
            }

            $matchesTotal = 0;
            $asRefTotal = 0;
            $yellowCardsTotal = 0;
            $redCardsFromSecondYellowTotal = 0;
            $redCardsTotal = 0;
            $asARTotal = 0;
            $asFOTotal = 0;
            $notHeldTotal = 0;
            $heldTotal = 0;
            $tournamentsTotal = 0;
            $equivalentTotal = 0;
            $costsTotal = 0;
            $incomeTotal = 0;
            $taxTotal = 0;
            $totalTotal = 0;

            foreach ($monthSummary as &$month) {
                $matchesTotal += $month['matches'];
                $asRefTotal += $month['asRef'];
                $yellowCardsTotal += $month['yellowCards'];
                $redCardsFromSecondYellowTotal += $month['redCardsFromSecondYellow'];
                $redCardsTotal += $month['redCards'];
                $asARTotal += $month['asAR'];
                $asFOTotal += $month['asFO'];
                $notHeldTotal += $month['notHeld'];
                $heldTotal += $month['held'];
                $tournamentsTotal += $month['tournaments'];
                $equivalentTotal += $month['equivalent'];
                $costsTotal += $month['costs'];
                $incomeTotal += $month['income'];
                $taxTotal += $month['tax'];
                $totalTotal += $month['total'];
            }
        } else
            $good = false;
    }
?>
    <div id="roundMenu" class="flex">
        <?php
        if ($correct && $good) {
            if(!$all)
                echo "<a href='../index.php?round=$roundId'>";
            else
                echo "<a href='../index.php?round=all'>";
        } else
            echo "<a href='../index.php'>";
        ?>
        <i class="material-icons m-48 m-black">arrow_back</i></a>
        <?php
        if ($correct && $good) {
            if($all) echo "<h2>Wszystkie</h2>";
            else echo "<h2>".$round['name']."</h2>";
        }
        ?>
    </div>

<?php if ($correct && $good) { ?>
    <div class="flex">
        <table>
            <tr>
                <th>Miesiąc</th>
                <th>Obsadzone</th>
                <th>Główny</th>
                <th><img src="gfx/yellowCard.png" alt="Żółte kartki"></th>
                <th><img src="gfx/secondYellow.png" alt="Czerwone kartki za drugą zółtą"></th>
                <th><img src="gfx/redCard.png" alt="Czerwone kartki"></th>
                <th>Asystent</th>
                <th>Techniczny</th>
                <th>Odwołane</th>
                <th>Sędziowane</th>
                <th>Turnieje</th>
                <th>Ekwiwalent</th>
                <th>Koszty</th>
                <th>Dochód</th>
                <th>Podatek</th>
                <th>Łącznie</th>
            </tr>

            <?php foreach ($monthSummary as &$month) {
                echo "<tr>";
                echo "<td class='separatorCol' style='border-left: 3px solid rgb(201, 9, 19)'>" . $month['name'] . "</td>";
                echo "<td class='moneyCol'>" . $month['matches'] . "</td>";
                echo "<td class='moneyCol'>" . $month['asRef'] . "</td>";
                echo "<td class='cardsCol'>" . $month['yellowCards'] . "</td>";
                echo "<td class='cardsCol'>" . $month['redCardsFromSecondYellow'] . "</td>";
                echo "<td class='cardsCol'>" . $month['redCards'] . "</td>";
                echo "<td class='moneyCol'>" . $month['asAR'] . "</td>";
                echo "<td class='moneyCol'>" . $month['asFO'] . "</td>";
                echo "<td class='moneyCol'>" . $month['notHeld'] . "</td>";
                echo "<td class='moneyCol totalCol separatorCol'>" . $month['held'] . "</td>";
                echo "<td class='moneyCol totalCol separatorCol'>". $month['tournaments'] . "</td>";
                echo "<td class='moneyCol'>" . $month['equivalent'] . " zł</td>";
                echo "<td class='moneyCol'>" . $month['costs'] . " zł</td>";
                echo "<td class='moneyCol'>" . $month['income'] . " zł</td>";
                echo "<td class='moneyCol'>" . $month['tax'] . " zł</td>";
                echo "<td class='moneyCol totalCol separatorCol'>" . $month['total'] . " zł</td>";
                echo "</tr>";
            }
            ?>

            <tr class="totalRow">
                <td class="bottomLeftEmpty"></td>
                <td class='moneyCol' style='border-left: 2px solid rgb(201, 9, 19)'><?= $matchesTotal ?></td>
                <td class='moneyCol'><?= $asRefTotal ?></td>
                <td class='cardsCol'><?= $yellowCardsTotal ?></td>
                <td class='cardsCol'><?= $redCardsFromSecondYellowTotal ?></td>
                <td class='cardsCol'><?= $redCardsTotal ?></td>
                <td class='moneyCol'><?= $asARTotal ?></td>
                <td class='moneyCol'><?= $asFOTotal ?></td>
                <td class='moneyCol'><?= $notHeldTotal ?></td>
                <td class='moneyCol separatorCol'><?= $heldTotal ?></td>
                <td class='moneyCol separatorCol'><?= $tournamentsTotal ?></td>
                <td class='moneyCol'><?= $equivalentTotal ?> zł</td>
                <td class='moneyCol'><?= $costsTotal ?> zł</td>
                <td class='moneyCol'><?= $incomeTotal ?> zł</td>
                <td class='moneyCol'><?= $taxTotal ?> zł</td>
                <td class='moneyCol separatorCol'><?= $totalTotal ?> zł</td>
            </tr>
        </table>
    </div>
    <?php
} else {
    if (!$correct) echo "Niepodano id";
    else if (!$good) echo "Niepoprawne id";
}

$conn->close();
