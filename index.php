<?php
/************************************************************
 * INDEX / HOMEPAGE — index.php (Full Update)
 * Menggunakan style.css yang Anda berikan.
 ************************************************************/

// ======================= BOOTSTRAP APP =======================
error_reporting(0); // HAPUS / set E_ALL saat debug

// --- Koneksi DB & set variabel dasar dari DB --- //
// Asumsi $koneksi didefinisikan di sini
require_once __DIR__ . '/lib/db.php'; 

// Pastikan konstanta base path
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // ganti sesuai base url/project Anda
}

// Fallback jika head/header/footer butuh variabel meta
$title_web     = isset($title_web)     ? $title_web     : 'Situs Sekolah';
$deskripsi_web = isset($deskripsi_web) ? $deskripsi_web : 'Website resmi SMAI PB Soedirman 2 Bekasi. Informasi pendaftaran, kurikulum, dan kegiatan siswa.';
$keyword_web   = isset($keyword_web)   ? $keyword_web   : 'SMAI, Soedirman, Bekasi, PPDB, sekolah favorit';
$admin_web     = isset($admin_web)     ? $admin_web     : 'Admin';

// ======================= HELPER =======================

// Pastikan fungsi first_img_url ada (dibuat ringkas)
if (!function_exists('first_img_url')) {
  function first_img_url(string $html, string $base = MY_PATH): string {
    if (!$html || !preg_match('/<img\b[^>]*src\s*=\s*(["\'])(.*?)\1/i', $html, $m)) {
      return rtrim($base,'/').'/img/not-images.png';
    }
    $src = trim($m[2]);
    if (preg_match('~^(https?:)?//~i', $src)) return $src;
    $src = preg_replace('~^(\./|\../)+~','',$src);
    $src = implode('/', array_map('rawurlencode', explode('/', $src)));
    return rtrim($base,'/').'/'.ltrim($src,'/');
  }
}

// Pastikan fungsi dateindo ada (dibuat ringkas)
if (!function_exists('dateindo')) {
  function dateindo($tanggalInggris) {
    $hari = ['Sun'=>'Minggu','Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu'];
    $bulan = ['Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mei','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Agu','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Des'];
    if (strtotime($tanggalInggris) === false) return $tanggalInggris;
    $tglEn = date('D, d M Y', strtotime($tanggalInggris));
    $parts = explode(', ', $tglEn);
    if (count($parts) !== 2) return $tanggalInggris;
    [$dEn, $sisa] = $parts;
    $sisaParts = explode(' ', $sisa);
    if (count($sisaParts) !== 3) return $tanggalInggris;
    [$tgl, $mon, $yr] = $sisaParts;
    return ($hari[$dEn] ?? $dEn) . ', ' . $tgl . ' ' . ($bulan[$mon] ?? $mon) . ' ' . $yr;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> - <?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include __DIR__ . "/lib/meta_tag.php";?>
<?php include __DIR__ . "/head.php";?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<link href="<?php echo MY_PATH; ?>css/style.css" rel="stylesheet">

<style>
  /* Variabel CSS dari style.css (diulang untuk memastikan) */
  :root{
    --brand: #22c55e;
    --brand-2:#86efac;
    --brand-dark:#16a34a;
    --ink:#12221a;
    --line:#e7f5eb;
    --bg:#ffffff;
    --soft:#f4fbf6;
  }

  /* Perbaikan CSS untuk tombol Play agar lebih kontras */
  #why-us .play-btn::before{
    /* Ubah warna segitiga play menjadi putih agar kontras */
    border-color: transparent transparent transparent #ffffff; 
    margin-left: 6px;
  }
  
  /* Tambahkan efek bayangan pada play-btn untuk menonjolkan dari background */
  #why-us .play-btn {
    background: radial-gradient(var(--brand) 60%, rgba(34, 197, 94, .35) 62%);
    box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.4), 0 10px 30px rgba(0,0,0,.25);
  }
  
  /* Tambahan CSS untuk Floating WhatsApp Button */
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

<?php include __DIR__ . "/lib/header.php";?>

<section id="pricing" class="pricing py-4 py-md-5">
  <div class="container container-narrow">
    <div class="row g-3 g-md-4" role="list">
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="50" role="listitem">
        <div class="feature-tile featured4 box4 h-100">
          <h3 class="m-0">
            <i class="bi bi-person-video3" aria-hidden="true"></i>
            <span class="d-block mt-2">PENERIMAAN<br>PESERTA DIDIK BARU</span>
          </h3>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100" role="listitem">
        <div class="feature-tile featured box2 h-100">
          <h3 class="m-0">
            <i class="bi bi-book" aria-hidden="true"></i> E-LEARNING
            <br class="d-none d-lg-block"><span class="d-inline-block mt-2">
            <i class="bi bi-award" aria-hidden="true"></i> ASSESMENT</span>
          </h3>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="150" role="listitem">
        <div class="feature-tile featured2 box h-100">
          <div class="w-100">
            <h3 class="mb-2"><i class="bi bi-journals" aria-hidden="true"></i> KURIKULUM</h3>
            <ul class="mb-0 small text-start d-inline-block">
              <li>Kalender Akademik</li>
              <li>Program</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200" role="listitem">
        <div class="feature-tile featured3 box h-100">
          <div class="w-100">
            <h3 class="mb-2"><i class="bi bi-mortarboard" aria-hidden="true"></i> KESISWAAN</h3>
            <ul class="mb-0 small text-start d-inline-block">
              <li>Kegiatan Siswa</li>
              <li>BESS</li>
            </ul>
          </div>
        </div>
      </div>
    </div>  
  </div>
</section>
<div class="container container-narrow">
  <div class="row align-items-center gy-3">
    <div class="col-lg-6">
      <div class="section-title d-flex align-items-center justify-content-between">
        <h2 class="mb-0">Artikel Terbaru</h2>
        <a href="<?php echo MY_PATH; ?>blog.php" class="btn btn-success btn-sm px-3">Selengkapnya</a>
      </div>
    </div>
  </div>

  <div class="mt-3" data-aos="fade-up">
    <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100" aria-label="Slider artikel">
      <div class="swiper-wrapper">
        <?php
        // ==== DATA ARTIKEL ====
        $limit  = isset($show_blog) ? max(1,(int)$show_blog) : 8;
        $page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        try {
          $stmt = $koneksi->prepare(
            "SELECT id_article, title, date
             FROM article
             ORDER BY id_article DESC
             LIMIT ?, ?"
          );
          $stmt->bind_param('ii', $offset, $limit);
          $stmt->execute();
          $res = $stmt->get_result();

          if ($res->num_rows === 0) {
            echo '<div class="swiper-slide"><div class="alert alert-warning w-100">Belum ada artikel.</div></div>';
          } else {
            while ($post = $res->fetch_assoc()):
              $title   = trim(stripslashes(strip_tags(substr($post['title'], 0, 60))));
              $link    = MY_PATH.'post/'.strip_tags($post['link_article']).'.html';
              $content = (string)$post['content'];
              $img     = first_img_url($content, MY_PATH);
              
              $tglRaw  = $post['date'] ?? '';
              $tglId   = $tglRaw ? dateindo($tglRaw) : '';
              $excerpt = trim(stripslashes(strip_tags(substr($content, 0, 110))));
        ?>
        <div class="swiper-slide">
          <article class="card h-100">
            <div class="ratio ratio-16x9">
              <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>"
                   class="w-100 h-100 object-fit-cover"
                   alt="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>"
                   loading="lazy"
                   onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';">
            </div>
            <div class="card-body">
              <h4 class="card-title mb-1"><b><?php echo htmlspecialchars($title, ENT_QUOTES); ?></b></h4>
              <h6 class="card-subtitle mb-2 text-muted">
                <?php echo htmlspecialchars($tglId, ENT_QUOTES); ?>
              </h6>
              <p class="card-text mb-3"><?php echo htmlspecialchars($excerpt, ENT_QUOTES); ?>…</p>
              <a href="<?php echo htmlspecialchars($link, ENT_QUOTES); ?>" class="btn btn-success btn-sm">Read More</a>
            </div>
            <div class="card-footer text-muted">Oleh: <?php echo htmlspecialchars($admin_web ?? 'Admin', ENT_QUOTES); ?></div>
          </article>
        </div>
        <?php
            endwhile;
          }
          $stmt->close();
        } catch (Throwable $e) {
          echo '<div class="swiper-slide"><div class="alert alert-danger">Kesalahan artikel: '.htmlspecialchars($e->getMessage(), ENT_QUOTES).'</div></div>';
        }
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</div>

<hr>
<section id="why-us" class="why-us">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-7 d-flex flex-column justify-content-center align-items-stretch" data-aos="fade-left">
            <div class="content">
            <h3>VIDEO SINGKAT <strong>SMAI PB SOEDIRMAN 2 BEKASI</strong></h3>
            <p>
            Alasan anda harus sekolah di SMAI PB SOEDIRMAN 2 BEKASI 
            </p>
          </div>
          <div class="accordion-list">
            <ul>
              <li data-aos="fade-up" data-aos-delay="100">
                <a data-bs-toggle="collapse" data-bs-target="#accordion-list-1" aria-expanded="true">
                  <span>01</span> Kelebihan 1
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i>
                </a>
                <div id="accordion-list-1" class="collapse show" data-bs-parent=".accordion-list">
                  <p>SMA IPB Soedirman 2 Bekasi sekolah favorit unggulan di bekasi.</p>
                </div>
              </li>
              <li data-aos="fade-up" data-aos-delay="200">
                <a data-bs-toggle="collapse" data-bs-target="#accordion-list-2" class="collapsed" aria-expanded="false">
                  <span>02</span> Kelebihan 2
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i>
                </a>
                <div id="accordion-list-2" class="collapse" data-bs-parent=".accordion-list">
                  <p>Ekstrakulikuler lengkap dan terpadu</p>
                </div>
              </li>
              <li data-aos="fade-up" data-aos-delay="300">
                <a data-bs-toggle="collapse" data-bs-target="#accordion-list-3" class="collapsed" aria-expanded="false">
                  <span>03</span> Kelebihan 3
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i>
                </a>
                <div id="accordion-list-3" class="collapse" data-bs-parent=".accordion-list">
                  <p>Pengajar dan staf profesional, berpendidikan tinggi dan berakhlakul karimah.</p>
                </div>
              </li>
            </ul>
          </div>
        </div> 
        
        <div class="col-lg-5" data-aos="fade-right">
          <div class="ratio ratio-16x9">
            <a href="https://youtu.be/wLHgFSxADSI?si=Neq6X4NsdRNTkwtj"
              class="glightbox" 
              data-type="video"
              aria-label="Putar video profil">
              <div class="video-box"
                style='background-image:url("https://smaipbsoedirman2bekasi.sch.id/images/slider/pangsoed-min5230617(1)5230617(1).png?1650220925838");'>
                <div class="play-btn"></div>
                <span class="video-badge">HD</span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
<hr>
<section id="clients" class="clients py-5">
  <div class="container container-narrow" data-aos="zoom-in">
    <div class="section-title text-center mb-4">
      <h2>GALERI FOTO</h2>
      <p class="mb-0">Dokumentasi kegiatan di SMA IPB Soedirman 2 Bekasi</p>
    </div>

    <div class="clients-slider swiper" aria-label="Slider galeri">
      <div class="swiper-wrapper align-items-center">
        <?php
        // ===== Galeri: coba match slug, fallback ke title =====
        try {
          $galCatRes = $koneksi->query("SELECT `id`, `title`, `link_categories` FROM `galery_categories` ORDER BY `id`");
          if ($galCatRes->num_rows === 0) {
            echo '<div class="swiper-slide"><div class="text-center text-muted">Belum ada kategori galeri.</div></div>';
          } else {
            while ($cat = $galCatRes->fetch_assoc()):
              $title_galery = trim((string)$cat['title']);
              $slug         = trim((string)$cat['link_categories']);
              $link_galery  = MY_PATH . "galery-foto/" . $slug;

              // Ambil satu gambar dari kategori
              $gambar = null;
              if ($stmtA = $koneksi->prepare("SELECT `gambar` FROM `galery` WHERE `categories` = ? ORDER BY `id_galery` DESC LIMIT 1")) {
                $stmtA->bind_param('s', $slug);
                $stmtA->execute();
                $resA = $stmtA->get_result();
                $rowA = $resA->fetch_assoc();
                $stmtA->close();
                $gambar = $rowA['gambar'] ?? null;
              }
              
              if ($gambar === null || $gambar === '') $gambar = 'not-images.png';
        ?>
          <div class="swiper-slide">
            <a href="<?php echo htmlspecialchars($link_galery, ENT_QUOTES); ?>"
               aria-label="Buka galeri kategori <?php echo htmlspecialchars($title_galery, ENT_QUOTES); ?>">
              <img src="<?php echo MY_PATH; ?>images/galery/<?php echo htmlspecialchars($gambar, ENT_QUOTES); ?>"
                   class="img-fluid"
                   alt="<?php echo htmlspecialchars($title_galery, ENT_QUOTES); ?>"
                   loading="lazy">
            </a>
          </div>
        <?php
            endwhile;
          }
        } catch (Throwable $e) {
          echo '<div class="swiper-slide"><div class="alert alert-danger">Kesalahan galeri: '.htmlspecialchars($e->getMessage(), ENT_QUOTES).'</div></div>';
        }
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<hr>
<section id="contact" class="contact py-5">
  <div class="container container-narrow" data-aos="fade-up">
    <div class="section-title text-center">
      <h2>Kontak Kami</h2>
    </div>

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
                  <p class="mb-0 small">SMAI PB Soedirman 2</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://twitter.com/pangsoed2bekasi?s=09" target="_blank" rel="noopener">
                <i class="bi bi-twitter fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">Twitter</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://www.instagram.com/pangsoed2bekasi/" target="_blank" rel="noopener">
                <i class="bi bi-instagram fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">Instagram</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2</p>
                </div>
              </a>
            </div>
            <div class="col-6">
              <a class="d-flex align-items-center p-3 border rounded-4 h-100 text-decoration-none"
                 href="https://www.youtube.com/channel/UCEmqrO0cYsrUoD21QvgZg7w" target="_blank" rel="noopener">
                <i class="bi bi-youtube fs-3 me-3"></i>
                <div>
                  <h4 class="h6 mb-1">YouTube</h4>
                  <p class="mb-0 small">SMAI PB Soedirman 2</p>
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
<?php include __DIR__ . "/lib/footer.php";?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>GLightbox({ selector: '.glightbox', touchNavigation: true, autoplayVideos: true });</script>
<?php include __DIR__ . "/js.php";?>

<script>
  // Init Swiper untuk artikel
  new Swiper('.testimonials-slider', {
    slidesPerView: 1,
    spaceBetween: 16,
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
      576: { slidesPerView: 2 },
      992: { slidesPerView: 3 }
    }
  });

  // Init Swiper untuk galeri
  new Swiper('.clients-slider', {
    slidesPerView: 2,
    spaceBetween: 16,
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
      576: { slidesPerView: 3 },
      768: { slidesPerView: 4 },
      992: { slidesPerView: 6 }
    }
  });
</script>
</body>
</html>