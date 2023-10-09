<?php session_start();
include "../specInit.php";
?>
<div style="margin-top:90px"></div>
<div class="container article">
    <?php
if (isset($_POST['comment'])) {
    $userID = $_SESSION['id'];
    $commentInfo = explode("\r\n", trim($_POST['comment']));
    $commentInfo = json_encode($commentInfo);
    $dateNeeded = date('Y-m-j H-i-s');
    $urlInfo = explode('/', $_SERVER['REQUEST_URI']);
    $lastOne = $urlInfo[count($urlInfo) - 1];
    $rate = $_POST['rate'];
    $needed_article_id = explode('.', $lastOne)[0];
    $stmt = $con->prepare("SELECT user_id FROM comments WHERE article_id = ?");
    $stmt->execute(array($needed_article_id));
    $commentsUsers = $stmt->fetchAll();
    $found = 0;
    foreach ($commentsUsers as $commentsUser) {
        if ($commentsUser['user_id'] == $userID) {
            $found = 1;
        }
    }
    if ($found == 0) {
        $stmt1 = $con->prepare("INSERT INTO comments(user_id,article_id,comment,date,rate) VALUES(?,?,?,?,?)");
        $stmt1->execute(array($userID, $needed_article_id, $commentInfo, $dateNeeded, $rate));
    } else {
        header('location: #comment');
    }
} elseif (isset($_POST['editComment'])) {
    $commentID = $_POST['comment_id'];
    $stmt = $con->prepare("SELECT * FROM comments WHERE comment_id = ?");
    $stmt->execute(array($commentID));
    $info = $stmt->fetch();
    $oldComment = $info['comment'];
    $oldRate = $info['rate'];
    if ($_POST['editComment'] != "") {
        if ($_POST['editComment'] != "DEL_COMMENT") {
            $commentInfo = explode("\r\n", trim($_POST['editComment']));
            $commentInfo = json_encode($commentInfo);
            $rate = $_POST['rate'];
            if ($oldComment != $commentInfo || $oldRate != $rate) {
                $userID = $_SESSION['id'];
                $dateNeeded = date('Y-m-j H-i-s');
                $urlInfo = explode('/', $_SERVER['REQUEST_URI']);
                $lastOne = $urlInfo[count($urlInfo) - 1];
                $needed_article_id = explode('.', $lastOne)[0];
                $stmt1 = $con->prepare("UPDATE comments SET comment = ? , date = ?, rate = ? WHERE comment_id = ?");
                $stmt1->execute(array($commentInfo, $dateNeeded, $rate, $commentID));}
        }
    }
} elseif (!isset($_POST['comment']) && !isset($_POST['editComment']) && isset($_POST['comment_id'])) {
    $commentID = $_POST['comment_id'];
    $stmt = $con->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->execute(array($commentID));
}
unset($_POST);

?>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-5 col-12">
                    <h1 class="article-title">New Title to show here</h1>
                </div>
                <div class="col-lg-4 offset-lg-3 col-12">
                    <h4 class="madeBy mt-2">Mabe by: Amr Rafat Mahmoud</h4>
                    <h4>Rating: <?php rating(1)?></h4>
                        <h4 class="publishDate">Date: 5/10/2023</h4>
                        <h4 class="subject">Subject: Career</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h3 class="subtitle">First</h3>
                <p class="paragraph mb-0">Many years ago,</p>
                <p class="paragraph mb-0">I had a dream.</p>
                <img src="imgs/1.jpeg" alt="" class="mx-auto d-block img-thumbnail img-fluid my-3 articleImg border-2">
                <h3 class="subtitle">Second</h3>
                <p class="paragraph mb-0">Many years ago,</p>
                <p class="paragraph mb-0">I had a dream.</p>
                <img src="imgs/2.jpeg" alt="" class="mx-auto d-block img-thumbnail img-fluid my-3 articleImg border-2">
                <p class="paragraph mb-0">Many years ago,</p>
                <p class="paragraph mb-0">I had a dream.</p>
                <img src="imgs/3.jpeg" alt="" class="mx-auto d-block img-thumbnail img-fluid my-3 articleImg border-2">
                <h3 class="subtitle">Good One!</h3>
                <p class="paragraph mb-0">Many years ago,</p>
                <p class="paragraph mb-0">I had a dream.</p>
                <p class="paragraph mb-0">Thank you!</p>
            </div>
            <div class="card-footer d-flex justify-content-end flex-wrap">
                <span class="me-2">Made by: Amr Rafat Mahmoud</span>
                <div class="vr me-2"></div>
                <span class="me-2">Rating: <?php rating(1)?></span>
                <div class="vr me-2">
                    </div>
                    <span>Article No: 1</span>
                </div>
            </div>
                <?php newComment()?>
                <?php $id = (isset($_SESSION['id'])) ? $_SESSION['id'] : 0;
commentsSection(1, $id)?>
        </div>
        <?php include "../../includes/templates/footer.php";