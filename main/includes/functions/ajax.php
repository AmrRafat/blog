<?php
include '../../config.php';
if (isset($_POST['user'])) { // Check username in DB
    $user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
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
} elseif (isset($_POST['editFav'])) {
    $userID = $_POST['userID'];
    $article = $_POST['article'];
    $stmt = $con->prepare("SELECT fav_articles as fav FROM users WHERE userid = ?");
    $stmt->execute(array($userID));
    $check = $stmt->rowCount();
    if ($check > 0) {
        $info = $stmt->fetch();
        $favList = json_decode($info['fav']);
    } else {
        $favList = array();
    }
    if ($_POST['editFav'] == 1) { // add to db
        $favList[] = $article;
    } elseif ($_POST['editFav'] == 0) { // remove from db
        if (($key = array_search($article, $favList)) !== false) {
            unset($favList[$key]);
        }
    }
    $favList = json_encode($favList);
    $stmt1 = $con->prepare("UPDATE users SET fav_articles = ? WHERE userid = ?");
    $stmt1->execute(array($favList, $userID));
    echo $favList;
}
