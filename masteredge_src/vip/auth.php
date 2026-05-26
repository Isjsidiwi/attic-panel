<?php 

$dbhost = 'localhost';
$dbuser = 'princeaalyan_hnjgfdgjh';
$dbpass = 'princeaalyan_hnjgfdgjh';
$db = 'princeaalyan_hnjgfdgjh'; // Updated to match your SQL file
$conn = mysqli_connect($dbhost, $dbuser, $dbpass , $db);

date_default_timezone_set('Asia/Jakarta');

if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Telegram Bot Configuration
$botToken = '8305220045:AAFH5AGagCllDpQ-CkEvrW_daWJGgzVZLv0';
$website = "https://api.telegram.org/bot" . $botToken;

// Owner Configuration - Only this user can access admin functions
$ownerID = 7341064972; // Replace with actual owner's Telegram ID

// Admin Commands that only owner can use
$adminCommands = [
    '/adduser',
    '/removeuser', 
    '/listusers',
    '/stats',
    '/settoken',
    '/modstatus',
    '/modinfo',
    '/createkey',
    '/deletekey',
    '/showkeys',
    '/resetkey',
    '/resetallkeys'
];

?>
