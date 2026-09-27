<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG SPBU Pertamina - Kab. Asahan</title>
  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            overflow-x: hidden; 
            background-color: #f8f9fa;
        }
      
        .text-pertamina-blue { color: #005BAC; }
        .text-pertamina-red { color: #E31E24; }
        .bg-pertamina-blue { background-color: #005BAC; }
        .bg-pertamina-red { background-color: #E31E24; }
      
        .navbar { 
            transition: all 0.3s; 
            background: #005BAC !important; /* Solid blue untuk kontras dengan peta */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .nav-link { 
            position: relative; 
            font-weight: 500;
        }
        .nav-link::after {
            content: ''; 
            position: absolute; 
            width: 0; 
            height: 2px;
            bottom: 0; 
            left: 50%; 
            background-color: #FFD700;
            transition: all 0.3s ease-in-out; 
            transform: translateX(-50%);
        }
        .nav-link:hover::after, .nav-link.active::after { 
            width: 80%; 
        }

        /* Hero / Map Section */
        .map-section {
            position: relative;
            height: calc(100vh - 76px); /* Mengurangi tinggi navbar */
            width: 100%;
            margin-top: 76px;
        }
        
        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

    
        .search-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 360px;
            max-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            border-top: 5px solid #E31E24;
        }

        .search-box {
            padding: 20px;
            background: white;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            z-index: 2;
        }

        .spbu-list-container {
            overflow-y: auto;
            padding: 15px;
            flex-grow: 1;
        }

        .spbu-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }
        .spbu-card:hover {
            border-color: #005BAC;
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(0,91,172,0.1);
        }

   
        .pertamina-marker {
            background-color: #E31E24;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.4);
        }
        .pertamina-marker i {
            transform: rotate(45deg);
            color: white;
            font-size: 14px;
        }

        /* Feature Cards */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            background: #ffffff;
            position: relative;
            z-index: 1;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: #E31E24;
            z-index: -1;
            transition: height 0.3s ease;
        }
        .feature-card:hover::before {
            height: 100%;
            opacity: 0.05;
        }
        .feature-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        .icon-box {
            width: 80px; height: 80px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; margin: 0 auto 20px auto;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .search-overlay {
                width: calc(100% - 40px);
                max-height: 40vh;
            }
        }
    </style>
</head>
<body>
    
 
    <nav class="navbar navbar-expand-lg navbar-dark position-fixed w-100 top-0" style="z-index: 1050;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="#">
                <div class="bg-white p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-gas-pump text-pertamina-red"></i>
                </div>
                SIG <span style="color: #FFD700; margin-left: 5px;">Asahan</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-3">
                    <li class="nav-item"><a class="nav-link text-white active px-3" href="#beranda">Peta Lokasi</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#fitur">Fitur Utama</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#tentang">Tentang Sistem</a></li>
                </ul>
                <div class="d-flex mt-3 mt-lg-0">
                    <a href="auth/login.php" class="btn btn-light text-pertamina-red fw-bold rounded-pill px-4 py-2 shadow-sm d-flex align-items-center">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk / Login
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section id="beranda" class="map-section">
        <div id="map"></div>
        
        <div class="search-overlay">
            <div class="search-box">
                <h5 class="fw-bold text-pertamina-blue mb-1"><i class="fa-solid fa-location-crosshairs me-2 text-pertamina-red"></i>Cari SPBU</h5>
                <p class="small text-muted mb-3">Temukan 15 lokasi SPBU di Asahan</p>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Ketik nama atau lokasi..." onkeyup="filterSPBU()">
                </div>
            </div>
            
            <div class="spbu-list-container" id="spbuList">
                <!-- Daftar SPBU diisi via JavaScript -->
            </div>
        </div>
    </section>

  
    <section id="fitur" class="py-5" style="background-color: #f8f9fa;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-danger bg-opacity-10 text-pertamina-red px-3 py-2 rounded-pill fw-bold mb-3 tracking-wide text-uppercase">
                    Keunggulan Sistem
                </span>
                <h2 class="fw-bold text-pertamina-blue display-6 mb-3">Fitur Utama Aplikasi</h2>
                <div class="mx-auto bg-pertamina-red rounded-pill" style="width: 80px; height: 4px;"></div>
                <p class="text-muted mt-3 mx-auto" style="max-width: 600px;">Sistem ini dilengkapi dengan berbagai fitur spasial canggih untuk memberikan pengalaman terbaik dalam mencari lokasi SPBU.</p>
            </div>
            
            <div class="row g-4 mt-2">
                <!-- Fitur 1 -->
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary shadow-sm">
                            <i class="fa-solid fa-map-marked-alt fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Peta Interaktif</h4>
                        <p class="text-muted mb-0">Visualisasi penyebaran lokasi SPBU di Kab. Asahan dengan titik koordinat presisi menggunakan teknologi <b>Leaflet JS</b> yang ringan dan cepat.</p>
                    </div>
                </div>
                <!-- Fitur 2 -->
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="icon-box bg-danger bg-opacity-10 text-pertamina-red shadow-sm">
                            <i class="fa-solid fa-circle-notch fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Analisis Radius Buffer</h4>
                        <p class="text-muted mb-0">Kemampuan analisis spasial cerdas untuk melihat jangkauan area SPBU terdekat dalam radius <b>3 KM</b> dan <b>5 KM</b> dari lokasi titik acuan (Khusus Login).</p>
                    </div>
                </div>
                <!-- Fitur 3 -->
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success shadow-sm">
                            <i class="fa-solid fa-bookmark fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Simpan Lokasi Favorit</h4>
                        <p class="text-muted mb-0">Pengguna terdaftar (User) dapat dengan mudah <b>menyimpan lokasi SPBU favorit</b> untuk mempercepat pencarian rute di kemudian hari.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="tentang" class="bg-pertamina-blue text-white pt-5 pb-3">
        <div class="container text-center pt-3">
            <div class="d-flex justify-content-center align-items-center mb-4 gap-2">
                <div class="bg-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-gas-pump fs-3 text-pertamina-red"></i>
                </div>
                <h3 class="fw-bold mb-0">SIG SPBU Asahan</h3>
            </div>
            
            <p class="text-white-50 mx-auto mb-4 lead" style="max-width: 700px; font-size: 1.05rem;">
                Sistem Informasi Geografis ini dibangun untuk memudahkan masyarakat dalam mencari dan memetakan lokasi SPBU Pertamina di Kabupaten Asahan secara cepat, akurat, dan berbasis spasial.
            </p>

            <div class="border-top border-light opacity-25 mb-4"></div>
            
            <div class="row align-items-center text-white-50 small">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    &copy; 2026 Sistem Informasi Geografis Pertamina. Hak Cipta Dilindungi.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Dibuat menggunakan Bootstrap & Leaflet JS
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Data 15 SPBU Kabupaten Asahan (Telah Diperbarui Sesuai Database Terkini)
        const dataSPBU = [
            { id: '14212267', nama: 'SPBU Huta Padang', alamat: 'Dusun 8, Desa Huta Padang, Kec. Bandar Pasir Mandoge', lat: 2.7846056, lng: 99.2480716 },
            { id: '14212273', nama: 'SPBU Air Batu', alamat: 'Jl. Ahmad Yani, Tj. Alam, Kec. Air Batu', lat: 2.9716155, lng: 99.6127402 },
            { id: '14212222', nama: 'SPBU Simpang Empat', alamat: 'Jl. Lintas Sumatra, Pulau Maria', lat: 2.7583560, lng: 99.5701655 },
            { id: '14212298', nama: 'SPBU Sei Kepayang', alamat: 'Tj Balai–Tj Ledong, Sei Kepayang Tengah', lat: 2.9541839, lng: 99.8079059 },
            { id: '14212252', nama: 'SPBU Sei Renggas', alamat: 'Jl Kisaran Barat, Sei Renggas', lat: 2.9596045, lng: 99.5888299 },
            { id: '14212293', nama: 'SPBU Teladan', alamat: 'Jl Imam Bonjol', lat: 2.9747426, lng: 99.6275023 },
            { id: '14212279', nama: 'SPBU Teluk Dalam', alamat: 'Air Teluk Hessa', lat: 2.9749163, lng: 99.5476747 },
            { id: '14212297', nama: 'SPBU Aek Songsongan', alamat: 'Bandar Pulau', lat: 2.6735048, lng: 99.5105762 },
            { id: '14212268', nama: 'SPBU Hessa Air Genting', alamat: 'Hessa Air Genting', lat: 2.9252132, lng: 99.3992203 },
            { id: '14212278', nama: 'SPBU Aek Loba', alamat: 'Aek Loba', lat: 2.6558141, lng: 99.3247362 },
            { id: '14212227', nama: 'SPBU Sentang', alamat: 'Jl Gatot Subroto', lat: 2.9679337, lng: 99.6219886 },
            { id: '14213233', nama: 'SPBU Aek Teluk Kiri', alamat: 'Aek Teluk Kiri', lat: 3.0192030, lng: 99.5792021 },
            { id: '14212220', nama: 'SPBU Mekar Baru', alamat: 'Jl HOS Cokroaminoto', lat: 2.9862791, lng: 99.6138160 },
            { id: '14212290', nama: 'SPBU Aek Ledong', alamat: 'Aek Ledong', lat: 2.5878489, lng: 99.6328480 },
            { id: '14212291', nama: 'SPBU Air Joman', alamat: 'Air Joman', lat: 2.9965100, lng: 99.6773966 } // <- Titik koordinat Air Joman sudah disesuaikan
        ];

        const map = L.map('map', {zoomControl: false}).setView([2.8, 99.5], 10);
      
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const customIcon = L.divIcon({
            className: 'custom-icon',
            html: `<div class="pertamina-marker"><i class="fa-solid fa-gas-pump"></i></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        const markersGroup = L.featureGroup().addTo(map);
        let markerRefs = [];
        function renderSPBUList(data) {
            const listContainer = document.getElementById('spbuList');
            listContainer.innerHTML = '';

            data.forEach((spbu, index) => {
                listContainer.innerHTML += `
                    <div class="spbu-card" onclick="fokusSPBU(${index})">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold text-pertamina-blue mb-0" style="font-size: 14px;">${spbu.nama}</h6>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">#${spbu.id}</span>
                        </div>
                        <p class="text-muted mb-0 small"><i class="fa-solid fa-location-dot text-pertamina-red me-1"></i> ${spbu.alamat}</p>
                    </div>
                `;
            });
        }

        dataSPBU.forEach((spbu, index) => {
            const marker = L.marker([spbu.lat, spbu.lng], { icon: customIcon }).addTo(markersGroup);
            
            const popupContent = `
                <div class="text-center" style="min-width: 180px;">
                    <div class="bg-pertamina-red text-white p-2 rounded-top" style="margin: -14px -20px 10px -20px;">
                        <h6 class="fw-bold mb-0">${spbu.nama}</h6>
                    </div>
                    <p class="small text-muted mb-2"><i class="fa-solid fa-map-pin me-1"></i> ${spbu.alamat}</p>
                    <a href="auth/login.php" class="btn btn-sm btn-outline-primary w-100 fw-bold" style="font-size:12px;">Login untuk Fitur Lengkap</a>
                </div>
            `;
            marker.bindPopup(popupContent);
            markerRefs.push({ data: spbu, marker: marker });
        });

        map.fitBounds(markersGroup.getBounds());
        
        renderSPBUList(dataSPBU);
        function filterSPBU() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            const filteredData = dataSPBU.filter(spbu => 
                spbu.nama.toLowerCase().includes(keyword) || 
                spbu.alamat.toLowerCase().includes(keyword)
            );
            
            renderSPBUList(filteredData);
            
            markerRefs.forEach(item => {
                if(item.data.nama.toLowerCase().includes(keyword) || item.data.alamat.toLowerCase().includes(keyword)) {
                    if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
                } else {
                    if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
                }
            });
        }

        function fokusSPBU(index) {
            const targetSpbu = dataSPBU[index];
            const targetMarkerRef = markerRefs.find(m => m.data.id === targetSpbu.id);
            
            if(targetMarkerRef) {
                map.flyTo([targetSpbu.lat, targetSpbu.lng], 16, { animate: true, duration: 1.5 });
                targetMarkerRef.marker.openPopup();
            }
        }
    </script>
</body>
</html>
