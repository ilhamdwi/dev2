<?php
/************************************************************
 * MPLS — Informasi (pages/struktur.php)
 * Sumber data: tabel mpls (field: id, category, descripsi)
 * Filter: category = 'informasi'
 * Gambar: otomatis dari /img (atau /img/img) sesuai id/slug
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}

// Koneksi DB
require_once APP_ROOT . '/lib/db.php';

// Meta dasar (fallback)
$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? 'Informasi MPLS';
$keyword_web   = $keyword_web   ?? 'mpls, informasi';
$admin_web     = $admin_web     ?? 'Admin';

/* ---------------- Helper ---------------- */
function slugify_for_file($text) {
  $orig = $text ?? '';
  if (function_exists('iconv')) {
    $conv = @iconv('UTF-8', 'ASCII//TRANSLIT', $orig);
    if ($conv !== false) $orig = $conv;
  }
  $s = preg_replace('~[^\\pL\\d]+~u', '-', $orig);
  $s = trim($s, '-');
  $s = strtolower($s);
  $s = preg_replace('~[^-a-z0-9]+~', '', $s);
  return $s ?: 'img';
}

/** Cari file gambar di /img atau /img/img berdasar id/slug */
function find_image_for_row($id, $category, $descripsi) {
  $candidates = [];
  $exts = ['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'];

  // 1) Berdasar ID (paling kuat)
  foreach ($exts as $e) $candidates[] = "{$id}.{$e}";

  // 2) Berdasar slug descripsi
  $slugD = slugify_for_file($descripsi);
  foreach ($exts as $e) $candidates[] = "{$slugD}.{$e}";

  // 3) Berdasar slug category
  $slugC = slugify_for_file($category);
  foreach ($exts as $e) $candidates[] = "{$slugC}.{$e}";

  foreach ($candidates as $file) {
    $abs1 = APP_ROOT . "/img/" . $file;
    if (file_exists($abs1)) {
      return "img/" . rawurlencode($file);
    }
    $abs2 = APP_ROOT . "/img/img/" . $file;
    if (file_exists($abs2)) {
      return "img/img/" . rawurlencode($file);
    }
  }
  return "img/mpls.png";
}

/* ---------------- Query DB ---------------- */
$rows = [];
if ($stmt = $koneksi->prepare("SELECT id, category, descripsi FROM isimenu WHERE category = ? ORDER BY id ASC")) {
  $cat = 'informasi';
  $stmt->bind_param('s', $cat);
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $rows[] = [
        'id'        => (int)$r['id'],
        'category'  => (string)$r['category'],
        'descripsi' => (string)$r['descripsi'],
      ];
    }
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — MPLS: Informasi</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<!-- Base URL untuk path relatif -->
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
    --accent:#73c0a4;
  }
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid var(--line);
  }
  .page-head h1 { font-weight: 700; color: var(--ink); }

  .info-card{
    background: var(--soft);
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .info-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  /* TIDAK CROPPING – rasio asli */
    .info-media{
    width:100%;
    background:#f3f6f5;
    overflow:hidden;
    }
    .info-media img{
    display:block;
    width:100%;
    height:auto;        /* pakai rasio asli */
    object-fit: contain;/* jaga-jaga jika diberi height tetap */
    }


  .info-head{
    padding:.85rem 1rem; display:flex; align-items:center; justify-content:space-between;
    border-top:1px dashed var(--line);
    background:#fff;
  }
  .badge-id{ background:var(--accent); color:#fff; font-weight:700 }
  .desc{ padding:1rem 1rem .25rem 1rem; color:#173127 }
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
    <h1 class="mb-1">MPLS~PAIS — Informasi</h1>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">
    <?php if (empty($rows)): ?>
      <div class="alert alert-warning">Belum ada data pada kategori <strong>informasi</strong>.</div>
    <?php else: ?>
      <div class="d-grid gap-3">
        <?php foreach ($rows as $r): 
          $imgUrl = find_image_for_row($r['id'], $r['category'], $r['descripsi']);
          $imgFull = htmlspecialchars($imgUrl, ENT_QUOTES);
          $desc = htmlspecialchars($r['descripsi'], ENT_QUOTES);
          $cat  = htmlspecialchars($r['category'], ENT_QUOTES);
        ?>
        <article class="info-card">
          <a href="<?php echo $imgFull; ?>" class="glightbox" data-type="image" aria-label="Perbesar gambar">
            <div class="info-media">
              <img src="<?php echo $imgFull; ?>"
                   alt="Gambar MPLS #<?php echo (int)$r['id']; ?>"
                   loading="lazy"
                   onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';">
            </div>
          </a>
          <div class="desc">
            <?php echo nl2br($desc); ?>
          </div>
          <div class="info-head">
            <span class="badge badge-id">#<?php echo (int)$r['id']; ?></span>
            <small class="text-muted"><?php echo $cat; ?></small>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>GLightbox({ selector: '.glightbox' });</script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
