<!-- db_connect.php -->
<?php
$servername = "localhost"; // آدرس سرور
$username = "root"; // نام کاربری MySQL
$password = ""; // رمز عبور
$dbname = "curtaindb"; // نام پایگاه داده

$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
