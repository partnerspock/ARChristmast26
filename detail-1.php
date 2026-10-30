<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karakter 1 - Kartu AR Interaktif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Orbitron:wght@600;800;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>

    <style>
        /* ==========================================================================
           PENGATURAN UTAMA KARTU, LINGKARAN, & KOTAK TEKS BAWAH
           ========================================================================== */
        :root {
            /* 1. UKURAN & RASIO KARTU:
               Sesuai ukuran gambar card.png asli yaitu 595 x 882 px. */
            --card-ratio-w: 595;
            --card-ratio-h: 882;
            --card-aspect-ratio: 595 / 882;
            --card-max-width: 430px; /* Lebar kartu di layar */

            /* 2. PENGATURAN LINGKARAN GAMBAR / POKEBALL (Bagian Atas):
               - Pusat Lingkaran: X = 50% (297.5px), Y = 39.34% (347px)
               - Ukuran Diameter: 68% */
            --circle-top: 39.34%;    /* Posisi vertikal dari atas */
            --circle-left: 50%;      /* Posisi horizontal dari kiri */
            --circle-size: 68%;      /* Diameter lingkaran (% terhadap lebar kartu) */

            /* 3. PENGATURAN KOTAK TEKS (Bagian Bawah Kartu):
               Area kotak hitam berbingkai pada gambar kartu
               - X: ~11% - 88% (lebar ~77%)
               - Y: mulai dari 65.8% sampai 91.5% (tinggi ~25.8%) */
            --box-top: 65.8%;        /* Posisi kotak dari atas kartu */
            --box-left: 50%;         /* Posisi horizontal di tengah kartu */
            --box-width: 77%;        /* Lebar kotak teks */
            --box-height: 25.8%;     /* Tinggi kotak teks */

            /* 4. WARNA NEON BLUE & BLACK UNTUK TOMBOL 3D */
            --neon-blue: #00f0ff;
            --neon-blue-glow: rgba(0, 240, 255, 0.6);
            --neon-blue-dark: #007a99;
            --neon-black-bg: #070b14;
            --neon-black-border: #00f0ff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Fredoka', 'Nunito', sans-serif;
            background-color: #faf8f5;
            /* Pola titik halus dan ceria */
            background-image: 
                radial-gradient(circle at 20px 20px, rgba(239, 68, 68, 0.06) 2.5px, transparent 0),
                radial-gradient(circle at 60px 60px, rgba(245, 158, 11, 0.09) 3.5px, transparent 0);
            background-size: 70px 70px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            position: relative;
        }

        /* Tombol Mengambang Santai untuk Kembali ke Menu (di pojok layar) */
        .floating-back {
            position: fixed;
            top: 18px;
            left: 18px;
            background: #ffffff;
            color: #dc2626;
            border: 2px solid #ef4444;
            border-radius: 50px;
            padding: 8px 16px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.15);
            transition: all 0.2s ease;
            z-index: 99;
        }

        .floating-back:hover {
            background: #ef4444;
            color: #ffffff;
            transform: scale(1.05);
        }

        /* ==========================================================================
           WADAH KARTU (CARD CONTAINER 595 x 882)
           ========================================================================== */
        .card-container {
            width: 100%;
            max-width: var(--card-max-width);
            aspect-ratio: var(--card-aspect-ratio);
            
            /* Gambar kartu asli sebagai background */
            background-image: url('assets/card.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 100% 100%;
            background-color: #ffffff;

            position: relative;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 
                0 22px 45px rgba(0, 0, 0, 0.14),
                0 6px 14px rgba(220, 38, 38, 0.08),
                0 0 0 5px #ffffff,
                0 0 0 8px rgba(245, 158, 11, 0.35);
        }

        /* ==========================================================================
           ELEMEN LINGKARAN ATAS (POKEBALL & AR)
           ========================================================================== */
        .pokeball-center {
            position: absolute;
            top: var(--circle-top);
            left: var(--circle-left);
            width: var(--circle-size);
            aspect-ratio: 1 / 1;
            transform: translate(-50%, -50%);
            background-image: url('assets/pokeball.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            z-index: 2;
            pointer-events: none;
            filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.25));
            animation: bouncePulse 2.4s infinite ease-in-out;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        /* Efek Glow di Belakang Bola */
        .pokeball-glow {
            position: absolute;
            top: var(--circle-top);
            left: var(--circle-left);
            width: calc(var(--circle-size) * 1.05);
            aspect-ratio: 1 / 1;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(254, 240, 138, 0.8) 0%, rgba(245, 158, 11, 0.3) 50%, transparent 70%);
            z-index: 1;
            pointer-events: none;
            animation: glowAnim 2s infinite alternate ease-in-out;
        }

        /* Tombol Lingkaran Bola (bisa diklik langsung juga) */
        .ar-circle-trigger {
            position: absolute;
            top: var(--circle-top);
            left: var(--circle-left);
            width: var(--circle-size);
            aspect-ratio: 1 / 1;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: transparent;
            border: none;
            cursor: pointer;
            z-index: 3;
            outline: none;
        }

        /* Efek Flash Putih */
        .flash {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, #ffffff 40%, rgba(0, 240, 255, 0.6) 80%);
            opacity: 0;
            pointer-events: none;
            z-index: 10;
            transition: opacity 0.35s ease;
        }

        /* 3D Model Viewer AR */
        model-viewer {
            position: absolute;
            top: var(--circle-top);
            left: var(--circle-left);
            width: calc(var(--circle-size) * 1.08);
            aspect-ratio: 1 / 1;
            transform: translate(-50%, -50%);
            display: none;
            z-index: 4;
            background-color: transparent;
            outline: none;
        }

        /* ==========================================================================
           KOTAK TEKS BAWAH (UNTUK ANDA MODIFIKASI ISI & STATS)
           ========================================================================== */
        .card-bottom-box {
            position: absolute;
            top: var(--box-top);
            left: var(--box-left);
            width: var(--box-width);
            height: var(--box-height);
            transform: translateX(-50%);
            
            /* Latar belakang transparan agar tekstur kotak kartu tetap terlihat */
            background: rgba(7, 11, 20, 0.45);
            backdrop-filter: blur(2px);
            border-radius: 12px;
            padding: 8px 12px 10px;
            
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            z-index: 5;
            color: #ffffff;
            box-shadow: inset 0 0 12px rgba(0, 240, 255, 0.15);
        }

        /* 1. Judul Karakter (Bisa Dimodif) */
        .char-title {
            font-family: 'Orbitron', 'Fredoka', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 0 8px rgba(0, 240, 255, 0.8), 0 2px 4px rgba(0, 0, 0, 0.6);
            margin-top: 2px;
        }

        /* 2. Badge Status / Tipe Elemen (Bisa Dimodif) */
        .char-stats {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
            margin: 2px 0;
        }

        .stat-pill {
            background: rgba(0, 240, 255, 0.12);
            border: 1px solid rgba(0, 240, 255, 0.5);
            color: var(--neon-blue);
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            text-shadow: 0 0 4px rgba(0, 240, 255, 0.5);
        }

        /* 3. Deskripsi Singkat Karakter (Bisa Dimodif) */
        .char-desc {
            font-size: 0.72rem;
            line-height: 1.25;
            color: #d1d5db;
            font-weight: 600;
            margin: 2px 0 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ==========================================================================
           TOMBOL 3D NEON BLUE LIGHT & BLACK UNTUK LIHAT AR
           ========================================================================== */
        .btn-neon-3d {
            width: 100%;
            padding: 9px 14px;
            background: var(--neon-black-bg);
            border: 2px solid var(--neon-blue);
            border-radius: 12px;
            cursor: pointer;
            position: relative;
            outline: none;

            /* Efek 3D Timbal-Balik (Bevel Bawah) & Kilauan Cahaya Neon */
            box-shadow: 
                0 4px 0 var(--neon-blue-dark),
                0 0 15px var(--neon-blue-glow),
                inset 0 0 8px rgba(0, 240, 255, 0.25);
            
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease-in-out;
            -webkit-tap-highlight-color: transparent;
        }

        /* Teks & Ikon di Dalam Tombol */
        .btn-neon-3d .btn-text {
            font-family: 'Orbitron', 'Fredoka', sans-serif;
            color: var(--neon-blue);
            font-weight: 800;
            font-size: 0.86rem;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            text-shadow: 
                0 0 6px var(--neon-blue),
                0 0 14px var(--neon-blue-glow);
        }

        .btn-neon-3d .btn-icon {
            font-size: 1rem;
            filter: drop-shadow(0 0 6px var(--neon-blue));
            animation: pulseIcon 1.5s infinite alternate;
        }

        /* Efek Hover 3D Neon */
        .btn-neon-3d:hover {
            background: #0b1120;
            box-shadow: 
                0 5px 0 var(--neon-blue-dark),
                0 0 24px rgba(0, 240, 255, 0.85),
                inset 0 0 14px rgba(0, 240, 255, 0.4);
            transform: translateY(-1px);
        }

        /* Efek Tekan 3D (Push-Down Effect) */
        .btn-neon-3d:active {
            transform: translateY(3px);
            box-shadow: 
                0 1px 0 var(--neon-blue-dark),
                0 0 10px var(--neon-blue-glow),
                inset 0 0 10px rgba(0, 240, 255, 0.4);
        }

        /* ==========================================================================
           KEYFRAME ANIMASI
           ========================================================================== */
        @keyframes bouncePulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.05); }
        }

        @keyframes glowAnim {
            0% { opacity: 0.4; transform: translate(-50%, -50%) scale(0.96); }
            100% { opacity: 0.85; transform: translate(-50%, -50%) scale(1.06); }
        }

        @keyframes pulseIcon {
            0% { transform: scale(0.95); opacity: 0.8; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        /* Responsif untuk Layar Sangat Kecil */
        @media (max-width: 380px) {
            .char-title { font-size: 0.95rem; }
            .char-desc { font-size: 0.68rem; }
            .btn-neon-3d .btn-text { font-size: 0.78rem; }
            .btn-neon-3d { padding: 7px 10px; }
        }
    </style>
</head>

<body>

    <!-- Tombol Mengambang Santai di Pojok Layar untuk Kembali ke Menu -->
    <a href="index.php" class="floating-back">
        <span>⬅</span> Menu
    </a>

    <!-- Wadah Kartu Ukuran Proporsional 595 x 882 -->
    <div class="card-container" id="cardBg">
        
        <!-- Efek Kilau Putih saat AR Terbuka -->
        <div class="flash" id="flashEffect"></div>

        <!-- Glow di Belakang Pokeball -->
        <div class="pokeball-glow" id="pokeGlow"></div>

        <!-- GAMBAR LINGKARAN POKEBALL -->
        <div class="pokeball-center" id="pokeCenter"></div>

        <!-- Area Klik Transparan pada Bola (Opsional bisa diklik juga) -->
        <button class="ar-circle-trigger" onclick="toggleAR()" aria-label="Buka AR Karakter"></button>

        <!-- 3D Model Viewer AR -->
        <model-viewer 
            id="viewer" 
            src="assets/1/1.glb" 
            ar 
            ar-modes="webxr scene-viewer quick-look" 
            camera-controls 
            auto-rotate 
            rotation-per-second="30deg"
            shadow-intensity="1"
            alt="Model 3D Karakter 1">
        </model-viewer>

        <!-- =====================================================================
             KOTAK TEKS BAWAH (SILAKAN MODIFIKASI TEKS DI BAWAH INI)
             ===================================================================== -->
        <div class="card-bottom-box">
            
            <!-- 1. NAMA / JUDUL KARAKTER -->
            <h2 class="char-title">KARAKTER 1</h2>

            <!-- 2. STATS / TIPE KARAKTER -->
            <div class="char-stats">
                <span class="stat-pill">⚡ Listrik</span>
                <span class="stat-pill">⭐ Bintang 5</span>
                <span class="stat-pill">ATK 95</span>
            </div>

            <!-- 3. DESKRIPSI SINGKAT (Bisa Anda Ganti dengan Teks Apapun) -->
            <p class="char-desc">
                Karakter legendaris berelemen petir. Ketuk tombol di bawah untuk memunculkan model 3D & Augmented Reality!
            </p>

            <!-- 4. TOMBOL 3D NEON BLUE LIGHT & BLACK UNTUK LIHAT AR -->
            <button class="btn-neon-3d" id="btnNeonAR" onclick="toggleAR()">
                <span class="btn-icon" id="neonIcon">⚡</span>
                <span class="btn-text" id="neonText">LIHAT AR 3D</span>
            </button>

        </div>

    </div>

    <script>
        let isARActive = false;

        function toggleAR() {
            const viewer = document.getElementById('viewer');
            const pokeCenter = document.getElementById('pokeCenter');
            const pokeGlow = document.getElementById('pokeGlow');
            const flash = document.getElementById('flashEffect');
            const neonText = document.getElementById('neonText');
            const neonIcon = document.getElementById('neonIcon');

            flash.style.opacity = '1';

            setTimeout(() => {
                if (!isARActive) {
                    // Buka AR / 3D Model
                    pokeCenter.style.display = 'none';
                    pokeGlow.style.display = 'none';
                    viewer.style.display = 'block';
                    
                    // Ubah teks tombol neon menjadi opsi Tutup / Kamera
                    neonText.innerText = 'TUTUP 3D';
                    neonIcon.innerText = '🔄';
                    isARActive = true;

                    // Jika dibuka di browser HP, otomatis siap untuk kamera AR
                    if (viewer.canActivateAR) {
                        neonText.innerText = 'MODE KAMERA AR';
                        neonIcon.innerText = '📱';
                    }
                } else {
                    // Jika di HP dalam mode kamera, jalankan AR kamera
                    if (viewer.canActivateAR && neonText.innerText === 'MODE KAMERA AR') {
                        viewer.activateAR();
                        return;
                    }
                    // Tutup dan kembalikan ke bola
                    viewer.style.display = 'none';
                    pokeCenter.style.display = 'block';
                    pokeGlow.style.display = 'block';
                    neonText.innerText = 'LIHAT AR 3D';
                    neonIcon.innerText = '⚡';
                    isARActive = false;
                }
                flash.style.opacity = '0';
            }, 300);
        }
    </script>
</body>

</html>
