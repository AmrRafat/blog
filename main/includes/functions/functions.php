<?php
// Function to create articles
function createArticle($articleTitle, $name, $date, $subject, $elementsArray, $articleNo)
{
    $imgsCount = 1;
    $elements = "";
    $elementsKeys = array_keys($elementsArray);
    foreach ($elementsKeys as $key) {
        if (str_contains($key, 'subtitle')) {
            $elements = $elements . '<h3 class="subtitle">' . $elementsArray[$key] . '</h3>';
        } elseif (str_contains($key, 'paragraph')) {
            foreach ($elementsArray[$key] as $line) {
                $elements = $elements . '<p class="paragraph mb-0">' . $line . '</p>';
            }
        } elseif (str_contains($key, 'img')) {
            $imgExt = explode('/', $elementsArray[$key]);
            $elements = $elements . '<img src="imgs/' . $imgsCount . '.' . $imgExt[1] . '" alt="" class="mx-auto d-block img-thumbnail img-fluid my-3 articleImg border-2">';
            $imgsCount++;
        }
    }
    return '<?php session_start();
    include "../specInit.php";
    if (isset($_POST["comment"])) {
        $userID = $_SESSION["id"];
        $commentInfo = explode("\r\n", trim($_POST["comment"]));
        $commentInfo = json_encode($commentInfo);
        $dateNeeded = date("Y-m-j H-i-s");
        $urlInfo = explode("/", $_SERVER["REQUEST_URI"]);
        $lastOne = $urlInfo[count($urlInfo) - 1];
        $rate = $_POST["rate"];
        $needed_article_id = explode(".", $lastOne)[0];
        $stmt = $con->prepare("SELECT user_id FROM comments WHERE article_id = ?");
        $stmt->execute(array($needed_article_id));
        $commentsUsers = $stmt->fetchAll();
        $found = 0;
        foreach ($commentsUsers as $commentsUser) {
            if ($commentsUser["user_id"] == $userID) {
                $found = 1;
            }
        }
        if ($found == 0) {
            $stmt1 = $con->prepare("INSERT INTO comments(user_id,article_id,comment,date,rate) VALUES(?,?,?,?,?)");
            $stmt1->execute(array($userID, $needed_article_id, $commentInfo, $dateNeeded, $rate));
        } else {
            header("location: #comment");
        }
    } elseif (isset($_POST["editComment"])) {
        $commentID = $_POST["comment_id"];
        $stmt = $con->prepare("SELECT * FROM comments WHERE comment_id = ?");
        $stmt->execute(array($commentID));
        $info = $stmt->fetch();
        $oldComment = $info["comment"];
        $oldRate = $info["rate"];
        if ($_POST["editComment"] != "") {
            if ($_POST["editComment"] != "DEL_COMMENT") {
                $commentInfo = explode("\r\n", trim($_POST["editComment"]));
                $commentInfo = json_encode($commentInfo);
                $rate = $_POST["rate"];
                if ($oldComment != $commentInfo || $oldRate != $rate) {
                    $userID = $_SESSION["id"];
                    $dateNeeded = date("Y-m-j H-i-s");
                    $urlInfo = explode("/", $_SERVER["REQUEST_URI"]);
                    $lastOne = $urlInfo[count($urlInfo) - 1];
                    $needed_article_id = explode(".", $lastOne)[0];
                    $stmt1 = $con->prepare("UPDATE comments SET comment = ? , date = ?, rate = ? WHERE comment_id = ?");
                    $stmt1->execute(array($commentInfo, $dateNeeded, $rate, $commentID));}
            }
        }
    } elseif (!isset($_POST["comment"]) && !isset($_POST["editComment"]) && isset($_POST["comment_id"])) {
        $commentID = $_POST["comment_id"];
        $stmt = $con->prepare("DELETE FROM comments WHERE comment_id = ?");
        $stmt->execute(array($commentID));
    }
    unset($_POST);
    ?>
    <div style="margin-top:90px"></div>
    <div class="container article">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-12">
                    <h1 class="article-title">' . $articleTitle . '</h1>
                    </div>
                    <div class="col-lg-4 offset-lg-3 col-12">
                    <h4 class="madeBy mt-2">Mabe by: ' . $name . '</h4>
                    <h4 class="rating">Rating: <?php rating (' . $articleNo . ') ?></h4>
                    <h4 class="publishDate">Date: ' . $date . '</h4>
                    <h4 class="subject">Subject: ' . $subject . '</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">' . $elements . '</div>
                <div class="card-footer d-flex justify-content-end flex-wrap">
                <span class="me-2">Made by: ' . $name . '</span>
                <div class="vr me-2"></div>
                <span class="me-2">Rating: <?php rating (' . $articleNo . ') ?></span>
                <div class="vr me-2"></div>
                <span>Article No: ' . $articleNo . '</span>
            </div>
        </div>
        <?php newComment() ?>
        <?php $id = (isset($_SESSION["id"])) ? $_SESSION["id"] : 0; commentsSection(' . $articleNo . ', $id ) ?>
    </div>
    <?php include "../../includes/templates/footer.php";';
}

function newComment()
{
    if (isset($_SESSION['id'])) {
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="mb-3" method="post">
            <div class="card">
                <div class="card-body">
                    <div class="form-floating mb-2">
                        <!-- TODO: Add max number of characters to description -->
                        <textarea class="form-control" name="comment" placeholder="Comment Here" id="commentArea" style="height: 100px; resize: none;" required></textarea>
                        <label for="commentArea">Comment</label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center px-3">
                    <div class="rateField">
                        <span>Leave a rate: </span>
                        <i class="fa-regular fa-star rateChoice one"></i>
                        <i class="fa-regular fa-star rateChoice two"></i>
                        <i class="fa-regular fa-star rateChoice three"></i>
                        <i class="fa-regular fa-star rateChoice four"></i>
                        <i class="fa-regular fa-star rateChoice five"></i>
                        <input type="hidden" name="rate" class="rate" value="5">
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-secondary me-2">Cancel</button>
                        <button type="submit" class="btn btn-outline-secondary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
        <?php
} else {?>
    <div class="card mb-2">
        <a href="../../index.php" class="btn btn-outline-secondary">Login/Register</a>
    </div>
    <?php
}
}

function commentsSection($article_id, $userID = 0)
{ // To get all comments of an article and set the first one as the user's comment
    global $con;
    $stmt = $con->prepare("SELECT *, users.fullname AS fullname FROM comments INNER JOIN users ON comments.user_id = users.userid WHERE comments.article_id = ?");
    $stmt->execute(array($article_id));
    $comments = $stmt->fetchAll();
    if (!empty($comments)) {
        if ($userID != 0) {
            $stmt1 = $con->prepare("SELECT *, users.fullname AS fullname FROM comments INNER JOIN users ON comments.user_id = users.userid WHERE comments.article_id = ? and comments.user_id = ?");
            $stmt1->execute(array($article_id, $userID));
            $check = $stmt1->rowCount();
            if ($check > 0) {
                $firstComment = $stmt1->fetch();
                ?>
    <!-- Comment of current user if found -->
    <form id="comment" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="comment_id" value="<?php echo $firstComment['comment_id'] ?>">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="https://placehold.co/300x300" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
                    <h4><?php echo $firstComment['fullname'] ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <?php
if ($_SESSION['id'] == $firstComment['user_id']) {
                    ?>
                <div class="me-2">
                    <button type="button" class="btn btn-outline-secondary editComment">Edit</button>
                    <button type="button" class="btn btn-outline-secondary delComment">Delete</button>
                </div>
                <?php
}
                ?>
                <div>
                    <?php
echo $firstComment['date'];
                ?>
                </div>
            </div>
        </div>
        <div class="card-body px-4">
            <?php
$mainComment = json_decode($firstComment['comment']);
                foreach ($mainComment as $part) {
                    ?>
            <p class="px-4 mb-0"><?php echo $part ?></p>
            <?php }
                ?>
            <div class="form-floating mb-2 editCommentText" style="display: none;">
                <!-- TODO: Add max number of characters to description -->
                <textarea class="form-control" name="editComment" placeholder="Comment Here" id="commentArea" style="height: 100px; resize: none;" required><?php $completeComment = "";foreach ($mainComment as $line) {$completeComment = $completeComment . $line . "\n";}
                echo trim($completeComment);?></textarea>
                        <label for="commentArea">Comment</label>
                    </div>
                </div>
                <div class="card-footer">
                    <span>Article rate: </span>
                    <input type="hidden" name="rate" class="rate" value="<?php echo $firstComment['rate'] ?>">
                    <?php
$userRate = $firstComment['rate'];
                for ($i = 0; $i < $userRate; $i++) {?>
                    <i class="fa-solid fa-star"></i>
                <?php }if ($userRate < 5) {
                    $remaining = 5 - $userRate;
                    for ($i = 0; $i < $remaining; $i++) {?>
                        <i class="fa-regular fa-star"></i>
                    <?php }
                }
                ?>
                </div>
            </div>
        </form>
        <?php }
        }
        foreach ($comments as $comment) {
            if ($comment['user_id'] == $userID) {
                continue;
            } else {
                ?>
                <!-- Other comments -->
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="https://placehold.co/300x300" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
                                <h4><?php echo $comment['fullname'] ?></h4>
                            </div>
                            <div class="d-flex align-items-center">
                <div>
                    <?php
echo $comment['date'];
                ?>
                </div>
            </div>
        </div>
        <div class="card-body px-4">
            <?php
$mainComment = json_decode($comment['comment']);
                foreach ($mainComment as $part) {
                    ?>
            <p class="px-4 mb-0"><?php echo $part ?></p>
            <?php }
                ?>
                </div>
                <div class="card-footer">
                    <span>Article rate: </span>
                    <?php
$userRate = $comment['rate'];
                for ($i = 0; $i < $userRate; $i++) {?>
                    <i class="fa-solid fa-star"></i>
                <?php }if ($userRate < 5) {
                    $remaining = 5 - $userRate;
                    for ($i = 0; $i < $remaining; $i++) {?>
                        <i class="fa-regular fa-star"></i>
                    <?php }
                }
                ?>
                </div>
            </div>

        <?php }}} else {
        ?>
            <div class="card mb-3 py-3">
                <div class="text-center" style="font-size: 18px;">No comments</div>
            </div>
            <?php
}
}

function rating($article_id)
{
    global $con;
    $stmt = $con->prepare("SELECT AVG(rate) as rating FROM comments WHERE article_id = ?");
    $stmt->execute(array($article_id));
    $check = $stmt->rowCount();
    if ($check == 0) {
        echo '<i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>';
    } else {
        $ratingInfo = $stmt->fetch();
        $rating = number_format($ratingInfo['rating'], 1);
        $wholeRating = intval($rating);
        for ($i = 0; $i < $wholeRating; $i++) {
            echo '<i class="fa-solid fa-star"></i>';
        }
        $decimalRating = $rating - $wholeRating;
        if ($decimalRating > 0.4) {
            echo '<i class="fa-regular fa-star-half-stroke"></i>';
            $wholeRating++;
        }
        $remaining = 5 - $wholeRating;
        for ($i = 0; $i < $remaining; $i++) {
            echo '<i class="fa-regular fa-star"></i>';
        }
    }
}