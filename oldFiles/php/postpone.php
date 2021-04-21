<div id="snackbar"></div>

<?php
    require_once "config.php";

    $conn = new mysqli(Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbName);
    $conn->set_charset("utf8");

    function analyzePOST($conn) {
        if(isset($_POST['postpone'])) {
            if (!isset($_POST['date'], $_POST['password'], $_GET['id'])) return 0;

            if (password_verify($_POST['password'], Config::$passwordHash)) {
                $updateStmt = $conn->prepare("UPDATE matches SET date=? WHERE id=?");
                $updateStmt->bind_param("si", $_POST['date'], $_GET['id']);
                $updateStmt->execute();

                return 1;
            }
        } else if(isset($_POST['remove'])) {
            if (!isset($_POST['password'], $_GET['id'])) return 0;

            if(password_verify($_POST['password'], Config::$passwordHash)) {
                $deleteStmt = $conn->prepare("DELETE FROM matches WHERE id=?");
                $deleteStmt->bind_param("i", $_GET['id']);
                $deleteStmt->execute();

                return 2;
            }
        }

        return 0;
    }

    $result = analyzePOST($conn);

    if ($result == 1) echo
                        "<script>
                            const x = document.getElementById('snackbar');
                            x.className = 'show';
                            x.innerText = 'Termin zaktualizowany';
                            setTimeout(function(){ x.className = x.className.replace('show', ''); }, 5000);
                        </script>";
    else if($result == 2) header("Location: https://".$_SERVER['HTTP_HOST']);
?>

<a href="../index.php"><i class="material-icons m-48 m-black">arrow_back</i></a>

<?php
    if(isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT homeTeam, awayTeam, date FROM matches WHERE id=?");
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $match = $result->fetch_all(MYSQLI_ASSOC)[0];

            $homeTeam = $match['homeTeam'];
            $awayTeam = $match['awayTeam'];
            $date = $match['date'];
            $t = strtotime($date);
            $readableDte = strftime('%A, %d.%m.%Y %H:%M', $t);

            echo "<h1>$homeTeam - $awayTeam</h1>";

            ?>
            <form action="#" method="POST" id="postponeForm">
                <label>Nowy termin<input type="datetime-local" name="date" id="postponeDatePicker" value="<?= $date ?>"></label>
                <label>Klucz uprawnień*<input type="password" required name="password"></label>
                <div id="formButtonsDiv">
                    <input type="submit" name="postpone" value="Zmień termin" disabled id="postponeButton"/>
                    <input type="submit" name="remove" value="Usuń"/>
                </div>
            </form>
            <?php
        } else echo "Niepoprawne id meczu";
    } else echo "Niepodano id meczu";

    $conn->close();
