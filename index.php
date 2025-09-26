<?php
// ======================= BOOTSTRAP APP =======================
error_reporting(0); // HAPUS / set E_ALL saat debug

// --- Koneksi DB & set variabel dasar dari DB --- //
require_once __DIR__ . '/lib/db.php'; // harus mendefinisikan $koneksi (mysqli)

// Pastikan konstanta base path
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // ganti sesuai base url/project Anda
}

// Fallback jika head/header/footer butuh variabel meta
$title_web     = isset($title_web)     ? $title_web     : 'Situs Sekolah';
$deskripsi_web = isset($deskripsi_web) ? $deskripsi_web : '';
$keyword_web   = isset($keyword_web)   ? $keyword_web   : '';
$admin_web     = isset($admin_web)     ? $admin_web     : 'Admin';

// ======================= HELPER =======================
if (!function_exists('cek_img_tag')) {
  // Ambil tag <img> pertama dari HTML konten, hapus width/height inline, tambahkan loading="lazy"
  if (!function_exists('first_img_url')) {
  function first_img_url(string $html, string $base = MY_PATH): string {
    if (!$html || !preg_match('/<img\b[^>]*>/i', $html, $m)) {
      return rtrim($base,'/').'/img/not-images.png';
    }
    $imgTag = $m[0];
    foreach (['src','data-src','data-original','data-lazy-src'] as $a) {
      if (preg_match('/\b'.$a.'\s*=\s*(["\'])(.*?)\1/i', $imgTag, $mm) && trim($mm[2])!=='') {
        $src = trim($mm[2]);
        break;
      }
    }
    if (empty($src)) return rtrim($base,'/').'/img/not-images.png';
    if (preg_match('~^(https?:)?//~i', $src)) return $src;           // sudah absolute
    $src = preg_replace('~^(\./|\../)+~','',$src);                   // bersihkan ./ ../
    $src = implode('/', array_map('rawurlencode', explode('/', $src))); // encode spasi
    return rtrim($base,'/').'/'.ltrim($src,'/');
  }
}

}
if (!function_exists('dateindo')) {
  // "D, d M Y" ke Indonesia sederhana
  function dateindo($tanggalInggris) {
    $hari = ['Sun'=>'Minggu','Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu'];
    $bulan = ['Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mei','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Agu','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Des'];
    $parts = explode(', ', $tanggalInggris);
    if (count($parts) !== 2) return $tanggalInggris;
    [$dEn, $sisa] = $parts;
    $sisaParts = explode(' ', $sisa);
    if (count($sisaParts) !== 3) return $tanggalInggris;
    [$tgl, $mon, $yr] = $sisaParts;
    return ($hari[$dEn] ?? $dEn) . ', ' . $tgl . ' ' . ($bulan[$mon] ?? $mon) . ' ' . $yr;
  }
}

// ======================= DEBUG DIAGNOSTIC (hapus di produksi) =======================
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// try {
//   $koneksi->set_charset('utf8mb4');
//   $dbNow = $koneksi->query("SELECT DATABASE()")->fetch_row()[0] ?? '(unknown)';
//   $cntArticle = $koneksi->query("SELECT COUNT(*) FROM `article`")->fetch_row()[0];
//   $hasGalCat  = $koneksi->query("SHOW TABLES LIKE 'galery_categories'")->num_rows;
//   $hasGal     = $koneksi->query("SHOW TABLES LIKE 'galery'")->num_rows;
//   echo "<!-- DB OK: $dbNow | article=$cntArticle | galery_categories=".($hasGalCat?'YES':'NO')." | galery=".($hasGal?'YES':'NO')." -->";
// } catch (Throwable $e) {
//   echo "<!-- DB ERROR: ".$e->getMessage()." -->";
// }

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<!-- Base untuk path relatif -->
<base href="<?php echo MY_PATH; ?>">

<?php include __DIR__ . "/lib/meta_tag.php";?>
<?php include __DIR__ . "/head.php";?>

<!-- Fallback CSS kalau head.php tidak memuat -->
<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<style>
  .section-title h2{ font-weight:700; letter-spacing:.3px }
  .card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease;
    height: 100%;
  }
  .card:hover{ transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.08) }
  .card .card-body{ padding: 1rem 1rem 0.5rem }
  .card .card-title{ font-size: 1rem; line-height: 1.35; margin-bottom:.35rem }
  .card .card-text{ font-size:.93rem }
  .card .card-footer{ background: transparent; border-top: 0; padding: .75rem 1rem 1rem; font-size:.88rem }

  .feature-tile{ border-radius: 1rem; padding: 1.25rem; min-height: 180px; display:flex; align-items:center; justify-content:center; text-align:center; box-shadow: 0 6px 18px rgba(0,0,0,.06); transition: transform .18s, box-shadow .18s }
  .feature-tile:hover{ transform: translateY(-4px); box-shadow: 0 12px 26px rgba(0,0,0,.08) }
  .feature-tile h3{ font-size:1.05rem; line-height:1.4; margin:0 }
  .feature-tile i{ font-size: 1.5rem; margin-right:.35rem; vertical-align:-2px }
  .video-cover{ background-size: cover; background-position: center; border-radius:1rem; overflow:hidden; box-shadow: 0 10px 24px rgba(0,0,0,.08) }
  .clients-slider .swiper-slide img{ max-height:68px; width:auto; object-fit:contain }

  .btn{ border-radius:.75rem }
  .container-narrow{ max-width: 1200px }

  /* Responsif untuk gambar dari cek_img_tag */
  .object-fit-cover{ object-fit: cover }
</style>
</head>
<body>

<?php include __DIR__ . "/lib/header.php";?>

<!-- ======= Icon Boxes Section (Pricing) ======= -->
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
<!-- End Icon Boxes Section -->

  <!-- ======= Artikel Section ======= -->
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
        // ==== DATA ====
        $limit  = isset($show_blog) ? max(1,(int)$show_blog) : 8;
        $page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        try {
          $stmt = $koneksi->prepare(
            "SELECT id_article, title, link_article, content, date
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

              // tanggal
              $tglRaw  = $post['date'] ?? '';
              $tglEn   = $tglRaw ? date('D, d M Y', strtotime($tglRaw)) : '';
              $tglId   = $tglEn ? (function_exists('dateindo') ? dateindo($tglEn) : $tglEn) : '';
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
                <?php echo (isset($datetime) && $datetime==='tgl_standar') ? $tglId : htmlspecialchars($tglRaw, ENT_QUOTES); ?>
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

<!-- ======= Why Us Section ======= -->
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
              <!-- Item 1: terbuka -->
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
              <!-- Item 2: tertutup -->
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
              <!-- Item 3: tertutup -->
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
              <div class="video-box"
                  style='background-image:url("https://smaipbsoedirman2bekasi.sch.id/images/slider/pangsoed-min5230617(1)5230617(1).png?1650220925838");'>
                <a href="https://youtu.be/wLHgFSxADSI?si=Neq6X4NsdRNTkwtj"
                  class="glightbox play-btn"
                  data-type="video"
                  aria-label="Putar video profil"></a>
                <span class="video-badge">HD</span>
              </div>
            </div>
          </div>
      </div>
    </div>
  </section>

<!-- End Why Us Section -->

<!-- ======= Clients / Galeri Foto ======= -->
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

              // Mode A: categories = slug
              $stmtA = $koneksi->prepare("SELECT `gambar` FROM `galery` WHERE `categories` = ? ORDER BY `id_galery` DESC LIMIT 1");
              $stmtA->bind_param('s', $slug);
              $stmtA->execute();
              $resA = $stmtA->get_result();
              $rowA = $resA->fetch_assoc();
              $stmtA->close();

              $gambar = $rowA['gambar'] ?? null;

              // Mode B (fallback): categories = title
              if ($gambar === null && $title_galery !== '') {
                $stmtB = $koneksi->prepare("SELECT * FROM galery WHERE `id_galery` = ? ORDER BY `id_galery` DESC LIMIT 1");
                $stmtB->bind_param('s', $title_galery);
                $stmtB->execute();
                $resB = $stmtB->get_result();
                $rowB = $resB->fetch_assoc();
                $stmtB->close();

                $gambar = $rowB['gambar'] ?? null;
              }

              // Jika skema Anda sebenarnya pakai ID kategori:
              // $catId = (int)$cat['id'];
              // $stmtI = $koneksi->prepare("SELECT `gambar` FROM `galery` WHERE `categories` = ? ORDER BY `id` DESC LIMIT 1");
              // $stmtI->bind_param('i', $catId);
              // $stmtI->execute(); $resI = $stmtI->get_result(); $rowI = $resI->fetch_assoc(); $stmtI->close();
              // $gambar = $gambar ?? ($rowI['gambar'] ?? null);

              if ($gambar === null) $gambar = 'not-images.png';
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
<!-- End Clients Section -->

<!-- ======= Contact Section ======= -->
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
              <p class="mb-0">Jl. Enau Raya Perum Puri Harapan, Bekasi</p>
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
<!-- End Contact Section -->

<?php include __DIR__ . "/lib/footer.php";?>

<!-- ===== JS ===== -->
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
