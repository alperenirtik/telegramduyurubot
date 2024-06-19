<?php
// Veritabanı bağlantısını include ediyoruz
require 'config.php';

try {
    // Admin bilgilerini ve duyuruları al
    $stmt = $conn->query("SELECT kullaniciadi, api, grup_id FROM admin WHERE id = 1"); // Örnek olarak id=1 varsayıldı
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Duyuru mesajlarını al
    $stmtDuyurular = $conn->query("SELECT id, duyuru_icerik, ilk_calistirma, son_calistirma, calisma_suresi FROM duyurular");
    $duyurular = $stmtDuyurular->fetchAll(PDO::FETCH_ASSOC);

    // Bot tokeni, grup ID'si
    $botToken = $admin['api']; // Bot tokeni
    $chatId = $admin['grup_id']; // Grup ID'si

    // Telegram API URL
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    foreach ($duyurular as $duyuru) {
        $currentTime = time();
        $ilkCalistirma = strtotime($duyuru['ilk_calistirma']);
        $sonCalistirma = strtotime($duyuru['son_calistirma']);
        $calismaSuresi = $duyuru['calisma_suresi'];

        // Zamanları saniyeye çevirme
        $ilkCalistirma = $ilkCalistirma !== false ? $ilkCalistirma : 0;
        $sonCalistirma = $sonCalistirma !== false ? $sonCalistirma : 0;
        $calismaSuresi = intval($calismaSuresi); // Güvenlik için tamsayıya dönüştürme
        $gecerliZaman = $sonCalistirma + $calismaSuresi;

        // İlk_calistirma == Son_calistirma durumu
        if ($ilkCalistirma == $sonCalistirma) {
            if ($currentTime >= $ilkCalistirma) {
                sendMessage($url, $chatId, $duyuru['duyuru_icerik']);
                updateLastRunTime($conn, $duyuru['id'], date('Y-m-d H:i:s', $currentTime));
            }
        } else {
            $gecerliZaman = $sonCalistirma + $calismaSuresi;

            // Zaman geçmişse mesajı gönder ve son_calistirma tarihini güncelle
            if ($currentTime >= $gecerliZaman) {
                sendMessage($url, $chatId, $duyuru['duyuru_icerik']);
                updateLastRunTime($conn, $duyuru['id'], date('Y-m-d H:i:s', $currentTime));
            }
        }
    }
} catch(PDOException $e) {
    echo "Veritabanı Hatası: " . $e->getMessage();
}

// Veritabanı bağlantısını kapat
$conn = null;

// Telegram API üzerinden mesaj gönderen fonksiyon
function sendMessage($url, $chatId, $message) {
    $htmlMessage = "$message"; // Örneğin sadece bold ve italic kullanıldı

    $data = [
        'chat_id' => $chatId,
        'text' => $htmlMessage,
        'parse_mode' => 'HTML' // HTML formatında gönderildiğini belirtmek için parse_mode kullanılır
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        echo "CURL Hatası: " . curl_error($ch);
    } else {
        echo "Mesaj başarıyla gönderildi: $message<br>";
    }
}

// Duyuru için son_calistirma tarihini güncelleyen fonksiyon
function updateLastRunTime($conn, $duyuruId, $newLastRunTime) {
    try {
        $stmt = $conn->prepare("UPDATE duyurular SET son_calistirma = :son_calistirma WHERE id = :id");
        $stmt->bindParam(':son_calistirma', $newLastRunTime);
        $stmt->bindParam(':id', $duyuruId);
        $stmt->execute();
        echo "Son çalıştırma tarihi güncellendi: $newLastRunTime<br>";
    } catch(PDOException $e) {
        echo "Veritabanı Hatası: " . $e->getMessage();
    }
}
?>
