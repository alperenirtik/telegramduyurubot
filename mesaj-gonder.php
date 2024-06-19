<?php
session_start(); // Oturumu başlat

// Oturum kontrolü yapın, eğer oturum açılmamışsa login.php'ye yönlendirin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login");
    exit;
}

require 'config.php'; // Veritabanı bağlantısını include ediyoruz

// Form submit edildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Duyuru içeriğini alın
    $duyuru_icerik = $_POST['duyuru_icerik'];

    try {
        // Admin tablosundan API token ve grup ID bilgilerini çekiyoruz (1 numaralı id için)
        $stmt = $conn->prepare("SELECT api, grup_id FROM admin WHERE id = 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // API token ve grup ID bilgilerini alıyoruz
        $apiToken = $admin['api'];
        $grupId = $admin['grup_id'];

        // Telegram bot API URL'si
        $apiUrl = "https://api.telegram.org/bot{$apiToken}/sendMessage";

        // Mesajı göndermek için POST verisi oluşturuyoruz
        $postData = http_build_query(array(
            'chat_id' => $grupId,
            'text' => $duyuru_icerik,
            'parse_mode' => 'HTML' // İsteğe bağlı: HTML etiketlerini kullanmak için
        ));

        // cURL ile Telegram bot API'sine istek gönderiyoruz
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Başarılı mesajı ayarlıyoruz
        $success_msg = "Mesaj başarıyla gönderildi!";
    } catch (PDOException $e) {
        $error_msg = "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj Gönder - Telegram Duyuru Bot</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/">Telegram Duyuru Botu</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Anasayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="duyuru-ekle">Duyuru Ekle</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="mesaj-gonder">Anlık Mesaj Gönder</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ayarlar">Ayarlar</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout">Çıkış Yap</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Mesaj Gönder</h2>
		
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="tag-buttons">
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Bold" onclick="insertTag('<b>', '</b>'); return false;"><i class="fas fa-bold"></i> Kalın</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Italic" onclick="insertTag('<i>', '</i>'); return false;"><i class="fas fa-italic"></i> Eğik</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Underline" onclick="insertTag('<u>', '</u>'); return false;"><i class="fas fa-underline"></i> Altı Çizili</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Strikethrough" onclick="insertTag('<s>', '</s>'); return false;"><i class="fas fa-strikethrough"></i> Üstü Çizili</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Quote" onclick="insertTag('<blockquote>', '</blockquote>'); return false;"><i class="fas fa-quote-right"></i> Alıntı</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Link" onclick="insertLink(); return false;"><i class="fas fa-link"></i> Link</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Code" onclick="insertTag('<code>', '</code>'); return false;"><i class="fas fa-code"></i> Kod</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Pre" onclick="insertTag('<pre>', '</pre>'); return false;"><i class="fas fa-terminal"></i> Pre</a>
            <a href="/" class="btn btn-outline-secondary btn-sm" title="Spoiler" onclick="insertTag('<tg-spoiler>', '</tg-spoiler>'); return false;"><i class="fas fa-eye-slash"></i> Spoiler</a>
        </div>

        <br>
		
        <form method="post">
            <div class="form-group">
                <label for="duyuru_icerik">Duyuru İçeriği:</label>
                <textarea id="duyuru_icerik" class="form-control" name="duyuru_icerik" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Mesaj Gönder</button>
        </form>
        <br>
    </div>



    <!-- Bootstrap JS ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function insertTag(openTag, closeTag) {
            var textarea = document.getElementById('duyuru_icerik');
            var startPos = textarea.selectionStart;
            var endPos = textarea.selectionEnd;
            var selectedText = textarea.value.substring(startPos, endPos);
            var replacement = openTag + selectedText + closeTag;
            textarea.value = textarea.value.substring(0, startPos) + replacement + textarea.value.substring(endPos);
            textarea.focus();
            textarea.setSelectionRange(endPos + openTag.length, endPos + openTag.length);
        }

        function insertLink() {
            var url = prompt('Enter URL:');
            if (url) {
                insertTag('<a href="' + url + '">', '</a>');
            }
        }
    </script>
</body>
</html>
