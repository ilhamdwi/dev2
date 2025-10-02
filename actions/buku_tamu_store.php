<?php
/************************************************************
 * BUKU TAMU — actions/buku_tamu_store.php (backend proses)
 * Sesuai lib/db.php yang mendefinisikan $koneksi (mysqli)
 * Fitur:
 * - CSRF protection
 * - Honeypot anti-bot
 * - Validasi server-side
 * - Prepared statements (anti SQL Injection)
 * - Redirect aman ke pages/buku_tamu.php
 ************************************************************/

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!defined('APP_ROOT')) {
  define('APP_ROOT', rtrim(str_replace('\\','/', dirname(__DIR__)), '/'));
}
if (!defined('MY_PATH')) {
  define('MY_PATH', '/dev2/'); // sesuaikan bila berbeda
}

require_once APP_ROOT . '/lib/db.php'; // harus menyediakan $koneksi (objek mysqli)
session_start();

/* ---------------- Helper redirect ---------------- */
function redirect_with(array $params = []): void {
  // Samakan dengan nama file form: pages/buku_tamu.php (underscore)
  $base = MY_PATH . 'pages/buku-tamu.php';
  if (!empty($params)) {
    $qs = http_build_query($params);
    header("Location: {$base}?{$qs}");
  } else {
    header("Location: {$base}");
  }
  exit;
}

/* ---------------- Batasi hanya POST ---------------- */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  redirect_with(['error' => 'Metode tidak diizinkan']);
}

/* ---------------- CSRF check ---------------- */
$csrf_post = $_POST['csrf_token'] ?? '';
$csrf_sess = $_SESSION['csrf_token'] ?? '';
if (!$csrf_post || !$csrf_sess || !hash_equals($csrf_sess, $csrf_post)) {
  redirect_with(['error' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
}

/* ---------------- Honeypot anti-bot ---------------- */
if (!empty($_POST['website'])) { // field tersembunyi pada form
  redirect_with(['error' => 'Terindikasi spam.']);
}

/* ---------------- Ambil & validasi input ---------------- */
$nama  = trim((string)($_POST['nama']  ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$pesan = trim((string)($_POST['pesan'] ?? ''));

if ($nama === '' || mb_strlen($nama) > 100) {
  redirect_with(['error' => 'Nama wajib diisi (maks. 100 karakter).']);
}
if ($email === '' || mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  redirect_with(['error' => 'Email tidak valid (maks. 150 karakter).']);
}
if ($pesan === '' || mb_strlen($pesan) > 5000) {
  redirect_with(['error' => 'Pesan wajib diisi (maks. 5000 karakter).']);
}

/* ---------------- Pastikan koneksi DB dari lib/db.php ---------------- */
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
  // lib/db.php kamu membuat $koneksi via mysqli_connect(...).
  // Jika variabelnya tidak ada/beda nama, $koneksi akan null.
  redirect_with(['error' => 'Koneksi database tidak tersedia.']);
}

// Pastikan charset (emoji/UTF8 penuh) — lib/db.php sudah set, ini jaga-jaga
if (method_exists($koneksi, 'set_charset')) {
  @mysqli_set_charset($koneksi, 'utf8mb4');
}

// Jadikan error mysqli sebagai exception (lib/db.php sudah set, aman dipanggil lagi)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ---------------- Simpan ke database (Prepared Statement) ---------------- */
try {
  // Asumsi di schema:
  // - date_buku_tamu DEFAULT CURRENT_TIMESTAMP
  // - status DEFAULT 'draft'
  $sql = "INSERT INTO buku_tamu (nama_buku_tamu, email_buku_tamu, pesan_buku_tamu, status)
          VALUES (?, ?, ?, 'Draft')";

  $stmt = $koneksi->prepare($sql);
  if (!$stmt) {
    redirect_with(['error' => 'Gagal menyiapkan statement database.']);
  }

  $stmt->bind_param('sss', $nama, $email, $pesan);
  $stmt->execute();
  $stmt->close();

  redirect_with(['success' => '1']);

} catch (Throwable $e) {
  // Opsional untuk debugging internal:
  // error_log('BukuTamu insert error: ' . $e->getMessage());
  redirect_with(['error' => 'Terjadi kesalahan saat menyimpan data.']);
}
