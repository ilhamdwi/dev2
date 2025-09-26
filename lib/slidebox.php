<?php
// Pastikan koneksi & base path
if (!isset($koneksi)) {
  require_once __DIR__ . '/../lib/db.php';
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/'); // ganti sesuai base URL
}

// Pastikan array kategori tersedia
$a_category = isset($a_category) && is_array($a_category) ? $a_category : [];
$jumlah_category = isset($jumlah_category) ? (int)$jumlah_category : (count($a_category) - 1);
$jumlah_category = max(-1, $jumlah_category); // jaga-jaga

// Judul artikel saat ini (untuk mengecualikan diri sendiri dari hasil)
$currentTitle = isset($title) ? trim($title) : '';

// Siapkan prepared statement sekali
// Ambil 2 artikel per kategori, kecuali judul yang sama
$sqlRelated = "
  SELECT id_article, title, link_article, content, date
  FROM article
  WHERE categories LIKE CONCAT('%', ?, '%')
    AND title <> ?
  ORDER BY id_article DESC
  LIMIT 2
";
$stmt = mysqli_prepare($koneksi, $sqlRelated);
if (!$stmt) {
  // Jika statement gagal disiapkan, kamu bisa log error:
  // error_log('Prepare related failed: ' . mysqli_error($koneksi));
}

// Penampung untuk menghindari duplikat antar kategori
$seenLinks = [];

// Helper aman untuk ambil gambar dari konten
$extractImage = function($html) {
  if (function_exists('cek_img_tag')) {
    return cek_img_tag($html);
  }
  // fallback: coba regex sederhana cari <img src="...">
  if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
    $src = $m[1];
    return '<img src="'.htmlspecialchars($src, ENT_QUOTES).'" alt="">';
  }
  return ''; // tidak ada
};

// Helper format tanggal Indonesia (fallback jika dateindo() tak ada)
$formatTanggalIndo = function($mysqlDate) {
  $tanggal = date('D, d M Y', strtotime($mysqlDate ?: 'now'));
  if (function_exists('dateindo')) {
    return dateindo($tanggal);
  }
  return $tanggal; // fallback ke format default
};

?>
<div class="slidebox">
  <a class="close"><i class="icon-remove-circle"></i></a>
  <div class="title"><h3>Baca Artikel Lainnya</h3></div>

  <ul class="popular_posts_list">
    <?php
    if ($stmt) {
      for ($j_cat = 0; $j_cat <= $jumlah_category; $j_cat++) {
        $cat_me = isset($a_category[$j_cat]) ? trim($a_category[$j_cat]) : '';
        if ($cat_me === '') continue;

        mysqli_stmt_bind_param($stmt, 'ss', $cat_me, $currentTitle);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        while ($data_related = mysqli_fetch_assoc($res)) {
          $title_related = htmlspecialchars(strip_tags(substr($data_related['title'] ?? '', 0, 35)), ENT_QUOTES);
          $link_article  = strip_tags($data_related['link_article'] ?? '');
          if ($link_article === '' || isset($seenLinks[$link_article])) {
            continue; // skip kosong/duplikat
          }
          $seenLinks[$link_article] = true;

          $link_related = MY_PATH . 'post/' . $link_article . '.html';
          $content_related = $data_related['content'] ?? '';
          $gambar_related = $extractImage($content_related);
          $date_related = $data_related['date'] ?? '';
          $date_ind_r = $formatTanggalIndo($date_related);
          ?>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <li>
              <a href="<?php echo htmlspecialchars($link_related, ENT_QUOTES); ?>">
                <div class="recent-img">
                  <?php if ($gambar_related === ''): ?>
                    <img src="<?php echo MY_PATH; ?>img/not-images.png" alt="<?php echo $title_related; ?>">
                  <?php else: ?>
                    <?php echo $gambar_related; ?>
                  <?php endif; ?>
                </div>
                <div class="title_post"><?php echo $title_related; ?>..</div>
                <small class="rp_date"><?php echo htmlspecialchars($date_ind_r, ENT_QUOTES); ?></small>
              </a>
            </li>
          </div>
          <?php
        }
      }
      mysqli_stmt_close($stmt);
    } else {
      // Fallback jika statement gagal disiapkan: tampilkan pesan
      echo '<li><span class="text-muted">Tidak dapat memuat artikel terkait saat ini.</span></li>';
    }
    ?>
  </ul>
</div>
