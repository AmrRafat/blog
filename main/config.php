<?php
// IMPORTANT !!!
// Please, create the database in your sql server then fill out the following
$host = ''; //mysql host name
$user = ''; //mysql username
$password = ''; //mysql password
$db = ''; //mysql database
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
    $con = new PDO($dsn, $user, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $con->prepare("SELECT SCHEMA_NAME
    FROM INFORMATION_SCHEMA.SCHEMATA
    WHERE SCHEMA_NAME = ?");
    $stmt->execute(array($db));
    $check = $stmt->rowCount();
    if ($check == 0) {
        echo 'Database needs to be created!';
    } else {
        $con->query('USE -------'); // remove ----- and put db name
        $stmt = $con->prepare('SHOW TABLES');
        $stmt->execute();
        $countRows = $stmt->rowCount();
        if ($countRows === 0) {
            echo 'No Table!';
            $stmt1 = $con->prepare('CREATE TABLE users (
                userid iNT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255),
                password VARCHAR(255),
                fullname VARCHAR(255),
                email VARCHAR(255),
                birthdate date DEFAULT NULL,
                gender tinyint(1) DEFAULT NULL,
                country VARCHAR(255) DEFAULT NULL,
                educationlvl VARCHAR(255) DEFAULT NULL,
                specialization VARCHAR(255) DEFAULT NULL,
                status tinyint(1) DEFAULT 0,
                access tinyint(1) DEFAULT 0,
                fav_articles text
                )');
            $stmt2 = $con->prepare("CREATE TABLE articles(
                article_id int(11) AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(255),
                date date DEFAULT CURRENT_TIMESTAMP,
                status tinyint(1) DEFAULT 0,
                title VARCHAR(255),
                description VARCHAR(255),
                ext VARCHAR(255) DEFAULT NULL,
                user_id int(11),
                elementsArray text
                )");
            $stmt3 = $con->prepare("CREATE TABLE comments(
                comment_id int(11) AUTO_INCREMENT PRIMARY KEY,
                user_id int(11),
                article_id int(11),
                comment text,
                rate int(11),
                date datetime
                )");
            $stmt4 = $con->prepare("CREATE TABLE questions (
                question_id int(11) AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(255),
                question text,
                details text,
                date datetime,
                user_id int(11),
                done int(11) DEFAULT 0
                )");
            $stmt5 = $con->prepare("CREATE TABLE answers(
                answer_id int(11) AUTO_INCREMENT PRIMARY KEY,
                question_id int(11),
                user_id int(11),
                answer text,
                date datetime,
                approved int(11) DEFAULT 0
                )");
            $stmt1->execute();
            $stmt2->execute();
            $stmt3->execute();
            $stmt4->execute();
            $stmt5->execute();
        }
    }
} catch (PDOException $e) {
    echo 'Failed to connect ' . $e->getMessage();
}
