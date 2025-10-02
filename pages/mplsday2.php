<?php
/************************************************************
 * STRUKTUR ORGANISASI — pages/struktur.php (4 posters stacked)
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

// Root filesystem project (.../dev2) agar include dari /pages aman
if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}

// Base URL situs
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}

// Koneksi DB & meta dasar
require_once APP_ROOT . '/lib/db.php';

// Fallback meta
$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? 'Informasi Pendaftaran dan Ekstrakurikuler SMA Islam PB Soedirman 2 Bekasi';
$keyword_web   = $keyword_web   ?? 'ppdb, ekstrakurikuler, sekolah bekasi';
$admin_web     = $admin_web     ?? 'Admin';

/* ========= Daftar 4 foto (ubah sesuai file Anda) ========= */
$rel_paths = [
  ['path' => 'img/seragam.png', 'caption' => 'Seragam Sekolah SMAI PB Soedirman 2 Bekasi'],
];

/* Helper: encode URL + fallback bila file tak ada */
function safe_image_url(string $rel_path): string {
  $encoded = implode('/', array_map('rawurlencode', explode('/', $rel_path)));
  $file = APP_ROOT . '/' . $rel_path;
  // Fallback ke not-images.png jika file tidak ada di server
  if (!file_exists($file) || is_dir($file)) {
    return MY_PATH . 'img/not-images.png';
  }
  return MY_PATH . $encoded;
}

/* Siapkan items */
$items = [];
foreach ($rel_paths as $it) {
  $url = safe_image_url($it['path']);
  $items[] = [
    'url'     => $url,
    'thumb'   => $url,       // pakai file yang sama untuk thumbnail
    'caption' => $it['caption'] ?: 'Gambar',
  ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — Info Sekolah</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<style>
  :root{
    --ink:#173127;
    --soft:#f8fffb;
    --line:#cde7dd;
    --accent:#73c0a4; /* Hijau */
  }
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid var(--line);
  }
  .page-head h1 { font-weight: 700; color: var(--ink); }
  
  /* --- NEW STYLE untuk MPLS INFO --- */
  .mpls-info { 
    background: var(--soft); 
    padding: 1.5rem;
    border: 1px solid var(--line);
    border-radius: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,.05);
    margin-bottom: 2rem;
  }
  .mpls-info h2 {
    color: var(--accent) !important;
    font-weight: 700;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--line);
  }
  .mpls-info p {
    font-size: 1.05rem;
    line-height: 1.7;
    color: var(--ink);
    margin-bottom: 1rem;
    white-space: pre-wrap; 
  }
  .mpls-info strong {
    color: var(--ink);
  }
  .mpls-info a {
    font-weight: 700;
    color: var(--accent); 
    text-decoration: none;
  }
  .mpls-info a:hover {
    text-decoration: underline;
  }
  .mpls-info ul {
    padding-left: 20px; 
    margin-bottom: 1rem;
  }
  /* --------------------------------------------------- */

  /* Kartu poster (1 baris 1 kartu, full width) */
  .poster-card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;

    /* Perbaikan Mobile: Hilangkan max-width dan margin: auto; di base style */
    max-width: none; 
    margin: 0;
  }
  .poster-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  /* Biarkan poster tampil utuh (tanpa crop) */
  .poster-img{
    width:100%;
    max-height: 500px;
    object-fit: contain; 
    display:block;
  }

  .poster-caption{ padding:.75rem 1rem; color:var(--ink); font-weight:600; font-size:1rem; text-align: center; }
  .stack-gap{ row-gap:1.5rem; } 

  /* =========== PERBAIKAN RESPONSIVITAS GAMBAR =========== */
  @media (min-width: 576px) {
    .stack-gap {
      display: grid; 
      /* Untuk mobile/tablet kecil, buat 1 kolom dengan lebar maksimum */
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
    .poster-card {
      max-width: 500px; /* Batasi lebar kartu di tablet kecil */
      margin: 0 auto; /* Pusatkan kartu */
    }
  }

  @media (min-width: 768px) {
    .stack-gap {
      /* 2 kolom untuk tablet ke atas */
      grid-template-columns: repeat(2, 1fr); 
    }
    .poster-card {
      max-width: none; /* Izinkan kartu melebar sesuai kolom grid */
      margin: 0; 
    }
  }

  @media (min-width: 992px) {
    .stack-gap {
      /* 3 kolom untuk desktop ke atas */
      grid-template-columns: repeat(3, 1fr); 
    }
  }
  #whatsapp-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 1050; /* Pastikan di atas elemen lain (seperti navbar) */
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background-color: #25d366; /* WhatsApp Green */
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s;
  }
  #whatsapp-btn:hover {
    background-color: #128c7e;
    transform: scale(1.05);
  }
  /* ====================================================== */
</style>
</head>
<body>

<?php include APP_ROOT . "/lib/header.php"; ?>

<section class="page-head py-4">
  <div class="container container-narrow">
    <h1 class="mb-1">Info Sekolah & Ekstrakurikuler</h1>
  </div>
</section>

<section class="py-4" style="background: #fdfdfd;"> <div class="container container-narrow">

  <div class="mpls-info">
    <p style="text-align: center; font-weight: 700; margin-bottom: 1rem;">Assalamualaikum Wr Wb</p>
    
    <p>
      Halo adik-adik peserta MPLS PAIS 2022. Selamat kalian sudah menjalani MPLS hari pertama. 
      Persiapkan diri kalian untuk MPLS PAIS 2022 hari kedua ya.... Hari kedua MPLS PAIS 2022 
      akan dilaksanakan pada hari <strong>Kamis, 21 Juli 2022</strong> mulai dari pukul <strong>06.45 sampai pukul 16.00</strong>.
      Berikut adalah susunan acara hari kedua MPLS PAIS 2022. Jangan lupa disimak ya....
    </p>

    <h2 class="h4">Jadwal Acara Hari Kedua: Kamis, 21 Juli 2022</h2>
    
    <p style="font-family: monospace; font-size: 1rem; line-height: 1.5;">
      06.45 – 07.15&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siswa Berkumpul di Sekolah
      <br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tadarus Al-Quran
      <br>
      07.15 -07.30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hafalan Surat dan Kultum
      <br>
      07.30 – 08.00&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shalat Dhuha
      <br>
      08.00 – 09.00&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Perkenalan ke Kelas-kelas
      <br>
      09.00 – 09.30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Makan Bersama dan Istirahat
      <br>
      09.30 – 10.30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Materi III : Pengenalan Teknologi dalam Proses Pembelajaran (Mr. Wahyu Indriyadi, S. Si)
      <br>
      10.30 – 10.40&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Istirahat Sejenak
      <br>
      10.40 – 11.45&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Materi IV : Pengenalan Lingkungan Sekolah (Mr. Febri Antono, S. Pd)
      <br>
      11.45 – 12.45&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ISHOMA
      <br>
      12.45 – 13.35&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Demo Ekskul Indoor
      <br>
      13.35 – 14.30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fun Game
      <br>
      14.30 – 15.00&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bercocok Tanam
      <br>
      15.00 – 15.30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sholat Ashar
      <br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pulang
    </p>
    
    <h2 class="h4 mt-4">Contact Person MPLS & Barang Bawaan</h2>

    <p style="margin-bottom: 0.5rem;"><strong>Contact Person MPLS :</strong></p>
    <p style="margin-bottom: 1rem;">
      Kakak Nazwa : <strong>0813-8087-2989</strong>
      <br>
      Mrs Yeyen : <strong>0877-5262-5925</strong>
    </p>

    <p style="margin-bottom: 0.5rem;"><strong>Barang Bawaan (Kamis, 21 Juli 2022):</strong></p>
    <ul>
      <li>name tag</li>
      <li>kalung rempah rempah</li>
      <li>baju olahrga</li>
      <li>snack buah tsabit dan minuman teh band</li>
      <li>Makan siang + air di thumbler</li>
      <li>kultum</li>
      <li>alat tulis</li>
      <li>perlengkapan sholat dan al quran</li>
    </ul>

    <p class="mt-4" style="color: red; font-weight: 700; border: 1px dashed red; padding: 10px;">
      NOTE: PESERTA DILARANG MEMBAWA ALAT KOSMETIK, HANDPHONE DAN ALAT ELEKTRONIK LAINYA SERTA BARANG-BARANG LAINYA YANG TIDAK DI PERLUKAN SELAMA MPLS PAIS 2022 BERLANGSUNG
    </p>
    
    <h2 class="h4 mt-4">Penugasan Malam Hari & Survey Sekolah</h2>

    <p style="margin-bottom: 0.5rem;"><strong>Penugasan Malam Hari (batas akhir hingga hari Rabu, 20 Juli 2022 Pukul 22:00 WIB)</strong></p>

    <p style="margin-bottom: 1rem;">
      Survey Sekolah : Silahkan isi quesioner survey pada link berikut ini
      <a href="https://docs.google.com/forms/d/e/1FAIpQLSetJcZuiyvOpjyhP0861nyOR69BkFj6uMHPQww4uvMN9n5_Tw/viewform" target="_blank" rel="noopener noreferrer">
        https://docs.google.com/forms/d/e/1FAIpQLSetJcZuiyvOpjyhP0861nyOR69BkFj6uMHPQww4uvMN9n5_Tw/viewform
      </a>
    </p>

    <p style="margin-bottom: 1rem;">
      Pemilihan kelas pada quesioner sesuai hari pertama Pra-MPLS. Yaitu <strong>X 1 dan X2 = X IPA Unggulan, X3 = X IPS Unggulan, X4 = X IPA, X5 = X IPS.</strong>
    </p>

    <p class="mb-0">
      Bagi siswa yang baru saja bergabung di MPLS~PAIS SMA Islam PB Soedirman 2 Bekasi, silahkan memilih kelas X IPS atau X IPA
    </p>
    
    <h2 class="h4 mt-4">Seragam Sekolah</h2>
    <p class="mb-0">Mari Perhatikan Seragam Sekolah berikut ini :</p>
  </div>
    <div class="d-grid stack-gap">
      <?php if (empty($items)): ?>
        <div class="alert alert-info">Belum ada data gambar yang dimuat.</div>
      <?php else: ?>
        <?php foreach ($items as $img): ?>
          <article class="poster-card">
            <a href="<?php echo htmlspecialchars($img['url'], ENT_QUOTES); ?>"
              class="glightbox"
              data-gallery="galeri-struktur"
              data-title="<?php echo htmlspecialchars($img['caption'], ENT_QUOTES); ?>">
              <img
                src="<?php echo htmlspecialchars($img['thumb'], ENT_QUOTES); ?>"
                alt="<?php echo htmlspecialchars($img['caption'], ENT_QUOTES); ?>"
                class="poster-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';"
              >
            </a>

            <div class="poster-caption">
              <?php echo htmlspecialchars($img['caption'], ENT_QUOTES); ?>
            </div>

          </article>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </div>
</section>
<?php
// GANTI DENGAN NOMOR WHATSAPP ASLI ADMIN (format: 628xxxx)
$whatsapp_number = '6282122241232'; 
$whatsapp_text = 'Assalamualaikum Admin SMAI PB Soedirman 2, saya ingin bertanya tentang PPDB.';
$whatsapp_link = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($whatsapp_text);
?>
<a id="whatsapp-btn" 
   href="<?php echo htmlspecialchars($whatsapp_link, ENT_QUOTES); ?>"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Hubungi kami via WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>

<?php include APP_ROOT . "/lib/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>GLightbox({ selector: '.glightbox' });</script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>