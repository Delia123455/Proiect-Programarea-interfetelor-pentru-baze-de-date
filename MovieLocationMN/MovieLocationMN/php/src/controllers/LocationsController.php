<?php
declare(strict_types=1);

class LocationsController {
    public function index(): void {
        try {
            $locations = db()->query('SELECT idlocatie, Nume, capacitate FROM Locations ORDER BY idlocatie ASC')->fetchAll();
            view('locations/index', ['locations' => $locations, 'flashes' => consume_flashes()]);
        } catch (PDOException $e) {
            flash('Eroare DB: ' . $e->getMessage(), 'err');
            view('locations/index', ['locations' => [], 'flashes' => consume_flashes()]);
        }
    }

    public function create(): void {
        $nume = trim($_POST['nume'] ?? '');
        $cap = trim($_POST['capacitate'] ?? '');

        if ($nume === '') { flash('Numele locației e obligatoriu.', 'err'); redirect('/locations'); }
        if (!ctype_digit($cap) || (int)$cap <= 0) { flash('Capacitatea trebuie să fie număr întreg > 0.', 'err'); redirect('/locations'); }

        try {
            $st = db()->prepare('INSERT INTO Locations (Nume, capacitate) VALUES (:n, :c)');
            $st->execute([':n' => $nume, ':c' => (int)$cap]);
            flash('Locație adăugată.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la adăugare locație: ' . $e->getMessage(), 'err');
        }
        redirect('/locations');
    }

    public function edit(): void {
        $id = $_GET['id'] ?? '';
        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/locations'); }

        $st = db()->prepare('SELECT idlocatie, Nume, capacitate FROM Locations WHERE idlocatie = :id');
        $st->execute([':id' => (int)$id]);
        $location = $st->fetch();

        if (!$location) { flash('Locație inexistentă.', 'err'); redirect('/locations'); }

        view('locations/edit', ['location' => $location, 'flashes' => consume_flashes()]);
    }

    public function update(): void {
        $id = $_POST['idlocatie'] ?? '';
        $nume = trim($_POST['nume'] ?? '');
        $cap = trim($_POST['capacitate'] ?? '');

        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/locations'); }
        if ($nume === '') { flash('Numele locației e obligatoriu.', 'err'); redirect('/locations/edit?id=' . urlencode($id)); }
        if (!ctype_digit($cap) || (int)$cap <= 0) { flash('Capacitatea trebuie să fie număr întreg > 0.', 'err'); redirect('/locations/edit?id=' . urlencode($id)); }

        try {
            $st = db()->prepare('UPDATE Locations SET Nume = :n, capacitate = :c WHERE idlocatie = :id');
            $st->execute([':n' => $nume, ':c' => (int)$cap, ':id' => (int)$id]);
            flash('Locație modificată.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la modificare locație: ' . $e->getMessage(), 'err');
        }
        redirect('/locations');
    }

    public function delete(): void {
        $id = $_POST['idlocatie'] ?? '';
        if (!ctype_digit($id)) { flash('ID invalid.', 'err'); redirect('/locations'); }

        try {
            $st = db()->prepare('DELETE FROM Locations WHERE idlocatie = :id');
            $st->execute([':id' => (int)$id]);
            flash('Locație ștearsă (și legăturile MN aferente).', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la ștergere locație: ' . $e->getMessage(), 'err');
        }
        redirect('/locations');
    }
}
