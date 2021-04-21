<?php
    require_once "functions.php";
    require_once "config.php";

    $conn = new mysqli(Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbName);
    $conn->set_charset("utf8");

    $all = false;

    if (!isset($_GET['round'])) {
        $roundId = findRoundId($conn);
    } else {
        if($_GET['round'] == 'all') $all = true;
        else $roundId = $_GET['round'];
    }

    if(!$all) {
        $roundRes = getRound($conn, $roundId);
        if (count($roundRes) == 0) {
            echo "Niepoprawne id";
            var_dump($roundId);
            return;
        }

        $round = $roundRes[0];
    }
?>

    <div id="menu" class="flex">
        <div class="menuRow flex">
            <?php
            if(!$all) {
                $previousRoundLink = '';
                $nextRoundLink = '';

                $indexPage = "index.php?round=";

                if (!empty(getRound($conn, $roundId - 1))) $previousRoundLink = $indexPage . ($roundId - 1);
                if (!empty(getRound($conn, $roundId + 1))) $nextRoundLink = $indexPage . ($roundId + 1);
            }
            ?>
            <?php if(!$all): ?>
                <?php if($previousRoundLink != ''): ?>
                    <a href="<?= $previousRoundLink ?>"><i class="material-icons m-48 m-blue rot-180">play_arrow</i></a>
                <?php endif; ?>
                <p><?= $round['name'] ?></p>

                <?php if($nextRoundLink != ""): ?>
                    <a href="<?= $nextRoundLink ?>"><i class="material-icons m-48 m-blue">play_arrow</i></a>
                <?php endif; ?>
            <?php else: ?>
                <p>Wszystkie</p>
            <?php endif; ?>

        </div>
        <div id="menuSecondRow" class="menuRow flex">
            <a href="index.php?page=add<?php if($all) echo ""?>"><i class="material-icons m-48 m-green">add_circle</i></a>
            <?php if(!$all): ?>
                <a href="index.php?page=round&id=<?= $roundId ?>"><i class="material-icons m-48 m-gray">description</i></a>
                <a href="index.php?round=all"><i class="material-icons m-48 m-yellow">bookmarks</i></a>
            <?php else: ?>
                <a href="index.php?page=round&id=all"><i class="material-icons m-48 m-gray">description</i></a>
                <a href="index.php"><i class="material-icons m-48 m-yellow">bookmark</i></a>
            <?php endif; ?>
        </div>
    </div>

<?php
    if(!$all) {
        $cmd = "SELECT * FROM (SELECT id, matchNumber, date, NULL AS name, compId, compName, pay, cards, homeTeam, homeGoals, awayGoals, awayTeam, role, yellowCards, redCardsFromSecondYellow, redCards, grade, comment, held FROM matches INNER JOIN competitions ON matches.competitionId = competitions.compId "
                ."UNION SELECT id, NULL AS matchNumber, date, name, NULL AS compId, NULL AS compName, pay, NULL AS cards, NULL AS homeTeam, NULL AS homeGoals, NULL AS awayGoals, NULL AS awayTeam, NULL AS role, NULL AS yellowCards, NULL AS redCardsFromSecondYellow, NULL AS redCards, NULL AS grade, NULL AS comment, NULL AS held FROM tournaments) "
                ." A WHERE date > ? AND date < ? ORDER BY date ASC";

        $matchesStmt = $conn->prepare($cmd);
        $matchesStmt->bind_param("ss", $round['startDate'], $round['endDate']);
    } else {
        $cmd = "SELECT * FROM (SELECT id, matchNumber, date, NULL AS name, compId, compName, pay, cards, homeTeam, homeGoals, awayGoals, awayTeam, role, yellowCards, redCardsFromSecondYellow, redCards, grade, comment, held FROM matches INNER JOIN competitions ON matches.competitionId = competitions.compId "
            ."UNION SELECT id, NULL AS matchNumber, date, name, NULL AS compId, NULL AS compName, pay, NULL AS cards, NULL AS homeTeam, NULL AS homeGoals, NULL AS awayGoals, NULL AS awayTeam, NULL AS role, NULL AS yellowCards, NULL AS redCardsFromSecondYellow, NULL AS redCards, NULL AS grade, NULL AS comment, NULL AS held FROM tournaments) "
            ." A ORDER BY date ASC";

        $matchesStmt = $conn->prepare($cmd);
    }
    $matchesStmt->execute();

    $matchesResult = $matchesStmt->get_result();

    $matches = $matchesResult->fetch_all(MYSQLI_ASSOC);

    if(count($matches) === 0) return;

?>
    <div>
        <table>
            <tr>
                <th>Numer</th>
                <th>Data</th>
                <th>Rozgrywki</th>
                <th>Gospodarz</th>
                <th class='emptyCell'></th>
                <th>Goście</th>
                <th>Rola</th>
                <th>Ek</th>
                <th>K</th>
                <th>D-d</th>
                <th>P</th>
                <th>R</th>
                <th><img src="gfx/yellowCard.png" alt="Żółte kartki"></th>
                <th><img src="gfx/secondYellow.png" alt="Czerwone kartki za drugą zółtą"></th>
                <th><img src="gfx/redCard.png" alt="Czerwone kartki"></th>
                <th>Ocena</th>
                <th>Komentarz</th>
            </tr>

<?php
    $sumOfEquivalent = 0;
    $sumOfCosts = 0;
    $sumOfIncome = 0;
    $sumOfTax = 0;
    $sumOfTotal = 0;

    $matchesAsReferee = 0;
    $matchesAsAR = 0;
    $matchesAsFO = 0;
    $matchesNotHeld = 0;

    $notHeldMultiplier = 0.5;
    $changeMultiplierDate = strtotime("2019-08-01");
    $newTaxDate = strtotime("2019-10-01");

    foreach ($matches as &$match) {
        if($match['name'] == null) {
            $t = strtotime($match['date']);
            $date = date('l, d.m.Y H:i', $t);

            $matchMonth = date('m', $t);
            $matchYear = date('Y', $t);

            $dateToDisplay = strftime('%A %d.%m.%Y&nbsp;%H:%M', $t);

            $already = strtotime($date) < time();

            $matchId = $match['id'];

            $cssClass = ($already ? "pastMatch" : "futureMatch");

            echo "<tr class='clickable-row " . $cssClass . "' data-id='$matchId'>";
            echo "<td class='matchNumberCol'>" . $match['matchNumber'] . "</td>";

            echo "<td class='dateCol'>" . $dateToDisplay . "</td>";
            echo "<td class='competitionCol'>" . $match['compName'] . "</td>";
            echo "<td class='homeTeamCol'>" . $match['homeTeam'] . "</td>";
            echo "<td class='resultCol'>" . $match['homeGoals'] . ':' . $match['awayGoals'] . "</td>";
            echo "<td class='awayTeamCol'>" . $match['awayTeam'] . "</td>";
            echo "<td class='roleCol'>" . $match['role'] . "</td>";

            if ($already) {
                $equivalent = getPay($conn, $match['pay'], $match['role'], $match['date']);

                if (!$match['held']) {
                    $matchesNotHeld++;

                    if($t < $changeMultiplierDate) $notHeldMultiplier = 0.5;
                    else $notHeldMultiplier = 0.7;

                    $equivalent = round($equivalent * $notHeldMultiplier);
                }

                $costs = round($equivalent * 0.2);
                $income = $equivalent - $costs;

                $taxValue = getTaxValue($conn, $match['date']);

                $tax = round($income * $taxValue);
                $total = $equivalent - $tax;

                $sumOfCosts += $costs;
                $sumOfEquivalent += $equivalent;
                $sumOfIncome += $income;
                $sumOfTax += $tax;
                $sumOfTotal += $total;

                switch ($match['role']) {
                    case 'R':
                        $matchesAsReferee++;
                        break;
                    case 'AR':
                        $matchesAsAR++;
                        break;
                    case 'FO':
                        $matchesAsFO++;
                        break;
                }

                echo "<td class='moneyCol'>" . $equivalent . "</td>";
                echo "<td class='moneyCol'>" . $costs . "</td>";
                echo "<td class='moneyCol'>" . $income . "</td>";
                echo "<td class='moneyCol'>" . $tax . "</td>";
                echo "<td class='moneyCol totalCol'>" . $total . "</td>";
            } else {
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
            }

            if($match['role'] == 'R' && $match['cards'] && ($match['held'] || !$already)) {
                echo "<td class='cardsCol'>" . $match['yellowCards'] . "</td>";
                echo "<td class='cardsCol'>" . $match['redCardsFromSecondYellow'] . "</td>";
                echo "<td class='cardsCol'>" . $match['redCards'] . "</td>";
            } else {
                echo "<td colspan=3 class='cellCrossedOut'>&nbsp;</td>";
            }

            if($match['grade'] != null)
                echo "<td class='gradeCol'>" . $match['grade'] . "</td>";
            else
                echo "<td class='cellCrossedOut gradeCol'>&nbsp;</td>";

            if($match['comment'] != null)
                echo "<td class='commentCol'>" . $match['comment'] . "</td>";
            else
                echo "<td class='commentCol cellCrossedOut'>&nbsp;</td>";

            echo "</tr>";
        } else {
            $t = strtotime($match['date']);
            $date = date('l, d.m.Y', $t);
            $dateToDisplay = strftime('%A %d.%m.%Y', $t);

            $already = strtotime($date) < time();

            $id = $match['id'];
            $name = $match['name'];
            $pay = $match['pay'];
            $grade = $match['grade'];

            $cssClass = ($already ? "pastTournament" : "futureTournament");

            echo "<tr class='clickable-row " . $cssClass . "' data-id='$id'>";

            echo "<td class='dateCol' colspan='2'>$dateToDisplay</td>";
            echo "<td class='competitionCol' colspan='9'>$name</td>";
            echo "<td class='moneyCol totalCol'>$pay</td>";
            echo "<td colspan=5 class='cellCrossedOut'>&nbsp;</td>";

            echo "</tr>";
        }
    }
    echo "</table></div>";

    $conn->close();

