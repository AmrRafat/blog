<?php
session_start();
include "init.php";
if (isset($_POST)) {
    // TODO: Remember to delete above if condition
// Check if User comming from HTTP Post Request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $hashedPass = sha1($password);
            // echo '<div class="alert alert-info positon-abosolute text-center" style="top:100px;">DONE!</div>';
            // Database Process{
            /*
            // Check if user is in database
            $stmt = $con->prepare(
            "SELECT * FROM users WHERE username = ? AND password = ? Limit 1");
            $stmt->execute(array($username, $hashedPass));
            $row = $stmt->fetch();
            $count = $stmt->rowCount();
            // if count > 0, Database contains record about this username
            if ($count > 0) {
            $_SESSION['username'] = $username; // Register Session Name
            $_SESSION['id'] = $row['user_id']; // Register Session ID
            $_SESSION['access'] = $row['access']; // Registering User Access
            header('location: records.php'); // Redirect to dashboard page
            exit();
            }*/
            //}
        } elseif (isset($_POST['signup'])) {
            echo "HI";
            // Code for applying a new user
        }
    }}
?>
<div class="startPage">
    <img src="layout/imgs/wave.svg">
    <div class="container">
        <div class="loginWindow">
            <div class="row justify-conent-center align-items-center windowContent">
                <div class="col-lg-5">
                    <h2 class="text-center">Welcome to</h1>
                    <h1 class="text-center">Featherpen</h1>
                </div>
                <div class="col-lg-5">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="mx-auto p-3 form-control text-center loginForm rounded-3 text-light needs-validation" novalidate>
                    <input type="hidden" name="login">
                    <div class="form-control bg-transparent mb-3 py-2 text-light">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control text-center mb-3" required>
                        <label class="form-label">Password</label>
                        <input type="password" name="password"class="form-control text-center" required>
                    </div>
                        <button type="submit"class="form-control w-50 mx-auto">Login</button>
                        <hr>
                        <label class="form-label">Don't have an account??</label>
                        <button type="button"class="form-control w-75 mx-auto signupBtn">Signup</button>
                    </form>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="signupForm form-control text-light rounded-3 px-5 py-3 mx-auto overflow-hidden">
                    <input type="hidden" name="signup">
                    <div class="part1">
                        <div class="row mb-3 alert alert-danger text-center msg p-1 w-75 mx-auto"></div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-4">
                                <label class="form-label">Username</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" name="user" minlength="5" maxlength="16" id="user" class="form-control"required>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-4">
                                <label class="form-label">Password</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="password" minlength="6" maxlength="12" name="pass1" id="pass1" class="form-control"required>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-4">
                                <label class="form-label">Repeat Password</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="password" minlength="6" maxlength="12" name="pass2" id="pass2" class="form-control"required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="button" class="mx-auto form-control back1">back</button>
                            </div>
                            <div class="col">
                                <button type="button" class="mx-auto form-control next1">next</button>
                            </div>
                        </div>
                    </div>
                    <div class="part2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-4">
                                <label class="form-label">Fullname</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" minlength="2" maxlength="25" name="fullname" class="form-control"required>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-4">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="email" name="email" class="form-control"required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col">
                                <button type="button" class="mx-auto form-control back2">back</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="mx-auto form-control doneSign">Signup</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "includes/templates/footer.php";
