<?php
$servername = "sql113.infinityfree.com"; // replace XXX with your actual server number
$username = "if0_40185804"; // your InfinityFree username
$password = "careerhub12"; // the password from your InfinityFree control panel
$dbname = "if0_40185804_uniconnect_db"; // your full database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
