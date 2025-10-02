<?php
/************************************************************
 * BUKU TAMU — pages/buku_tamu.php (frontend form + list)
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}

require_once APP_ROOT . '/lib/db.php'; // menggunakan $koneksi (mysqli)
session_start();

/* ====== CSRF Token ====== */
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

/* ====== Helper alert dari query string ====== */
$success = isset($_GET['success']) && $_GET['success'] === '1';
$error   = isset($_GET['error'])   ? $_GET['error']   : '';

/* ====== Ambil daftar buku tamu (nama + pesan) ====== */
/* Menampilkan 20 entri terbaru; jika ingin hanya published, tambahkan WHERE status='published' */
$guestbook_rows = [];
if (isset($koneksi) && $koneksi instanceof mysqli) {
  @mysqli_set_charset($koneksi, 'utf8mb4');
  $sql = "SELECT nama_buku_tamu, pesan_buku_tamu, status
          FROM buku_tamu
          ORDER BY id_buku DESC
          LIMIT 20";
  if ($res = $koneksi->query($sql)) {
    while ($row = $res->fetch_assoc()) {
      $guestbook_rows[] = [
        'nama'  => (string)($row['nama_buku_tamu'] ?? ''),
        'pesan' => (string)($row['pesan_buku_tamu'] ?? ''),
        'status' => (string)($row['status'] ?? ''),
      ];
    }
    $res->close();
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo htmlspecialchars($title_web ?? 'Situs Sekolah', ENT_QUOTES); ?> — Buku Tamu</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web ?? '', ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web ?? '', ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web ?? 'Admin', ENT_QUOTES); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid #e8f2ec;
  }
  .page-head h1 { font-weight: 700; color: #173127; }
  .card { border-radius: 1rem; box-shadow: 0 10px 24px rgba(0,0,0,.06); }
  /* honeypot */
  .hp-field { position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden; }
  .list-empty { color:#6c757d; font-style: italic; }
  /* ekstra ruang agar tidak tumpang tindih dengan footer pada layout tertentu */
  body { padding-bottom: 3rem; }
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
    <h1 class="mb-1">Buku Tamu</h1>
    <p class="text-muted mb-0">Silakan tinggalkan pesan Anda.</p>
  </div>
</section>

<section class="py-5">
  <div class="container container-narrow">
    <div class="row justify-content-center">
      <div class="col-xl-8">

        <?php if ($success): ?>
          <div class="alert alert-success" role="alert">
            Terima kasih! Pesan Anda sudah kami terima dan berstatus <strong>draft</strong>.
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error, ENT_QUOTES); ?>
          </div>
        <?php endif; ?>

        <!-- Form Buku Tamu -->
        <div class="card">
          <div class="card-body p-4 p-md-5">
            <form method="post" action="<?php echo MY_PATH; ?>actions/buku_tamu_store.php" novalidate>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES); ?>">
              <!-- Honeypot anti-bot -->
              <div class="hp-field">
                <label>Website</label>
                <input type="text" name="website" autocomplete="off" tabindex="-1">
              </div>

              <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" maxlength="100" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="150" required>
              </div>
              <div class="mb-3">
                <label for="pesan" class="form-label">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="5" maxlength="5000" required></textarea>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-send-fill me-1"></i>Kirim
                </button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
              </div>

              <p class="text-muted small mt-3 mb-0">
                Catatan: Entri baru otomatis berstatus <strong>draft</strong> sampai diverifikasi admin.
              </p>
            </form>
          </div>
        </div>

      </div><!-- /col -->
    </div><!-- /row -->
  </div><!-- /container -->
</section>

<!-- Daftar Buku Tamu (Nama & Pesan) -->
<section class="py-5 mb-5"><!-- mb-5 memberikan jarak jelas dari footer -->
  <div class="container container-narrow">
    <div class="row justify-content-center">
      <div class="col-xl-8">
        <div class="card">
          <div class="card-body p-4 p-md-5">
            <h2 class="h5 mb-3"><i class="bi bi-chat-left-text me-2"></i>Daftar Buku Tamu Terbaru</h2>

            <?php if (empty($guestbook_rows)): ?>
              <p class="list-empty mb-0">Belum ada entri buku tamu.</p>
            <?php else: ?>
              <div class="list-group">
                    <div class="list-group-item bg-light fw-semibold">
                        <div class="row g-2">
                        <div class="col-12 col-md-4">Nama</div>
                        <div class="col-12 col-md-6">Pesan</div>
                        <div class="col-12 col-md-2 text-md-center">Status</div>
                        </div>
                    </div>
                    <?php foreach ($guestbook_rows as $row): ?>
                        <div class="list-group-item">
                        <div class="row g-2 align-items-start">
                            <div class="col-12 col-md-4">
                            <?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>
                            </div>
                            <div class="col-12 col-md-6">
                            <?php echo nl2br(htmlspecialchars($row['pesan'], ENT_QUOTES)); ?>
                            </div>
                            <div class="col-12 col-md-2 text-md-center">
                            <?php if ($row['status'] === 'published'): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                            </div>
                        </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

            <?php endif; ?>

            <p class="text-muted small mt-3 mb-0">
              *Menampilkan maksimal 20 entri terbaru.
            </p>
          </div>
        </div>
      </div><!-- /col -->
    </div><!-- /row -->
  </div><!-- /container -->
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

<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
