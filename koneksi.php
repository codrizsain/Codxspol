<?php
$host     = "localhost";
$user     = "root"; // sesuaikan dengan user mysql anda
$password = "";     // sesuaikan dengan password mysql anda
$dbname   = "pertaminadb";

$koneksi = mysqli_connect($host, $user, $password, $dbname);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>