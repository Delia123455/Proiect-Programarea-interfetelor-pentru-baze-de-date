<?php $title='Locations'; ob_start(); ?>
<h2>Locations</h2>

<div class="card">
  <h3>Adaugă locație</h3>
  <form method="post" action="/locations/create" class="row">
    <input name="nume" placeholder="Nume locație (unic)" required>
    <input name="capacitate" placeholder="Capacitate" required>
    <button type="submit">Adaugă</button>
  </form>
</div>

<div class="card">
  <h3>Lista locații</h3>
  <table>
    <thead><tr><th>ID</th><th>Nume</th><th>Capacitate</th><th style="text-align:right;">Acțiuni</th></tr></thead>
    <tbody>
      <?php if (empty($locations)): ?>
        <tr><td colspan="4">Nicio locație.</td></tr>
      <?php else: foreach ($locations as $l): ?>
        <tr>
          <td><?= (int)$l['idlocatie'] ?></td>
          <td><?= h($l['Nume']) ?></td>
          <td><?= (int)$l['capacitate'] ?></td>
          <td>
            <div class="actions">
              <a class="btn" href="/locations/edit?id=<?= (int)$l['idlocatie'] ?>">Edit</a>
              <form method="post" action="/locations/delete" onsubmit="return confirm('Ștergi locația?')">
                <input type="hidden" name="idlocatie" value="<?= (int)$l['idlocatie'] ?>">
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
