<?php
/************************************************************
 * GURU & STRUKTUR — pages/struktur.php (3 kolom per baris)
 * - Tiap kolom = 1 jabatan; grid 3 kolom per baris (Bootstrap)
 * - Foto 64x64 bulat; tema hijau–putih
 * - Pencarian client-side (sembunyikan item & kolom kosong)
 ************************************************************/

// ... (Bagian PHP data guru tetap sama) ...
// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan
}

require_once APP_ROOT . '/lib/db.php';

$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? '';
$keyword_web   = $keyword_web   ?? '';
$admin_web     = $admin_web     ?? 'Admin';

/* Folder foto guru (ubah jika beda) */
$GURU_IMG_BASE = 'images/guru';

/* Urutan kolom yang diinginkan (opsional). Sisanya alfabetis. */
$JABATAN_PRIORITY = [
  'Kepala Sekolah'        => 1,
  'Wakasek Kurikulum'     => 2,
  'Wakasek Kesiswaan'     => 3,
  'Wakasek Sardik & Humas'=> 4,
  'Kepala Tata Usaha'     => 5,
];

/* Helper foto */
function guru_photo_url(?string $raw, string $baseRel, string $fallbackUrl): string {
  $raw = trim((string)$raw);
  if ($raw === '') return $fallbackUrl;
  if (preg_match('~^(https?:)?//~i', $raw)) return $raw;
  $rel     = (strpos($raw, '/') !== false) ? $raw : rtrim($baseRel, '/').'/'.$raw;
  $encoded = implode('/', array_map('rawurlencode', explode('/', $rel)));
  // biarkan <img onerror> yang fallback kalau filenya tidak ada
  return MY_PATH . $encoded;
}

/* Ambil data guru */
$groups = []; // [jabatan => [ ['nama'=>..., 'gambar'=>...], ... ] ]
try {
  $stmt = $koneksi->prepare("SELECT jabatan, nama, gambar FROM guru ORDER BY nama ASC");
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) {
    $jabatan = trim((string)$row['jabatan']);
    $nama    = trim((string)$row['nama']);
    $gambar  = (string)($row['gambar'] ?? '');
    if ($jabatan === '') $jabatan = 'Lainnya';
    $groups[$jabatan][] = ['nama' => $nama, 'gambar' => $gambar];
  }
  $stmt->close();
} catch (Throwable $e) {
  $groups = [];
  $db_error = $e->getMessage();
}

/* Urutkan kelompok sesuai prioritas lalu alfabetis */
uksort($groups, function($a, $b) use ($JABATAN_PRIORITY){
  $pa = $JABATAN_PRIORITY[$a] ?? PHP_INT_MAX;
  $pb = $JABATAN_PRIORITY[$b] ?? PHP_INT_MAX;
  if ($pa === $pb) return strcasecmp($a, $b);
  return $pa <=> $pb;
});

/* Inisial (fallback) */
function inisial_nama(string $nama): string {
  $parts = preg_split('~\s+~', trim($nama));
  if (!$parts || $parts[0]==='') return 'NA';
  $f = mb_substr($parts[0], 0, 1, 'UTF-8');
  $s = isset($parts[1]) ? mb_substr($parts[1], 0, 1, 'UTF-8') : '';
  return mb_strtoupper($f.$s, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NEW <?php echo htmlspecialchars($title_web, ENT_QUOTES); ?> — Struktur / Data Guru</title>
<meta name="description" content="<?php echo htmlspecialchars($deskripsi_web, ENT_QUOTES); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($keyword_web, ENT_QUOTES); ?>" />
<meta name="author" content="<?php echo htmlspecialchars($admin_web, ENT_QUOTES); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">

<style>
  /* ... (Bagian Style CSS tetap sama) ... */
  .page-head{
    background:
      radial-gradient(900px 420px at 90% -10%, rgba(134,239,172,.25), transparent 60%),
      #fff;
    border-bottom:1px solid var(--line,#e8f2ec);
  }
  .page-head h1{ font-weight:700; color:#173127 }

  .search-wrap{ background:#f8fffb; border:1px solid #cde7dd; border-radius:.9rem; padding:.75rem }
  .form-control.search{ border:0; background:transparent }

  /* Kartu kolom (jabatan) */
  .group-wrap{ display:block }
  .group-col{
    height:100%;
    display:flex; flex-direction:column;
    background:#f8fffb; border:1px solid #cde7dd; border-radius:1rem;
    box-shadow:0 6px 18px rgba(0,0,0,.06);
  }
  .group-head{
    display:flex; align-items:center; gap:.5rem;
    padding:.75rem .9rem; border-bottom:1px solid #e3f2ea;
  }
  .group-head .dot{
    width:10px; height:10px; border-radius:50%; background:#36b37e; flex-shrink:0;
    box-shadow:0 0 0 3px rgba(54,179,126,.15);
  }
  .group-title{ margin:0; font-weight:700; color:#0a8a55 }

  .group-body{ padding:.6rem .6rem .9rem; flex:1 1 auto }
  .staff{
    display:flex; align-items:center; gap:10px; padding:.5rem .5rem;
    border-radius:.65rem; transition:background .15s ease;
  }
  .staff:hover{ background:#eefaf4 }
  .photo{
    width:64px; height:64px; border-radius:50%; object-fit:cover;
    border:1px solid #cde7dd; background:#fff; flex-shrink:0;
  }
  .avatar{
    width:64px; height:64px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-weight:700; color:#0a5c3f; background:#e7f7ef; border:1px solid #cde7dd; flex-shrink:0;
  }
  .name{ font-weight:700; color:#173127; line-height:1.25; margin:0 }
  .role{ font-size:.86rem; color:#335b4c; opacity:.85; margin:0 }

  .empty-state{ border:1px dashed #cde7dd; background:#f8fffb; border-radius:1rem; padding:1.25rem; color:#446356 }
  .no-result{ display:none; }
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
    <h1 class="mb-1">Data Guru SMAI PB Soedirman 2 Bekasi</h1>
    <p class="text-muted mb-0">Guru Guru Aktif di SMAI PB Soedirman 2 Bekasi</p>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">

    <div class="search-wrap mb-3">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
        <input type="search" class="form-control search" id="searchBox" placeholder="Cari nama guru atau jabatan…">
      </div>
    </div>

    <?php if (isset($db_error)): ?>
      <div class="empty-state mb-3">
        <div class="d-flex align-items-center"><i class="bi bi-exclamation-triangle me-2"></i>
          Terjadi kesalahan mengambil data: <code><?php echo htmlspecialchars($db_error, ENT_QUOTES); ?></code>
        </div>
      </div>
    <?php endif; ?>

    <?php if (empty($groups)): ?>
      <div class="empty-state text-center">
        <i class="bi bi-people fs-4 d-block mb-2"></i>
        <strong>Data guru belum tersedia.</strong><br>
        Tambahkan data pada tabel <code>guru</code>.
      </div>
    <?php else: ?>

      <div class="row g-3" id="colsContainer">
        <?php foreach ($groups as $jabatan => $list): ?>
          <div class="col-12 col-md-6 col-lg-4 group-wrap" data-group="<?php echo htmlspecialchars(mb_strtolower($jabatan,'UTF-8'), ENT_QUOTES); ?>">
            <section class="group-col">
              <header class="group-head">
                <span class="dot" aria-hidden="true"></span>
                <h3 class="group-title h6 mb-0"><?php echo htmlspecialchars($jabatan, ENT_QUOTES); ?></h3>
              </header>
              <div class="group-body">
                <?php foreach ($list as $row): ?>
                  <?php
                    $nama = $row['nama'];
                    $foto = guru_photo_url($row['gambar'] ?? '', $GURU_IMG_BASE, MY_PATH.'img/not-images.png');
                    $needle = mb_strtolower($jabatan.' '.$nama,'UTF-8');
                  ?>
                  <article class="staff staff-item" data-name="<?php echo htmlspecialchars($needle, ENT_QUOTES); ?>">
                    <?php if (!empty($row['gambar'])): ?>
                      <img class="photo"
                              src="<?php echo htmlspecialchars($foto, ENT_QUOTES); ?>"
                              alt="<?php echo htmlspecialchars($nama, ENT_QUOTES); ?>"
                              loading="lazy"
                              onerror="this.onerror=null;this.src='<?php echo MY_PATH; ?>img/not-images.png';"
                              >
                    <?php else: ?>
                      <div class="avatar"><?php echo htmlspecialchars(inisial_nama($nama), ENT_QUOTES); ?></div>
                    <?php endif; ?>
                    <div>
                      <p class="name mb-1"><?php echo htmlspecialchars($nama, ENT_QUOTES); ?></p>
                      <p class="role mb-0"><?php echo htmlspecialchars($jabatan, ENT_QUOTES); ?></p>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="empty-state text-center no-result" id="noResult">
        Tidak ada data yang cocok dengan pencarian Anda.
      </div>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js"></script>

<script>
  // Pencarian: sembunyikan item yang tidak match + sembunyikan kolom (group-wrap) jika kosong
  (function(){
    var box = document.getElementById('searchBox');
    var noRes = document.getElementById('noResult');
    if (!box) return;

    function applyFilter(){
      var q = box.value.trim().toLowerCase();
      var anyShown = false;

      document.querySelectorAll('.group-wrap').forEach(function(col){
        var shownInCol = 0;
        col.querySelectorAll('.staff-item').forEach(function(item){
          var hay = (item.getAttribute('data-name') || '').toLowerCase();
          var hit = (q === '' || hay.indexOf(q) !== -1);
          item.style.display = hit ? '' : 'none';
          if (hit) shownInCol++;
        });
        col.style.display = shownInCol ? '' : 'none';
        if (shownInCol) anyShown = true;
      });

      noRes.style.display = anyShown ? 'none' : '';
    }

    box.addEventListener('input', applyFilter);
    applyFilter();
  })();
</script>
</body>
</html>