<?php
// Includes
//---------
include 'config.php';

// ===============================================
// Routes
// ------
$tpl = 'includes/templates/'; // Templates Dir
$func = 'includes/functions/'; // Functions Dir
$css = 'layout/css/'; // Css Dir
$js = 'layout/js/'; // Js Dir
$langDir = 'includes/langs/'; // Langs Dir
$imgs = 'layout/imgs/'; // imgs Dir

// ===============================================
//Include the header and other files
include $func . "functions.php"; // Funtions file
include $tpl . "header.php"; // Header file
include $tpl . "navbar.php"; // navbar

// ===============================================
$subjects = [
'Economy',
'Life-hacks',
'Housing',
'Health',
'Career',
'Life-style',
'Education',
'Technology',
'Gaming',
'Politics',
'Arts',
'Parenthood'
];
