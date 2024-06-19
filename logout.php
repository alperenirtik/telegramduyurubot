<?php
session_start(); // Oturumu başlat

// Oturumu sonlandır
$_SESSION = array(); // Oturum değişkenlerini boşalt
session_destroy(); // Oturumu sonlandır

// Kullanıcıyı giriş sayfasına yönlendir
header("location: login");
exit;
?>
