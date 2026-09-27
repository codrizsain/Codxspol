<?php 
session_start(); 
include '../config/koneksi.php';

// Jika sudah login, arahkan kembali ke dashboard sesuai role
if(isset($_SESSION['role'])) { 
    if($_SESSION['role'] == 'admin'){
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;
} 

$pesan_error = "";
$pesan_sukses = "";
$tampilkan_daftar = false; // Flag untuk mengingat form mana yang aktif

// ==========================================
// PROSES PENDAFTARAN (REGISTER)
// ==========================================
if(isset($_POST['daftar'])) {
    $tampilkan_daftar = true; // Tetap di form daftar jika disubmit
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Mengambil token captcha dari form (hidden input) - DIPERBAIKI UNTUK PHP LAMA
    $jawaban_captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';

    // Validasi Custom Captcha Checkbox
    if(!empty($jawaban_captcha) && $jawaban_captcha === $_SESSION['captcha_secret']) {
        // Cek apakah username sudah ada
        $cek_user = mysqli_query($koneksi, "SELECT * FROM akun WHERE username='$username'");
        if(mysqli_num_rows($cek_user) > 0){
            $pesan_error = "Username sudah digunakan! Silakan pilih yang lain.";
        } else {
            // Masukkan data ke database dengan role otomatis 'user'
            $insert = mysqli_query($koneksi, "INSERT INTO akun (nama, username, password, role) VALUES ('$nama', '$username', '$password', 'user')");
            if($insert){
                $pesan_sukses = "Pendaftaran berhasil! Silakan Login menggunakan akun Anda.";
                $tampilkan_daftar = false; // Pindah ke form login jika sukses
            } else {
                $pesan_error = "Terjadi kesalahan sistem, pendaftaran gagal.";
            }
        }
    } else {
        $pesan_error = "Verifikasi Captcha gagal! Harap centang kotak 'Saya bukan robot'.";
    }
}

// ==========================================
// GENERATE CUSTOM CAPTCHA SECRET TOKEN
// ==========================================
// Membuat token rahasia setiap kali halaman dimuat untuk keamanan
// Menggunakan md5(uniqid()) sebagai pengganti random_bytes() agar kompatibel dengan PHP versi lama (PHP 5.x)
$_SESSION['captcha_secret'] = substr(md5(uniqid(rand(), true)), 0, 16);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Daftar - SIG SPBU Pertamina</title>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(0, 91, 172, 0.85), rgba(227, 30, 36, 0.85)), url('https://images.unsplash.com/photo-1621213329971-ceb325df200a?q=80&w=2070') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 20px 0;
        }

        /* Efek Glassmorphism */
        .login-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border-radius: 1.5rem;
            color: white;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
            transition: all 0.4s ease;
        }

        /* Input Field Styling */
        .input-group-custom { position: relative; margin-bottom: 1.2rem; }
        .input-group-custom i {
            position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8); z-index: 10; font-size: 1.1rem;
        }
        .login-glass .form-control {
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3);
            color: white; padding: 0.8rem 1rem 0.8rem 3rem; transition: all 0.3s;
        }
        .login-glass .form-control:focus {
            background: rgba(255, 255, 255, 0.25); border-color: #FFD700; box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }
        .login-glass .form-control::placeholder { color: rgba(255, 255, 255, 0.7); }
        
        /* =======================================
           CUSTOM CHECKBOX CAPTCHA STYLE 
           ======================================= */
        .custom-recaptcha {
            background: #fafafa;
            border-radius: 4px;
            border: 1px solid #d3d3d3;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #333;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            user-select: none;
            transition: box-shadow 0.3s;
        }
        .custom-recaptcha:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .cr-left { display: flex; align-items: center; gap: 15px; }
        .cr-checkbox {
            width: 28px; height: 28px;
            border: 2px solid #c1c1c1;
            border-radius: 3px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        /* State Loading */
        .cr-checkbox.loading { border-color: transparent; background: transparent; }
        .spinner {
            width: 26px; height: 26px;
            border: 3px solid #e0e0e0;
            border-top-color: #005BAC; /* Warna biru Pertamina */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        /* State Success */
        .cr-checkbox.success { border-color: transparent; }
        
        .cr-text { font-family: Roboto, helvetica, arial, sans-serif; font-size: 14px; font-weight: 500; color: #222; }
        .cr-logo { display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 10px; color: #999; line-height: 1.2; }
        .cr-logo i { font-size: 22px; color: #005BAC; margin-bottom: 2px; }

        /* Buttons */
        .btn-pertamina-red {
            background-color: #E31E24; color: white; border: none; transition: all 0.3s; font-weight: 600; padding: 0.8rem;
        }
        .btn-pertamina-red:hover { background-color: #c0191e; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(227, 30, 36, 0.4); color: white; }
        
        .btn-pertamina-blue {
            background-color: #005BAC; color: white; border: none; transition: all 0.3s; font-weight: 600; padding: 0.8rem;
        }
        .btn-pertamina-blue:hover { background-color: #004583; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 91, 172, 0.4); color: white; }

        .icon-header {
            width: 80px; height: 80px; background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .icon-header i { font-size: 35px; color: #E31E24; }

        /* Links */
        .toggle-link {
            color: #FFD700; font-weight: 600; cursor: pointer; text-decoration: none; transition: 0.3s;
        }
        .toggle-link:hover { color: white; text-decoration: underline; }
        .back-link { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s; font-weight: 400; }
        .back-link:hover { color: #FFD700; text-decoration: none; }
        
        .circle-bg { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.1); z-index: 1; filter: blur(8px); }
        .circle-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .circle-2 { width: 400px; height: 400px; bottom: -150px; right: -150px; }

        /* Animasi Transisi Form */
        .form-section { display: none; animation: fadeIn 0.5s; }
        .form-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="circle-bg circle-1"></div>
    <div class="circle-bg circle-2"></div>

    <div class="container d-flex justify-content-center">
        <div class="login-glass">
            
            <div class="text-center mb-4">
                <div class="icon-header">
                    <i class="fa-solid fa-gas-pump"></i>
                </div>
                <h3 class="fw-bold mb-1" id="form-title">Selamat Datang</h3>
                <p class="mb-0" style="color: rgba(255,255,255,0.8);">Sistem Informasi Geografis SPBU</p>
            </div>

            <!-- Pesan Notifikasi PHP -->
            <?php if(isset($_GET['pesan']) && !$tampilkan_daftar): ?>
                <div class='alert alert-danger alert-dismissible fade show small rounded-4 shadow-sm' role='alert' style='background: rgba(227,30,36,0.3); color: #fff; border: 1px solid rgba(227,30,36,0.5);'>
                    <i class='fa-solid fa-circle-exclamation me-2'></i> <?= htmlspecialchars($_GET['pesan']) ?>
                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='alert'></button>
                </div>
            <?php endif; ?>

            <?php if($pesan_error): ?>
                <div class='alert alert-danger alert-dismissible fade show small rounded-4 shadow-sm' role='alert' style='background: rgba(227,30,36,0.3); color: #fff; border: 1px solid rgba(227,30,36,0.5);'>
                    <i class='fa-solid fa-circle-exclamation me-2'></i> <?= $pesan_error ?>
                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='alert'></button>
                </div>
            <?php endif; ?>

            <?php if($pesan_sukses): ?>
                <div class='alert alert-success alert-dismissible fade show small rounded-4 shadow-sm' role='alert' style='background: rgba(25, 135, 84, 0.4); color: #fff; border: 1px solid rgba(25, 135, 84, 0.6);'>
                    <i class='fa-solid fa-circle-check me-2'></i> <?= $pesan_sukses ?>
                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='alert'></button>
                </div>
            <?php endif; ?>

            <!-- ============================== -->
            <!-- FORM 1: LOGIN                  -->
            <!-- ============================== -->
            <div id="form-login" class="form-section <?= (!$tampilkan_daftar) ? 'active' : '' ?>">
                <form action="cek_login.php" method="POST">
                    <div class="input-group-custom">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" class="form-control rounded-pill" required placeholder="Masukkan Username">
                    </div>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control rounded-pill" required placeholder="Masukkan Password">
                    </div>
                    <button type="submit" class="btn btn-pertamina-red w-100 rounded-pill mt-2 shadow">
                        MASUK SEKARANG <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
                <div class="text-center mt-3">
                    <p class="small text-white-50">Belum punya akun? <a class="toggle-link" onclick="toggleForm('register')">Daftar di sini</a></p>
                </div>
            </div>

            <!-- ============================== -->
            <!-- FORM 2: REGISTER (DAFTAR)      -->
            <!-- ============================== -->
            <div id="form-register" class="form-section <?= ($tampilkan_daftar) ? 'active' : '' ?>">
                <form action="login.php" method="POST" id="registerForm">
                    <div class="input-group-custom">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" name="nama" class="form-control rounded-pill" required placeholder="Nama Lengkap">
                    </div>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" class="form-control rounded-pill" required placeholder="Buat Username">
                    </div>
                    <div class="input-group-custom mb-3">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control rounded-pill" required placeholder="Buat Password">
                    </div>
                    
                    <!-- Fitur Custom Checkbox Captcha -->
                    <div class="custom-recaptcha mb-4" id="captchaBox" onclick="verifyCaptcha('<?= $_SESSION['captcha_secret'] ?>')">
                        <div class="cr-left">
                            <div class="cr-checkbox" id="captchaCheckbox"></div>
                            <div class="cr-text">Saya bukan robot</div>
                        </div>
                        <div class="cr-logo">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Sistem<br>Keamanan</span>
                        </div>
                        <!-- Input tersembunyi untuk menyimpan secret token -->
                        <input type="hidden" name="captcha" id="captchaInput" value="" required>
                    </div>

                    <button type="submit" name="daftar" id="btnRegister" class="btn btn-pertamina-blue w-100 rounded-pill shadow">
                        BUAT AKUN <i class="fa-solid fa-user-plus ms-2"></i>
                    </button>
                </form>
                <div class="text-center mt-3">
                    <p class="small text-white-50">Sudah punya akun? <a class="toggle-link" onclick="toggleForm('login')">Masuk di sini</a></p>
                </div>
            </div>

            <!-- Tombol Kembali (Universal) -->
            <div class="text-center mt-4 border-top pt-3 border-light border-opacity-25">
                <a href="../index.php" class="back-link small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
            
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Toggle Form & Custom Captcha -->
    <script>
        function toggleForm(type) {
            const formLogin = document.getElementById('form-login');
            const formRegister = document.getElementById('form-register');
            const title = document.getElementById('form-title');

            if(type === 'register') {
                formLogin.classList.remove('active');
                formRegister.classList.add('active');
                title.innerText = 'Buat Akun Baru';
            } else {
                formRegister.classList.remove('active');
                formLogin.classList.add('active');
                title.innerText = 'Selamat Datang';
            }
        }

        // Jalankan saat load (jika ada error di form register agar judul sesuai)
        window.onload = function() {
            if(document.getElementById('form-register').classList.contains('active')){
                document.getElementById('form-title').innerText = 'Buat Akun Baru';
            }
        };

        // Fungsi Animasi Custom Captcha
        function verifyCaptcha(secretToken) {
            const cb = document.getElementById('captchaCheckbox');
            const input = document.getElementById('captchaInput');
            const box = document.getElementById('captchaBox');

            // Jika sudah sukses atau sedang loading, cegah klik ganda
            if(cb.classList.contains('success') || cb.classList.contains('loading')) return;

            // 1. Ubah ke status Loading (Animasi Muter)
            cb.classList.add('loading');
            cb.innerHTML = '<div class="spinner"></div>';
            box.style.cursor = 'default';

            // 2. Simulasikan jeda pemrosesan jaringan (1.5 detik)
            setTimeout(() => {
                // 3. Ubah ke status Sukses (Centang Hijau)
                cb.classList.remove('loading');
                cb.classList.add('success');
                cb.innerHTML = '<i class="fa-solid fa-check text-success fs-3"></i>';
                
                // 4. Masukkan token rahasia ke input hidden agar lolos validasi PHP saat tombol submit ditekan
                input.value = secretToken; 
            }, 1500); // 1500ms = 1.5 detik loading
        }

        // Cegah tombol register ditekan sebelum captcha hijau
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const captchaVal = document.getElementById('captchaInput').value;
            if(!captchaVal) {
                e.preventDefault(); // Batalkan pengiriman
                alert("Verifikasi gagal! Harap klik kotak 'Saya bukan robot' terlebih dahulu.");
            }
        });
    </script>
</body>
</html>
