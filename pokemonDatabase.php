<?php 
    $username = "admin";
    $password = "pass";
    $bdd = null;
    $bddError = false;

    try {
        $bdd = new PDO("mysql:host=localhost;dbname=php_projet", $username, $password);
    } catch (PDOException $error) {
        $bddError = true;
    }
?>