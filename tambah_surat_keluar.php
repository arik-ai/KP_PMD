<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fungsi untuk generate nomor surat
function generateNoSurat($conn, $tanggal_surat, $kodeSurat) {
    $kodeDinas = "432.312";
    $tahunSurat = date("Y", strtotime($tanggal_surat));

    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no_surat, '/', 2), '/', -1) AS UNSIGNED)) AS max_no 
              FROM surat_keluar WHERE YEAR(tanggal_surat) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tahunSurat);
    $stmt->execute();
    $result = $stmt->get_result();
    $maxNo = $result->fetch_assoc()['max_no'];

    $nextNo = $maxNo ? $maxNo + 1 : 1;
    $noUrut = str_pad($nextNo, 3, "0", STR_PAD_LEFT);

    return "$kodeSurat/$noUrut/$kodeDinas/$tahunSurat";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_surat = mysqli_real_escape_string($conn, $_POST['kode_surat']);
    $perihal_surat = mysqli_real_escape_string($conn, $_POST['perihal_surat']);
    $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $agenda_keluar = !empty($_POST['agenda_keluar']) ? mysqli_real_escape_string($conn, $_POST['agenda_keluar']) : NULL;
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $id_sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);
    $user_input_keluar = $_SESSION['id'];

    // Validasi apakah user_input_keluar ada di tabel users
    $cek_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $cek_user->bind_param("i", $user_input_keluar);
    $cek_user->execute();
    $cek_result = $cek_user->get_result();

    if ($cek_result->num_rows == 0) {
        die("ID user tidak valid atau tidak ditemukan di tabel users.");
    }

    $querySifat = "SELECT nama_sifat_surat FROM sifat_surat WHERE id_sifat = ?";
    $stmtSifat = $conn->prepare($querySifat);
    $stmtSifat->bind_param("i", $id_sifat_surat);
    $stmtSifat->execute();
    $resultSifat = $stmtSifat->get_result();
    $rowSifat = $resultSifat->fetch_assoc();
    $nama_sifat_surat = $rowSifat['nama_sifat_surat'];

    $no_surat = generateNoSurat($conn, $tanggal_surat, $kode_surat);

    $query = "INSERT INTO surat_keluar (no_surat, tanggal_surat, agenda_keluar, perihal_surat, alamat, penerima, nama_sifat_surat, user_input_keluar) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssi", $no_surat, $tanggal_surat, $agenda_keluar, $perihal_surat, $alamat, $penerima, $nama_sifat_surat, $user_input_keluar);

    if ($stmt->execute()) {
        header("Location: detail_surat_keluar.php?id=" . urlencode($stmt->insert_id));
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

$sifat_surat_query = "SELECT * FROM sifat_surat";
$sifat_surat_result = $conn->query($sifat_surat_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tambah Surat Keluar</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="sidebar">
    <div class="logo"><img src="logo.png" alt="Logo" /></div>
    <ul class="sidebar-menu">
        <li><a href="index.php"><span class="icon">🏠</span> Dashboard</a></li>
        <li><a href="surat_masuk.php"><span class="icon">📂</span> Data Surat Masuk</a></li>
        <li><a href="surat_keluar.php" class="active"><span class="icon">📤</span> Data Surat Keluar</a></li>
        <li><a href="surat_perjanjian_kontrak.php"><span class="icon">📜</span> Surat Perjanjian Kontrak</a></li>
        <li><a href="surat_keputusan.php"><span class="icon">📋</span> Surat Keputusan</a></li>
        <li><a href="surat_tugas.php"><span class="icon">📄</span> Surat Tugas</a></li>
        <li><a href="arsip.php"><span class="icon">📚</span> Arsip Surat</a></li>
        <li><a href="laporan.php"><span class="icon">📊</span> Laporan</a></li>
        <li><a href="data_master.php"><span class="icon">⚙️</span> Data Master</a></li>
        <li><a href="logout.php"><span class="icon">🔒</span> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h2>Administrasi</h2>
        <div class="profile">
            <span><?= htmlspecialchars($_SESSION['role']); ?></span>
            <div class="profile-icon">👤</div>
        </div>
    </div>

    <div class="container">
        <h2>Tambah Surat Keluar</h2>
        <form action="" method="post" class="form-container">
            <div class="form-row">
                <div class="form-group">
                    <label for="kode_surat">Kode Surat</label>
                    <input type="text" id="kode_surat" name="kode_surat" placeholder="Ketik kode atau keterangan" required />
                </div>
                <div class="form-group">
                    <label for="perihal_surat">Perihal Surat</label>
                    <input type="text" id="perihal_surat" name="perihal_surat" placeholder="Input perihal surat" required />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal_surat">Tanggal Surat</label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" required />
                </div>
                <div class="form-group">
                    <label for="agenda_keluar">Agenda Surat</label>
                    <input type="date" id="agenda_keluar" name="agenda_keluar" />
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat </label>
                    <input type="text" id="alamat" name="alamat" placeholder="Input alamat" required />
                </div>
                <div class="form-group">
                    <label for="penerima">Penerima</label>
                    <input type="text" id="penerima" name="penerima" placeholder="Input penerima" required />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sifat_surat">Sifat Surat</label>
                    <select id="sifat_surat" name="sifat_surat" required>
                        <option value="">--Pilih Sifat--</option>
                        <?php while ($row = $sifat_surat_result->fetch_assoc()) : ?>
                            <option value="<?= htmlspecialchars($row['id_sifat']) ?>"><?= htmlspecialchars($row['nama_sifat_surat']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <button type="submit" class="btn btn-primary">Tambah Surat</button>
                <a href="surat_keluar.php" class="btn btn-secondary btn-equal">Batal</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>https://dpmd.pamekasankab.go.id/</p>
</footer>

<script>
    const kodeSuratList = [
        { kode: "000", keterangan: "UMUM" },
        { kode: "010", keterangan: "Urusan Dalam" },
        { kode: "020", keterangan: "Peralatan" },
        { kode: "030", keterangan: "Kekayaan Daerah" },
        { kode: "040", keterangan: "Perpus/Dokumen/Kearsipan/Sandi" },
        { kode: "050", keterangan: "Perencanaan" },
        { kode: "060", keterangan: "Organisasi/Ketatalaksanaan" },
        { kode: "070", keterangan: "Penelitian" },
        { kode: "080", keterangan: "Konferensi" },
        { kode: "090", keterangan: "Perjalanan Dinas" },
        { kode: "100", keterangan: "PEMERINTAHAN" },
        { kode: "110", keterangan: "Pemerintah Pusat" },
        { kode: "120", keterangan: "Pemerintah Provinsi" },
        { kode: "130", keterangan: "Pemerintah Kabupaten/Kota" },
        { kode: "140", keterangan: "Pemerintah Desa" },
        { kode: "150", keterangan: "Legislatif MPR/DPR" },
        { kode: "160", keterangan: "DPRD Provinsi" },
        { kode: "170", keterangan: "DPRD Kabupaten" },
        { kode: "180", keterangan: "Hukum" },
        { kode: "190", keterangan: "Hubungan Luar Negeri" },
        { kode: "200", keterangan: "POLITIK" },
        { kode: "210", keterangan: "Kepartaian" },
        { kode: "220", keterangan: "Organisasi Kemasyarakatan" },
        { kode: "230", keterangan: "Organisasi Profesi" },
        { kode: "240", keterangan: "Organisasi Pemuda" },
        { kode: "250", keterangan: "Organisasi Buruh, Tani, Nelayan & Angkutan" },
        { kode: "260", keterangan: "Organisasi Wanita" },
        { kode: "270", keterangan: "Pemilihan Umum" },
        { kode: "280", keterangan: "Pelanggaran Pemilu" },
        { kode: "290", keterangan: "-" },
        { kode: "300", keterangan: "KEAMANAN/KETERTIBAN" },
        { kode: "310", keterangan: "Pertahanan" },
        { kode: "320", keterangan: "Kemiliteran" },
        { kode: "330", keterangan: "Keamanan" },
        { kode: "340", keterangan: "Perlindungan Masyarakat" },
        { kode: "350", keterangan: "Kejahatan" },
        { kode: "360", keterangan: "Bencana" },
        { kode: "370", keterangan: "Kecelakaan" },
        { kode: "380", keterangan: "-" },
        { kode: "390", keterangan: "-" },
        { kode: "400", keterangan: "KESEJAHTERAAN RAKYAT" },
        { kode: "410", keterangan: "Pembangunan Desa" },
        { kode: "420", keterangan: "Pendidikan" },
        { kode: "430", keterangan: "Kebudayaan" },
        { kode: "440", keterangan: "Kesehatan" },
        { kode: "450", keterangan: "Agama" },
        { kode: "460", keterangan: "Sosial" },
        { kode: "470", keterangan: "Kependudukan" },
        { kode: "480", keterangan: "Media Massa" },
        { kode: "490", keterangan: "-" },
        { kode: "500", keterangan: "PEREKONOMIAN" },
        { kode: "510", keterangan: "Perdagangan" },
        { kode: "520", keterangan: "Pertanian" },
        { kode: "530", keterangan: "Perindustrian" },
        { kode: "540", keterangan: "Pertambangan/Kesamudraan" },
        { kode: "550", keterangan: "Perhubungan" },
        { kode: "560", keterangan: "Tenaga Kerja" },
        { kode: "570", keterangan: "Permodalan" },
        { kode: "580", keterangan: "Perbankan/Moneter" },
        { kode: "590", keterangan: "Agraria" },
        { kode: "600", keterangan: "PEKERJAAN UMUM DAN KETENAGAAN" },
        { kode: "610", keterangan: "Pengairan" },
        { kode: "620", keterangan: "Jalan" },
        { kode: "630", keterangan: "Jembatan" },
        { kode: "640", keterangan: "Bangunan" },
        { kode: "650", keterangan: "Tata Kota" },
        { kode: "660", keterangan: "Tata Lingkungan" },
        { kode: "670", keterangan: "Ketenagaan" },
        { kode: "680", keterangan: "Peralatan" },
        { kode: "690", keterangan: "Air Minum" },
        { kode: "700", keterangan: "PENGAWASAN" },
        { kode: "710", keterangan: "Bidang Pemerintahan" },
        { kode: "720", keterangan: "Bidang Politik" },
        { kode: "730", keterangan: "Bidang Keamanan/Ketertiban" },
        { kode: "740", keterangan: "Bidang Kesejahteraan Rakyat" },
        { kode: "750", keterangan: "Bidang Perekonomian" },
        { kode: "760", keterangan: "Bidang Pekerjaan Umum" },
        { kode: "770", keterangan: "-" },
        { kode: "780", keterangan: "Bidang Kepegawaian" },
        { kode: "790", keterangan: "Bidang Pekerjaan Umum" },
        { kode: "800", keterangan: "KEPEGAWAIAN" },
        { kode: "810", keterangan: "Pengadaan" },
        { kode: "820", keterangan: "Mutasi" },
        { kode: "830", keterangan: "Kedudukan" },
        { kode: "840", keterangan: "Kesejahteraan Pegawai" },
        { kode: "850", keterangan: "Cuti" },
        { kode: "860", keterangan: "Penilaian" },
        { kode: "870", keterangan: "Tata Usaha Kepegawaian" },
        { kode: "880", keterangan: "Pemberhentian" },
        { kode: "890", keterangan: "Pendidikan Pegawai" },
        { kode: "900", keterangan: "KEUANGAN" },
        { kode: "910", keterangan: "Anggaran" },
        { kode: "920", keterangan: "Otoritas/SKO" },
        { kode: "930", keterangan: "Verifikasi" },
        { kode: "940", keterangan: "Akuntansi" },
        { kode: "950", keterangan: "Perbendaharaan" },
        { kode: "960", keterangan: "Pembinaan Kebendaharaan" },
        { kode: "970", keterangan: "Pendapatan" },
        { kode: "980", keterangan: "-" },
        { kode: "990", keterangan: "Pembelajaran" },
        { kode: "030", keterangan: "KEKAYAAN DAERAH" },
        { kode: "031", keterangan: "Sumber Daya Alam" },
        { kode: "032", keterangan: "Aset Daerah" },
        { kode: "040", keterangan: "PERPUST./DOK./KEARSIPAN/SANDI" },
        { kode: "041", keterangan: "Perpustakaan" },
        { kode: "042", keterangan: "Dokumentasi" },
        { kode: "045", keterangan: "Kearsipan" },
        { kode: "046", keterangan: "Sandi" },
        { kode: "080", keterangan: "KONFERENSI" },
        { kode: "081", keterangan: "Gubernur" },
        { kode: "082", keterangan: "Bupati/Walikota" },
        { kode: "083", keterangan: "Komponen, Eselon Lainnya" },
        { kode: "084", keterangan: "Instansi Lainnya" },
        { kode: "085", keterangan: "Internasional di Dalam Negeri" },
        { kode: "086", keterangan: "Internasional di Luar Negeri" },
        { kode: "090", keterangan: "PERJALANAN DINAS" },
        { kode: "091", keterangan: "Perjalanan Pres./Wkl.Pres. ke Daerah" },
        { kode: "092", keterangan: "Perjalanan Menteri ke Daerah" },
        { kode: "093", keterangan: "Perjalanan Pejabat Daerah" },
        { kode: "094", keterangan: "Perjalanan Pegawai" },
        { kode: "095", keterangan: "Perjalanan Tamu Asing ke Daerah" },
        { kode: "096", keterangan: "Perjalanan Pres./Wkl. Pres. ke Luar Negeri" },
        { kode: "097", keterangan: "Perjalanan Menteri ke Luar Negeri" },
        { kode: "098", keterangan: "Perjalanan Pejabat Tinggi ke L. Negeri" },
        { kode: "099", keterangan: "Perjalanan Pegawai ke Luar Negeri" },
        { kode: "100", keterangan: "PEMERINTAHAN" },
        { kode: "101", keterangan: "GDN (Gerakan Disiplin Nasional)" },
        { kode: "110", keterangan: "PEMERINTAHAN PUSAT" },
        { kode: "111", keterangan: "Presiden" },
        { kode: "112", keterangan: "Wakil Presiden" },
        { kode: "113", keterangan: "Susunan Kabinet" },
        { kode: "114", keterangan: "Departemen Dalam Negeri" },
        { kode: "115", keterangan: "Departemen Lainnya" },
        { kode: "116", keterangan: "Lembaga Tinggi Negara" },
        { kode: "117", keterangan: "Lembaga Pemerintah Non Departemen" },
        { kode: "118", keterangan: "Otonomi/Desentralisasi/Dekonsentrasi" },
        { kode: "119", keterangan: "Kerjasama Antar Departemen" },
        { kode: "150", keterangan: "LEGISLATIF MPR/DPR" },
        { kode: "151", keterangan: "Keanggotaan DPR" },
        { kode: "152", keterangan: "Persidangan" },
        { kode: "153", keterangan: "Kesejahteraan" },
        { kode: "154", keterangan: "Hak" },
        { kode: "155", keterangan: "Keanggotaan DPR" },
        { kode: "156", keterangan: "Persidangan DPR" },
        { kode: "157", keterangan: "Kesejahteraan" },
        { kode: "158", keterangan: "Jawaban Pemerintahan" },
        { kode: "159", keterangan: "Hak" },
        { kode: "160", keterangan: "DPRD PROVINSI" },
        { kode: "161", keterangan: "Keanggotaan" },
        { kode: "162", keterangan: "Persidangan Kesejahteraan" },
        { kode: "163", keterangan: "Hak" },
        { kode: "164", keterangan: "Sekretaris DPRD Provinsi" }
    ];

    const inputKode = document.getElementById('kode_surat');
    const datalist = document.createElement('datalist');
    datalist.id = 'kodeList';
    document.body.appendChild(datalist);
    inputKode.setAttribute('list', 'kodeList');

    function updateDatalist(search = '') {
        datalist.innerHTML = '';
        kodeSuratList.forEach(item => {
            const label = `${item.kode} - ${item.keterangan}`;
            if (label.toLowerCase().includes(search.toLowerCase())) {
                const option = document.createElement('option');
                option.value = label;
                datalist.appendChild(option);
            }
        });
    }

    inputKode.addEventListener('input', () => {
        updateDatalist(inputKode.value);
    });

    document.querySelector('form').addEventListener('submit', function () {
        const userInput = inputKode.value.trim().toLowerCase();
        const match = kodeSuratList.find(item =>
            `${item.kode} - ${item.keterangan}`.toLowerCase() === userInput ||
            item.kode === userInput ||
            item.keterangan.toLowerCase() === userInput
        );
        if (match) {
            inputKode.value = match.kode;
        }
    });

    updateDatalist();
</script>
</body>
</html>
