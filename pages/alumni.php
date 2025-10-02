<?php
/************************************************************
 * DATA ALUMNI — pages/data_alumni.php
 * Sumber data: tabel `alumni`
 * Kolom tampil: nama, jurusan, jenis_kelamin, tempat_lahir, tanggal_lahir,
 *               tahun_lulus, status, nama_instansi
 * Tambahan:
 *  - Diagram Donat (diperkecil) + selector "Tahun untuk Donut"
 *  - Tren per Tahun (Line chart) Top-6 jurusan + "Lainnya"
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

// Meta fallback
$title_web     = $title_web     ?? 'Situs Sekolah';
$deskripsi_web = $deskripsi_web ?? 'Data Alumni';
$keyword_web   = $keyword_web   ?? 'alumni, data alumni';
$admin_web     = $admin_web     ?? 'Admin';

/* ---------------- Helper kecil ---------------- */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function indo_date($dateStr){
  if (!$dateStr) return '';
  $ts = strtotime($dateStr);
  if (!$ts) return h($dateStr);
  $bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  $d = (int)date('j', $ts);
  $m = (int)date('n', $ts);
  $y = date('Y', $ts);
  return $d.' '.$bulan[$m].' '.$y;
}

/* ---------------- Filter & Pagination (untuk tabel) ---------------- */
$q        = trim($_GET['q'] ?? '');
$jurusan  = trim($_GET['jurusan'] ?? '');
$tahun    = trim($_GET['tahun'] ?? '');
$status   = trim($_GET['status'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = min(50, max(5, (int)($_GET['per_page'] ?? 10)));
$offset   = ($page - 1) * $per_page;

/* ---------------- Tahun khusus Donut (tidak mempengaruhi tabel) ---- */
$donut_year = trim($_GET['donut_year'] ?? ''); // "" = semua tahun (agregat)

$conds = [];
$params = [];
$types  = '';

if ($q !== '') {
  $conds[] = "(nama LIKE CONCAT('%', ?, '%') OR nama_instansi LIKE CONCAT('%', ?, '%'))";
  $params[] = $q; $params[] = $q; $types .= 'ss';
}
if ($jurusan !== '') {
  $conds[] = "jurusan = ?";
  $params[] = $jurusan; $types .= 's';
}
if ($tahun !== '') {
  $conds[] = "tahun_lulus = ?";
  $params[] = $tahun; $types .= 's';
}
if ($status !== '') {
  $conds[] = "status = ?";
  $params[] = $status; $types .= 's';
}

$where = $conds ? ('WHERE '.implode(' AND ', $conds)) : '';

/* ---------------- Total rows (tabel) ---------------- */
$sql_cnt = "SELECT COUNT(*) AS total FROM alumni $where";
$stmt_cnt = mysqli_prepare($koneksi, $sql_cnt);
if ($params) mysqli_stmt_bind_param($stmt_cnt, $types, ...$params);
mysqli_stmt_execute($stmt_cnt);
$res_cnt = mysqli_stmt_get_result($stmt_cnt);
$row_cnt = mysqli_fetch_assoc($res_cnt);
$total   = (int)($row_cnt['total'] ?? 0);
mysqli_free_result($res_cnt);
mysqli_stmt_close($stmt_cnt);

/* ---------------- Data rows (tabel) ---------------- */
$sql = "SELECT nama, jurusan, jenis_kelamin, tempat_lahir, tanggal_lahir, tahun_lulus, status, nama_instansi
        FROM alumni
        $where
        ORDER BY tahun_lulus DESC, nama ASC
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($koneksi, $sql);
$p2 = $params; $t2 = $types . 'ii';
$p2[] = $per_page; $p2[] = $offset;
mysqli_stmt_bind_param($stmt, $t2, ...$p2);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;

mysqli_free_result($res);
mysqli_stmt_close($stmt);

/* ---------------- Data untuk dropdown (opsional) ---------------- */
$opts = ['jurusan'=>[], 'tahun'=>[], 'status'=>[]];

$qr1 = mysqli_query($koneksi, "SELECT DISTINCT jurusan FROM alumni WHERE jurusan<>'' ORDER BY jurusan");
while ($r = mysqli_fetch_row($qr1)) $opts['jurusan'][] = $r[0];
mysqli_free_result($qr1);

$qr2 = mysqli_query($koneksi, "SELECT DISTINCT tahun_lulus FROM alumni WHERE tahun_lulus<>'' ORDER BY tahun_lulus DESC");
while ($r = mysqli_fetch_row($qr2)) $opts['tahun'][] = $r[0];
mysqli_free_result($qr2);

$qr3 = mysqli_query($koneksi, "SELECT DISTINCT status FROM alumni WHERE status<>'' ORDER BY status");
while ($r = mysqli_fetch_row($qr3)) $opts['status'][] = $r[0];
mysqli_free_result($qr3);

/* ---------------- Diagram Donat (diperkecil) ----------------
   - Mengikuti filter q/jurusan/tahun/status
   - Jika donut_year diisi, donut hanya untuk tahun tsb
---------------------------------------------------------------- */
$conds_pie   = $conds;
$params_pie  = $params;
$types_pie   = $types;

if ($donut_year !== '') {
  $conds_pie[]  = "tahun_lulus = ?";
  $params_pie[] = $donut_year;
  $types_pie   .= 's';
}
$where_pie = $conds_pie ? ('WHERE '.implode(' AND ', $conds_pie)) : '';

$sql_pie = "
  SELECT
    CASE WHEN jurusan IS NULL OR jurusan='' THEN 'Tidak Tercantum' ELSE jurusan END AS jurusan_label,
    COUNT(*) AS jml
  FROM alumni
  $where_pie
  GROUP BY jurusan_label
  ORDER BY jml DESC
";
$stmt_p = mysqli_prepare($koneksi, $sql_pie);
if ($params_pie) mysqli_stmt_bind_param($stmt_p, $types_pie, ...$params_pie);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);

$pie_labels = [];
$pie_counts = [];
$pie_total  = 0;
while ($rp = mysqli_fetch_assoc($res_p)) {
  $pie_labels[] = $rp['jurusan_label'];
  $pie_counts[] = (int)$rp['jml'];
  $pie_total   += (int)$rp['jml'];
}
mysqli_free_result($res_p);
mysqli_stmt_close($stmt_p);

/* ---------------- Tren per Tahun (Line chart) ----------------
   - Mengikuti filter q/jurusan/tahun/status
   - Ambil jumlah lulusan per tahun per jurusan
---------------------------------------------------------------- */
$where_trend = $where; // sama dengan filter tabel (tanpa donut_year)
$sql_trend = "
  SELECT
    tahun_lulus,
    CASE WHEN jurusan IS NULL OR jurusan='' THEN 'Tidak Tercantum' ELSE jurusan END AS jurusan_label,
    COUNT(*) AS jml
  FROM alumni
  $where_trend
  GROUP BY tahun_lulus, jurusan_label
  ORDER BY tahun_lulus ASC
";
$res_t = mysqli_query($koneksi, $sql_trend);

$years_set = [];
$jur_map   = [];  // jurusan => [year=>count]
$tot_per_j = [];  // total per jurusan (untuk top-N)

while ($rt = mysqli_fetch_assoc($res_t)) {
  $y   = (string)$rt['tahun_lulus'];
  $jl  = (string)$rt['jurusan_label'];
  $cnt = (int)$rt['jml'];
  if ($y === '') continue;

  $years_set[$y] = true;
  if (!isset($jur_map[$jl])) $jur_map[$jl] = [];
  $jur_map[$jl][$y] = ($jur_map[$jl][$y] ?? 0) + $cnt;
  $tot_per_j[$jl] = ($tot_per_j[$jl] ?? 0) + $cnt;
}
mysqli_free_result($res_t);

// urut tahun ASC
$trend_years = array_keys($years_set);
sort($trend_years, SORT_NATURAL);

// batasi dataset: Top-6 jurusan
arsort($tot_per_j); // desc
$MAX_SERIES = 6;
$top_jur   = array_slice(array_keys($tot_per_j), 0, $MAX_SERIES, true);
$has_other = count($tot_per_j) > $MAX_SERIES;

// siapkan series final
$series = []; // list of [label, data[] per tahun]
foreach ($top_jur as $jl) {
  $row = [];
  foreach ($trend_years as $y) $row[] = (int)($jur_map[$jl][$y] ?? 0);
  $series[] = ['label'=>$jl, 'data'=>$row];
}
if ($has_other) {
  // hitung "Lainnya"
  $row = array_fill(0, count($trend_years), 0);
  foreach ($jur_map as $jl => $m) {
    if (in_array($jl, $top_jur, true)) continue;
    foreach ($trend_years as $idx => $y) {
      $row[$idx] += (int)($m[$y] ?? 0);
    }
  }
  $series[] = ['label' => 'Lainnya', 'data' => $row];
}

// helper build_url
$total_pages = max(1, (int)ceil($total / $per_page));
function build_url($override = []){
  $base = $_GET;
  foreach ($override as $k=>$v){
    if ($v === null) unset($base[$k]); else $base[$k] = $v;
  }
  return '?'.http_build_query($base);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo h($title_web); ?> — Data Alumni</title>
<meta name="description" content="<?php echo h($deskripsi_web); ?>" />
<meta name="keywords" content="<?php echo h($keyword_web); ?>" />
<meta name="author" content="<?php echo h($admin_web); ?>" />

<base href="<?php echo MY_PATH; ?>">

<?php include APP_ROOT . "/lib/meta_tag.php"; ?>
<?php include APP_ROOT . "/head.php"; ?>

<link href="<?php echo MY_PATH; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .page-head{border-bottom:1px solid #e6eef0;background:#fff}
  .page-head h1{font-weight:700}
  .filter-card{background:#f8fafb;border:1px solid #e6eef0;border-radius:.75rem;padding:1rem}
  .donut-card,.trend-card{background:#fff;border:1px solid #e6eef0;border-radius:.75rem}
  .donut-card .card-header,.trend-card .card-header{background:#f6f9fb;border-bottom:1px solid #e6eef0;font-weight:600}
  .table-responsive{border:1px solid #e6eef0;border-radius:.5rem;background:#fff}
  /* Perkecil donut */
  .donut-wrap{max-width: 420px; margin: 0 auto;}
  /* Chart container agar tinggi konsisten */
  .chart-fixed-h{position: relative; height: 180px;} /* donut kecil */
  .chart-trend-h{position: relative; height: 220px;} /* line trend */
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
  <div class="container">
    <h1 class="mb-1">Data Alumni</h1>
    <p class="text-muted mb-0">Diagram donat diperkecil & bisa dipilih per tahun. Grafik tren menunjukkan naik/turun per tahun berdasarkan filter aktif.</p>
  </div>
</section>

<section class="py-3">
  <div class="container">

    <!-- Filter Tabel -->
    <form class="filter-card mb-3" method="get" action="">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Cari (Nama / Instansi)</label>
          <input type="text" class="form-control" name="q" value="<?php echo h($q); ?>" placeholder="cth: Fulan / PT Maju">
        </div>
        <div class="col-md-3">
          <label class="form-label">Jurusan</label>
          <select class="form-select" name="jurusan">
            <option value="">Semua</option>
            <?php foreach ($opts['jurusan'] as $opt): ?>
              <option value="<?php echo h($opt); ?>" <?php if($opt===$jurusan) echo 'selected'; ?>>
                <?php echo h($opt); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Tahun Lulus (Filter Tabel)</label>
          <select class="form-select" name="tahun">
            <option value="">Semua</option>
            <?php foreach ($opts['tahun'] as $opt): ?>
              <option value="<?php echo h($opt); ?>" <?php if($opt===$tahun) echo 'selected'; ?>>
                <?php echo h($opt); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Status</label>
          <select class="form-select" name="status">
            <option value="">Semua</option>
            <?php foreach ($opts['status'] as $opt): ?>
              <option value="<?php echo h($opt); ?>" <?php if($opt===$status) echo 'selected'; ?>>
                <?php echo h($opt); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label">Rows</label>
          <select class="form-select" name="per_page">
            <?php foreach ([10,20,30,50] as $pp): ?>
              <option value="<?php echo $pp; ?>" <?php if($pp===$per_page) echo 'selected'; ?>><?php echo $pp; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
          <a class="btn btn-outline-secondary" href="<?php echo h(build_url(['q'=>null,'jurusan'=>null,'tahun'=>null,'status'=>null,'page'=>1])); ?>">
            Reset
          </a>
        </div>
      </div>
    </form>

    <!-- Diagram Donat (kecil) + Selector Tahun Khusus Donut -->
    <div class="card donut-card mb-3">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span>Persentase Lulusan per Jurusan (Donut Kecil)</span>
        <form method="get" class="d-flex align-items-end gap-2" action="">
          <!-- Pertahankan filter lain -->
          <input type="hidden" name="q" value="<?php echo h($q); ?>">
          <input type="hidden" name="jurusan" value="<?php echo h($jurusan); ?>">
          <input type="hidden" name="tahun" value="<?php echo h($tahun); ?>">
          <input type="hidden" name="status" value="<?php echo h($status); ?>">
          <input type="hidden" name="per_page" value="<?php echo h($per_page); ?>">
          <label class="form-label m-0 me-1 small text-muted">Tahun untuk Donut</label>
          <select name="donut_year" class="form-select form-select-sm">
            <option value="">Semua Tahun (Agregat)</option>
            <?php foreach ($opts['tahun'] as $opt): ?>
              <option value="<?php echo h($opt); ?>" <?php if($opt===$donut_year) echo 'selected'; ?>>
                <?php echo h($opt); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-outline-primary">Terapkan</button>
        </form>
      </div>
      <div class="card-body">
        <?php if ($pie_total <= 0): ?>
          <div class="text-center text-muted">Tidak ada data untuk ditampilkan.</div>
        <?php else: ?>
          <div class="donut-wrap">
            <div class="chart-fixed-h">
              <canvas id="jurusanDonut" aria-label="Donut distribusi jurusan" role="img"></canvas>
            </div>
          </div>
          <div class="small text-muted mt-2 text-center">
            Total: <?php echo $pie_total; ?> alumni
            <?php if ($donut_year!=='') echo ' (tahun '.$donut_year.')'; ?>.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tren per Tahun (Line Chart) -->
    <div class="card trend-card mb-3">
      <div class="card-header">Tren Lulusan per Tahun (Top-6 Jurusan)</div>
      <div class="card-body">
        <?php if (empty($trend_years)): ?>
          <div class="text-center text-muted">Tidak ada data tren untuk filter saat ini.</div>
        <?php else: ?>
          <div class="chart-trend-h">
            <canvas id="jurusanTrend" aria-label="Tren lulusan per tahun" role="img"></canvas>
          </div>
          <div class="small text-muted mt-2">
            Menampilkan <?php echo count($series); ?> seri (Top-6 jurusan<?php echo $has_other?', termasuk "Lainnya"':''; ?>) pada tahun:
            <?php echo h(implode(', ', $trend_years)); ?>.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tabel -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:60px">No</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Jenis Kelamin</th>
            <th>TTL</th>
            <th>Tahun Lulus</th>
            <th>Status</th>
            <th>Nama Instansi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data / tidak ditemukan.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $i => $r): ?>
              <tr>
                <td><?php echo ($offset + $i + 1); ?></td>
                <td><?php echo h($r['nama']); ?></td>
                <td><?php echo h($r['jurusan']); ?></td>
                <td><?php echo h($r['jenis_kelamin']); ?></td>
                <td>
                  <?php
                    $ttl = trim(($r['tempat_lahir'] ?? ''));
                    if ($ttl !== '') $ttl .= ', ';
                    $ttl .= indo_date($r['tanggal_lahir'] ?? '');
                    echo h($ttl);
                  ?>
                </td>
                <td><?php echo h($r['tahun_lulus']); ?></td>
                <td><?php echo h($r['status']); ?></td>
                <td><?php echo h($r['nama_instansi']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div class="text-muted">
        Menampilkan <?php echo count($rows); ?> dari <?php echo $total; ?> data.
        Halaman <?php echo $page; ?> / <?php echo $total_pages; ?>.
      </div>
      <nav>
        <ul class="pagination mb-0">
          <li class="page-item <?php if($page<=1) echo 'disabled'; ?>">
            <a class="page-link" href="<?php echo h(build_url(['page'=>1])); ?>">&laquo;</a>
          </li>
          <li class="page-item <?php if($page<=1) echo 'disabled'; ?>">
            <a class="page-link" href="<?php echo h(build_url(['page'=>max(1,$page-1)])); ?>">&lsaquo;</a>
          </li>
          <li class="page-item active"><span class="page-link"><?php echo $page; ?></span></li>
          <li class="page-item <?php if($page>=$total_pages) echo 'disabled'; ?>">
            <a class="page-link" href="<?php echo h(build_url(['page'=>min($total_pages,$page+1)])); ?>">&rsaquo;</a>
          </li>
          <li class="page-item <?php if($page>=$total_pages) echo 'disabled'; ?>">
            <a class="page-link" href="<?php echo h(build_url(['page'=>$total_pages])); ?>">&raquo;</a>
          </li>
        </ul>
      </nav>
    </div>

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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script>
  // Utility warna (HSL)
  function genColors(n, s=70, l=55){
    var arr = [];
    for (var i=0;i<n;i++){
      var hue = Math.round(360 * i / Math.max(1, n));
      arr.push('hsl(' + hue + ','+s+'%,'+l+'%)');
    }
    return arr;
  }

  // Donut (diperkecil)
  (function(){
    var total = <?php echo (int)$pie_total; ?>;
    if (!total) return;

    var labels = <?php echo json_encode($pie_labels, JSON_UNESCAPED_UNICODE); ?>;
    var counts = <?php echo json_encode($pie_counts); ?>;
    var colors = genColors(labels.length, 72, 60);

    var ctx = document.getElementById('jurusanDonut');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: counts,
          backgroundColor: colors,
          borderWidth: 1
        }]
      },
      options: {
        maintainAspectRatio: false, // agar tinggi 180px dipatuhi
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12 } },
          tooltip: {
            callbacks: {
              label: function(context){
                var val = context.parsed || 0;
                var pct = total ? (val / total * 100) : 0;
                return context.label + ': ' + val + ' (' + pct.toFixed(1) + '%)';
              }
            }
          }
        }
      }
    });
  })();

  // Tren per Tahun (Line)
  (function(){
    var years  = <?php echo json_encode($trend_years); ?>;
    var series = <?php echo json_encode($series, JSON_UNESCAPED_UNICODE); ?>;
    if (!years || !years.length || !series || !series.length) return;

    var ctx = document.getElementById('jurusanTrend');
    if (!ctx) return;

    var colors = genColors(series.length, 70, 45);
    var datasets = series.map(function(s, i){
      return {
        label: s.label,
        data: s.data,
        borderColor: colors[i],
        backgroundColor: colors[i],
        tension: 0.3,
        pointRadius: 2,
        fill: false
      };
    });

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: years,
        datasets: datasets
      },
      options: {
        maintainAspectRatio: false, // patuhi tinggi 220px
        responsive: true,
        interaction: { mode: 'nearest', intersect: false },
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              title: function(items){ return 'Tahun: ' + items[0].label; },
              label: function(ctx){ return ctx.dataset.label + ': ' + ctx.formattedValue; }
            }
          }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision:0 } }
        }
      }
    });
  })();
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
<script src="<?php echo MY_PATH; ?>js/bootstrap.bundle.min.js" defer></script>
<?php include APP_ROOT . "/js.php"; ?>
</body>
</html>
