<?php
/************************************************************
 * MPLS — Informasi (pages/struktur.php) — TANPA GAMBAR
 * Sumber data: tabel isimenu (field: id, category, descripsi)
 * Filter: category = 'informasi'
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

/* ---------------- Query DB ---------------- */
$rows = [];
if ($stmt = $koneksi->prepare("SELECT id, category, descripsi FROM isimenu WHERE category = ? ORDER BY id ASC")) {
  $cat = 'seragam';
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

  .info-body{ padding:1rem 1.1rem; color:#173127 }
  .info-head{
    padding:.85rem 1.1rem; display:flex; align-items:center; justify-content:space-between;
    border-top:1px dashed var(--line);
    background:#fff;
  }
  .badge-id{ background:var(--accent); color:#fff; font-weight:700 }
  .muted{ color:#436257 }
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
    <h1 class="mb-1">MPLS~PAIS — Seragam</h1>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">
    <?php if (empty($rows)): ?>
      <div class="alert alert-warning">Belum ada data pada kategori <strong>informasi</strong>.</div>
    <?php else: ?>
      <div class="d-grid gap-3">
        <?php foreach ($rows as $r):
          $desc = htmlspecialchars($r['descripsi'], ENT_QUOTES);
          $cat  = htmlspecialchars($r['category'], ENT_QUOTES);
        ?>
        <article class="info-card">
          <div class="info-body">
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
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
