<?php

$dsn = "mysql:host/localhost;dbname=[DATABASE NAME];port=[PORT NUMBER HERE]";
$user = "root";
$pass = "";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    $con = new PDO($dsn, $user, $pass, $options);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->query('USE [DATABASE NAME]'); // DB name
} catch (PDOException $e) {
    echo 'Failed to connect ' . $e->getMessage();
}
