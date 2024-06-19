<?php
session_start(); // Oturumu başlat

// Oturum kontrolü yapın, eğer oturum açılmamışsa login.php'ye yönlendirin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login");
    exit;
}

// Veritabanı bağlantısını include ediyoruz
require 'config.php';

// Duyurunun ID'sini alın
if (isset($_GET['id'])) {
    $id = htmlspecialchars(strip_tags($_GET['id']));

    try {
        // ID'ye göre duyuruyu seç
        $stmt = $conn->prepare("SELECT * FROM duyurular WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $duyuru = $stmt->fetch(PDO::FETCH_ASSOC);

        // ID'ye göre duyuru bulunamazsa, kullanıcıyı index.php'ye yönlendir
        if (!$duyuru) {
            header("location: index");
            exit;
        }

    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }

} else {
    // Eğer GET yöntemi ile id parametresi gelmemişse, kullanıcıyı index.php'ye yönlendir
    header("location: /");
    exit;
}

// Duyuru güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = htmlspecialchars(strip_tags($_POST['id']));
    $duyuru_icerik = $_POST['duyuru_icerik']; // Güvenli bir şekilde işlemek için htmlspecialchars kullanmayacağız
    $duyuru_icerik = strip_tags($duyuru_icerik, '<b><i><u><s><blockquote><a><code><pre><tg-spoiler>'); // Sadece belirli etiketlere izin ver

    $calisma_suresi = htmlspecialchars(strip_tags($_POST['calisma_suresi']));

    try {
        // Duyuru içeriğini ve çalışma süresini güncelle
        $stmt = $conn->prepare("UPDATE duyurular SET duyuru_icerik = :duyuru_icerik, calisma_suresi = :calisma_suresi WHERE id = :id");
        $stmt->bindParam(':duyuru_icerik', $duyuru_icerik);
        $stmt->bindParam(':calisma_suresi', $calisma_suresi, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: /"); // Anasayfaya yönlendir
        exit;
    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyuru Düzenle - Telegram Duyuru Bot</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Responsive DataTables CSS -->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="index">Anasayfa</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="duyuru-ekle">Duyuru Ekle</a>
                </li>
				
				 <li class="nav-item">
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
        <h2 class="mb-4">Duyuru Düzenle</h2>
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
                <label for="duyuru_icerik">Duyuru İçeriği</label>
                <textarea class="form-control" id="duyuru_icerik" name="duyuru_icerik" rows="5"><?php echo $duyuru['duyuru_icerik']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="calisma_suresi">Çalışma Süresi (saniye)</label>
                <input type="number" class="form-control" id="calisma_suresi" name="calisma_suresi" value="<?php echo htmlspecialchars($duyuru['calisma_suresi']); ?>" required>
            </div>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($duyuru['id']); ?>">
            <button type="submit" name="update" class="btn btn-primary">Güncelle</button>
            <a href="index.php" class="btn btn-secondary">İptal</a>
        </form>
    </div>

    <!-- Bootstrap JS ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive DataTables JS -->
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#duyurularTable').DataTable({
                responsive: true // Mobil uyumlu DataTables
            });
        });
   
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
