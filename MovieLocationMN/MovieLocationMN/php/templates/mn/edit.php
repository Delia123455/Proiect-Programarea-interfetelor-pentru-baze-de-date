<?php $title='Edit MN'; ob_start(); ?>
<h2>Edit MN (<?= (int)$link['idfilm'] ?>, <?= (int)$link['idlocatie'] ?>)</h2>

<div class="card">
  <form method="post" action="/mn/update" class="row">
    <input type="hidden" name="old_idfilm" value="<?= (int)$link['idfilm'] ?>">
    <input type="hidden" name="old_idlocatie" value="<?= (int)$link['idlocatie'] ?>">

    <select name="idfilm" required>
      <?php foreach ($movies as $m): ?>
        <option value="<?= (int)$m['idfilm'] ?>" <?= ((int)$m['idfilm']===(int)$link['idfilm']) ? 'selected' : '' ?>>
          <?= h($m['Nume']) ?> (ID <?= (int)$m['idfilm'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <select name="idlocatie" required>
      <?php foreach ($locations as $l): ?>
        <option value="<?= (int)$l['idlocatie'] ?>" <?= ((int)$l['idlocatie']===(int)$link['idlocatie']) ? 'selected' : '' ?>>
          <?= h($l['Nume']) ?> (ID <?= (int)$l['idlocatie'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit">Salvează</button>
  </form>
  <p><a href="/mn">Înapoi</a></p>
</div>
<?php $content=ob_get_clean(); require __DIR__ . '/../_layout.php'; ?>
