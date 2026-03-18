<?php $title='Movies'; ob_start(); ?>
<h2>Movies</h2>

<div class="card">
  <h3>Adaugă film</h3>
  <form method="post" action="/movies/create" class="row">
    <input name="nume" placeholder="Nume film" required>
    <input name="durata" placeholder="Durata (minute)" required>
    <button type="submit">Adaugă</button>
  </form>
</div>

<div class="card">
  <h3>Lista filme</h3>
  <table>
    <thead><tr><th>ID</th><th>Nume</th><th>Durata</th><th style="text-align:right;">Acțiuni</th></tr></thead>
    <tbody>
      <?php if (empty($movies)): ?>
        <tr><td colspan="4">Niciun film.</td></tr>
      <?php else: foreach ($movies as $m): ?>
        <tr>
          <td><?= (int)$m['idfilm'] ?></td>
          <td><?= h($m['Nume']) ?></td>
          <td><?= (int)$m['durata'] ?></td>
          <td>
            <div class="actions">
              <a class="btn" href="/movies/edit?id=<?= (int)$m['idfilm'] ?>">Edit</a>
              <form method="post" action="/movies/delete" onsubmit="return confirm('Ștergi filmul?')">
                <input type="hidden" name="idfilm" value="<?= (int)$m['idfilm'] ?>">
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
