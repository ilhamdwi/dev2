<?php if (!defined('MY_PATH')) define('MY_PATH', '/dev2/'); ?>

<!-- Topbar -->
<div class="topbar py-1">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <i class="bi bi-telephone me-1"></i> 021-88875365
      <span class="ms-3"><i class="bi bi-whatsapp me-1"></i> 0821-2224-1232</span>
      <a href="https://docs.google.com/forms/d/e/1FAIpQLSfD5JVIOOWf0HgZSksjunrxVtFpz3m4atpfNFDjVKiD589GrA/viewform" target="_blank"><i class="bi bi-book"></i>PPDB ONLINE</a>&nbsp
      <a href="https://cbt.smaipbsoedirman2bekasi.sch.id/" target="_blank"><i class="bi bi-calendar2-check"></i>TEST ONLINE</a>&nbsp
      <a href="https://forms.office.com/Pages/ResponsePage.aspx?id=DmDqzG_jpE6obImnsfixvYpVGOp9VM9LnN-UWwOMsJlUNkxMRDE3WjlTNDRQV0w3WVNDNFdaUFlMQS4u" target="_blank"><i class="bi bi-calendar2-check"></i>PRESENSI</a>
    </div>
    <div class="social-group">
      <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
      <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
      <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
      <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
    </div>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-semibold d-flex align-items-center" href="<?php echo MY_PATH; ?>">
      <img src="<?php echo MY_PATH; ?>images/logo_pangsoed.png" alt="Logo" height="40" class="me-2">

    </a>
    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
      aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 main-nav">
        <li class="nav-item"><a class="nav-link active" href="<?php echo MY_PATH; ?>">Beranda</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/struktur.php">Struktur Organisasi</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/selayang.php">Selayang Pandang</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/visimisi.php">Visi & Misi</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/sejarah.php">Sejarah</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/dataguru.php">Data Guru & Karyawan</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kurikulum</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kurikulum.php">Kurikulum SMAI PB Soedirman 2</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/program.php">Program</a></li>
            <li><a class="dropdown-item" href="https://skl.smaipbsoedirman2bekasi.sch.id/">Kelulusan Kelas XII</a></li>
            <li><a class="dropdown-item" href="https://cbt.smaipbsoedirman2bekasi.sch.id">Computer Based Test (CBT)</a></li>
            <li><a class="dropdown-item" href="https://forms.office.com/Pages/ResponsePage.aspx?id=DmDqzG_jpE6obImnsfixvYpVGOp9VM9LnN-UWwOMsJlUNkxMRDE3WjlTNDRQV0w3WVNDNFdaUFlMQS4u">Presensi Online</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kesiswaan</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kesiswaan.php">Kesiswaan</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kegiatan.php">Info Kegiatan</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/ekstrakurikuler2.php">Ekstrakurikuler</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/prestasi.php">Prestasi</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/tartib.php">Tata Tertib</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Humas</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/alumni.php">Alumni</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">PPDB 2026/2027</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kesiswaan.php">Alur Pendaftaran</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kegiatan.php">Rincian Biaya</a></li>
            <li><a class="dropdown-item" href="https://docs.google.com/forms/d/e/1FAIpQLScjQ7hV1dv5C5OhlwVjMMorXohl769o6vJ6LC9UWZggAZb6Yw/viewform">Formulir Pendaftaran Siswa Baru(Petugas) </a></li>
            <li><a class="dropdown-item" href="https://drive.google.com/drive/u/0/folders/1S0Q3ixgAnlfA5oi3-IEJoYK2-huUIj1X">Cetak Formulir Pendaftaran PPDB 2026/2027</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Galeri</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/fasilitas.php">Fasilitas Sekolah</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/eskul.php">Ekstrakurikuler</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">E-Library</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="https://sites.google.com/view/perpustakaan-digital-pangsoed-/tutorial">Perpustakaan Digital</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo MY_PATH; ?>pages/kontak.php">Kontak</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo MY_PATH; ?>pages/buku-tamu.php">Buku Tamu</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo MY_PATH; ?>download.php">Download</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">MPLS~PAIS</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/informasi.php">Informasi Umum MPLS~PAIS</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kelompok.php">Kelompok MPLS~PAIS</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/seragam.php">Seragam MPLS~PAIS</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kegmpls1.php">MPLS~PAIS Hari ke 1</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/mars.php">Mars Yasma PB Soedirman</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/materi.php">Materi PAIS</a></li>
            <li><a class="dropdown-item" href="<?php echo MY_PATH; ?>pages/kegmpls2.php">MPLS~PAIS Hari ke 2</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


<!-- Hero (opsional; ganti gambar) -->
<section class="hero-banner">
  <img src="<?php echo MY_PATH; ?>img/Gedung SMA 2022.jpeg" alt="Hero" class="hero-img">
  <div class="hero-overlay"></div>

  <div class="container hero-content">
    <div class="hero-textbox">
      <h1 class="display-5 fw-bold mb-2 hero-title">Sekolah Unggul dengan Layanan SKS</h1>
      <p class="lead mb-0 hero-subtitle">Mencetak generasi berkarakter, berprestasi, dan bertaqwa.</p>
    </div>

    <!-- SEARCH BAR -->
    <form class="hero-search" method="get" action="<?php echo MY_PATH; ?>berita-cari.php" role="search">
      <div class="input-group input-group-lg mt-3">
        <input type="search" class="form-control" name="q" placeholder="Cari berita, pengumuman, atau artikel..." aria-label="Cari">
        <button class="btn btn-brand" type="submit">
          <i class="bi bi-search me-1"></i><span class="d-none d-sm-inline">Cari</span>
        </button>
      </div>
    </form>

    <a href="<?php echo MY_PATH; ?>ppdb.php" class="btn btn-brand btn-lg mt-3">Daftar PPDB</a>
  </div>
</section>
