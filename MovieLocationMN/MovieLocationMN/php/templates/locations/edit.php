<?php $title='Edit Location'; ob_start(); ?>
<h2>Edit Location #<?= (int)$location['idlocatie'] ?></h2>

<div class="card">
  <form method="post" action="/locations/update" class="row">
    <input type="hidden" name="idlocatie" value="<?= (int)$location['idlocatie'] ?>">
    <input name="nume" value="<?= h($location['Nume']) ?>" required>
    <input name="capacitate" value="<?= (int)$location['capacitate'] ?>" required>
    <button type="submit">Salvează</button>
  </form>
  <p><a href="/locations">Înapoi</a></p>
</div>
<?php $content=ob_get_clean(); require __DIR__ . '/../_layout.php'; ?>
