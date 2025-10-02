<?php
/************************************************************
 * KONTAK KAMI — pages/kontak.php (Kontak Person)
 * Menggantikan file struktur.php
 ************************************************************/

// Root filesystem project (.../dev2)
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
$deskripsi_web = $deskripsi_web ?? 'Informasi kontak person dan media sosial SMAI PB Soedirman 2 Bekasi.';
$keyword_web   = $keyword_web   ?? 'kontak, telepon, email, alamat, media sosial sekolah';
$admin_web     = $admin_web     ?? 'Admin';

// Judul halaman spesifik
$page_title = 'Kontak Kami';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — <?php echo htmlspecialchars($page_title, ENT_QUOTES); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* Menjaga styling page-head dari template lama */
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid var(--line, #e8f2ec);
  }
  .page-head h1 { font-weight: 700; color: #173127; }
  
  /* Styling untuk blok kontak baru (diasumsikan menggunakan utility classes dari style.css/bootstrap) */
  .contact .info {
      background: var(--soft, #f4fbf6); /* Latar lembut untuk kolom info */
  }
  .contact .info i {
      color: var(--brand, #22c55e); /* Ikon berwarna hijau brand */
  }
  .contact .section-title h2 {
      font-weight: 700; letter-spacing: .2px; color: #173127;
      position: relative; display: inline-block; padding-bottom: .35rem;
  }
  .contact .section-title h2::after {
      content: ""; position: absolute; left: 0; bottom: 0; height: 3px; width: 48%;
      background: linear-gradient(90deg, var(--brand, #22c55e) 0%, var(--brand-2, #86efac) 100%);
      border-radius: 2px;
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
</style>
</head>
<body>

<?php include APP_ROOT . "/lib/header.php"; ?>

<section class="page-head py-4">
  <div class="container container-narrow">
    <h1 class="mb-1"><?php echo htmlspecialchars($page_title, ENT_QUOTES); ?></h1>
    <p class="text-muted mb-0">Hubungi kami melalui alamat, telepon, atau media sosial.</p>
  </div>
</section>

<section id="contact" class="contact py-5">
  <div class="container container-narrow" data-aos="fade-up">
    
    <div class="row g-4 mt-1">
      <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
        <div class="info p-3 p-md-4 border rounded-4 h-100">
          <div class="d-flex align-items-start mb-3">
            <i class="bi bi-geo-alt fs-4 me-3"></i>
            <div>
              <h4 class="mb-1">Alamat</h4>
              <p class="mb-0">Jl. Puri Harapan Jl. Enau Raya No.Kel, Setia Asih, Kec. Tarumajaya, Kabupaten Bekasi, Jawa Barat 17215</p>
            </div>
          </div>
          <div class="d-flex align-items-start mb-3">
            <i class="bi bi-envelope fs-4 me-3"></i>
            <div>
              <h4 class="mb-1">Email</h4>
              <p class="mb-0">info@smaipbsoedirman2bekasi.sch.id</p>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <i class="bi bi-phone fs-4 me-3"></i>
            <div>
              <h4 class="mb-1">Telp</h4>
              <p class="mb-0">(021)-88875365</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
        <div class="p-3 p-md-4 border rounded-4 h-100">
          <h3>Social Media Kami</h3>
          <p class="mb-3">Tetap terhubung dengan kami, follow akun berikut:</p>

          <div class="row g-3">
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://www.facebook.com/pangsoed.pangsoed" target="_blank" rel="noopener">
                <i class="bi bi-facebook fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">Facebook</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2 Bekasi</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://twitter.com/pangsoed2bekasi?s=09" target="_blank" rel="noopener">
                <i class="bi bi-twitter fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">Twitter</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2 Bekasi</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://www.instagram.com/pangsoed2bekasi/" target="_blank" rel="noopener">
                <i class="bi bi-instagram fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">Instagram</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2 Bekasi</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://www.youtube.com/channel/UCEmqrO0cYsrUoD21QvgZg7w" target="_blank" rel="noopener">
                <i class="bi bi-youtube fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">YouTube</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2 Bekasi</p>
                </div>
              </a>
            </div>
          </div>

        </div>
      </div>
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
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>