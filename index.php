<?php
session_start(); // Oturumu başlat

// Oturum kontrolü yapın, eğer oturum açılmamışsa login.php'ye yönlendirin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login");
    exit;
}

// Veritabanı bağlantısını include ediyoruz
require 'config.php';

// Duyuruları veritabanından çek
try {
    $stmt = $conn->query("SELECT * FROM duyurular");
    $duyurular = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}

// Duyuru silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM duyurular WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Refresh:0"); // Sayfayı yenile
    } catch(PDOException $e) {
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
    <title>Anasayfa - Telegram Duyuru Bot</title>
    <!-- Bootstrap CSS -->
	<link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Responsive DataTables CSS -->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">

	<style>
		@media (max-width: 576px) { /* Mobil cihazlar için */
			.dataTables_length,
			.dataTables_filter {
				display: none !important; /* Elementleri gizle */
			}
		}
	</style>


</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/">Telegram Duyuru Botu</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Anasayfa</a>
                </li>
                <li class="nav-item">
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
        <h2 class="text-center mb-4">Duyurular</h2>
        <div class="table-responsive">
            <table id="duyurularTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Duyuru İçeriği</th>
                        <th>İlk Çalıştırma</th>
                        <th>Son Çalıştırma</th>
                        <th>Çalışma Süresi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($duyurular as $duyuru): ?>
                    <tr>
                        <td><?php echo $duyuru['id']; ?></td>
						<td>
							<?php 
							$duyuru_icerik = strip_tags($duyuru['duyuru_icerik']); // HTML etiketlerini kaldır
							if (mb_strlen($duyuru_icerik) > 20) {
								$kisaltilmis = mb_substr($duyuru_icerik, 0, 20) . '...';
								echo '<span class="kisaltilmis-duyuru" title="'.$duyuru_icerik.'">'.$kisaltilmis.'</span>';
							} else {
								echo $duyuru_icerik;
							}
							?>
						</td>
                        <td><?php echo $duyuru['ilk_calistirma']; ?></td>
                        <td><?php echo $duyuru['son_calistirma']; ?></td>
                        <td><?php echo $duyuru['calisma_suresi']; ?> saniye</td>
                        <td>
                            <a href="duzenle?id=<?php echo $duyuru['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Bu duyuruyu silmek istediğinizden emin misiniz?');">
                                <input type="hidden" name="delete_id" value="<?php echo $duyuru['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i> Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
			<br>
        </div>
    </div>
   <!-- Bootstrap JS ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- FontAwesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive DataTables JS -->
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
   <script>
        $(document).ready(function() {
            $('#duyurularTable').DataTable({
                responsive: true, // Mobil uyumlu DataTables
                language: {
                    "decimal":        "",
                    "emptyTable":     "Tabloda herhangi bir veri mevcut değil",
                    "info":           "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
                    "infoEmpty":      "Kayıt yok",
                    "infoFiltered":   "(_MAX_ kayıt içerisinden bulunan)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Göster _MENU_ kayıt",
                    "loadingRecords": "Yükleniyor...",
                    "processing":     "İşleniyor...",
                    "search":         "Ara:",
                    "zeroRecords":    "Eşleşen kayıt bulunamadı",
                    "paginate": {
                        "first":      "İlk",
                        "last":       "Son",
                        "next":       "Sonraki",
                        "previous":   "Önceki"
                    },
                    "aria": {
                        "sortAscending":  ": artan sütun sıralamasını aktifleştir",
                        "sortDescending": ": azalan sütun sıralamasını aktifleştir"
                    }
                }
            });
        });
    </script>
</body>

</html>
