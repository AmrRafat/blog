<?php
session_start();
include "init.php";
if (isset($_POST['username'])) {
    // Set the variables
    $id = $_SESSION['id'];
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
    $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
    $fullname = $firstName . ' ' . $lastName;
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $birthdate = isset($_POST['date']) ? $_POST['date'] : null;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
    $country = isset($_POST['country']) ? $_POST['country'] : null;
    $educationLevel = isset($_POST['eduLvl']) ? $_POST['eduLvl'] : null;
    $specialization = isset($_POST['specialization']) ? filter_var($_POST['specialization'], FILTER_SANITIZE_STRING) : 'Not applied';
    // Update info in DB
    $stmt = $con->prepare("UPDATE users SET username = ?, fullname = ?, email = ?, birthdate = ?, gender = ?, country = ?, educationlvl = ?, specialization = ? WHERE userid = ?");
    $stmt->execute(array($username, $fullname, $email, $birthdate, $gender, $country, $educationLevel, $specialization, $id));
    // Change setting the session info
    $_SESSION['username'] = $username; // Register Session Name
    $_SESSION['fullname'] = $fullname; // Register Fullname
    unset($_POST);
}
$stmt = $con->prepare("SELECT * FROM users WHERE userid = ? LIMIT 1");
$stmt->execute(array($_SESSION['id']));
$info = $stmt->fetch();

?>
    <div style="margin-top:80px"></div>
    <div class="container pt-3">
        <div class="card">
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-lg-4 text-center">
                        <img src="https://placehold.co/300x300" alt="" class="img-fluid img-thumbnail">
                        <hr>
                        <div class="profileMenu">
                            <div class="row align-items-center">
                            <div class="col-md-9 profileMenuOptions mx-auto"><a href="questionsTool.php" class="btn form-control newQuestion">Ask a Question</a></div>
                            <div class="col-md-9 profileMenuOptions mx-auto"><a href="articlesTool.php" class="btn form-control newArticle">New Article</a></div>
                                <div class="col-md-9 profileMenuOptions mx-auto"><a href="articles.php?my_articles" class="btn form-control myArticles">My Articles</a></div>
                                <div class="col-md-9 profileMenuOptions mx-auto"><a href="questions.php?my_questions" class="btn form-control myQuestions">My Questions</a></div>
                                <div class="col-md-9 profileMenuOptions mx-auto"><a href="articles.php?my_fav" class="btn form-control fav">Favorites</a></div>
                            </div>
                        </div>
                    </div>
                    <hr class="horizontalLine my-3">
                    <div class="vr p-0 verticalLine"></div>
                    <div class="col mb-3">
                        <div class="row justify-content-between align-items-center px-3">
                            <div class="col profileName">
                                <h2 class="ps-2 mb-0"><?php echo $_SESSION['fullname'] ?></h2>
                            </div>
                            <div class="col-xl-6 col-md-7">
                                <?php
if ($info['status'] == 0) {
    echo '<div class="row gap-2 px-2 justify-content-end align-items-center">';
    echo '<div class="col p-0">';
    echo '<button type="button" class="btn btn-danger form-control">Send Activation</button>';
    echo '</div>';
    echo '<div class="col p-0">';
} else {
    echo '<div class="row gap-2 px-2 justify-content-center align-items-center">';
    echo '<div class="col-sm-4 col-6 p-0">';
}
echo '<button type="button" class="btn btn-primary form-control editProfile">Edit profile</button>';
echo '</div>';
?>
                            </div>
                            </div>
                        </div>
                        <hr>
                        <form action="<?php $_SERVER['PHP_SELF']?>" method="post" class="editProfileForm px-4">
                        <input type="hidden" name="id" id="profileId" value="<?php echo $_SESSION["id"] ?>">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Username</span>
                                    <input type="text" name="username" id="username" minlength="5" maxlength="16" autocomplete="off" class="form-control position-relative" value="<?php echo $_SESSION['username'] ?>" readonly>
                                    <i class="fa-solid fa-circle-xmark inavail"></i>
                                    <i class="fa-solid fa-circle-check avail"></i>
                                    <span class="userLength">5 - 16 charcters</span>
                                </div>
                                <div class="input-group mb-3">
                                    <?php
$fullname = explode(' ', $_SESSION['fullname']);
$firstName = $fullname[0];
$lastName = $fullname[1];
?>
                                    <span class="input-group-text">First name</span>
                                    <input type="text" class="form-control" name="firstName" id="firstname" value="<?php echo $firstName ?>" autocomplete="off" readonly>
                                    <span class="input-group-text">Last name</span>
                                    <input type="text" class="form-control" name="lastName" id="lastname" value="<?php echo $lastName ?>" autocomplete="off" readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Email</span>
                                    <input type="text" name="email" id="email" class="form-control" value="<?php echo $info['email'] ?>" autocomplete="off" readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Birthdate</span>
                                    <input type="date" name="date" id="date" class="form-control" style="cursor: pointer;" <?php if ($info['birthdate'] != null) {echo "value = " . $info['birthdate'];}?> readonly>
                                    <span class="input-group-text">Age</span>
                                    <input type="text" name="age" id="age" class="form-control age" value="<?php
if ($info['birthdate'] != null && $info['birthdate'] != '0000-00-00') {
    $today = date('Y-m-d');
    $bd = date_create($info['birthdate']);
    $today = date_create($today);
    $diff = date_diff($bd, $today);
    $diffArray = get_object_vars($diff);
    $age = $diffArray['y'];
    echo $age;
} else {
    echo 'Not available';
}
?>" readonly>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Country</span> <!-- TODO: Use API to get all countries -->
                                    <select disabled name="country" id="country" class="form-select" style="cursor: pointer;">
                                        <?php
$countries = ["United States", "Canada", "Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and/or Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Cook Islands", "Costa Rica", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecudaor", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France, Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kosovo", "Kuwait", "Kyrgyzstan", "Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfork Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia South Sandwich Islands", "South Sudan", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbarn and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States minor outlying islands", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City State", "Venezuela", "Vietnam", "Virigan Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zaire", "Zambia", "Zimbabwe"];
sort($countries);
array_unshift($countries, 'Select Country');
foreach ($countries as $oneCountry) {?>
                                        <option <?php if ($info['country'] == null) {
    if ($oneCountry == 'Select Country') {echo "selected disabled='disabled'";}
} else {
    if ($oneCountry == 'Select Country') {
        echo "disabled = 'disabled'";
    } elseif ($info['country'] == $oneCountry) {
        echo 'selected';}}?> value="<?php echo $oneCountry ?>"><?php echo $oneCountry ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Gender</span>
                                    <select disabled name="gender" id="gender" class="form-select" style="cursor: pointer;">
                                        <?php $gender = $info['gender'];?>
                                        <option <?php if ($gender == 0) {echo 'selected';}?> value="" disabled='disabled'>Select gender</option>
                                        <option <?php if ($gender == 1) {echo 'selected';}?> value="1">Female</option>
                                        <option <?php if ($gender == 2) {echo 'selected';}?> value="2">Male</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Educational Level</span>
                                    <select disabled name="eduLvl" id="eduLvl" class="form-select" style="cursor: pointer;">
                                        <?php $eduLvl = $info['educationlvl'];?>
                                        <option <?php if ($eduLvl == null) {echo "selected";}?> value="" disabled="disabled">-- select Educational Level --</option>
                                        <option <?php if ($eduLvl == "No formal education") {echo "selected";}?> value="No formal education">No formal education</option>
                                        <option <?php if ($eduLvl == "Primary education") {echo "selected";}?> value="Primary education">Primary education</option>
                                        <option <?php if ($eduLvl == "Secondary education") {echo "selected";}?> value="Secondary education">Secondary education or high school</option>
                                        <option <?php if ($eduLvl == "GED") {echo "selected";}?> value="GED">GED</option>
                                        <option <?php if ($eduLvl == "Vocational qualification") {echo "selected";}?> value="Vocational qualification">Vocational qualification</option>
                                        <option <?php if ($eduLvl == "Bachelor's degree") {echo "selected";}?> value="Bachelor's degree">Bachelor's degree</option>
                                        <option <?php if ($eduLvl == "Master's degree") {echo "selected";}?> value="Master's degree">Master's degree</option>
                                        <option <?php if ($eduLvl == "Doctorate or higher") {echo "selected";}?> value="Doctorate or higher">Doctorate or higher</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Specialization</span>
                                    <input type="text" name="specialization" id="specialization" autocomplete="off" class="form-control" value="<?php echo $info['specialization'] ?>" readonly>
                                </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include "includes/templates/footer.php";