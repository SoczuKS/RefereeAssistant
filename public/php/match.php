<div id="snackbar">Rezultat zapisany</div>

<?php
    require_once "config.php";

    $conn = new mysqli(Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbName);
    $conn->set_charset("utf8");

    function analyzePOST($conn) {
        if(isset($_POST['complete'])) {
            if (!isset($_POST['homeGoals'], $_POST['awayGoals'], $_POST['yellowCards'], $_POST['redCardsFromSecondYellow'], $_POST['redCards'], $_GET['id'], $_POST['password'])) return false;

            if (password_verify($_POST['password'], Config::$passwordHash)) {
                $updateStmt = $conn->prepare("UPDATE matches SET homeGoals=?, awayGoals=?, yellowCards=?, redCardsFromSecondYellow=?, redCards=?, grade=?, comment=?, held=? WHERE id=?");

                $comment = !empty($_POST['comment']) ? $_POST['comment'] : null;
                $grade = !empty($_POST['grade']) && is_numeric($_POST['grade']) ? $_POST['grade'] : null;
                $held = isset($_POST['held']) ? 1 : 0;

                $updateStmt->bind_param("iiiiiisii", $_POST['homeGoals'], $_POST['awayGoals'], $_POST['yellowCards'], $_POST['redCardsFromSecondYellow'], $_POST['redCards'], $grade, $comment, $held, $_GET['id']);
                $updateStmt->execute();

                return true;
            }
        }

        return false;
    }

    if (analyzePOST($conn)) echo
                    "<script>
                        const x = document.getElementById('snackbar');
                        x.className = 'show';
                        setTimeout(function(){ x.className = x.className.replace('show', ''); }, 5000);
                    </script>";
?>

<a href=".."><i class="material-icons m-48 m-black">arrow_back</i></a>

<?php
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM matches WHERE id=?");
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $match = $result->fetch_all(MYSQLI_ASSOC)[0];

            $homeTeam = $match['homeTeam'];
            $homeGoals = $match['homeGoals'];
            $awayTeam = $match['awayTeam'];
            $awayGoals = $match['awayGoals'];
            $yellowCards = $match['yellowCards'];
            $redCardsFromSecondYellow = $match['redCardsFromSecondYellow'];
            $redCards = $match['redCards'];
            $comment = $match['comment'];
            $grade = $match['grade'];
            $held = $match['held'];
            $t = strtotime($match['date']);
            $date = strftime('%A, %d.%m.%Y %H:%M', $t);

            echo "<h1>$homeTeam - $awayTeam</h1>";
            echo "<h2>$date</h2>";

            $submitted = $match['homeGoals'] !== NULL && $match['awayGoals'] !== NULL;

            ?>
            <form action="#" method="POST" id="matchForm">
                <label>Wynik gospodarza*<input type="number" required name="homeGoals" <?php if ($submitted) echo "disabled value='$homeGoals'"; ?>></label>
                <label>Wynik gościa*<input type="number" required name="awayGoals" <?php if ($submitted) echo "disabled value='$awayGoals'"; ?>></label>
                <label>Odbył się<input type="checkbox" name="held" <?php if ($submitted) { if ($held) echo "checked "; } else echo "checked "; if ($submitted) echo "disabled"; ?>></label>

                <fieldset>
                    <legend>Kartki</legend>
                    <div id="cardsInputDiv">
                        <div>
                            <img src="gfx/yellowCard.png" alt='Żółte'>
                            <input class="cardsInput" type="number" required name="yellowCards" <?php if ($submitted) echo "disabled value='$yellowCards'"; else echo "value=0"; ?>>
                        </div>
                        <div>
                            <img src="gfx/secondYellow.png" alt='Czerwone za drugą żółtą'>
                            <input class="cardsInput" type="number" required name="redCardsFromSecondYellow" <?php if ($submitted) echo "disabled value='$redCardsFromSecondYellow'"; else echo "value=0"; ?>>
                        </div>
                        <div>
                            <img src="gfx/redCard.png" alt='Czerwone'>
                            <input class="cardsInput" type="number" required name="redCards" <?php if ($submitted) echo "disabled value='$redCards'"; else echo "value=0"; ?>>
                        </div>
                    </div>
                </fieldset>

                <label>Ocena<input type="number" name="grade" min="0" max="10" step=".1" <?php if ($submitted) echo "disabled value='$grade'"; ?>></label>
                <label>Komentarz<textarea name="comment" <?php if ($submitted) echo "disabled"; ?>><?php if ($submitted) echo $comment; ?></textarea></label>
                <?php if(!$submitted): ?>
                    <label>Klucz uprawnień*<input type="password" required name="password"></label>
                <?php endif ?>
                <input type="submit" name="complete" value="Uzupełnij" <?php if ($submitted) echo "disabled"; ?>/>
            </form>
            <?php
        } else echo "Niepoprawne id meczu";
    } else echo "Niepodano id meczu";

    $conn->close();
