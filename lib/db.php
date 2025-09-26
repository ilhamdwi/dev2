<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "u8161352_smaipbsoedirman2";

$koneksi = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($koneksi, 'utf8mb4');

if (!$koneksi) {
  die('Koneksi database gagal: ' . mysqli_connect_error());
}

/* --- SETTINGS & ADMIN --- */
$admin_web = 'Admin';
$title_web = 'Situs Sekolah';
$deskripsi_web = $keyword_web = $logo_web = $icon_web = '';

if ($res = $koneksi->query("SELECT `nama` FROM `admin_web` ORDER BY `id_admin` LIMIT 1")) {
  if ($row = $res->fetch_assoc()) $admin_web = strip_tags($row['nama']);
  $res->close();
}

if ($res = $koneksi->query("SELECT `title`,`deskripsi`,`keyword`,`logo`,`favicon` FROM `setting_web` LIMIT 1")) {
  if ($row = $res->fetch_assoc()) {
    $title_web     = strip_tags($row['title'] ?? $title_web);
    $deskripsi_web = strip_tags($row['deskripsi'] ?? '');
    $keyword_web   = strip_tags($row['keyword'] ?? '');
    $logo_web      = strip_tags($row['logo'] ?? '');
    $icon_web      = strip_tags($row['favicon'] ?? '');
  }
  $res->close();
}
