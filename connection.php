<?php
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "coursework";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
?>