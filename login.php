<?php
session_start(); // Oturumu başlat

// Veritabanı bağlantısını include ediyoruz
require 'config.php';

// Form submit edildiğinde çalışacak kısım
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Kullanıcıyı veritabanından sorgula
        $stmt = $conn->prepare("SELECT * FROM admin WHERE kullaniciadi = :username AND sifre = MD5(:password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Kullanıcı doğrulandı, oturumu başlat
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            
            // Kullanıcıyı ana sayfaya yönlendir
            header('Location: /');
            exit;
        } else {
            // Kullanıcı adı veya şifre hatalı
            $login_err = "Kullanıcı adı veya şifre hatalı.";
        }
    } catch(PDOException $e) {
        // Veritabanı hatası
        echo "Hata: " . $e->getMessage();
    }
}

$conn = null; // Veritabanı bağlantısını kapat

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Giriş - Telegram Duyuru Bot</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Stil ayarları */
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Tg Bot Admin Giriş</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($login_err)) echo '<div class="alert alert-danger">' . $login_err . '</div>'; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="username">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Şifre:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
