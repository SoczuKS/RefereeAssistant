<div id="snackbar">Niepowodzenie</div>

<?php
    require_once "functions.php";
    require_once "config.php";

    function analyzePOST($conn) {
        if (!isset($_POST['competition'], $_POST['date'], $_POST['homeTeam'], $_POST['awayTeam'], $_POST['role'], $_POST['password'])) return false;

        if (password_verify($_POST['password'], Config::$passwordHash)) {
            $insertStmt = $conn->prepare("INSERT INTO matches (matchNumber, date, competitionId, homeTeam, awayTeam, role) VALUES(?,?,?,?,?,?)");

            $matchNumber = !empty($_POST['matchNumber']) ? $_POST['matchNumber'] : NULL;

            $insertStmt->bind_param("isisss", $matchNumber, $_POST['date'], $_POST['competition'], $_POST['homeTeam'], $_POST['awayTeam'], $_POST['role']);

            $insertStmt->execute();

            return true;
        }

        return false;
    }

    $conn = new mysqli(Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbName);
    $conn->set_charset("utf8");

    if (!empty($_POST))
        if (!analyzePOST($conn)) echo
                    "<script>
                        const x = document.getElementById('snackbar');
                        x.className = 'show';
                        setTimeout(function(){ x.className = x.className.replace('show', ''); }, 5000);
                    </script>";
    ?>

<a href=".." style="width: 48px; height: 48px"><i class="material-icons m-48 m-black">arrow_back</i></a>

<form action="#" method="POST" id="addMatchForm">
    <label>Numer meczu<input name="matchNumber" pattern="[0-9]{5,}"></label>
    <label>Data*<input name="date" required type="datetime-local"></label>

    <label>Rozgrywki*
        <select name="competition" form="addMatchForm">
            <?php
                $competitions = getCompetitions($conn);

                foreach ($competitions as &$comp) {
                    $id = $comp['compId'];
                    $name = $comp['compName'];

                    echo "<option value='$id'>$name</option>";
                }
            ?>
        </select>
    </label>

    <label>Gospodarz*<input name="homeTeam" required type="text"></label>
    <label>Goście*<input name="awayTeam" required type="text"></label>
    <label>Rola*
        <select name="role" form="addMatchForm">
            <option value="R">Sędzia główny</option>
            <option value="AR">Sędzia asystent</option>
        </select>
    </label>
    <label>Klucz uprawnień*<input type="password" required name="password"></label>
    <input type="submit" name="add" value="Dodaj"/>
</form>

<?php
    $conn->close();
