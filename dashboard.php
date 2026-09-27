<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    header("location:../auth/login.php"); 
    exit; 
}
include '../config/koneksi.php';

// Menghitung Total Statistik
$query_total_spbu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM spbu");
$total_spbu = mysqli_fetch_assoc($query_total_spbu)['total'];

$query_total_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM akun WHERE role='user'");
$total_user = mysqli_fetch_assoc($query_total_user)['total'];

$query_total_saved = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM lokasi_tersimpan");
$total_saved = $query_total_saved ? mysqli_fetch_assoc($query_total_saved)['total'] : 0;

// Mengambil 5 SPBU terbaru untuk preview di dashboard
$query_latest_spbu = mysqli_query($koneksi, "SELECT * FROM spbu ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIG SPBU</title>
    
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

        /* Variables */
        :root {
            --pertamina-red: #E31E24;
            --pertamina-blue: #005BAC;
            --sidebar-width: 260px;
        }

        /* Layout Container */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

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

        .sidebar-header {
            padding: 20px;
            background: var(--pertamina-red);
            color: white;
            text-align: center;
        }

        #sidebar ul.components {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            font-size: 1.05em;
            display: block;
            color: #555;
            text-decoration: none;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }

        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--pertamina-blue);
            background: #f8f9fa;
            border-left: 4px solid var(--pertamina-blue);
            font-weight: 500;
        }

        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styling */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        /* Topbar Styling */
        .topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-icon-red { background: rgba(227, 30, 36, 0.1); color: var(--pertamina-red); }
        .bg-icon-blue { background: rgba(0, 91, 172, 0.1); color: var(--pertamina-blue); }
        .bg-icon-green { background: rgba(25, 135, 84, 0.1); color: #198754; }

        /* Table Styling */
        .custom-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        
        .table-header {
            background: var(--pertamina-blue);
            color: white;
            padding: 15px 25px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                position: fixed;
            }
            #sidebar.active {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar Overlay (Mobile) -->
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
                <li class="active">
                    <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                </li>
                <li>
                    <a href="spbu.php"><i class="fa-solid fa-map-location-dot"></i> Kelola Data SPBU</a>
                </li>
                <li>
                    <a href="pengguna.php"><i class="fa-solid fa-users"></i> Kelola Pengguna</a>
                </li>
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
                    <h5 class="mb-0 fw-bold text-dark d-none d-md-block">Dashboard Statistik</h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Jam dan Tanggal Realtime -->
                    <div class="text-muted small me-3 d-none d-sm-flex align-items-center bg-light px-3 py-2 rounded-pill border">
                        <i class="fa-regular fa-clock me-2 text-primary"></i> 
                        <span id="realtimeClock" class="fw-bold text-dark"></span>
                    </div>
                    <a href="../index.php" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fa-solid fa-eye me-1"></i> Lihat Web
                    </a>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="container-fluid p-4 p-md-5">
                
                <div class="mb-4">
                    <h3 class="fw-bold" style="color: var(--pertamina-blue);">Halo, <?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Administrator'; ?>!</h3>
                    <p class="text-muted">Selamat datang di panel kontrol Sistem Informasi Geografis SPBU Pertamina.</p>
                </div>

                <!-- Stats Row -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <p class="text-muted mb-1 fw-medium">Total SPBU</p>
                                <h2 class="fw-bold mb-0 text-dark"><?php echo $total_spbu; ?></h2>
                            </div>
                            <div class="stat-icon bg-icon-red">
                                <i class="fa-solid fa-gas-pump"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <p class="text-muted mb-1 fw-medium">Total Pengguna Aktif</p>
                                <h2 class="fw-bold mb-0 text-dark"><?php echo $total_user; ?></h2>
                            </div>
                            <div class="stat-icon bg-icon-blue">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <p class="text-muted mb-1 fw-medium">Interaksi (Lokasi Disimpan)</p>
                                <h2 class="fw-bold mb-0 text-dark"><?php echo $total_saved; ?></h2>
                            </div>
                            <div class="stat-icon bg-icon-green">
                                <i class="fa-solid fa-bookmark"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Data Preview -->
                <div class="custom-table">
                    <div class="table-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-list me-2 text-warning"></i> 5 SPBU Terbaru Ditambahkan</h6>
                        <a href="spbu.php" class="btn btn-light btn-sm text-primary fw-bold" style="font-size: 12px;">Kelola Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">No. SPBU</th>
                                    <th>Nama SPBU</th>
                                    <th>Alamat Lengkap</th>
                                    <th>Koordinat (Lat, Lng)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if($query_latest_spbu && mysqli_num_rows($query_latest_spbu) > 0) {
                                    while($row = mysqli_fetch_assoc($query_latest_spbu)) {
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo htmlspecialchars($row['id_spbu']); ?></td>
                                    <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['nama_spbu']); ?></td>
                                    <td class="small text-muted text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($row['alamat']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace fw-normal">
                                            <?php echo htmlspecialchars($row['latitude']); ?>, <?php echo htmlspecialchars($row['longitude']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada data SPBU.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Jam Realtime & Sidebar -->
    <script>
        // Fungsi Jam Realtime
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            let hours = now.getHours().toString().padStart(2, '0');
            let minutes = now.getMinutes().toString().padStart(2, '0');
            let seconds = now.getSeconds().toString().padStart(2, '0');
            
            const formatString = `${dayName}, ${date} ${monthName} ${year} • ${hours}:${minutes}:${seconds}`;
            document.getElementById('realtimeClock').textContent = formatString;
        }
        
        // Panggil fungsi setiap detik
        setInterval(updateClock, 1000);
        updateClock(); // Panggil sekali saat load agar tidak delay

        // Toggle Sidebar Mobile
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