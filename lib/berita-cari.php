<?php 
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
include 'db.php';

/* Pastikan konstanta MY_PATH didefinisikan sekali di project */
if (!defined('MY_PATH')) {
    define('MY_PATH', '/dev/2'); // sesuaikan dengan base URL, misal 'http://localhost/dev2/'
}

/* Ambil keyword dengan aman */
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$keyword = mysqli_real_escape_string($koneksi, $keyword);
$keyword = strtolower(str_replace(' ', '-', $keyword));

if (empty($keyword)) {
    $keyword = "berita";
}

/* Redirect ke URL baru */
header("Location: " . MY_PATH . "berita/search/" . $keyword);
exit;
