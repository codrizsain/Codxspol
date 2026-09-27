<?php
session_start();
// Pastikan hanya user yang bisa mengakses
if(!isset($_SESSION['role']) || $_SESSION['role'] != "user"){ 
    header("location:../auth/login.php"); 
    exit; 
}
include '../config/koneksi.php';

// Ambil data SPBU dari database
$query_spbu = mysqli_query($koneksi, "SELECT * FROM spbu");
$data_spbu = [];

if($query_spbu) {
    while($row = mysqli_fetch_assoc($query_spbu)){
        $data_spbu[] = $row;
    }
}

// FALLBACK AUTOMATION: Jika tabel database masih kosong, gunakan data bawaan
if(count($data_spbu) == 0){
    $data_spbu = [
        [ 'id' => 1, 'id_spbu' => '14212267', 'nama_spbu' => 'SPBU Huta Padang', 'alamat' => 'Dusun 8, Desa Huta Padang, Kec. Bandar Pasir Mandoge', 'latitude' => 2.7846056, 'longitude' => 99.2480716 ],
        [ 'id' => 2, 'id_spbu' => '14212273', 'nama_spbu' => 'SPBU Air Batu', 'alamat' => 'Jl. Ahmad Yani, Tj. Alam, Kec. Air Batu', 'latitude' => 2.9716155, 'longitude' => 99.6127402 ],
        [ 'id' => 3, 'id_spbu' => '14212222', 'nama_spbu' => 'SPBU Simpang Empat', 'alamat' => 'Jl. Lintas Sumatra, Pulau Maria', 'latitude' => 2.7583560, 'longitude' => 99.5701655 ],
        [ 'id' => 4, 'id_spbu' => '14212298', 'nama_spbu' => 'SPBU Sei Kepayang', 'alamat' => 'Tj Balai–Tj Ledong, Sei Kepayang Tengah', 'latitude' => 2.9541839, 'longitude' => 99.8079059 ],
        [ 'id' => 5, 'id_spbu' => '14212252', 'nama_spbu' => 'SPBU Sei Renggas', 'alamat' => 'Jl Kisaran Barat, Sei Renggas', 'latitude' => 2.9596045, 'longitude' => 99.5888299 ],
        [ 'id' => 6, 'id_spbu' => '14212293', 'nama_spbu' => 'SPBU Teladan', 'alamat' => 'Jl Imam Bonjol', 'latitude' => 2.9747426, 'longitude' => 99.6275023 ],
        [ 'id' => 7, 'id_spbu' => '14212279', 'nama_spbu' => 'SPBU Teluk Dalam', 'alamat' => 'Air Teluk Hessa', 'latitude' => 2.9749163, 'longitude' => 99.5476747 ],
        [ 'id' => 8, 'id_spbu' => '14212297', 'nama_spbu' => 'SPBU Aek Songsongan', 'alamat' => 'Bandar Pulau', 'latitude' => 2.6735048, 'longitude' => 99.5105762 ],
        [ 'id' => 9, 'id_spbu' => '14212268', 'nama_spbu' => 'SPBU Hessa Air Genting', 'alamat' => 'Hessa Air Genting', 'latitude' => 2.9252132, 'longitude' => 99.3992203 ],
        [ 'id' => 10, 'id_spbu' => '14212278', 'nama_spbu' => 'SPBU Aek Loba', 'alamat' => 'Aek Loba', 'latitude' => 2.6558141, 'longitude' => 99.3247362 ],
        [ 'id' => 11, 'id_spbu' => '14212227', 'nama_spbu' => 'SPBU Sentang', 'alamat' => 'Jl Gatot Subroto', 'latitude' => 2.9679337, 'longitude' => 99.6219886 ],
        [ 'id' => 12, 'id_spbu' => '14213233', 'nama_spbu' => 'SPBU Aek Teluk Kiri', 'alamat' => 'Aek Teluk Kiri', 'latitude' => 3.0192030, 'longitude' => 99.5792021 ],
        [ 'id' => 13, 'id_spbu' => '14212220', 'nama_spbu' => 'SPBU Mekar Baru', 'alamat' => 'Jl HOS Cokroaminoto', 'latitude' => 2.9862791, 'longitude' => 99.6138160 ],
        [ 'id' => 14, 'id_spbu' => '14212290', 'nama_spbu' => 'SPBU Aek Ledong', 'alamat' => 'Aek Ledong', 'latitude' => 2.5878489, 'longitude' => 99.6328480 ],
        [ 'id' => 15, 'id_spbu' => '14212291', 'nama_spbu' => 'SPBU Air Joman', 'alamat' => 'Air Joman', 'latitude' => 2.9965100, 'longitude' => 99.6773966 ]
    ];
}
// TUTUP TAG PHP UTAMA DI SINI SEBELUM MASUK KE HTML & JS
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Interaktif - SIG SPBU</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .navbar-custom {
            background-color: #005BAC;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 70px;
            z-index: 1050;
        }

        .map-container {
            position: relative;
            height: calc(100vh - 70px);
            width: 100%;
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
            width: 320px;
            max-height: calc(100vh - 110px);
            display: flex;
            flex-direction: column;
            border-top: 4px solid #005BAC;
        }

        .search-box {
            padding: 15px;
            background: white;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid #eee;
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

        .floating-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 300px;
            padding: 20px;
            border-top: 4px solid #E31E24;
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

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .leaflet-popup-content {
            margin: 0;
            width: 260px !important;
        }
        .popup-header {
            background: #E31E24;
            color: white;
            padding: 12px 15px;
            text-align: center;
            font-weight: 600;
        }
        .popup-body {
            padding: 15px;
        }
        .popup-body p {
            margin-bottom: 10px;
            font-size: 13px;
            color: #555;
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

        @media (max-width: 768px) {
            .search-overlay { width: calc(100% - 40px); max-height: 40vh; }
            .floating-panel { top: auto; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
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

    <div class="map-container">
        <div id="map"></div>
        
        <div class="search-overlay d-none d-md-flex">
            <div class="search-box">
                <h6 class="fw-bold text-pertamina-blue mb-2"><i class="fa-solid fa-list me-2"></i>Daftar SPBU</h6>
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama atau alamat..." onkeyup="filterSPBU()">
                </div>
            </div>
            <div class="spbu-list-container" id="spbuList">
                </div>
        </div>

        <div class="floating-panel">
            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-notch text-primary me-2"></i>Analisis Radius</h6>
            <p class="small text-muted mb-3">Klik marker SPBU di peta lalu pilih radius jangkauan.</p>
            
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary fw-bold btn-sm" onclick="drawBuffer(3000)">
                    <i class="fa-solid fa-route me-1"></i> Radius 3 KM
                </button>
                <button class="btn btn-outline-danger fw-bold btn-sm" onclick="drawBuffer(5000)">
                    <i class="fa-solid fa-route me-1"></i> Radius 5 KM
                </button>
                <button class="btn btn-secondary fw-bold mt-1 btn-sm" onclick="clearBuffer()">
                    <i class="fa-solid fa-eraser me-1"></i> Bersihkan Area
                </button>
            </div>
            
            <hr class="my-3">
            <div class="text-center">
                <a href="simpan_lokasi.php" class="text-decoration-none small fw-bold text-success">
                    <i class="fa-solid fa-bookmark me-1"></i> Lihat SPBU Tersimpan
                </a>
            </div>
        </div>
    </div>

    <script id="spbu-data" type="application/json"><?php echo json_encode($data_spbu); ?></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const fallbackData = [
            { id: 1, id_spbu: '14212267', nama_spbu: 'SPBU Huta Padang', alamat: 'Dusun 8, Desa Huta Padang, Kec. Bandar Pasir Mandoge', latitude: 2.7846056, longitude: 99.2480716 },
            { id: 2, id_spbu: '14212273', nama_spbu: 'SPBU Air Batu', alamat: 'Jl. Ahmad Yani, Tj. Alam, Kec. Air Batu', latitude: 2.9716155, longitude: 99.6127402 },
            { id: 3, id_spbu: '14212222', nama_spbu: 'SPBU Simpang Empat', alamat: 'Jl. Lintas Sumatra, Pulau Maria', latitude: 2.7583560, longitude: 99.5701655 },
            { id: 4, id_spbu: '14212298', nama_spbu: 'SPBU Sei Kepayang', alamat: 'Tj Balai–Tj Ledong, Sei Kepayang Tengah', latitude: 2.9541839, longitude: 99.8079059 },
            { id: 5, id_spbu: '14212252', nama_spbu: 'SPBU Sei Renggas', alamat: 'Jl Kisaran Barat, Sei Renggas', latitude: 2.9596045, longitude: 99.5888299 },
            { id: 6, id_spbu: '14212293', nama_spbu: 'SPBU Teladan', alamat: 'Jl Imam Bonjol', latitude: 2.9747426, longitude: 99.6275023 },
            { id: 7, id_spbu: '14212279', nama_spbu: 'SPBU Teluk Dalam', alamat: 'Air Teluk Hessa', latitude: 2.9749163, longitude: 99.5476747 },
            { id: 8, id_spbu: '14212297', nama_spbu: 'SPBU Aek Songsongan', alamat: 'Bandar Pulau', latitude: 2.6735048, longitude: 99.5105762 },
            { id: 9, id_spbu: '14212268', nama_spbu: 'SPBU Hessa Air Genting', alamat: 'Hessa Air Genting', latitude: 2.9252132, longitude: 99.3992203 },
            { id: 10, id_spbu: '14212278', nama_spbu: 'SPBU Aek Loba', alamat: 'Aek Loba', latitude: 2.6558141, longitude: 99.3247362 },
            { id: 11, id_spbu: '14212227', nama_spbu: 'SPBU Sentang', alamat: 'Jl Gatot Subroto', latitude: 2.9679337, longitude: 99.6219886 },
            { id: 12, id_spbu: '14213233', nama_spbu: 'SPBU Aek Teluk Kiri', alamat: 'Aek Teluk Kiri', latitude: 3.0192030, longitude: 99.5792021 },
            { id: 13, id_spbu: '14212220', nama_spbu: 'SPBU Mekar Baru', alamat: 'Jl HOS Cokroaminoto', latitude: 2.9862791, longitude: 99.6138160 },
            { id: 14, id_spbu: '14212290', nama_spbu: 'SPBU Aek Ledong', alamat: 'Aek Ledong', latitude: 2.5878489, longitude: 99.6328480 },
            { id: 15, id_spbu: '14212291', nama_spbu: 'SPBU Air Joman', alamat: 'Air Joman', latitude: 2.9965100, longitude: 99.6773966 }
        ];

        let spbuData = [];
        try {
            const rawData = document.getElementById('spbu-data').textContent.trim();
            // Jika rawData mengandung tag PHP, artinya dijalankan di preview statis
            if (rawData.includes('<?php')) {
                spbuData = fallbackData;
            } else if (rawData) {
                spbuData = JSON.parse(rawData);
            } else {
                spbuData = fallbackData;
            }
        } catch (error) {
            console.error("Gagal memparsing data JSON SPBU:", error);
            spbuData = fallbackData;
        }
        
        const map = L.map('map', {zoomControl: false}).setView([2.8, 99.5], 11);
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
        
        let currentBuffer = null;
        let activeSpbu = null;
        const markersGroup = L.featureGroup().addTo(map);
        let markerRefs = [];

        function renderSPBUList(data) {
            const listContainer = document.getElementById('spbuList');
            listContainer.innerHTML = '';
            
            if(!data || data.length === 0) {
                listContainer.innerHTML = '<div class="text-center text-muted small mt-3">Tidak ada data SPBU ditemukan.</div>';
                return;
            }

            data.forEach((spbu) => {
                listContainer.innerHTML += `
                    <div class="spbu-card" onclick="fokusSPBU('${spbu.id}')">
                        <h6 class="fw-bold text-pertamina-blue mb-1" style="font-size: 13px;">${spbu.nama_spbu}</h6>
                        <p class="text-muted mb-0" style="font-size: 11px;"><i class="fa-solid fa-location-dot text-danger me-1"></i> ${spbu.alamat}</p>
                    </div>
                `;
            });
        }

        if (spbuData && spbuData.length > 0) {
            spbuData.forEach(spbu => {
                const marker = L.marker([spbu.latitude, spbu.longitude], {icon: customIcon}).addTo(markersGroup);
                
                const popupContent = `
                    <div class="popup-header">${spbu.nama_spbu}</div>
                    <div class="popup-body">
                        <p><i class="fa-solid fa-map-pin text-danger me-2"></i> ${spbu.alamat}</p>
                        <div class="bg-light p-2 rounded small text-center mb-3 font-monospace border">
                            Lat: ${spbu.latitude} <br> Lng: ${spbu.longitude}
                        </div>
                        <form action="simpan_lokasi.php" method="POST">
                            <input type="hidden" name="id_spbu" value="${spbu.id}">
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold shadow-sm">
                                <i class="fa-solid fa-bookmark me-1"></i> Simpan ke Favorit
                            </button>
                        </form>
                    </div>
                `;
                marker.bindPopup(popupContent);
                markerRefs.push({ id: spbu.id, marker: marker, data: spbu });
                
                marker.on('click', () => { 
                    activeSpbu = spbu; 
                    clearBuffer();
                });
            });

            renderSPBUList(spbuData);
            map.fitBounds(markersGroup.getBounds());
        }

        function filterSPBU() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            const filteredData = spbuData.filter(spbu => 
                (spbu.nama_spbu && spbu.nama_spbu.toLowerCase().includes(keyword)) || 
                (spbu.alamat && spbu.alamat.toLowerCase().includes(keyword))
            );
            
            renderSPBUList(filteredData);
            
            markerRefs.forEach(item => {
                const isMatch = (item.data.nama_spbu && item.data.nama_spbu.toLowerCase().includes(keyword)) || 
                                (item.data.alamat && item.data.alamat.toLowerCase().includes(keyword));
                                
                if(isMatch) {
                    if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
                } else {
                    if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
                }
            });
        }

        function fokusSPBU(id) {
            const target = markerRefs.find(m => m.id.toString() === id.toString());
            if(target) {
                map.flyTo([target.data.latitude, target.data.longitude], 16, { animate: true, duration: 1.5 });
                target.marker.openPopup();
                activeSpbu = target.data;
                clearBuffer();
            }
        }

        function drawBuffer(radius) {
            if (!activeSpbu) {
                alert("Silakan klik salah satu icon / marker SPBU di peta atau di daftar terlebih dahulu!");
                return;
            }
            clearBuffer();
            currentBuffer = L.circle([activeSpbu.latitude, activeSpbu.longitude], {
                color: '#E31E24', fillColor: '#E31E24', fillOpacity: 0.15, radius: radius, weight: 2, dashArray: '5, 5'
            }).addTo(map);
            map.fitBounds(currentBuffer.getBounds());
        }

        function clearBuffer() {
            if (currentBuffer) { map.removeLayer(currentBuffer); currentBuffer = null; }
        }
    </script>
</body>
</html>
