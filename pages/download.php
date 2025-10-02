<?php
/************************************************************
 * MPLS — Download File (pages/struktur.php) — DARI TABLE download
 * Sumber data: tabel download (field: id_download, code, title, file_download, datetime)
 * TIDAK ADA FILTER BERDASARKAN CODE
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}
// Base path untuk file yang diunduh (Asumsi folder 'download' berada di root MY_PATH)
if (!defined('DOWNLOAD_PATH')) {
    define('DOWNLOAD_PATH', MY_PATH . 'download/');
}


// Koneksi DB
require_once APP_ROOT . '/lib/db.php';

// Meta dasar (fallback)
$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? 'Halaman Unduh Semua File Sekolah';
$keyword_web   = $keyword_web   ?? 'download, file, dokumen, sekolah';
$admin_web     = $admin_web     ?? 'Admin';

/* ---------------- Query DB ---------------- */
$rows = [];

// Query diubah: Menghapus WHERE clause untuk mengambil SEMUA data.
if ($stmt = $koneksi->prepare(
    "SELECT id_download, code, title, file_download, datetime 
     FROM download 
     ORDER BY datetime DESC" // Tetap diurutkan berdasarkan tanggal terbaru
)) {
  // $stmt->bind_param('s', $filter_code); // Baris ini dihapus karena filter sudah tidak ada
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $rows[] = [
        'id'            => (int)$r['id_download'],
        'code'          => (string)$r['code'], // Tetap ambil code untuk ditampilkan jika perlu
        'title'         => (string)$r['title'],
        'file_download' => (string)$r['file_download'],
        'datetime'      => (string)$r['datetime'],
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
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — Daftar Unduh File</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

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

  /* Ganti info-card menjadi download-card */
  .download-card{
    background: var(--soft);
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .download-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  .download-body{ padding:1rem 1.1rem; display: flex; justify-content: space-between; align-items: center; }
  .download-title h5 { margin-bottom: 0; font-weight: 600; color: var(--ink); }
  .download-title small { color:#436257 }

  .badge-id{ background:var(--accent); color:#fff; font-weight:700 }
  .muted{ color:#436257 }
  
  /* Style untuk Tombol WhatsApp */
  #whatsapp-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 1050; 
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background-color: #25d366; 
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
    <h1 class="mb-1">Daftar Semua File Unduhan Sekolah</h1> 
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">
    <?php if (empty($rows)): ?>
      <div class="alert alert-warning">Belum ada file yang tersedia untuk diunduh.</div>
    <?php else: ?>
      <div class="d-grid gap-3">
        <?php foreach ($rows as $r):
          $title     = htmlspecialchars($r['title'], ENT_QUOTES);
          $fileName  = htmlspecialchars($r['file_download'], ENT_QUOTES);
          $fileCode  = htmlspecialchars($r['code'], ENT_QUOTES); // Code tetap diambil
          $fileLink  = DOWNLOAD_PATH . $fileName;
          $dateTime  = $r['datetime'] ? date($r['datetime']) : 'N/A';
        ?>
        <article class="download-card">
          <div class="download-body">
            <div class="download-title">
                <h5><?php echo $title; ?></h5>
                <small class="muted">Kategori: <?php echo $fileCode; ?> | Diunggah: <?php echo $dateTime; ?></small>
            </div>
            
            <a href="<?php echo htmlspecialchars($fileLink, ENT_QUOTES); ?>" 
               target="_blank"
               class="btn btn-sm btn-success d-flex align-items-center" 
               download="<?php echo $fileName; ?>"
               title="Unduh file <?php echo $title; ?>">
              <i class="bi bi-download me-2"></i> Unduh
            </a>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>