<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakbakalim";

try {
    // Veritabanına bağlan
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // PDO hataları exception olarak ayarla
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Karakter setini UTF-8 olarak ayarla
    $conn->exec("SET NAMES 'utf8mb4'");
    $conn->exec("SET CHARACTER SET utf8mb4");
    $conn->exec("SET COLLATION_CONNECTION = 'utf8mb4_general_ci'");

    // İstanbul (Türkiye) saat dilimini ayarla
	date_default_timezone_set('Europe/Istanbul');
    $conn->exec("SET time_zone = '+03:00'");

    echo "";
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}
?>
