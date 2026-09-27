<?php
session_start();
include '../config/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

$query = mysqli_query($koneksi, "SELECT * FROM akun WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($query);

if($cek > 0){
    $data = mysqli_fetch_assoc($query);
    $_SESSION['id_user'] = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];

    if($data['role'] == "admin"){
        header("location:../admin/dashboard.php");
    } else {
        header("location:../user/peta.php");
    }
} else {
    header("location:login.php?pesan=Username atau password salah!");
}
?>
