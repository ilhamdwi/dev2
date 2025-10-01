<?php
/************************************************************
 * STRUKTUR ORGANISASI — pages/struktur.php (1 Image + 1 YouTube inline)
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
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

/* ========= Konten: 1 Gambar & 1 Video YouTube =========
   - Ganti 'images/struktur.jpg' ke file gambar Anda
   - Ganti $yt_id dengan ID video YouTube (contoh: dQw4w9WgXcQ)
*/
$yt_id = 'JWrWNtcz43g';

/* Helpers */
function safe_image_url(string $rel_path): string {
  $encoded = implode('/', array_map('rawurlencode', explode('/', $rel_path)));
  $file = APP_ROOT . '/' . $rel_path;
  if (!file_exists($file)) {
    return MY_PATH . 'img/not-images.png';
  }
  return MY_PATH . $encoded;
}
function youtube_embed_url(string $id): string { return "https://www.youtube.com/embed/{$id}"; }
function youtube_thumb_url(string $id): string { return "https://img.youtube.com/vi/{$id}/hqdefault.jpg"; }

/* Siapkan items uniform */
$items = [
  [
    'type'    => 'image',
    'url'     => safe_image_url('/images/9(1).png'), // <— ganti file gambarnya
    'thumb'   => safe_image_url('/images/9(1).png'),
    'caption' => 'Kesiswaan SMAI PB Soedirman 2 Bekasi',
    'dataType'=> 'image',
  ],
  [
    'type'    => 'youtube',
    'url'     => youtube_embed_url($yt_id),  // pakai EMBED agar inline
    'thumb'   => youtube_thumb_url($yt_id),
    'caption' => 'Profil Sekolah (Video)',
    'dataType'=> 'video',
    'dataSource' => 'youtube',
  ],
];
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

<!-- CSS jika head.php tidak memuat -->
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

  .poster-card{
    background:#f8fffb;
    border:1px solid #cde7dd;
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .poster-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  .poster-wrap { position: relative; }
  .poster-img{
    width:100%;
    height:auto;
    display:block;
  }
  .poster-caption{ padding:.75rem 1rem; color:#173127; font-weight:600; font-size:1rem }

  /* Play overlay untuk video */
  .play-badge{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    pointer-events:none;
  }
  .play-badge .circle{
    width:64px; height:64px; border-radius:50%;
    background: rgba(23,49,39,.85);
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 10px 20px rgba(0,0,0,.25);
  }
  .play-badge i{ font-size:28px; color:#fff; margin-left:3px; }
  .stack-gap{ row-gap:1rem; display:grid; }
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

      <?php foreach ($items as $it): ?>
        <article class="poster-card">
          <a href="<?php echo htmlspecialchars($it['url'], ENT_QUOTES); ?>"
             class="glightbox"
             data-gallery="galeri-struktur"
             data-type="<?php echo htmlspecialchars($it['dataType'], ENT_QUOTES); ?>"
             <?php if (!empty($it['dataSource'])): ?>
               data-source="<?php echo htmlspecialchars($it['dataSource'], ENT_QUOTES); ?>"
             <?php endif; ?>
             data-title="<?php echo htmlspecialchars($it['caption'], ENT_QUOTES); ?>">
            <div class="poster-wrap">
              <img
                src="<?php echo htmlspecialchars($it['thumb'], ENT_QUOTES); ?>"
                alt="<?php echo htmlspecialchars($it['caption'], ENT_QUOTES); ?>"
                class="poster-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';"
              >
              <?php if ($it['dataType'] === 'video'): ?>
                <div class="play-badge">
                  <div class="circle"><i class="bi bi-play-fill"></i></div>
                </div>
              <?php endif; ?>
            </div>
          </a>
          <div class="poster-caption">
            <?php echo htmlspecialchars($it['caption'], ENT_QUOTES); ?>
          </div>
        </article>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<?php include APP_ROOT . "/lib/footer.php"; ?>

<!-- JS global -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js" defer></script>
<script>
  // Inisialisasi GLightbox setelah script dimuat
  document.addEventListener('DOMContentLoaded', function () {
    GLightbox({
      selector: '.glightbox',
      touchNavigation: true,
      autoplayVideos: true,
    });
  });
</script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
