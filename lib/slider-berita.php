<ul id="newsslider">
<?php
if (!isset($koneksi)) {
  require_once __DIR__ . '/../lib/db.php'; // sesuaikan path bila perlu
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/'); // ganti ke base URL situsmu
}

// Fallback ekstrak gambar bila cek_img_tag() tidak ada
$extractImage = function(string $html) {
  if (function_exists('cek_img_tag')) {
    return cek_img_tag($html); // diasumsikan return HTML <img ...>
  }
  if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
    $src = $m[1];
    return '<img src="'.htmlspecialchars($src, ENT_QUOTES).'" alt="">';
  }
  return ''; // tidak ada img
};

// Ambil 10 artikel terbaru
$sql = "SELECT title, link_article, content FROM article ORDER BY id_article DESC LIMIT 10";
$rs  = mysqli_query($koneksi, $sql);

if ($rs && mysqli_num_rows($rs) > 0):
  while ($post = mysqli_fetch_assoc($rs)):
    $title_full   = trim(strip_tags($post['title'] ?? ''));
    $title_top    = mb_substr($title_full, 0, 40);
    $title_short  = mb_substr($title_full, 0, 20);
    $link_slug    = strip_tags($post['link_article'] ?? '');
    $link_blog    = MY_PATH . 'post/' . $link_slug . '.html';
    $content_html = $post['content'] ?? '';
    $img_html     = $extractImage($content_html);
    $content_snip = trim(strip_tags($content_html));
    $content_snip = mb_substr($content_snip, 0, 270);
    ?>
    <li>
      <a href="<?php echo htmlspecialchars($link_blog, ENT_QUOTES); ?>">
        <?php if ($img_html === ''): ?>
          <img src="<?php echo MY_PATH; ?>img/not-images.png"
               alt="<?php echo htmlspecialchars($title_short, ENT_QUOTES); ?>" />
        <?php else: ?>
          <?php echo $img_html; ?>
        <?php endif; ?>
      </a>

      <a href="<?php echo htmlspecialchars($link_blog, ENT_QUOTES); ?>">
        <?php echo htmlspecialchars($title_short, ENT_QUOTES); ?>..
      </a>
      <h3></h3>
      <p>
        <b>
          <a href="<?php echo htmlspecialchars($link_blog, ENT_QUOTES); ?>">
            <?php echo htmlspecialchars($title_top, ENT_QUOTES); ?>
          </a>
        </b><br>
        <?php echo htmlspecialchars($content_snip, ENT_QUOTES); ?><br>
      </p>
    </li>
  <?php endwhile; ?>
<?php else: ?>
  <h3>Artikel Masih Kosong</h3>
<?php endif; ?>
</ul>
