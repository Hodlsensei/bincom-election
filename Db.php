<?php
$host = "sql106.infinityfree.com";
$user = "if0_41609855";
$password = "ZtaoqnNaFi"; // Replace with your actual vPanel password
$database = "if0_41609855_bincom";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>