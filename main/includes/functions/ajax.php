<?php
include '../../config.php';
if (isset($_POST['user'])) { // Check username in DB
    $user = $_POST['user'];
    $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute(array($user));
    $check = $stmt->rowCount();
    if ($check > 0) {
        if (!isset($_POST['id'])) {
            echo '0';
        } else {
            $data = $stmt->fetch();
            $idInDb = $data['userid'];
            if ($idInDb == $_POST['id']) {
                echo '2';
            } else {
                echo '0';
            }
        }
    } else {
        echo '1';
    }
}
