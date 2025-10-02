<?php
// =============================
// Konfigurasi dasar & koneksi DB
// =============================
date_default_timezone_set("Asia/Jakarta");

// Jika sudah punya file koneksi sendiri, cukup include ini dan HAPUS blok mysqli_connect di bawah.
include 'db.php';

// Jika ada base path situs
if (!defined('MY_PATH')) {
    define('MY_PATH', '/'); // contoh: '/dev2/' atau 'https://domainanda/'
}

// =============================
// Variabel statistik
// =============================
$all_hits = 0;
$today = date('Y-m-d');
$yesterday = date("Y-m-d", strtotime("-1 day"));

/**
 * Helper: ambil single value dari query prepared
 */
function fetch_single_value(mysqli $conn, string $sql, array $params = [], string $types = '') {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    if ($params) {
        if ($types === '') {
            // auto-deduce types (sederhana: semua string)
            $types = str_repeat('s', count($params));
        }
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        return null;
    }

    $res = $stmt->get_result();
    $val = null;
    if ($res && $row = $res->fetch_row()) {
        $val = $row[0];
    }
    $stmt->close();
    return $val;
}

/**
 * Helper: jalankan prepared statement tanpa result
 */
function exec_stmt(mysqli $conn, string $sql, array $params = [], string $types = ''): bool {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    if ($params) {
        if ($types === '') {
            $types = str_repeat('s', count($params));
        }
        $stmt->bind_param($types, ...$params);
    }

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// =============================
// Cek/insert/update statistik harian ($today)
// =============================
// Ambil hits hari ini
$hits_today = fetch_single_value(
    $koneksi,
    "SELECT hits FROM statistik WHERE date = ? LIMIT 1",
    [$today]
);

// Jika belum ada baris untuk hari ini, insert
if ($hits_today === null) {
    exec_stmt(
        $koneksi,
        // Sesuaikan kolom/urutan kolom sesuai tabel Anda
        "INSERT INTO statistik (date, hits, online) VALUES (?, 1, 1)",
        [$today]
    );
    $hits_today = 1;
} else {
    // Update hits++ dan set online = 1
    exec_stmt(
        $koneksi,
        "UPDATE statistik SET hits = hits + 1, online = 1 WHERE date = ?",
        [$today]
    );
    // Ambil lagi nilai terbaru setelah increment
    $hits_today = fetch_single_value(
        $koneksi,
        "SELECT hits FROM statistik WHERE date = ? LIMIT 1",
        [$today]
    );
}

// =============================
// Total hits (all time)
// =============================
$all_hits = fetch_single_value(
    $koneksi,
    "SELECT COALESCE(SUM(hits), 0) FROM statistik"
);
if ($all_hits === null) $all_hits = 0;

// =============================
// Hits kemarin
// =============================
$hits_yesterday = fetch_single_value(
    $koneksi,
    "SELECT hits FROM statistik WHERE date = ? LIMIT 1",
    [$yesterday]
);
if ($hits_yesterday === null) $hits_yesterday = 0;

// =============================
// Deteksi browser & update tabel browser
// =============================
function getBrowserInfo(): array
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = '';

    if (preg_match('/linux/i', $u_agent))      $platform = 'linux';
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) $platform = 'mac';
    elseif (preg_match('/windows|win32/i', $u_agent))      $platform = 'windows';

    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer'; $ub = 'MSIE';
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';    $ub = 'Firefox';
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';      $ub = 'Chrome';
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';       $ub = 'Safari';
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';              $ub = 'Opera';
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';           $ub = 'Netscape';
    } else {
        $ub = 'other';
    }

    $known = ['Version', $ub, 'other'];
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        $version = '';
    } else {
        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
    }
    if ($version === null || $version === '') $version = '?';

    return [
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform
    ];
}

$ua = getBrowserInfo();
$my_browser = strtolower($ua['name'] . ' ' . $ua['version']);

if (strpos($my_browser, 'firefox') !== false)      $input_ke_browser = 'Firefox';
elseif (strpos($my_browser, 'chrome') !== false)   $input_ke_browser = 'Chrome';
elseif (strpos($my_browser, 'opera') !== false)    $input_ke_browser = 'Opera';
elseif (strpos($my_browser, 'ie') !== false)       $input_ke_browser = 'IE';
elseif (strpos($my_browser, 'safari') !== false)   $input_ke_browser = 'Safari';
else                                               $input_ke_browser = 'Others';

// Update hits browser; jika tidak ada barisnya, insert
exec_stmt(
    $koneksi,
    "UPDATE browser SET hits = hits + 1 WHERE name = ?",
    [$input_ke_browser]
);
if (mysqli_affected_rows($koneksi) === 0) {
    // coba insert (asumsikan name UNIQUE)
    exec_stmt(
        $koneksi,
        "INSERT INTO browser (name, hits) VALUES (?, 1)",
        [$input_ke_browser]
    );
}

// =============================
// Ambil recent posts
// =============================
$recent_posts = [];
if ($stmt = $koneksi->prepare("SELECT title, link_article FROM article ORDER BY id_article DESC LIMIT 5")) {
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $recent_posts[] = [
                'title' => strip_tags($row['title']),
                'link'  => MY_PATH . 'post/' . strip_tags($row['link_article']) . '.html'
            ];
        }
    }
    $stmt->close();
}

// =============================
// TAMPILAN FOOTER
// =============================
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<footer id="footer" class="site-footer">

    <div class="footer-top">
      <div class="container">
        <div class="row">

          <!-- PPDB -->
          <div class="col-lg-3 col-md-6 footer-links">
            <h4>PPDB 2025</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="pages/alur_pendaftaran">Alur Pendaftaran</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="pages/biaya.php">Rincian Biaya</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="https://docs.google.com/forms/d/e/1FAIpQLSfD5JVIOOWf0HgZSksjunrxVtFpz3m4atpfNFDjVKiD589GrA/viewform">Formulir Pendaftaran Siswa Baru</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="https://drive.google.com/drive/u/0/folders/1S0Q3ixgAnlfA5oi3-IEJoYK2-huUIj1X">Cetak Formulir Pendaftaran</a></li>
            </ul>
          </div>

          <!-- Statistik -->
          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Statistik Web</h4>
            <ul>
              <li>
                <i class="bx bx-chevron-right"></i>
                <a href="#">
                  Total Page Halaman:
                  <small class="rp_num">
                    <?php
                    $min_digits = 0;
                    $count = (string) $all_hits;
                    $count = sprintf('%0' . $min_digits . 's', $count);
                    $len = strlen($count);
                    for ($i = 0; $i < $len; $i++) {
                        echo ''. substr($count, $i, 1) . '';
                    }
                    ?>
                  </small>
                </a>
              </li>
              <li>
                <i class="bx bx-chevron-right"></i>
                <a href="#">
                  Halaman hari ini:
                  <small class="rp_num">
                    <?php
                    $count = (string) $hits_today;
                    $count = sprintf('%0' . $min_digits . 's', $count);
                    $len = strlen($count);
                    for ($i = 0; $i < $len; $i++) {
                        echo ''. substr($count, $i, 1) .'';
                    }
                    ?>
                  </small>
                </a>
              </li>
              <li>
                <i class="bx bx-chevron-right"></i>
                <a href="#">
                  Halaman kemarin:
                  <small class="rp_num">
                    <?php
                    $count = (string) $hits_yesterday;
                    $count = sprintf('%0' . $min_digits . 's', $count);
                    $len = strlen($count);
                    for ($i = 0; $i < $len; $i++) {
                        echo ''. substr($count, $i, 1) .'';
                    }
                    ?>
                  </small>
                </a>
              </li>
            </ul>
          </div>

          <!-- Recent Post -->
          <div class="col-lg-3 col-md-6 footer-contact">
            <h4>Recent Post</h4>
            <ul>
                <?php if (!empty($recent_posts)): ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <li><a href="<?php echo htmlspecialchars($post['link'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </a></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><em>Belum ada posting.</em></li>
                <?php endif; ?>
            </ul>
          </div>

          <!-- Sosial Media -->
          <div class="col-lg-3 col-md-6 footer-info">
            <h3>Sosial Media</h3>
            <p>Follow account sosial media Pangsoed untuk update informasi terbaru</p>
            <!-- <div class="social-links mt-3">
              <a href="https://twitter.com/pangsoed2bekasi?s=09" class="twitter" target="_blank" rel="noopener"><i class="bx bxl-twitter"></i></a>
              <a href="https://www.facebook.com/pangsoed.pangsoed" class="facebook" target="_blank" rel="noopener"><i class="bx bxl-facebook"></i></a>
              <a href="https://www.instagram.com/pangsoed2bekasi/" class="instagram" target="_blank" rel="noopener"><i class="bx bxl-instagram"></i></a>
              <a href="https://api.whatsapp.com/send/?phone=6282122241232&text&app_absent=0" class="whatsapp" target="_blank" rel="noopener"><i class="bx bxl-whatsapp"></i></a>
              <a href="https://www.youtube.com/channel/UCEmqrO0cYsrUoD21QvgZg7w" target="_blank" class="youtube" rel="noopener"><i class="bx bxl-youtube"></i></a>
            </div> -->
            <div class="social-group">
              <a href="https://www.facebook.com/pangsoed.pangsoed" class="social-icon"><i class="bi bi-facebook"></i></a>
              <a href="https://www.youtube.com/channel/UCEmqrO0cYsrUoD21QvgZg7w" class="social-icon"><i class="bi bi-youtube"></i></a>
              <a href="https://twitter.com/pangsoed2bekasi?s=09" class="social-icon"><i class="bi bi-twitter"></i></a>
              <a href="https://www.instagram.com/pangsoed2bekasi/" class="social-icon"><i class="bi bi-instagram"></i></a>
            </div>
           </div>
          </div>

        </div>
      </div>
    </div>

    <div class="container">
        <div class="copyright">
            <span class="copy-text">
            &copy; Copyright <strong>Nugro Company</strong>. All Rights Reserved
            </span>
        </div>
    </div>
</footer>
