<?php
session_start();
if (isset($_SESSION['username'])) {
    header('location: articles.php'); // Redirect to articles
    exit();
}
include "init.php";
if (isset($_POST)) {
// Check if User comming from HTTP Post Request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['login'])) {
            // Get info
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $hashedPass = sha1($password);
            // Check if user is in database
            $stmt = $con->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->execute(array($username, $hashedPass));
            $row = $stmt->fetch();
            $count = $stmt->rowCount();
            // if count > 0, Database contains record about this username
            if ($count > 0) {
                $_SESSION['username'] = $username; // Register Session Name
                $_SESSION['fullname'] = $row['fullname']; // Register Fullname
                $_SESSION['id'] = $row['userid']; // Register Session ID
                $_SESSION['status'] = $row['status']; // Get account status
                $_SESSION['access'] = $row['access']; // Registering User Access
                header('location: articles.php'); // Redirect to articles
                exit();
            }
        } elseif (isset($_POST['signup'])) {
            // Get all info
            $user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
            $pass = sha1(filter_var($_POST['pass1']), FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
            $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
            $fullName = $firstName . ' ' . $lastName;
            // Apply info into DB
            $stmt = $con->prepare("INSERT INTO users(username, password, email, fullname) VALUES(?,?,?,?)");
            $stmt->execute(array($user, $pass, $email, $fullName));
            $stmt1 = $con->prepare("SELECT * FROM users WHERE username = ?");
            $stmt1->execute(array($user));
            $row = $stmt1->fetch();
            $count = $stmt1->rowCount();
            // if count > 0, Database contains record about this username
            if ($count > 0) {
                $_SESSION['username'] = $user; // Register Session Name
                $_SESSION['fullname'] = $row['fullname']; // Register Fullname
                $_SESSION['id'] = $row['userid']; // Register Session ID
                $_SESSION['status'] = $row['status']; // Get account status
                $_SESSION['access'] = $row['access']; // Registering User Access
                header('location: articles.php');
                exit();
            }
        }
    }}
?>
<div style="margin-top:80px"></div>
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
                            <input type="text" name="username" id="loginUsername" class="form-control text-center mb-3" required>
                            <label class="form-label">Password</label>
                            <div class="pwBox">
                                <input type="password" name="password" id="loginPw" class="form-control text-center" required>
                                <i class="fa-regular fa-eye showPw"></i>
                            </div>
                        </div>
                        <button type="submit"class="form-control w-50 mx-auto">Login</button>
                        <hr>
                        <label class="form-label">Don't have an account??</label>
                        <button type="button"class="form-control w-75 mx-auto signupBtn">Signup</button>
                    </form>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="signupForm form-control text-light rounded-3 px-5 py-3 mx-auto overflow-hidden" novalidate>
                        <input type="hidden" name="signup">
                        <div class="part1">
                            <div class="row mb-3 rounded text-center msg p-1 w-75 mx-auto"></div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">Username</label>
                                </div>
                                <div class="col-lg-8 userNameCol">
                                    <input type="text" name="user" minlength="5" maxlength="16" autocomplete="off" id="user" class="form-control"required>
                                    <i class="fa-solid fa-circle-xmark inavail"></i>
                                    <i class="fa-solid fa-circle-check avail"></i>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">Password</label>
                                </div>
                                <div class="col-lg-8 passCol">
                                    <input type="password" minlength="6" maxlength="12" name="pass1" autocomplete="off" id="pass1" class="form-control"required>
                                    <i class="fa-regular fa-eye showPw"></i>
                                    <i class="fa-solid fa-circle-xmark diff"></i>
                                    <i class="fa-solid fa-circle-check matching"></i>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">Repeat Password</label>
                                </div>
                                <div class="col-lg-8 passCol">
                                    <input type="password" minlength="6" maxlength="12" name="pass2" autocomplete="off" id="pass2" class="form-control"required>
                                    <i class="fa-regular fa-eye showPw"></i>
                                    <i class="fa-solid fa-circle-xmark diff"></i>
                                    <i class="fa-solid fa-circle-check matching"></i>
                                </div>
                            </div>
                            <div class="row flex-row-reverse">
                                <div class="col">
                                    <button type="button" class="mx-auto form-control next1">next</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="mx-auto form-control back1">back</button>
                                </div>
                            </div>
                        </div>
                        <div class="part2">
                            <div class="row mb-3 rounded text-center msg1 p-1 w-75 mx-auto"></div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">First name</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" minlength="2" maxlength="10" name="firstName" autocomplete="off" id="firstname" class="form-control"required>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">Last name</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" minlength="2" maxlength="10" name="lastName" autocomplete="off" id="lastname" class="form-control"required>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-lg-4">
                                    <label class="form-label">Email</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="email" name="email" id="email" autocomplete="off" class="form-control"required>
                                </div>
                            </div>
                            <div class="row mt-5 flex-row-reverse">
                                <div class="col">
                                    <button type="submit" class="mx-auto form-control doneSign">Signup</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="mx-auto form-control back2">back</button>
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
