<?php
/************************************************************
 * STRUKTUR ORGANISASI — pages/struktur.php (Teks + Penjelasan Kegiatan)
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

/* ========= DATA: Daftar Kegiatan + Penjelasan =========
   - Silakan edit isi array $programs di bawah sesuai kebutuhan sekolah.
   - Setiap kegiatan berisi: name, goal, activities(array), schedule, pic, indicators(array)
*/
$programs = [
  [
    'name'       => 'MPLS (Masa Pengenalan Lingkungan Sekolah)',
    'goal'       => 'Membantu siswa beradaptasi dengan lingkungan sekolah yang baru, mengenal potensi diri, menumbuhkan motivasi dan cara belajar yang efektif, serta mengembangkan interaksi positif dengan warga sekolah',
    'activities' => [
      'Memperkenalkan siswa kepada guru, staf, dan kakak kelas',
      'mengenalkan sarana dan prasarana sekolah',
      'Mengenalkan tata tertib,sistem dan norma yang berlaku di sekolah',
      'Mengenalkan kegiatan EkstraKurikuler di sekolah'
    ],
    'schedule'   => 'Setiap Penerimaan Peserta Didik Baru',
    'pic'        => 'Wakasek Kesiswaan & Pembina OSIS',
    'indicators' => [
      'Siswa Mengenal Guru, staff dan kaka kelas',
      'Siswa dapat mengenal dan merasa nyaman di lingkungan sekolah',
    ],
  ],
  [
    'name'       => 'LDKS (Latihan Dasar Kepemimpinan Siswa)',
    'goal'       => 'Mempersiapkan sumber daya manusia organisasi di sekolah dan mengembangkan potensi kepemimpinan para siswa. ',
    'schedule'   => 'Setahun Sekali, Setiap Penerimaan Siswa Baru',
    'pic'        => 'Kesiswaan & BESS',
    'activities' => [
    ],
    'indicators' => [
      'Bertumbuhnya sifat kepemimpinan yang ada di dalam diri Siswa',
    ],
  ],
  [
    'name'       => 'PerJuSA (Perkemahan Jumat Sabtu)',
    'goal'       => '<empererat hubungan antar siswa dan mengembangkan keterampilan serta kemandirian para siswa dalam menghadapi kehidupan di masa depan.',
    'activities' => [
      'Mengikuti Perlombaan dan Permainan',
      'Menjelajahi Alam',
      'Senam Pagi bersama',
      'Mendapatkan materi dan latihan keterampilan Pramuka',
      'Makan bersama, sholat berjamaah atau bakti lingkungan'
    ],
    'schedule'   => 'Jumat dan Sabtu; Sesuai Kehenda Sekolah',
    'pic'        => 'Pembina Pramuka',
    'indicators' => [
      'Siswa akan lebih mengenal satu sama lain',
      'Tumbuhnya jiwa KORSA ( Kekompakan ) dengan teman temannya'
    ],
  ],
  [
    'name'       => 'MABIT ( Malam Bina Iman dan Taqwa)',
    'goal'       => 'Mpembinaan spiritual bagi siswa dan peserta didik agar memiliki keimanan dan ketaqwaan yang kuat kepada Allah SWT.',
    'activities' => [
        'Sholat Berjamaah',
        'Tilawah dan Tadarus',
        'Dzikir dan Doa',
        'Qiyamulail',
        'Muhasabah',
    ],
    'schedule'   => 'Event Sekolah',
    'pic'        => 'Pembina Rohis & Rohis',
    'indicators' => [
      'Siswa lebih dekat dengan Allah SWT',
      'Lebih menghargai satu sama lain'
    ],
  ],
  [
    'name'       => 'PangFEST ( PAngsoed Festival)',
    'goal'       => 'Mempromosikan Sekolah SMAI PB Soedirman 2 Bekasi kepada siswa siswa yang mengikuti perlombaan.',
    'activities' => [
      'Perlombaan Futsal',
      'Perlombaan MTQ & Adzan',
      'Pelombaan Speech Contest',
    ],
    'schedule'   => 'Peringantan MILAD SMAI PB Soedirman 2 Bekasi',
    'pic'        => 'BESS & Pembina BESS',
    'indicators' => [
      'Keterlibatan siswa lintas kelas meningkat',
      'Program sosial keagamaan terselenggara periodik',
    ],
  ],
  
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
  .act-body .label{ font-weight:600; min-width:140px; color:#214638 }
  .act-body ul{ margin:0; padding-left:1.1rem }
  .act-body li{ margin-bottom:.25rem }
  .stack-gap{ row-gap:1rem; }
</style>
</head>
<body>

<?php include APP_ROOT . "/lib/header.php"; ?>

<section class="page-head py-4">
  <div class="container container-narrow">
    <h1 class="mb-2">Kegiatan SMAI PB Soedirman 2</h1>
    <p class="lead-desc mb-0">
      Berikut daftar kegiatan kesiswaan beserta tujuan, bentuk kegiatan, jadwal pelaksanaan, penanggung jawab, dan indikator keberhasilannya.
    </p>
  </div>
</section>

<section class="py-4">
  <div class="container container-narrow">
    <div class="d-grid stack-gap">
      <?php foreach ($programs as $i => $p): ?>
        <article class="act-card">
          <div class="act-head">
            <span class="badge text-white"><?php echo str_pad((string)($i+1), 2, '0', STR_PAD_LEFT); ?></span>
            <h2 class="act-title"><?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?></h2>
          </div>
          <div class="act-body">
            <div class="row">
              <div class="col-md-3 label">Tujuan</div>
              <div class="col-md-9"><?php echo htmlspecialchars($p['goal'], ENT_QUOTES); ?></div>
            </div>
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
            <div class="row">
              <div class="col-md-3 label">Jadwal</div>
              <div class="col-md-9"><?php echo htmlspecialchars($p['schedule'], ENT_QUOTES); ?></div>
            </div>
            <div class="row">
              <div class="col-md-3 label">Penanggung Jawab</div>
              <div class="col-md-9"><?php echo htmlspecialchars($p['pic'], ENT_QUOTES); ?></div>
            </div>
            <div class="row">
              <div class="col-md-3 label">Indikator</div>
              <div class="col-md-9">
                <ul>
                  <?php foreach ($p['indicators'] as $ind): ?>
                    <li><?php echo htmlspecialchars($ind, ENT_QUOTES); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include APP_ROOT . "/lib/footer.php"; ?>

<!-- JS global (opsional, tidak wajib untuk halaman statis ini) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js" defer></script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
