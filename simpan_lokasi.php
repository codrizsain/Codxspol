<?php
session_start();
// Pastikan hanya user yang bisa mengakses
if(!isset($_SESSION['role']) || $_SESSION['role'] != "user"){ 
    header("location:../auth/login.php"); 
    exit; 
}
include '../config/koneksi.php';

$user_id = $_SESSION['id_user'];

// ==========================================
// 1. PROSES SIMPAN LOKASI (Dari peta.php)
// ==========================================
if(isset($_POST['id_spbu'])){
    $spbu_id = $_POST['id_spbu'];

    // Cek apakah SPBU ini sudah pernah disimpan oleh user ini
    $cek = mysqli_query($koneksi, "SELECT * FROM lokasi_tersimpan WHERE user_id='$user_id' AND spbu_id='$spbu_id'");
    if(mysqli_num_rows($cek) == 0){
        // Jika belum ada, simpan ke database
        mysqli_query($koneksi, "INSERT INTO lokasi_tersimpan (user_id, spbu_id) VALUES ('$user_id', '$spbu_id')");
        echo "<script>alert('Lokasi berhasil ditambahkan ke Favorit!'); window.location='peta.php';</script>";
    } else {
        echo "<script>alert('SPBU ini sudah ada di daftar Favorit Anda.'); window.location='peta.php';</script>";
    }
    exit;
}

// ==========================================
// 2. PROSES HAPUS LOKASI FAVORIT
// ==========================================
if(isset($_POST['hapus_id'])){
    $hapus_id = $_POST['hapus_id'];
    mysqli_query($koneksi, "DELETE FROM lokasi_tersimpan WHERE user_id='$user_id' AND spbu_id='$hapus_id'");
    echo "<script>alert('Lokasi berhasil dihapus dari Favorit!'); window.location='simpan_lokasi.php';</script>";
    exit;
}

// ==========================================
// 3. AMBIL DATA UNTUK DITAMPILKAN DI HALAMAN
// ==========================================
// Melakukan JOIN antara tabel lokasi_tersimpan dan spbu
$query_saved = mysqli_query($koneksi, "
    SELECT spbu.* FROM lokasi_tersimpan 
    JOIN spbu ON lokasi_tersimpan.spbu_id = spbu.id 
    WHERE lokasi_tersimpan.user_id = '$user_id'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Tersimpan - SIG SPBU</title>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }

        /* Navbar Custom */
        .navbar-custom {
            background-color: #005BAC;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 70px;
        }
        
        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s;
        }
        .btn-logout:hover {
            background: #E31E24;
            border-color: #E31E24;
            color: white;
        }

        /* Card Styling */
        .card-spbu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            overflow: hidden;
        }
        .card-spbu:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.1);
        }
        .card-header-custom {
            background-color: #005BAC;
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        .empty-state i {
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="peta.php">
                <i class="fa-solid fa-map-location-dot me-2 text-warning fs-4"></i>
                SIG SPBU <span class="fw-light ms-2 d-none d-sm-inline">| Dashboard Pengguna</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <div class="text-white d-none d-md-block text-end">
                    <div class="fw-bold" style="font-size: 14px;">Halo, <?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'User'; ?></div>
                    <div class="small text-white-50" style="font-size: 12px;"><i class="fa-solid fa-circle text-success" style="font-size: 8px;"></i> Online</div>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo isset($_SESSION['nama']) ? urlencode($_SESSION['nama']) : 'User'; ?>&background=E31E24&color=fff" alt="User" class="rounded-circle border border-2 border-white shadow-sm" width="40" height="40">
                <a href="../auth/logout.php" class="btn btn-logout btn-sm rounded-pill px-3 py-2 fw-medium ms-2">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold" style="color: #005BAC;"><i class="fa-solid fa-bookmark text-warning me-2"></i>SPBU Favorit Saya</h3>
                <p class="text-muted">Daftar lokasi SPBU yang telah Anda simpan.</p>
            </div>
            <a href="peta.php" class="btn btn-outline-primary fw-bold rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Peta
            </a>
        </div>

        <div class="row g-4">
            <?php 
            if($query_saved && mysqli_num_rows($query_saved) > 0) {
                while($spbu = mysqli_fetch_assoc($query_saved)) {
            ?>
                <!-- Card Item SPBU -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-spbu h-100">
                        <div class="card-header-custom">
                            <i class="fa-solid fa-gas-pump me-2 text-warning"></i>
                            ID: <?php echo htmlspecialchars($spbu['id_spbu']); ?>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3" style="color: #333;"><?php echo htmlspecialchars($spbu['nama_spbu']); ?></h5>
                            <p class="text-muted small mb-3">
                                <i class="fa-solid fa-map-pin text-danger me-2"></i> 
                                <?php echo htmlspecialchars($spbu['alamat']); ?>
                            </p>
                            <div class="bg-light p-2 rounded small text-center mb-0 font-monospace border">
                                Lat: <?php echo htmlspecialchars($spbu['latitude']); ?> <br> 
                                Lng: <?php echo htmlspecialchars($spbu['longitude']); ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-3 pt-0">
                            <!-- Form Hapus Lokasi -->
                            <form action="simpan_lokasi.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus SPBU ini dari daftar favorit?');">
                                <input type="hidden" name="hapus_id" value="<?php echo $spbu['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger w-100 fw-bold rounded-pill">
                                    <i class="fa-solid fa-trash-can me-1"></i> Hapus dari Favorit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php 
                } // End while
            } else { 
            ?>
                <!-- Jika Belum Ada Data Tersimpan -->
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fa-regular fa-folder-open"></i>
                        <h4 class="fw-bold text-dark mb-2">Belum ada lokasi yang disimpan</h4>
                        <p class="text-muted mb-4">Silakan kembali ke Peta Interaktif untuk mencari dan menyimpan lokasi SPBU Pertamina favorit Anda.</p>
                        <a href="peta.php" class="btn btn-primary fw-bold rounded-pill px-4 shadow">
                            <i class="fa-solid fa-map-location-dot me-2"></i> Cari SPBU Sekarang
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>