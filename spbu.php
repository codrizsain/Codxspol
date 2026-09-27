<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    header("location:../auth/login.php"); 
    exit; 
}
include '../config/koneksi.php';

$pesan = "";

// PROSES TAMBAH DATA
if(isset($_POST['tambah'])){
    $id_spbu = mysqli_real_escape_string($koneksi, $_POST['id_spbu']);
    $nama_spbu = mysqli_real_escape_string($koneksi, $_POST['nama_spbu']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $latitude = mysqli_real_escape_string($koneksi, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($koneksi, $_POST['longitude']);

    $query = mysqli_query($koneksi, "INSERT INTO spbu (id_spbu, nama_spbu, alamat, latitude, longitude) VALUES ('$id_spbu', '$nama_spbu', '$alamat', '$latitude', '$longitude')");
    if($query){
        $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Data SPBU berhasil <strong>ditambahkan</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal menambah data!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// PROSES EDIT DATA
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $id_spbu = mysqli_real_escape_string($koneksi, $_POST['id_spbu']);
    $nama_spbu = mysqli_real_escape_string($koneksi, $_POST['nama_spbu']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $latitude = mysqli_real_escape_string($koneksi, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($koneksi, $_POST['longitude']);

    $query = mysqli_query($koneksi, "UPDATE spbu SET id_spbu='$id_spbu', nama_spbu='$nama_spbu', alamat='$alamat', latitude='$latitude', longitude='$longitude' WHERE id='$id'");
    if($query){
        $pesan = "<div class='alert alert-info alert-dismissible fade show' role='alert'>Data SPBU berhasil <strong>diperbarui</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal memperbarui data!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// PROSES HAPUS DATA
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM lokasi_tersimpan WHERE spbu_id='$id'");
    $query = mysqli_query($koneksi, "DELETE FROM spbu WHERE id='$id'");
    
    if($query){
        header("Location: spbu.php?msg=hapus_sukses");
        exit;
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'hapus_sukses'){
    $pesan = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>Data SPBU berhasil <strong>dihapus</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// AMBIL SEMUA DATA SPBU
$query_spbu = mysqli_query($koneksi, "SELECT * FROM spbu ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola SPBU - SIG SPBU</title>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet CSS for Map Picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        :root { --pertamina-red: #E31E24; --pertamina-blue: #005BAC; --sidebar-width: 260px; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }

        /* Sidebar */
        #sidebar { min-width: var(--sidebar-width); max-width: var(--sidebar-width); background: white; color: #333; transition: all 0.3s; min-height: 100vh; box-shadow: 2px 0 15px rgba(0,0,0,0.05); z-index: 1000; }
        .sidebar-header { padding: 20px; background: var(--pertamina-red); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #eee; }
        #sidebar ul li a { padding: 12px 25px; font-size: 1.05em; display: block; color: #555; text-decoration: none; transition: 0.3s; border-left: 4px solid transparent; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--pertamina-blue); background: #f8f9fa; border-left: 4px solid var(--pertamina-blue); font-weight: 500; }
        #sidebar ul li a i { margin-right: 10px; width: 20px; text-align: center; }

        /* Content & Tables */
        #content { width: 100%; min-height: 100vh; transition: all 0.3s; display: flex; flex-direction: column; }
        .topbar { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .custom-table-container { background: white; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.03); overflow: hidden; padding: 20px; }
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
        .table thead th { background-color: #f8f9fa; color: #555; font-weight: 600; border-bottom: 2px solid #eee; }

        #mapPicker { width: 100%; height: 300px; border-radius: 8px; border: 1px solid #ccc; z-index: 10; }

        @media (max-width: 768px) {
            #sidebar { margin-left: calc(var(--sidebar-width) * -1); position: fixed; }
            #sidebar.active { margin-left: 0; }
            .sidebar-overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 999; }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-gas-pump fs-1 mb-2"></i>
                <h5 class="fw-bold mb-0">Admin Panel</h5>
                <small class="text-white-50">SIG SPBU Asahan</small>
            </div>

            <ul class="list-unstyled components">
                <p class="text-muted small px-4 mb-2 fw-bold text-uppercase">Menu Utama</p>
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li class="active"><a href="spbu.php"><i class="fa-solid fa-map-location-dot"></i> Kelola Data SPBU</a></li>
                <li><a href="pengguna.php"><i class="fa-solid fa-users"></i> Kelola Pengguna</a></li>
            </ul>

            <div class="px-4 mt-auto pb-4 pt-3 border-top">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=005BAC&color=fff" class="rounded-circle me-3" width="40" alt="Admin">
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 14px;">Administrator</h6>
                        <small class="text-muted" style="font-size: 12px;"><i class="fa-solid fa-circle text-success" style="font-size: 8px;"></i> Online</small>
                    </div>
                </div>
                <a href="../auth/logout.php" class="btn btn-outline-danger w-100 btn-sm fw-bold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-md-none shadow-sm me-3">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-dark d-none d-md-block">Data Master SPBU</h5>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fa-solid fa-plus me-1"></i> Tambah SPBU Baru
                    </button>
                </div>
            </div>

            <div class="container-fluid p-4 p-md-5">
                
                <?= $pesan ?>

                <div class="custom-table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0" style="color: var(--pertamina-blue);">Daftar Seluruh SPBU</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">ID SPBU</th>
                                    <th width="20%">Nama SPBU</th>
                                    <th width="30%">Alamat</th>
                                    <th width="15%">Koordinat</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if(mysqli_num_rows($query_spbu) > 0) {
                                    while($row = mysqli_fetch_assoc($query_spbu)) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><span class="badge bg-light text-dark border border-secondary">#<?= htmlspecialchars($row['id_spbu']); ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_spbu']); ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($row['alamat']); ?></td>
                                    <td class="font-monospace small bg-light rounded text-center">
                                        <?= htmlspecialchars($row['latitude']); ?><br>
                                        <?= htmlspecialchars($row['longitude']); ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>" title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="spbu.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus SPBU <?= htmlspecialchars($row['nama_spbu']); ?>?');" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal Edit Untuk Setiap Baris -->
                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data SPBU</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="spbu.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">ID SPBU / Kode</label>
                                                        <input type="text" name="id_spbu" class="form-control" value="<?= htmlspecialchars($row['id_spbu']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Nama SPBU</label>
                                                        <input type="text" name="nama_spbu" class="form-control" value="<?= htmlspecialchars($row['nama_spbu']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Alamat Lengkap</label>
                                                        <textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($row['alamat']); ?></textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-medium text-muted">Latitude</label>
                                                            <input type="number" step="any" name="latitude" class="form-control" value="<?= htmlspecialchars($row['latitude']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-medium text-muted">Longitude</label>
                                                            <input type="number" step="any" name="longitude" class="form-control" value="<?= htmlspecialchars($row['longitude']); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    } 
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='fa-regular fa-folder-open fs-1 mb-3 d-block'></i>Belum ada data SPBU yang ditambahkan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data (DENGAN PETA PICKER) -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: var(--pertamina-blue);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-map-location-dot me-2"></i>Tambah SPBU via Peta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="spbu.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Bagian Kiri: Peta -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium text-muted d-block">Pilih Titik di Peta</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="searchMapInput" class="form-control form-control-sm" placeholder="Cari daerah di Asahan...">
                                    <button class="btn btn-outline-primary btn-sm" type="button" onclick="searchMapLocation()"><i class="fa-solid fa-search"></i> Cari</button>
                                </div>
                                <div id="mapPicker"></div>
                                <small class="text-muted d-block mt-2"><i class="fa-solid fa-circle-info text-primary"></i> Klik pada peta untuk mengambil Latitude & Longitude otomatis.</small>
                            </div>
                            
                            <!-- Bagian Kanan: Form -->
                            <div class="col-md-6 mb-3">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted">ID SPBU / Kode</label>
                                    <input type="text" name="id_spbu" class="form-control" placeholder="Contoh: 14212267" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted">Nama SPBU</label>
                                    <input type="text" name="nama_spbu" class="form-control" placeholder="Contoh: SPBU Huta Padang" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap..." required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="form-label fw-medium text-muted">Latitude</label>
                                        <input type="number" step="any" name="latitude" id="latInput" class="form-control bg-light" placeholder="Klik dari peta" required readonly>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label fw-medium text-muted">Longitude</label>
                                        <input type="number" step="any" name="longitude" id="lngInput" class="form-control bg-light" placeholder="Klik dari peta" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success fw-bold">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS untuk Map Picker -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Sidebar Toggle
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if(sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function() {
                    sidebar.classList.toggle('active'); overlay.classList.toggle('active');
                });
            }
            if(overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active'); overlay.classList.remove('active');
                });
            }
        });

        // Script untuk Map Picker di dalam Modal Tambah
        let pickerMap = null;
        let pickerMarker = null;

        // Inisialisasi peta HANYA ketika modal terbuka (mencegah error ukuran peta)
        document.getElementById('modalTambah').addEventListener('shown.bs.modal', function () {
            if (!pickerMap) {
                // Pusat default di Kabupaten Asahan
                pickerMap = L.map('mapPicker').setView([2.9, 99.5], 10);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(pickerMap);

                // Event ketika peta diklik
                pickerMap.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    
                    // Isi input otomatis
                    document.getElementById('latInput').value = lat.toFixed(7);
                    document.getElementById('lngInput').value = lng.toFixed(7);
                    
                    // Pasang atau pindahkan pin marker
                    if (!pickerMarker) {
                        pickerMarker = L.marker([lat, lng]).addTo(pickerMap);
                    } else {
                        pickerMarker.setLatLng([lat, lng]);
                    }
                });
            }
            // Fix bug ukuran abu-abu pada leaflet dalam modal
            pickerMap.invalidateSize();
        });

        // Fitur Cari Daerah (Nominatim API gratis pengganti Google API)
        function searchMapLocation() {
            const query = document.getElementById('searchMapInput').value;
            if(!query) return;
            
            // Tambahkan keyword Asahan agar pencarian lebih akurat
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + query + ', Asahan')
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;
                        pickerMap.flyTo([lat, lon], 14);
                    } else {
                        alert('Daerah tidak ditemukan. Coba gunakan nama desa atau kecamatan yang lebih spesifik.');
                    }
                })
                .catch(err => alert('Gagal mencari lokasi. Pastikan internet aktif.'));
        }
    </script>
</body>
</html>