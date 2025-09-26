<?php
require_once 'db.php'; // pastikan $koneksi (mysqli) tersedia

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($q !== '') {
  $sql = "SELECT id_article, title, link_article
          FROM article
          WHERE title LIKE CONCAT('%', ?, '%')
             OR content LIKE CONCAT('%', ?, '%')
          ORDER BY id_article DESC
          LIMIT 50";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param('ss', $q, $q);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) {
    $results[] = $row;
  }
  $stmt->close();
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Hasil Pencarian</title></head><body>
<h1>Hasil untuk: <?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></h1>
<ul>
  <?php if ($results): foreach ($results as $r): ?>
    <li>
      <a href="<?php echo MY_PATH . 'post/' . htmlspecialchars($r['link_article'], ENT_QUOTES, 'UTF-8') . '.html'; ?>">
        <?php echo htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8'); ?>
      </a>
    </li>
  <?php endforeach; else: ?>
    <li><em>Tidak ada hasil.</em></li>
  <?php endif; ?>
</ul>
</body></html>
