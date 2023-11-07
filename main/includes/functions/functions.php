<?php
function commentsSection($article_id, $userID)
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
    <form id="comment" action="?<?php echo $firstComment['article_id'] ?>" method="post">
        <input type="hidden" name="comment_id" value="<?php echo $firstComment['comment_id'] ?>">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                <?php
// Get avatar of user
                $stmt2 = $con->prepare('SELECT avatar FROM users WHERE userid = ?');
                $stmt2->execute(array($userID));
                $userData = $stmt2->fetch();
                $avatar = 'layout/imgs/avatars/' . $userData['avatar'];
                ?>
                    <img src="<?php echo $avatar ?>" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
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
                <div class="card-footer ratingInComment">
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
                            <?php
$stmt3 = $con->prepare('SELECT avatar FROM users WHERE userid = ?');
                $stmt3->execute(array($comment['user_id']));
                $userAvatarData = $stmt3->fetch();
                $userAvatar = 'layout/imgs/avatars/' . $userAvatarData['avatar'];
                ?>
                                <img src="<?php echo $userAvatar ?>" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
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
