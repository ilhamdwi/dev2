<?php
/************************************************************
 * STRUKTUR ORGANISASI — pages/struktur.php
 * Sumber data: tabel ekstrakurikuler
 * Field: id, category, title, tujuan, activity, jadwal, pic, keberhasilan
 * Tampilan: dipisahkan per category (wajib, pilihan, osis)
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}

// Koneksi DB & meta dasar
require_once APP_ROOT . '/lib/db.php';

// Fallback meta
$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? 'Kesiswaan dan Kegiatan Siswa';
$keyword_web   = $keyword_web   ?? 'kesiswaan, kegiatan, ekstrakurikuler';
$admin_web     = $admin_web     ?? 'Admin';

/* ==========================================================
   HELPER FOTO
   - find_image_for_item(): cari file di /images, /images/images, /img
     nama file:
       a) slug dari title (contoh: "Pramuka" -> pramuka.jpg|png|webp|gif)
       b) ATAU kalau Anda nanti menambahkan kolom gambar (mis. `img`)
          cukup panggil find_image_for_item($title, $row['img'])
   ========================================================== */
function slugify_for_file($text) {
  $orig = $text ?? '';
  if (function_exists('iconv')) {
    $conv = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = ($conv !== false) ? $conv : $orig;
  }
  $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = strtolower($text);
  $text = preg_replace('~[^-a-z0-9]+~', '', $text);
  return $text ?: 'img';
}

function find_image_for_item($name, $explicit = null) {
  // Normalisasi path eksplisit (opsional)
  $normalize = function($p) {
    $p = trim((string)$p);
    $p = ltrim($p, '/');
    $p = preg_replace('~^(images|img)/+~', '', $p);
    return $p;
  };

  $candidates = [];

  // Jika eksplisit disediakan (mis. "pramuka.jpg" atau "folder/pramuka.png")
  if (!empty($explicit)) {
    $candidates[] = $normalize($explicit);
  }

  // Turunan dari judul
  $base = slugify_for_file($name);
  $alts = [$base, str_replace('-', '_', $base)];
  $exts = ['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'];
  foreach ($alts as $a) {
    foreach ($exts as $e) $candidates[] = "{$a}.{$e}";
  }

  // Lokasi pencarian
  $dirs = ['images', 'images/images', 'img'];

  foreach ($candidates as $file) {
    foreach ($dirs as $dir) {
      $abs = APP_ROOT . "/{$dir}/" . $file;
      if (file_exists($abs)) {
        // URL relatif hormati <base href=MY_PATH>
        return "{$dir}/" . rawurlencode($file);
      }
    }
  }
  return null; // tidak ketemu → kartu tanpa gambar
}

/* ==========================================================
   AMBIL DATA DARI DB
   - Group by category di PHP, urut kategori: wajib, pilihan, osis
   - activity & keberhasilan dipecah jadi list
   ========================================================== */
$byCat = [
  'wajib'   => [],
  'pilihan' => [],
  'osis'    => [],
];
$rawCats = []; // untuk kategori non-standar bila ada

$sql = "SELECT id, category, title, tujuan, activity, jadwal, pic, keberhasilan
        FROM ekstrakurikuler
        ORDER BY FIELD(LOWER(category),'wajib','pilihan','osis'), title";
$res = mysqli_query($koneksi, $sql);

if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    $catKey = strtolower(trim($row['category'] ?? ''));
    if ($catKey === '') $catKey = 'lainnya';

    // Pecah list activity/keberhasilan
    $split_to_list = function($text) {
      $text = (string)$text;
      if ($text === '') return [];
      // dukung pemisah baris baru, ; dan |
      $parts = preg_split('~[\r\n;|]+~', $text);
      $parts = array_map('trim', $parts);
      return array_values(array_filter($parts, fn($v) => $v !== ''));
    };

    $item = [
      'id'          => (int)$row['id'],
      'title'       => $row['title'] ?? '',
      'tujuan'      => $row['tujuan'] ?? '',
      'activities'  => $split_to_list($row['activity'] ?? ''),
      'jadwal'      => $row['jadwal'] ?? '',
      'pic'         => $row['pic'] ?? '',
      'indikator'   => $split_to_list($row['keberhasilan'] ?? ''),
      // Jika kelak ada kolom `img`, tinggal pasang di sini:
      // 'img'      => $row['img'] ?? null,
    ];

    if (isset($byCat[$catKey])) {
      $byCat[$catKey][] = $item;
    } else {
      if (!isset($rawCats[$catKey])) $rawCats[$catKey] = [];
      $rawCats[$catKey][] = $item;
    }
  }
  mysqli_free_result($res);
} else {
  // Jika query gagal, Anda bisa echo error bila debugging:
  // echo mysqli_error($koneksi);
}

/* Label kategori untuk heading */
$catLabels = [
  'wajib'   => 'Ekstrakurikuler Wajib',
  'pilihan' => 'Ekstrakurikuler Pilihan',
  'osis'    => 'BESS / OSIS',
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — Kesiswaan & Kegiatan</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<!-- Base untuk path relatif -->
<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root{
    --ink:#173127;
    --soft:#f8fffb;
    --line:#cde7dd;
    --accent:#73c0a4;
  }
  .page-head {
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #ffffff;
    border-bottom: 1px solid var(--line);
  }
  .page-head h1 { font-weight: 700; color: var(--ink); }
  .lead-desc{ color:#436257 }

  /* Section heading */
  .sec-head{
    display:flex; align-items:baseline; justify-content:space-between;
    margin: 1.25rem 0 .75rem 0;
  }
  .sec-title{
    margin:0; font-size:1.25rem; font-weight:800; color:var(--ink);
  }
  .sec-desc{ margin:0; color:#436257; font-size:.95rem }

  /* Kartu kegiatan */
  .act-card{
    background: var(--soft);
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .act-card:hover{ transform:translateY(-3px); box-shadow:0 12px 26px rgba(0,0,0,.08) }

  /* Media image */
  .act-media{ width:100%; aspect-ratio: 16/9; background:#f3f6f5; overflow:hidden }
  .act-media img{ width:100%; height:100%; object-fit:cover; display:block }
  @media (min-width: 992px){
    .act-media{ aspect-ratio: 21/9; }
  }

  .act-head{
    padding:1rem 1.25rem;
    border-bottom:1px dashed var(--line);
    display:flex; align-items:center; gap:.6rem;
  }
  .act-head .badge{
    background:var(--accent);
  }
  .act-title{
    margin:0; font-weight:700; color:var(--ink); font-size:1.125rem;
  }
  .act-body{ padding:1rem 1.25rem; color:#173127 }
  .act-body .row + .row{ margin-top:.75rem }
  .act-body .label{ font-weight:600; min-width:160px; color:#214638 }
  .act-body ul{ margin:0; padding-left:1.1rem }
  .act-body li{ margin-bottom:.25rem }

  .stack-gap{ row-gap:1rem; }
  .sec-gap{ margin-top:1.25rem; }
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
    <h1 class="mb-2">Ekstrakurikuler SMAI PB Soedirman 2 Bekasi</h1>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">

    <?php
    // Urutan kategori utama
    foreach (['wajib','pilihan','osis'] as $cat) :
      $items = $byCat[$cat];
      if (!empty($items)) :
    ?>
      <div class="sec-gap" id="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>">
        <div class="sec-head">
          <h2 class="sec-title"><?php echo htmlspecialchars($catLabels[$cat] ?? strtoupper($cat), ENT_QUOTES); ?></h2>
        </div>

        <div class="d-grid stack-gap mt-3">
          <?php foreach ($items as $i => $p): 
            $imgWeb = find_image_for_item($p['title'] /*, $p['img'] ?? null */);
          ?>
            <article class="act-card">
              <?php if ($imgWeb): ?>
                <div class="act-media">
                  <img src="<?php echo htmlspecialchars($imgWeb, ENT_QUOTES); ?>"
                       alt="<?php echo htmlspecialchars('Foto ' . $p['title'], ENT_QUOTES); ?>">
                </div>
              <?php endif; ?>

              <div class="act-head">
                <span class="badge text-white">
                  <?php echo str_pad((string)($i+1), 2, '0', STR_PAD_LEFT); ?>
                </span>
                <h3 class="act-title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?></h3>
              </div>

              <div class="act-body">
                <?php if ($p['tujuan'] !== ''): ?>
                <div class="row">
                  <div class="col-md-3 label">Tujuan</div>
                  <div class="col-md-9"><?php echo htmlspecialchars($p['tujuan'], ENT_QUOTES); ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($p['activities'])): ?>
                <div class="row">
                  <div class="col-md-3 label">Bentuk Kegiatan</div>
                  <div class="col-md-9">
                    <ul>
                      <?php foreach ($p['activities'] as $a): ?>
                        <li><?php echo htmlspecialchars($a, ENT_QUOTES); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
                <?php endif; ?>

                <?php if ($p['jadwal'] !== ''): ?>
                <div class="row">
                  <div class="col-md-3 label">Jadwal</div>
                  <div class="col-md-9"><?php echo htmlspecialchars($p['jadwal'], ENT_QUOTES); ?></div>
                </div>
                <?php endif; ?>

                <?php if ($p['pic'] !== ''): ?>
                <div class="row">
                  <div class="col-md-3 label">Penanggung Jawab</div>
                  <div class="col-md-9"><?php echo htmlspecialchars($p['pic'], ENT_QUOTES); ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($p['indikator'])): ?>
                <div class="row">
                  <div class="col-md-3 label">Indikator/Keberhasilan</div>
                  <div class="col-md-9">
                    <ul>
                      <?php foreach ($p['indikator'] as $ind): ?>
                        <li><?php echo htmlspecialchars($ind, ENT_QUOTES); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php
      endif;
    endforeach;

    // Jika ada kategori lain di DB, render juga di bawah
    if (!empty($rawCats)) :
      foreach ($rawCats as $cat => $items) :
    ?>
      <div class="sec-gap" id="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>">
        <div class="sec-head">
          <h2 class="sec-title"><?php echo htmlspecialchars(ucwords($cat), ENT_QUOTES); ?></h2>
          <p class="sec-desc">Kategori tambahan dari basis data.</p>
        </div>
        <div class="d-grid stack-gap mt-3">
          <?php foreach ($items as $i => $p): 
            $imgWeb = find_image_for_item($p['title'] /*, $p['img'] ?? null */);
          ?>
            <article class="act-card">
              <?php if ($imgWeb): ?>
                <div class="act-media">
                  <img src="<?php echo htmlspecialchars($imgWeb, ENT_QUOTES); ?>"
                       alt="<?php echo htmlspecialchars('Foto ' . $p['title'], ENT_QUOTES); ?>">
                </div>
              <?php endif; ?>
              <div class="act-head">
                <span class="badge text-white"><?php echo str_pad((string)($i+1), 2, '0', STR_PAD_LEFT); ?></span>
                <h3 class="act-title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?></h3>
              </div>
              <div class="act-body">
                <?php if ($p['tujuan'] !== ''): ?>
                  <div class="row"><div class="col-md-3 label">Tujuan</div><div class="col-md-9"><?php echo htmlspecialchars($p['tujuan'], ENT_QUOTES); ?></div></div>
                <?php endif; ?>
                <?php if (!empty($p['activities'])): ?>
                  <div class="row"><div class="col-md-3 label">Bentuk Kegiatan</div><div class="col-md-9"><ul>
                    <?php foreach ($p['activities'] as $a): ?><li><?php echo htmlspecialchars($a, ENT_QUOTES); ?></li><?php endforeach; ?>
                  </ul></div></div>
                <?php endif; ?>
                <?php if ($p['jadwal'] !== ''): ?>
                  <div class="row"><div class="col-md-3 label">Jadwal</div><div class="col-md-9"><?php echo htmlspecialchars($p['jadwal'], ENT_QUOTES); ?></div></div>
                <?php endif; ?>
                <?php if ($p['pic'] !== ''): ?>
                  <div class="row"><div class="col-md-3 label">Penanggung Jawab</div><div class="col-md-9"><?php echo htmlspecialchars($p['pic'], ENT_QUOTES); ?></div></div>
                <?php endif; ?>
                <?php if (!empty($p['indikator'])): ?>
                  <div class="row"><div class="col-md-3 label">Indikator/Keberhasilan</div><div class="col-md-9"><ul>
                    <?php foreach ($p['indikator'] as $ind): ?><li><?php echo htmlspecialchars($ind, ENT_QUOTES); ?></li><?php endforeach; ?>
                  </ul></div></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php
      endforeach;
    endif;

    // Jika semuanya kosong
    if (empty($byCat['wajib']) && empty($byCat['pilihan']) && empty($byCat['osis']) && empty($rawCats)) :
    ?>
      <div class="alert alert-warning mt-3">Belum ada data ekstrakurikuler di basis data.</div>
    <?php endif; ?>

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

<!-- JS global -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js" defer></script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
