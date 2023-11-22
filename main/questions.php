<?php
session_start();
include "init.php";
?>
<div style="margin-top:100px"></div>
<div class="container">
    <?php if (empty($_GET)) {?>
        <h1 class="text-center mt-3">Questions</h1>
        <div class="row px-3 mt-4">
            <h3 class="text-center mb-3">New Questions (not answered)</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row pt-3">
            <?php
$stmt3 = $con->prepare("SELECT * FROM questions WHERE done = 0");
    $stmt3->execute();
    $checkRows = $stmt3->rowCount();
    if ($checkRows > 0) {
        $questions = $stmt3->fetchAll();
        $stmt5 = $con->prepare('SELECT * FROM answers WHERE question_id = ?');
        foreach ($questions as $question) {
            $stmt5->execute(array($question['question_id']));
            $check = $stmt5->rowCount();
            $status = ($question['done'] == 1) ? 'Done' : 'Not Done';
            ?>
                <a href="answer.php?question=<?php echo $question['question_id'] ?>" class="col-12 col-md-4 mb-3 text-reset text-decoration-none questionCard">
                    <div class="card">
                        <div class="card-body">
                            <h3><?php echo $question['question'] ?></h3>
                        </div>
                        <div class="card-footer d-flex justify-content-center flex-wrap questionsFooter">
                            <span>Asked at: <?php echo date("j/m/Y", strtotime($question['date'])) ?></span>
                            <div class="vr mx-2"></div>
                            <span>Answers: <?php echo $check ?></span>
                            <div class="vr mx-2"></div>
                            <span><?php echo $status ?></span>
                        </div>
                    </div>
                </a>
            <?php }
    } else {?>
                <div class="col-12 col-md-8 col-lg-6 alert alert-success text-center mx-auto">All Questions Are Answered!!</div>
                <?php
}
    ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row px-3 mt-4">
            <h3 class="text-center mb-3">Questions Sections</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row pt-3">
                        <?php foreach ($subjects as $subject) {?>
                            <a href="?<?php echo $subject ?>" class="col-12 col-md-4 mb-3 text-decoration-none text-reset questionCard">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="text-center"><?php echo $subject ?></h3>
                                    </div>
                                </div>
                            </a>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {
    $subjectName = array_keys($_GET)[0];
    ?>
            <?php
if ($subjectName == "my_questions") {
        $stmt = $con->prepare('SELECT * FROM questions WHERE user_id = ?');
        $stmt->execute(array($_SESSION['id']));
        echo '<h1 class="text-center mb-3">My questions</h1>';
    } else {
        $stmt = $con->prepare('SELECT * FROM questions WHERE subject = ?');
        $stmt->execute(array($subjectName));
        echo '<h1 class="text-center mb-3">' . $subjectName . '</h1>';
    }
    echo '<div class="row">';
    $questions = $stmt->fetchAll();
    $stmt1 = $con->prepare('SELECT * FROM answers WHERE question_id = ?');
    foreach ($questions as $question) {
        $stmt1->execute(array($question['question_id']));
        $check = $stmt1->rowCount();
        $status = ($question['done'] == 1) ? 'Done' : 'Not Done';
        ?>
            <a href="answer.php?question=<?php echo $question['question_id'] ?>" class="col-12 col-md-4 mb-3 text-reset text-decoration-none questionCard">
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $question['question'] ?></h3>
                    </div>
                    <div class="card-footer d-flex justify-content-center flex-wrap questionsFooter">
                        <span>Asked at: <?php echo date("j/m/Y", strtotime($question['date'])) ?></span>
                        <div class="vr mx-2"></div>
                        <span>Answers: <?php echo $check ?></span>
                        <div class="vr mx-2"></div>
                        <span><?php echo $status ?></span>
                    </div>
                </div>
            </a>
        <?php }?>
        </div>
    <?php }?>
</div>
        <?php
include "includes/templates/footer.php";