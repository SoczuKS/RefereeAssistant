<?php
    function monthNameNatural($month) {
        $months['01'] = "Styczeń";
        $months['02'] = "Luty";
        $months['03'] = "Marzec";
        $months['04'] = "Kwiecień";
        $months['05'] = "Maj";
        $months['06'] = "Czerwiec";
        $months['07'] = "Lipiec";
        $months['08'] = "Sierpień";
        $months['09'] = "Wrzesień";
        $months['10'] = "Październik";
        $months['11'] = "Listopad";
        $months['12'] = "Grudzień";

        return $months[$month];
    }

    function getTaxValue($conn, $matchday) {
        $query = "SELECT value FROM tax WHERE (? >= startDate AND ? < endDate) OR (? >= startDate AND endDate IS NULL)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $matchday, $matchday, $matchday);
        $stmt->execute();

        return $stmt->get_result()->fetch_all()[0][0];
    }

    function findPayTable($conn, $matchday) {
        $query = "SELECT payTable FROM payTableTimeframe WHERE (? >= startDate AND ? < endDate) OR (? >= startDate AND endDate IS NULL)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $matchday, $matchday, $matchday);
        $stmt->execute();

        return $stmt->get_result()->fetch_all()[0][0];
    }

    function getPay($conn, $id, $role, $matchday) {
        $payTable = findPayTable($conn, $matchday);

        $query = "SELECT ".$role." FROM {$payTable} WHERE id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all()[0][0];
    }

    function getRound($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM rounds WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function findRoundId($conn) {
        $stmt = $conn->prepare("SELECT id FROM rounds WHERE startDate <= CURDATE() AND endDate > CURDATE()");
        $stmt->execute();

        return $stmt->get_result()->fetch_all()[0][0];
    }

    function getCompetitions($conn) {
        $stmt = $conn->prepare("SELECT * FROM competitions");
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }