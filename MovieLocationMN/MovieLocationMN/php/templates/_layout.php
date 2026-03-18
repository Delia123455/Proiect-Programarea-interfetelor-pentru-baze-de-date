<?php $flashes = $flashes ?? []; ?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title ?? 'ML M:N Demo') ?></title>
  <link rel="stylesheet" href="/static/style.css">
</head>
<body>
  <nav class="nav">
    <div class="container">
      <a class="brand" href="/">ML</a>
      <a href="/movies">Movies</a>
      <a href="/locations">Locations</a>
      <a href="/mn">MN</a>
    </div>
  </nav>

  <main class="container">
    <?php foreach ($flashes as $f): ?>
      <div class="alert <?= ($f['type'] === 'ok') ? 'ok' : 'err' ?>"><?= h($f['msg']) ?></div>
    <?php endforeach; ?>

    <?= $content ?? '' ?>
  </main>
</body>
</html>
