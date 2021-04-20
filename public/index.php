<?php
    $devIPs = ['192.168.0.1', '192.168.0.169', gethostbyname("ravkr.duckdns.org"), '127.0.0.1', '192.168.0.8', '192.168.0.9', '192.168.0.14', '192.168.0.100'];

    if (in_array($_SERVER['REMOTE_ADDR'], $devIPs)) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }

    setlocale(LC_TIME, "pl_PL.utf8");

    require_once "php/config.php"
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#C90913">
    <meta name="author" content="SoczuKS">
    <meta name="description" content="Referee calendar and pay table">
    <meta name="keywords" content="referee,money,pay,helper,calendar,assistant">
    <title>Referee Assistant</title>
    <link href="//fonts.googleapis.com/css?family=Open+Sans&subset=latin-ext" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/round.css" rel="stylesheet">
    <link href="css/material.css" rel="stylesheet">
    <link href="css/snackbar.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
    <link href="css/postpone.css" rel="stylesheet">
    <link href="css/match.css" rel="stylesheet">
    <script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/pl.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', onLoad);
    </script>
</head>
<body>
<div class="flex mainDiv">
    <div class="flex mainFlex">
        <?php
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'match':
                    require_once "php/match.php";
                    break;

                case 'add':
                    require_once "php/add.php";
                    break;

                case 'round':
                    require_once "php/round.php";
                    break;

                case 'postpone':
                    require_once "php/postpone.php";
                    break;

                default:
                    require_once "php/main.php";
            }
        } else {
            require_once "php/main.php";
        }
        ?>
    </div>
</div>
</body>
</html>