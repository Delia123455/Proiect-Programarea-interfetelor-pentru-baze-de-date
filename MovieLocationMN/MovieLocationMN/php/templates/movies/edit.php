<?php $title='Edit Movie'; ob_start(); ?>
<h2>Edit Movie #<?= (int)$movie['idfilm'] ?></h2>

<div class="card">
  <form method="post" action="/movies/update" class="row">
    <input type="hidden" name="idfilm" value="<?= (int)$movie['idfilm'] ?>">
    <input name="nume" value="<?= h($movie['Nume']) ?>" required>
    <input name="durata" value="<?= (int)$movie['durata'] ?>" required>
    <button type="submit">Salvează</button>
  </form>
  <p><a href="/movies">Înapoi</a></p>
</div>
<?php $content=ob_get_clean(); require __DIR__ . '/../_layout.php'; ?>
