<?php
declare(strict_types=1);

class MoviesController {
    public function index(): void {
        try {
            $movies = db()->query('SELECT idfilm, Nume, durata FROM Movies ORDER BY idfilm ASC')->fetchAll();
            view('movies/index', ['movies' => $movies, 'flashes' => consume_flashes()]);
        } catch (PDOException $e) {
            flash('Eroare DB: ' . $e->getMessage(), 'err');
            view('movies/index', ['movies' => [], 'flashes' => consume_flashes()]);
        }
    }

    public function create(): void {
        $nume = trim($_POST['nume'] ?? '');
        $durata = trim($_POST['durata'] ?? '');

        if ($nume === '') { flash('Numele filmului e obligatoriu.', 'err'); redirect('/movies'); }
        if (!ctype_digit($durata) || (int)$durata <= 0) { flash('Durata trebuie să fie număr întreg > 0.', 'err'); redirect('/movies'); }

        try {
            $st = db()->prepare('INSERT INTO Movies (Nume, durata) VALUES (:n, :d)');
            $st->execute([':n' => $nume, ':d' => (int)$durata]);
            flash('Film adăugat.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la adăugare film: ' . $e->getMessage(), 'err');
        }
        redirect('/movies');
    }

    public function edit(): void {
        $id = $_GET['id'] ?? '';
        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/movies'); }

        $st = db()->prepare('SELECT idfilm, Nume, durata FROM Movies WHERE idfilm = :id');
        $st->execute([':id' => (int)$id]);
        $movie = $st->fetch();

        if (!$movie) { flash('Film inexistent.', 'err'); redirect('/movies'); }

        view('movies/edit', ['movie' => $movie, 'flashes' => consume_flashes()]);
    }

    public function update(): void {
        $id = $_POST['idfilm'] ?? '';
        $nume = trim($_POST['nume'] ?? '');
        $durata = trim($_POST['durata'] ?? '');

        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/movies'); }
        if ($nume === '') { flash('Numele filmului e obligatoriu.', 'err'); redirect('/movies/edit?id=' . urlencode($id)); }
        if (!ctype_digit($durata) || (int)$durata <= 0) { flash('Durata trebuie să fie număr întreg > 0.', 'err'); redirect('/movies/edit?id=' . urlencode($id)); }

        try {
            $st = db()->prepare('UPDATE Movies SET Nume = :n, durata = :d WHERE idfilm = :id');
            $st->execute([':n' => $nume, ':d' => (int)$durata, ':id' => (int)$id]);
            flash('Film modificat.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la modificare film: ' . $e->getMessage(), 'err');
        }
        redirect('/movies');
    }

    public function delete(): void {
        $id = $_POST['idfilm'] ?? '';
        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/movies'); }

        try {
            $st = db()->prepare('DELETE FROM Movies WHERE idfilm = :id');
            $st->execute([':id' => (int)$id]);
            flash('Film șters (și legăturile MN aferente).', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la ștergere film: ' . $e->getMessage(), 'err');
        }
        redirect('/movies');
    }
}
