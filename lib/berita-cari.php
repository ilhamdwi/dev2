<?php 
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
include 'db.php';

/* Pastikan konstanta MY_PATH didefinisikan sekali di project */
if (!defined('MY_PATH')) {
    define('MY_PATH', '/dev2/'); // KOREKSI PATH
}

/* Ambil keyword dengan aman */
$keyword = isset($_GET['q']) ? $_GET['q'] : ''; // Ambil dari 'q', bukan 'keyword'
$keyword = mysqli_real_escape_string($koneksi, $keyword);
$keyword = strtolower(str_replace(' ', '-', $keyword));

if (empty($keyword)) {
    $keyword = "berita";
}

/* Redirect ke URL Clean URL yang baru */
// Output: /dev2/berita/search/keyword
header("Location: " . MY_PATH . "berita/search/" . $keyword);
exit;