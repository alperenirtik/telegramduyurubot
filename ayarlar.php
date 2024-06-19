<?php
session_start(); // Oturumu başlat

// Oturum kontrolü yapın, eğer oturum açılmamışsa login.php'ye yönlendirin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login");
    exit;
}

// Veritabanı bağlantısını include ediyoruz
require 'config.php';

// Başlangıçta mesajları boş olarak tanımlıyoruz
$password_update_message = '';
$settings_update_message = '';

// Admin bilgilerini getir
try {
    $stmt = $conn->query("SELECT kullaniciadi, api, grup_id FROM admin WHERE id = 1"); // Burada id=1 varsayıldı, tek bir satır olduğu varsayılıyor
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Parola güncelleme işlemi
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];

        // Parolayı güncelle
        $hashed_password = md5($new_password); // MD5 ile şifrelenmiş parola
        try {
            $stmt = $conn->prepare("UPDATE admin SET sifre = :sifre WHERE id = 1"); // Burada id=1 varsayıldı, tek bir satır olduğu varsayılıyor
            $stmt->bindParam(':sifre', $hashed_password);
            $stmt->execute();
            $_SESSION['password_update_message'] = "Parola başarıyla güncellendi."; // Session'a mesajı kaydediyoruz
            header("location: ayarlar"); // Yeniden yönlendirme yapıyoruz
            exit;
        } catch(PDOException $e) {
            echo "Hata: " . $e->getMessage();
        }
    }

    // Kullanıcı adı, API anahtarı ve grup_id güncelleme işlemi
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_settings'])) {
        $new_username = $_POST['new_username'];
        $new_api_key = $_POST['new_api_key'];
        $new_grup_id = $_POST['grup_id'];
        try {
            $stmt = $conn->prepare("UPDATE admin SET kullaniciadi = :kullaniciadi, api = :api, grup_id = :grup_id WHERE id = 1"); // Burada id=1 varsayıldı, tek bir satır olduğu varsayılıyor
            $stmt->bindParam(':kullaniciadi', $new_username);
            $stmt->bindParam(':api', $new_api_key);
            $stmt->bindParam(':grup_id', $new_grup_id);
            $stmt->execute();
            $_SESSION['settings_update_message'] = "Kullanıcı adı, API anahtarı ve Grup ID başarıyla güncellendi."; // Session'a mesajı kaydediyoruz
            header("location: ayarlar"); // Yeniden yönlendirme yapıyoruz
            exit;
        } catch(PDOException $e) {
            echo "Hata: " . $e->getMessage();
        }
    }

} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}

$conn = null; // Veritabanı bağlantısını kapat
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Telegram Duyuru Bot</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
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
                 <li class="nav-item">
                    <a class="nav-link" href="mesaj-gonder">Anlık Mesaj Gönder</a>
                </li>
				<li class="nav-item active">
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
        <h2 class="mb-4">Ayarlar</h2>

        <?php if (isset($_SESSION['password_update_message'])) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['password_update_message']; ?>
        </div>
        <?php
            unset($_SESSION['password_update_message']); // Mesajı temizle
        ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['settings_update_message'])) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['settings_update_message']; ?>
        </div>
        <?php
            unset($_SESSION['settings_update_message']); // Mesajı temizle
        ?>
        <?php endif; ?>

        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="new_username">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo $admin['kullaniciadi']; ?>" required>
            </div>
            <div class="form-group">
                <label for="new_api_key">API Anahtarı <i>(BotFather ile yeni bir bot oluşturup API alınız.)</i></label>
                <input type="password" class="form-control" id="new_api_key" name="new_api_key" value="<?php echo $admin['api']; ?>" required>
            </div>
            <div class="form-group">
                <label for="grup_id">Grup ID  <i>(Grubunuza Rose ekleyerek /id yazıp öğrenebilirsiniz.)</i></label>
                <input type="text" class="form-control" id="grup_id" name="grup_id" value="<?php echo $admin['grup_id']; ?>" required>
            </div>
            <button type="submit" name="update_settings" class="btn btn-primary btn-block">Ayarları Güncelle</button>
        </form>

        <hr>

        <form method="post">
            <div class="form-group">
                <label for="new_password">Yeni Parola</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" name="update_password" class="btn btn-danger btn-block">Parolayı Güncelle</button>
        </form>
		<br>
    </div>

    <!-- Bootstrap JS ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
