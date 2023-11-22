<?php
session_start();
include "init.php";
// Preparing question info
$stmt = $con->prepare("SELECT questions.*, users.fullname as fullname FROM questions INNER JOIN users ON users.userid = questions.user_id WHERE question_id = ?");
$stmt->execute(array($_GET['question']));
$info = $stmt->fetch();
$details = json_decode($info['details']);
$detailsToShow = "";
foreach ($details as $detailsLine) {
    $detailsToShow = $detailsToShow . '<p class= "mb-0" style="font-size: 18px;">' . $detailsLine . '</p>';
}
if (isset($_POST['answer'])) {
    // Set variables
    $answerSubmitted = filter_var($_POST['answer'], FILTER_SANITIZE_STRING);
    $userID = $_SESSION['id'];
    $answer = json_encode(explode("\r\n", $answerSubmitted));
    $question_id = $_GET['question'];
    // Check if there is an answer already
    $stmt = $con->prepare("SELECT * FROM answers WHERE user_id = ? and question_id = ?");
    $stmt->execute(array($userID, $question_id));
    $check = $stmt->rowCount();
    if ($check == 0) {
        // Apply to DB
        $stmt1 = $con->prepare("INSERT INTO answers(question_id, user_id, answer,date) VALUES(?,?,?,now())");
        $stmt1->execute(array($question_id, $userID, $answer));
    }
} elseif (isset($_POST['editAnswer'])) {
    // Check if edited answer submitted is not empty
    if (trim($_POST['editAnswer']) != "") {
        // Set variables
        $answerID = $_POST['answer_id'];
        $editAnswer = json_encode(explode("\r\n", trim(filter_var($_POST['editAnswer'], FILTER_SANITIZE_STRING))));
        // Comparing to old answer
        // First get old answer
        $stmt = $con->prepare("SELECT answer FROM answers WHERE answer_id = ?");
        $stmt->execute(array($answerID));
        $data = $stmt->fetch();
        $oldAnswer = $data['answer'];
        // Compare and make changes if there is a difference
        if ($oldAnswer !== $editAnswer) {
            $stmt1 = $con->prepare("UPDATE answers SET answer = ?, date = now() WHERE answer_id = ?");
            $stmt1->execute(array($editAnswer, $answerID));
        }
    }
} elseif (!isset($_POST["answer"]) && !isset($_POST["editAnswer"]) && isset($_POST["answer_id"])) {
    // Delete an answer
    $answerID = $_POST["answer_id"];
    $stmt = $con->prepare("DELETE FROM answers WHERE answer_id = ?");
    $stmt->execute(array($answerID));
}
unset($_POST);
?>
<div style="margin-top:100px"></div>
<div class="container">
    <div class="card rounded-4 mb-3">
        <div class="card-header pt-3">
            <div class="questionTitle input-group d-flex justify-content-between align-items-center mb-3">
                <h3><?php echo $info['question'] ?></h3>
                <div>
                    <span>Subject: <?php echo $info['subject'] ?></span>
                    <?php
if (isset($_SESSION['id'])) {
    if ($info['user_id'] == $_SESSION['id'] && $info['done'] == 0) {?>
<button type="button" class="btn btn-outline-secondary ms-2 mrkDone" data-qid="<?php echo $info['question_id'] ?>">Change to answered</button>
<?php } else {?>
                            <button type="button" class="btn btn-outline-secondary ms-2 mrkUndone" data-qid="<?php echo $info['question_id'] ?>">Change to unanswered</button>
                        <?php }
} else {
    echo '<div class="vr"></div>';
    if ($info['done'] == 0) {
        echo '<span class="ms-2">Not answered</span>';
    } else {
        echo '<span class="ms-2">Answered</span>';
    }
}
?>
                </div>
            </div>
        </div>
        <div class="card-body questionDetails pt-3">
            <?php echo $detailsToShow ?>
        </div>
        <div class="card-footer py-3 questionToolFooter">
            <div class="d-flex justify-content-lg-end justify-content-center questionFooterInfo flex-wrap">
                <span>Asked by: <?php echo $info['fullname'] ?></span>
                <div class="vr mx-2"></div>
                <hr class="my-1">
                <span>Asked at: <?php echo date("j/m/Y", strtotime($info['date'])) ?></span>
            </div>
        </div>
    </div>
    <?php
// Add an answer section
if (isset($_SESSION['id'])) {
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?question=<?php echo $_GET['question'] ?>" class="mb-3" method="post">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="form-floating mb-2">
                    <textarea class="form-control" name="answer" placeholder="answer Here" id="answer" style="height: 100px; resize: none;" required></textarea>
                    <label for="answer">Answer</label>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center px-3">
                <div>
                    <button type="reset" class="btn btn-outline-secondary me-2">Cancel</button>
                    <button type="submit" class="btn btn-outline-secondary">Submit</button>
                </div>
            </div>
        </div>
    </form>
    <?php
} else {?>
<div class="card mb-3">
    <a href="index.php" class="btn btn-outline-secondary">Login/Register</a>
</div>
<?php
}
// Showing answers section
$stmt = $con->prepare("SELECT *, users.fullname AS fullname FROM answers INNER JOIN users ON answers.user_id = users.userid WHERE answers.question_id = ?");
$stmt->execute(array($_GET['question']));
$answers = $stmt->fetchAll();
if (!empty($answers)) {
    $userID = (isset($_SESSION['id'])) ? $_SESSION['id'] : 0;
    if ($userID != 0) {
        // Showing answer of user if logged in
        $stmt1 = $con->prepare("SELECT *, users.fullname AS fullname FROM answers INNER JOIN users ON answers.user_id = users.userid WHERE answers.question_id = ? and answers.user_id = ?");
        $stmt1->execute(array($_GET['question'], $userID));
        $check = $stmt1->rowCount();
        if ($check > 0) {
            $firstAnswer = $stmt1->fetch();
            ?>
    <form id="answer" action="<?php echo $_SERVER['PHP_SELF'] ?>?question=<?php echo $_GET['question'] ?>" method="post">
        <input type="hidden" name="answer_id" value="<?php echo $firstAnswer['answer_id'] ?>">
        <div class="card mb-3 answers">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <?php
// Get avatar
            $stmt3 = $con->prepare('SELECT avatar FROM users WHERE userid = ?');
            $stmt3->execute(array($_SESSION['id']));
            $avatarData = $stmt3->fetch();
            $avatarImage = $avatarData['avatar'] == null ? "layout/imgs/avatars/defaultAvatar.jpg" : "layout/imgs/avatars/" . $avatarData['avatar'];
            ?>
                    <img src="<?php echo $avatarImage ?>" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
                    <h4><?php echo $firstAnswer['fullname'] ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <button type="button" class="btn btn-outline-secondary editAnswer">Edit</button>
                        <button type="button" class="btn btn-outline-secondary delAnswer">Delete</button>
                    </div>
                    <div>
                        <?php
echo $firstAnswer['date'];
            ?>
                </div>
            </div>
        </div>
        <div class="card-body px-4">
            <?php
$mainAnswer = json_decode($firstAnswer['answer']);
            foreach ($mainAnswer as $part) {
                ?>
            <p class="px-4 mb-0"><?php echo $part ?></p>
            <?php }
            ?>
            <div class="form-floating mb-2 editAnswerText" style="display: none;">
                <textarea class="form-control" name="editAnswer" placeholder="Answer Here" id="answer" style="height: 100px; resize: none;" required><?php $completeAnswer = "";foreach ($mainAnswer as $line) {$completeAnswer = $completeAnswer . $line . "\n";}
            echo trim($completeAnswer);?></textarea>
                        <label for="answer">Answer</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <?php
if ($firstAnswer['approved'] == 0) {
                echo '<i class="fa-regular fa-square-check btn text-reset p-0 checkAnswer" data-answerid="' . $firstAnswer['answer_id'] . '" data-questionid = "' . $firstAnswer['question_id'] . '"></i>';
            } else {
                echo '<i class="fa-solid fa-square-check btn text-reset p-0 checkAnswer" data-answerid="' . $firstAnswer['answer_id'] . '" data-questionid = "' . $firstAnswer['question_id'] . '"></i>';
            }
            ?>
                    </div>
                </div>
            </div>
        </form>
        <?php }
    }
    foreach ($answers as $answer) {
        if ($answer['user_id'] == $userID) {
            continue;
        } else {
            // Checking for other answers
            ?>
                    <div class="card mb-3 answers">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                            <?php
// Get avatar
            $stmt3 = $con->prepare('SELECT avatar FROM users WHERE userid = ?');
            $stmt3->execute(array($answer['user_id']));
            $avatarData = $stmt3->fetch();
            $avatarImage = $avatarData['avatar'] == null ? "layout/imgs/avatars/defaultAvatar.jpg" : "layout/imgs/avatars/" . $avatarData['avatar'];
            ?>
                    <img src="<?php echo $avatarImage ?>" alt="" class="rounded-circle d-block me-3" style="max-height: 50px; max-width:50px;">
                                <h4><?php echo $answer['fullname'] ?></h4>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <?php
echo $answer['date'];
            ?>
                </div>
            </div>
        </div>
        <div class="card-body px-4">
            <?php
$mainAnswer = json_decode($answer['answer']);
            foreach ($mainAnswer as $part) {
                ?>
            <p class="px-4 mb-0"><?php echo $part ?></p>
            <?php }
            ?>
            <div class="d-flex justify-content-end">
                <?php
if ($answer['approved'] == 0) {
                echo '<i class="fa-regular fa-square-check btn text-reset p-0 checkAnswer" data-answerid="' . $answer['answer_id'] . '"  data-questionid="' . $answer['question_id'] . '"></i>';
            } else {
                echo '<i class="fa-solid fa-square-check btn text-reset p-0 checkAnswer" data-answerid="' . $answer['answer_id'] . '" data-questionid="' . $answer['question_id'] . '"></i>';
            }
            ?>
            </div>
                </div>
            </div>
            <?php }
    }
} else {
    ?>
            <div class="card mb-3 py-3">
                <div class="text-center" style="font-size: 18px;">No answers</div>
            </div>
            <?php
}?>
</div>
<?php
include "includes/templates/footer.php";
