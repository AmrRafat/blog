<?php
session_start();
include "init.php";

if (isset($_POST['questionTitle'])) {
    // Get variables
    $title = filter_var($_POST['questionTitle'], FILTER_SANITIZE_STRING);
    $details = json_encode(explode("\r\n", trim(filter_var($_POST['details'], FILTER_SANITIZE_STRING))));
    $userID = $_SESSION['id'];
    $date = date("Y-m-j H-i-s");
    $subject = $_POST['subject'];
    // Apply info into DB
    $stmt = $con->prepare("INSERT INTO questions(subject, question, details, date, user_id) VALUES(?,?,?,?,?)");
    $stmt->execute(array($subject, $title, $details, $date, $userID));
}

?>
<div style="margin-top:100px"></div>
<div class="container">
<h1 class="text-center mb-4">
    Ask a question
</h1>
<form action="<?php $_SERVER['PHP_SELF']?>" method="post" class="questionToolForm">
    <div class="card rounded-4">
        <div class="card-header pt-3">
            <div class="questionTitle input-group mb-3">
                <label class="input-group-text">Question title</label>
                <input type="text" name="questionTitle" class="form-control" required>
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="form-floating">
                <textarea class="form-control" name="details" placeholder="Comment Here" id="details" style="height: 150px; resize: none;" required></textarea>
                <label for="details">Question Details</label>
            </div>
        </div>
        <div class="card-footer py-3 questionToolFooter">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <label class="input-group-text">Subject</label>
                        <select name="subject" class="form-select" required>
                            <option value="" selected>Choose a subject</option>
                            <?php
foreach ($subjects as $subject) {?>
                                <option value="<?php echo $subject ?>"><?php echo $subject ?></option>
                                <?php }?>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Submit</button>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-2">
                    <div class="d-flex justify-content-lg-end justify-content-center questionFooterInfo flex-wrap">
                        <span>Asked by: <?php echo $_SESSION['fullname'] ?></span>
                        <div class="vr mx-2"></div>
                        <hr class="my-1">
                        <span>Asked at: <?php echo date("Y-m-j H-i-s") ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>



<?php
include "includes/templates/footer.php";
