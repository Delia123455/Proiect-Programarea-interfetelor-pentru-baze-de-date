<?php $title='MN'; ob_start(); ?>
<h2>MN (Movies <-> Locations)</h2>

<div class="card">
  <h3>Adaugă legătură</h3>
  <form method="post" action="/mn/create" class="row">
    <select name="idfilm" required>
      <option value="" disabled selected>Alege film</option>
      <?php foreach ($movies as $m): ?>
        <option value="<?= (int)$m['idfilm'] ?>"><?= h($m['Nume']) ?> (ID <?= (int)$m['idfilm'] ?>)</option>
      <?php endforeach; ?>
    </select>

    <select name="idlocatie" required>
      <option value="" disabled selected>Alege locație</option>
      <?php foreach ($locations as $l): ?>
        <option value="<?= (int)$l['idlocatie'] ?>"><?= h($l['Nume']) ?> (ID <?= (int)$l['idlocatie'] ?>)</option>
      <?php endforeach; ?>
    </select>

    <button type="submit">Adaugă</button>
  </form>

  <?php if (empty($movies) || empty($locations)): ?>
    <p class="muted">Ai nevoie de cel puțin 1 film și 1 locație ca să creezi legături.</p>
  <?php endif; ?>
</div>

<div class="card">
  <h3>Lista legături</h3>
  <table>
    <thead><tr><th>Film</th><th>Locație</th><th style="text-align:right;">Acțiuni</th></tr></thead>
    <tbody>
      <?php if (empty($links)): ?>
        <tr><td colspan="3">Nicio legătură.</td></tr>
      <?php else: foreach ($links as $x): ?>
        <tr>
          <td><?= h($x['film']) ?> (ID <?= (int)$x['idfilm'] ?>)</td>
          <td><?= h($x['locatie']) ?> (ID <?= (int)$x['idlocatie'] ?>)</td>
          <td>
            <div class="actions">
              <a class="btn" href="/mn/edit?idfilm=<?= (int)$x['idfilm'] ?>&idlocatie=<?= (int)$x['idlocatie'] ?>">Edit</a>
              <form method="post" action="/mn/delete" onsubmit="return confirm('Ștergi legătura?')">
                <input type="hidden" name="idfilm" value="<?= (int)$x['idfilm'] ?>">
                <input type="hidden" name="idlocatie" value="<?= (int)$x['idlocatie'] ?>">
                <button class="btn danger" type="submit">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php $content=ob_get_clean(); require __DIR__ . '/../_layout.php'; ?>
