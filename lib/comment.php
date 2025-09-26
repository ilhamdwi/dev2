<?php
if (!defined('MY_PATH')) {
  define('MY_PATH', '/'); // ganti sesuai base URL situs
}

/**
 * Render satu komentar + anak-anaknya (rekursif)
 * @param array  $row      Baris komentar
 * @param mysqli $koneksi  Koneksi mysqli
 */
function getComments(array $row, mysqli $koneksi): void
{
    $id_koment         = (int)($row['id_koment'] ?? 0);
    $nama_comment      = htmlspecialchars(trim($row['nama_comment'] ?? ''), ENT_QUOTES);
    $url_website_komen = trim($row['url_website_komen'] ?? '');
    $url_website_komen = filter_var($url_website_komen, FILTER_VALIDATE_URL) ? $url_website_komen : '';
    $comment_message   = htmlspecialchars(trim($row['comment'] ?? ''), ENT_QUOTES);
    $date_comment      = htmlspecialchars(trim($row['date_comment'] ?? ''), ENT_QUOTES);

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
          <span class="comment-date link-style1"><?php echo $date_comment; ?></span>
          <a class="comment-reply-link link-style3 reply" href="#respon" onclick="return show();" id="<?php echo $id_koment; ?>">Balas</a>
        </div>

        <div class="comment-body">
          <p><?php echo $comment_message; ?></p>
        </div>
      </div>
    <?php

    // Ambil anak-anak komentar
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM comment WHERE status='Y' AND parent_id = ? ORDER BY id_koment ASC");
    mysqli_stmt_bind_param($stmt, 'i', $id_koment);
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

    echo '</li>';
}
