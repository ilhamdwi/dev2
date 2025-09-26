<?php
// pastikan ada koneksi mysqli di $koneksi dan MY_PATH sudah didefinisikan
$currentId = isset($cari['id_article']) ? (int)$cari['id_article'] : 0;
if ($currentId <= 0) {
  // Jika kamu menyimpan ID di variabel lain (mis. $a_id), pakai itu:
  // $currentId = (int)$a_id;
}

// SEBELUMNYA (id lebih besar, ambil yang terdekat)
$prev = null;
if ($stmt = mysqli_prepare($koneksi, "SELECT id_article, title, link_article FROM article WHERE id_article > ? ORDER BY id_article ASC LIMIT 1")) {
  mysqli_stmt_bind_param($stmt, 'i', $currentId);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $prev = mysqli_fetch_assoc($res) ?: null;
  mysqli_stmt_close($stmt);
}

// SELANJUTNYA (id lebih kecil, ambil yang terdekat)
$next = null;
if ($stmt = mysqli_prepare($koneksi, "SELECT id_article, title, link_article FROM article WHERE id_article < ? ORDER BY id_article DESC LIMIT 1")) {
  mysqli_stmt_bind_param($stmt, 'i', $currentId);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $next = mysqli_fetch_assoc($res) ?: null;
  mysqli_stmt_close($stmt);
}

// Render tombol/link jika ada datanya
if ($prev) {
  $titlePrev = htmlspecialchars(strip_tags($prev['title']), ENT_QUOTES);
  $urlPrev   = MY_PATH . 'post/' . htmlspecialchars(strip_tags($prev['link_article']), ENT_QUOTES) . '.html';
  ?>
  <a class="left" href="<?php echo $urlPrev; ?>">
    <?php echo $titlePrev; ?>
    <p><i class="icon-double-angle-left"></i> Sebelumnya</p>
  </a>
  <?php
}

if ($next) {
  $titleNext = htmlspecialchars(strip_tags($next['title']), ENT_QUOTES);
  $urlNext   = MY_PATH . 'post/' . htmlspecialchars(strip_tags($next['link_article']), ENT_QUOTES) . '.html';
  ?>
  <a class="right" href="<?php echo $urlNext; ?>">
    <?php echo $titleNext; ?>
    <p>Selanjutnya <i class="icon-double-angle-right"></i></p>
  </a>
  <?php
}
?>
