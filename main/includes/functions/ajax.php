<?php
include '../../config.php';
// Signup functions
if (isset($_POST['user'])) {
    $user = $_POST['user'];
    $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute(array($user));
    $check = $stmt->rowCount();
    if ($check > 0) {
        echo '0';
    } else {
        echo '1';
    }
}
