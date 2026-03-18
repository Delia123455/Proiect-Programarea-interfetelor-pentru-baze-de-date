<?php
declare(strict_types=1);

class MNController {
    public function index(): void {
        try {
            $pdo = db();

            $links = $pdo->query("
                SELECT MN.idfilm, MN.idlocatie, m.Nume AS film, l.Nume AS locatie
                FROM MN
                JOIN Movies m ON m.idfilm = MN.idfilm
                JOIN Locations l ON l.idlocatie = MN.idlocatie
                ORDER BY m.Nume ASC, l.Nume ASC
            ")->fetchAll();

            $movies = $pdo->query("SELECT idfilm, Nume FROM Movies ORDER BY Nume ASC")->fetchAll();
            $locations = $pdo->query("SELECT idlocatie, Nume FROM Locations ORDER BY Nume ASC")->fetchAll();

            view('mn/index', ['links'=>$links, 'movies'=>$movies, 'locations'=>$locations, 'flashes'=>consume_flashes()]);
        } catch (PDOException $e) {
            flash('Eroare DB: ' . $e->getMessage(), 'err');
            view('mn/index', ['links'=>[], 'movies'=>[], 'locations'=>[], 'flashes'=>consume_flashes()]);
        }
    }

    public function create(): void {
        $f = $_POST['idfilm'] ?? '';
        $l = $_POST['idlocatie'] ?? '';
        if (!ctype_digit($f) || !ctype_digit($l)) { flash('Selectează film și locație valide.', 'err'); redirect('/mn'); }

        try {
            $st = db()->prepare('INSERT INTO MN (idfilm, idlocatie) VALUES (:f, :l)');
            $st->execute([':f' => (int)$f, ':l' => (int)$l]);
            flash('Legătură MN adăugată.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la adăugare legătură: ' . $e->getMessage(), 'err');
        }
        redirect('/mn');
    }

    public function edit(): void {
        $f = $_GET['idfilm'] ?? '';
        $l = $_GET['idlocatie'] ?? '';
        if (!ctype_digit($f) || !ctype_digit($l)) { flash('Cheie invalidă.', 'err'); redirect('/mn'); }

        $pdo = db();
        $st = $pdo->prepare("
            SELECT MN.idfilm, MN.idlocatie, m.Nume AS film, l.Nume AS locatie
            FROM MN
            JOIN Movies m ON m.idfilm = MN.idfilm
            JOIN Locations l ON l.idlocatie = MN.idlocatie
            WHERE MN.idfilm = :f AND MN.idlocatie = :l
        ");
        $st->execute([':f' => (int)$f, ':l' => (int)$l]);
        $link = $st->fetch();

        if (!$link) { flash('Legătură inexistentă.', 'err'); redirect('/mn'); }

        $movies = $pdo->query("SELECT idfilm, Nume FROM Movies ORDER BY Nume ASC")->fetchAll();
        $locations = $pdo->query("SELECT idlocatie, Nume FROM Locations ORDER BY Nume ASC")->fetchAll();

        view('mn/edit', ['link'=>$link, 'movies'=>$movies, 'locations'=>$locations, 'flashes'=>consume_flashes()]);
    }

    public function update(): void {
        $old_f = $_POST['old_idfilm'] ?? '';
        $old_l = $_POST['old_idlocatie'] ?? '';
        $new_f = $_POST['idfilm'] ?? '';
        $new_l = $_POST['idlocatie'] ?? '';

        if (!ctype_digit($old_f) || !ctype_digit($old_l) || !ctype_digit($new_f) || !ctype_digit($new_l)) {
            flash('Date invalide.', 'err');
            redirect('/mn');
        }

        $pdo = db();
        try {
            $pdo->beginTransaction();

            $del = $pdo->prepare('DELETE FROM MN WHERE idfilm = :f AND idlocatie = :l');
            $del->execute([':f' => (int)$old_f, ':l' => (int)$old_l]);

            $ins = $pdo->prepare('INSERT INTO MN (idfilm, idlocatie) VALUES (:f, :l)');
            $ins->execute([':f' => (int)$new_f, ':l' => (int)$new_l]);

            $pdo->commit();
            flash('Legătură MN modificată.', 'ok');
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            flash('Eroare la modificare: ' . $e->getMessage(), 'err');
        }

        redirect('/mn');
    }

    public function delete(): void {
        $f = $_POST['idfilm'] ?? '';
        $l = $_POST['idlocatie'] ?? '';
        if (!ctype_digit($f) || !ctype_digit($l)) { flash('Cheie invalidă.', 'err'); redirect('/mn'); }

        try {
            $st = db()->prepare('DELETE FROM MN WHERE idfilm = :f AND idlocatie = :l');
            $st->execute([':f' => (int)$f, ':l' => (int)$l]);
            flash('Legătură MN ștearsă.', 'ok');
        } catch (PDOException $e) {
            flash('Eroare la ștergere: ' . $e->getMessage(), 'err');
        }
        redirect('/mn');
    }
}
