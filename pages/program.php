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
$deskripsi_web = $deskripsi_web ?? '';
$keyword_web   = $keyword_web   ?? '';
$admin_web     = $admin_web     ?? 'Admin';

/* ========= Daftar 4 foto (ubah sesuai file Anda) ========= */
$rel_paths = [
  ['path' => 'images/reguler.png', 'caption' => 'Kelas Reguler IPA & IPS'],
  ['path' => 'images/ipsunggulan.png', 'caption' => 'Kelas IPS Unggulan'],
  ['path' => 'images/ipaunggulan.png', 'caption' => 'Kelas IPA Unggulan'],
  ['path' => 'images/8.png', 'caption' => 'Kelas Akselerasi'],
];

/* Helper: encode URL + fallback bila file tak ada */
function safe_image_url(string $rel_path): string {
  $encoded = implode('/', array_map('rawurlencode', explode('/', $rel_path)));
  $file = APP_ROOT . '/' . $rel_path;
  if (!file_exists($file)) {
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
    'thumb'   => $url,             // pakai file yang sama untuk thumbnail
    'caption' => $it['caption'] ?: 'Gambar',
  ];
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

  /* Kartu poster (1 baris 1 kartu, full width) */
  .poster-card{
    background:#f8fffb;
    border:1px solid #cde7dd;
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .poster-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  /* Biarkan poster tampil utuh (tanpa crop) */
  .poster-img{
    width:100%;
    height:auto;
    display:block;
  }

  .poster-caption{ padding:.75rem 1rem; color:#173127; font-weight:600; font-size:1rem }
  .poster-actions{ padding:0 1rem 1rem 1rem }
  .poster-actions a{ text-decoration:none }
  .stack-gap{ row-gap:1rem; } /* jarak antar kartu */
</style>
</head>
<body>

<?php include APP_ROOT . "/lib/header.php"; ?>

<section class="page-head py-4">
  <div class="container container-narrow">
    <h1 class="mb-1">Kurikulum / Struktur SMAI PB Soedirman 2</h1>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">
    <div class="d-grid stack-gap">

      <?php foreach ($items as $img): ?>
        <article class="poster-card">
          <!-- Klik untuk perbesar -->
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
