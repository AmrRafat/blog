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
} elseif (isset($_POST['editFav'])) { // Add and remove from favourite list
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
} elseif (isset($_POST['articleID'])) {
    $articleID = $_POST['articleID'];
    $stmt = $con->prepare("DELETE FROM articles WHERE article_id = ?");
    $stmt->execute(array($articleID));
    foreach (scandir('../../layout/imgs/articles/' . $articleID) as $item) {
        if ($item == '.' || $item == "..") {
            continue;
        } else {
            unlink('../../layout/imgs/articles/' . $articleID . '/' . $item);
        }
    }
    rmdir('../../layout/imgs/articles/' . $articleID);
} elseif (isset($_POST['qID'])) {
    if ($_POST['way'] == 'done') {
        $stmt = $con->prepare("SELECT * FROM answers WHERE approved = 1 && question_id = ?");
        $stmt->execute(array($_POST['qID']));
        $count = $stmt->rowCount();
        if ($count > 0) {
            $stmt1 = $con->prepare('UPDATE questions SET done = 1 WHERE question_id = ?');
            $stmt1->execute(array($_POST['qID']));
            echo 1;
        } else {
            echo 0;
        }
    } else {
        $stmt1 = $con->prepare('UPDATE questions SET done = 0 WHERE question_id = ?');
        $stmt1->execute(array($_POST['qID']));
    }
} elseif (isset($_POST['markAnswer'])) {
    $answer2Change = $_POST['answerID'];
    $way2Change = $_POST['markAnswer'];
    $questionTarget = $_POST['questionID'];
    if ($way2Change == 1) {
        $stmt = $con->prepare("UPDATE answers SET approved = 0 WHERE question_id = ?");
        $stmt->execute(array($questionTarget));
        $stmt1 = $con->prepare("UPDATE answers SET approved = 1 WHERE answer_id = ?");
        $stmt1->execute(array($answer2Change));
    } else {
        $stmt1 = $con->prepare("UPDATE answers SET approved = 0 WHERE answer_id = ?");
        $stmt1->execute(array($answer2Change));
    }
}
