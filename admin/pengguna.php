<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    header("location:../auth/login.php"); 
    exit; 
}
include '../config/koneksi.php';

$pesan = "";

// PROSES TAMBAH DATA PENGGUNA
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Cek apakah username sudah ada
    $cek_username = mysqli_query($koneksi, "SELECT * FROM akun WHERE username='$username'");
    if(mysqli_num_rows($cek_username) > 0){
        $pesan = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>Gagal menambah! <strong>Username sudah digunakan</strong>.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO akun (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')");
        if($query){
            $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Data Pengguna berhasil <strong>ditambahkan</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal menambah data pengguna!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

// PROSES EDIT DATA PENGGUNA
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    $query = mysqli_query($koneksi, "UPDATE akun SET nama='$nama', username='$username', password='$password', role='$role' WHERE id='$id'");
    if($query){
        $pesan = "<div class='alert alert-info alert-dismissible fade show' role='alert'>Data Pengguna berhasil <strong>diperbarui</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal memperbarui data pengguna!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// PROSES HAPUS DATA PENGGUNA
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    
    // Mencegah admin menghapus dirinya sendiri
    if($id == $_SESSION['id_user']) {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Anda <strong>tidak dapat menghapus akun Anda sendiri</strong> yang sedang aktif!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        // Hapus juga data dari lokasi_tersimpan jika user ini punya simpanan (Mencegah Error Relasi)
        mysqli_query($koneksi, "DELETE FROM lokasi_tersimpan WHERE user_id='$id'");
        $query = mysqli_query($koneksi, "DELETE FROM akun WHERE id='$id'");
        
        if($query){
            header("Location: pengguna.php?msg=hapus_sukses");
            exit;
        }
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'hapus_sukses'){
    $pesan = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>Data Pengguna berhasil <strong>dihapus</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// AMBIL SEMUA DATA PENGGUNA
$query_pengguna = mysqli_query($koneksi, "SELECT * FROM akun ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - SIG SPBU</title>
    
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
            overflow-x: hidden;
        }

        :root {
            --pertamina-red: #E31E24;
            --pertamina-blue: #005BAC;
            --sidebar-width: 260px;
        }

        /* Layout Container */
        .wrapper { display: flex; width: 100%; align-items: stretch; }

        /* Sidebar Styling */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: white;
            color: #333;
            transition: all 0.3s;
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .sidebar-header { padding: 20px; background: var(--pertamina-red); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #eee; }
        #sidebar ul li a {
            padding: 12px 25px; font-size: 1.05em; display: block; color: #555; text-decoration: none; transition: 0.3s; border-left: 4px solid transparent;
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--pertamina-blue); background: #f8f9fa; border-left: 4px solid var(--pertamina-blue); font-weight: 500;
        }
        #sidebar ul li a i { margin-right: 10px; width: 20px; text-align: center; }

        /* Main Content Styling */
        #content { width: 100%; min-height: 100vh; transition: all 0.3s; display: flex; flex-direction: column; }
        .topbar { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }

        /* Table Styling */
        .custom-table-container { background: white; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.03); overflow: hidden; padding: 20px; }
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
        .table thead th { background-color: #f8f9fa; color: #555; font-weight: 600; border-bottom: 2px solid #eee; }

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
                <li><a href="spbu.php"><i class="fa-solid fa-map-location-dot"></i> Kelola Data SPBU</a></li>
                <!-- Menu Pengguna Aktif -->
                <li class="active"><a href="pengguna.php"><i class="fa-solid fa-users"></i> Kelola Pengguna</a></li>
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
                    <h5 class="mb-0 fw-bold text-dark d-none d-md-block">Data Master Pengguna</h5>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Pengguna Baru
                    </button>
                </div>
            </div>

            <div class="container-fluid p-4 p-md-5">
                
                <?= $pesan ?>

                <div class="custom-table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0" style="color: var(--pertamina-blue);">Daftar Akun Sistem</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="25%">Nama Lengkap</th>
                                    <th width="20%">Username</th>
                                    <th width="20%">Password</th>
                                    <th width="15%">Role Akses</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if(mysqli_num_rows($query_pengguna) > 0) {
                                    while($row = mysqli_fetch_assoc($query_pengguna)) {
                                ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $no++; ?></td>
                                    <td class="fw-bold text-dark">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                                <i class="fa-regular fa-user text-muted"></i>
                                            </div>
                                            <?= htmlspecialchars($row['nama']); ?>
                                        </div>
                                    </td>
                                    <td class="fw-medium">@<?= htmlspecialchars($row['username']); ?></td>
                                    <td class="text-muted">
                                        <!-- Password disamarkan sedikit di UI utama agar tidak terlalu polos -->
                                        <span class="small font-monospace bg-light border px-2 py-1 rounded"><?= htmlspecialchars($row['password']); ?></span>
                                    </td>
                                    <td>
                                        <?php if($row['role'] == 'admin'): ?>
                                            <span class="badge bg-danger text-white rounded-pill px-3 py-2"><i class="fa-solid fa-shield-halved me-1"></i> Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary text-white rounded-pill px-3 py-2"><i class="fa-solid fa-user me-1"></i> User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>" title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="pengguna.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus Pengguna <?= htmlspecialchars($row['nama']); ?>? Semua lokasi yang disimpan user ini juga akan terhapus.');" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal Edit Untuk Setiap Baris -->
                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen me-2"></i>Edit Data Pengguna</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="pengguna.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Nama Lengkap</label>
                                                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Username</label>
                                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Password</label>
                                                        <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($row['password']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium text-muted">Role Akses</label>
                                                        <select name="role" class="form-select" required>
                                                            <option value="admin" <?= ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                            <option value="user" <?= ($row['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                                        </select>
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
                                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='fa-solid fa-users-slash fs-1 mb-3 d-block'></i>Belum ada data pengguna yang ditambahkan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data Pengguna -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: var(--pertamina-blue);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="pengguna.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username unik" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Password</label>
                            <input type="text" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Role Akses</label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                <option value="admin">Admin (Akses Penuh)</option>
                                <option value="user">User (Akses Peta)</option>
                            </select>
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
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if(sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            }

            if(overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>
