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
$deskripsi_web = $deskripsi_web ?? 'Informasi Pendaftaran dan Ekstrakurikuler SMA Islam PB Soedirman 2 Bekasi'; // Diperbarui
$keyword_web   = $keyword_web   ?? 'ppdb, ekstrakurikuler, sekolah bekasi'; // Diperbarui
$admin_web     = $admin_web     ?? 'Admin';

/* ========= Daftar 4 foto (ubah sesuai file Anda) ========= */
$rel_paths = [
  ['path' => 'img/pendaftaran.png', 'caption' => 'Alur Pedaftaran SMAI PB Soedirman 2 Bekasi'],
  ['path' => 'img/infodaftar.png', 'caption' => 'Jadwal Pendaftaran'],
  ['path' => 'img/infotest.png', 'caption' => 'Jadwal Test Masuk SMAI PB Soedirman 2 Bekasi'],
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
  
  /* --- NEW STYLE untuk PPDB INFO (agar tidak jelek) --- */
  .ppdb-info {
    background: var(--soft); /* Background lembut */
    padding: 1.5rem;
    border: 1px solid var(--line);
    border-radius: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,.05);
    margin-bottom: 2rem;
  }
  .ppdb-info h2 {
    color: var(--accent) !important;
    font-weight: 700;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--line);
  }
  .ppdb-info p {
    font-size: 1.05rem;
    line-height: 1.7;
    color: var(--ink);
    margin-bottom: 1rem;
  }
  .ppdb-info strong {
    color: var(--ink);
  }
  .ppdb-info a {
    font-weight: 700;
    color: var(--accent); /* Link menonjol */
    text-decoration: none;
  }
  .ppdb-info a:hover {
    text-decoration: underline;
  }
  /* --------------------------------------------------- */

  /* Kartu poster (1 baris 1 kartu, full width) */
  .poster-card{
    background:#fff; /* Ubah ke putih agar kontras dengan soft background */
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .poster-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  /* Biarkan poster tampil utuh (tanpa crop) */
  .poster-img{
    width:100%;
    max-height: 500px; /* Batasi tinggi maksimum */
    object-fit: contain; /* Agar gambar tidak terpotong, tapi tampil utuh */
    display:block;
  }

  .poster-caption{ padding:.75rem 1rem; color:var(--ink); font-weight:600; font-size:1rem; text-align: center; }
  .stack-gap{ row-gap:1.5rem; } /* Jarak antar kartu dibuat sedikit lebih besar */
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

    <div class="ppdb-info">
      <h2 class="h4 mb-3">Pendaftaran Peserta Didik Baru (PPDB)</h2>
      <p>
        <strong>Segera daftarkan Putra/Putri Anda di SMA Islam PB Soedirman 2 Bekasi melalui Pendaftaran Online:</strong>
        <br>
        Klik link ini : 
        <a href="http://bit.ly/ppdbpangsoed2" target="_blank" rel="noopener noreferrer">
          http://bit.ly/ppdbpangsoed2
        </a>
      </p>

      <p class="mb-0">
        Contact Person : WA <strong>0821 2224 1232</strong>
        <br>
        Daftar Ulang kelas XI dan XII : Rp 600.000 belum termasuk uang kegiatan.
      </p>
    </div>

    <h2 class="h4 mb-3" style="color: var(--ink); font-weight: 700;">Gambar Terkait Pendaftaran Peserta Didik Baru</h2>

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
$whatsapp_number = '6281234567890'; 
$whatsapp_text = 'Halo Admin SMAI PB Soedirman 2, saya ingin bertanya tentang PPDB.';
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