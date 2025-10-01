<?php
/************************************************************
 * STRUKTUR ORGANISASI — pages/struktur.php (image version)
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
$deskripsi_web = $deskripsi_web ?? '';
$keyword_web   = $keyword_web   ?? '';
$admin_web     = $admin_web     ?? 'Admin';

/* ========= Lokasi gambar struktur =========
   File fisik: C:\xampp\htdocs\dev2\images\Struktur organisasi sekolah (75 x 125 cm)_page-0001.jpg
   Relatif dari root web (/dev2): images/Struktur organisasi sekolah (75 x 125 cm)_page-0001.jpg
*/
$rel_path = 'images/VISI MISI 2025-2026_20250912_214953_0000_pages-to-jpg-0001(1).jpg';
$file_path = APP_ROOT . '/' . $rel_path;

/* Buat URL yang aman (encode setiap segmen agar spasi/kurung dll tidak jadi masalah) */
$encoded_url = MY_PATH . implode('/', array_map('rawurlencode', explode('/', $rel_path)));

/* Fallback jika file belum ada */
if (!file_exists($file_path)) {
  $encoded_url = MY_PATH . 'img/not-images.png';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — Struktur Organisasi</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<!-- Base untuk path relatif -->
<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<!-- Fallback CSS bila head.php tidak memuat -->
<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<style>
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid var(--line, #e8f2ec);
  }
  .page-head h1 { font-weight: 700; color: #173127; }

  .image-frame{
    background:#f8fffb;
    border:1px solid #cde7dd;
    border-radius:1rem;
    padding:1rem;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
  }
  .image-frame img{ width:100%; height:auto; display:block; border-radius:.75rem; }
  .image-actions a{ text-decoration:none }
</style>
</head>
<body>

<?php include APP_ROOT . "/lib/header.php"; ?>

<section class="page-head py-4">
  <div class="container container-narrow">
    <h1 class="mb-1">VISI & MISI</h1>
    <p class="text-muted mb-0">SMAI PB Soedirman 2 Bekasi</p>
  </div>
</section>

<section class="py-5">
  <div class="container container-narrow">
    <div class="row justify-content-center">
      <div class="col-xl-10">
        <div class="image-frame">
          <!-- Klik untuk perbesar -->
          <a href="<?php echo htmlspecialchars($encoded_url, ENT_QUOTES); ?>" class="glightbox" data-type="image" aria-label="Perbesar gambar struktur">
            <img
              src="<?php echo htmlspecialchars($encoded_url, ENT_QUOTES); ?>"
              alt="VISI MISI 2025-2026"
              loading="lazy"
              onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';"
            >
          </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 image-actions">
          <small class="text-muted">Klik gambar untuk memperbesar.</small>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-success btn-sm"
               href="<?php echo htmlspecialchars($encoded_url, ENT_QUOTES); ?>"
               download>
              <i class="bi bi-download me-1"></i>Download
            </a>
            <a class="btn btn-success btn-sm"
               href="<?php echo htmlspecialchars($encoded_url, ENT_QUOTES); ?>"
               target="_blank" rel="noopener">
              <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Tab Baru
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include APP_ROOT . "/lib/footer.php"; ?>

<!-- JS global -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>GLightbox({ selector: '.glightbox' });</script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
