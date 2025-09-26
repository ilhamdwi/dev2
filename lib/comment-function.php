<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
include 'db.php'; // harus mendefinisikan $koneksi (mysqli_connect)

// pastikan base path ada (ganti sesuai situsmu)
if (!defined('MY_PATH')) {
    define('MY_PATH', '/');
}

function getComments(array $row, mysqli $koneksi) {
    // Ambil & sanitasi data utama
    $id_koment         = (int)$row['id_koment'];
    $nama_comment      = strip_tags($row['nama_comment']);
    $url_website_komen = trim(strip_tags($row['url_website_komen']));
    $comment_message   = strip_tags($row['comment']);
    $date_comment      = strip_tags($row['date_comment']);

    // ----- Tampilkan satu comment (template kamu) -----
    ?>
    <li class="comment" id="comment-<?php echo $id_koment; ?>">
      <div class="avatar">
        <img src="<?php echo MY_PATH; ?>img/avatar.jpg" class="avatar" alt="avatar">
      </div>
      <div class="comment-container">
        <h4 class="comment-author">
          <?php if ($url_website_komen): ?>
            <a href="<?php echo htmlspecialchars($url_website_komen, ENT_QUOTES); ?>" target="_blank" rel="nofollow noopener">
              <?php echo $nama_comment; ?>
            </a>
          <?php else: ?>
            <?php echo $nama_comment; ?>
          <?php endif; ?>
        </h4>

        <div class="comment-meta">
          <?php if ($url_website_komen): ?>
            <a href="<?php echo htmlspecialchars($url_website_komen, ENT_QUOTES); ?>" target="_blank" class="comment-date link-style1" rel="nofollow noopener">
              <?php echo $date_comment; ?>
            </a>
          <?php else: ?>
            <span class="comment-date link-style1"><?php echo $date_comment; ?></span>
          <?php endif; ?>

          <a class="comment-reply-link link-style3 reply" href="#respon" onclick="return show();" id="<?php echo $id_koment; ?>">Balas</a>
        </div>

        <div class="comment-body">
          <p><?php echo $comment_message; ?></p>
        </div>
      </div>
    <?php

    // ----- Ambil & render anak (reply) secara rekursif -----
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM comment WHERE status='Y' AND parent_id = ? ORDER BY id_koment ASC");
    mysqli_stmt_bind_param($stmt, "i", $id_koment);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && mysqli_num_rows($res) > 0) {
        echo '<ul class="children">';
        while ($child = mysqli_fetch_assoc($res)) {
            getComments($child, $koneksi);
        }
        echo '</ul>';
    }
    mysqli_stmt_close($stmt);

    echo '</li>'; // tutup <li class="comment">
}
?>

<!-- ====== BLOK KOMENTAR STATIS (contoh template kamu) ====== -->
<div class="blog-comments">
  <h4 class="comments-count">8 Comments</h4>

  <!-- Contoh komentar statis kamu yang lama tetap boleh ada di sini... -->
  <!-- ... (dipertahankan jika memang dibutuhkan) ... -->

  <div class="reply-form">
    <h4>Leave a Reply</h4>
    <p>Your email address will not be published. Required fields are marked * </p>
    <form action="">
      <div class="row">
        <div class="col-md-6 form-group">
          <input name="name" type="text" class="form-control" placeholder="Your Name*">
        </div>
        <div class="col-md-6 form-group">
          <input name="email" type="text" class="form-control" placeholder="Your Email*">
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <input name="website" type="text" class="form-control" placeholder="Your Website">
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <textarea name="comment" class="form-control" placeholder="Your Comment*"></textarea>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Post Comment</button>
    </form>
  </div>
</div>

<?php
// ====== CONTOH CARA MEMANGGIL DARI KOMENTAR ROOT ======
// ambil semua komentar root (parent_id = 0) lalu render rekursif
$root = mysqli_query($koneksi, "SELECT * FROM comment WHERE status='Y' AND (parent_id IS NULL OR parent_id=0) ORDER BY id_koment ASC");
if ($root && mysqli_num_rows($root) > 0) {
    echo '<ul class="comment-list">';
    while ($row = mysqli_fetch_assoc($root)) {
        getComments($row, $koneksi);
    }
    echo '</ul>';
}
?>
