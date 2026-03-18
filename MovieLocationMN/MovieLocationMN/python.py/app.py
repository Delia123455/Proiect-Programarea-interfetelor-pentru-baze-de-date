from flask import Flask, render_template, request, redirect, url_for, flash
import mysql.connector
from mysql.connector import Error

app = Flask(__name__)
app.secret_key = "dev-secret-change-me"

# ====== DB CONFIG  ======
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "Andreea2004!",
    "database": "ML",
    "port": 3306
}


def get_conn():
    return mysql.connector.connect(**DB_CONFIG)


# ---------------- HOME ----------------
@app.route("/")
def home():
    return redirect(url_for("mn_list"))


# ===================== MOVIES =====================
@app.route("/movies")
def movies_list():
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT idfilm, Nume, durata FROM Movies ORDER BY idfilm ASC")
        movies = cur.fetchall()
        return render_template("movies.html", movies=movies)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return render_template("movies.html", movies=[])
    finally:
        if conn:
            conn.close()


@app.route("/movies/add", methods=["POST"])
def movies_add():
    nume = (request.form.get("nume") or "").strip()
    durata = (request.form.get("durata") or "").strip()

    if not nume:
        flash("Numele filmului e obligatoriu.", "error")
        return redirect(url_for("movies_list"))

    try:
        durata_int = int(durata)
        if durata_int <= 0:
            raise ValueError
    except ValueError:
        flash("Durata trebuie să fie număr întreg > 0.", "error")
        return redirect(url_for("movies_list"))

    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("INSERT INTO Movies (Nume, durata) VALUES (%s, %s)", (nume, durata_int))
        conn.commit()
        flash("Film adăugat.", "ok")
    except Error as e:
        flash(f"Eroare la adăugare film: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("movies_list"))


@app.route("/movies/<int:idfilm>/edit")
def movies_edit(idfilm):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT idfilm, Nume, durata FROM Movies WHERE idfilm=%s", (idfilm,))
        movie = cur.fetchone()
        if not movie:
            flash("Film inexistent.", "error")
            return redirect(url_for("movies_list"))
        return render_template("movie_edit.html", movie=movie)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return redirect(url_for("movies_list"))
    finally:
        if conn:
            conn.close()


@app.route("/movies/<int:idfilm>/edit", methods=["POST"])
def movies_update(idfilm):
    nume = (request.form.get("nume") or "").strip()
    durata = (request.form.get("durata") or "").strip()

    if not nume:
        flash("Numele filmului e obligatoriu.", "error")
        return redirect(url_for("movies_edit", idfilm=idfilm))

    try:
        durata_int = int(durata)
        if durata_int <= 0:
            raise ValueError
    except ValueError:
        flash("Durata trebuie să fie număr întreg > 0.", "error")
        return redirect(url_for("movies_edit", idfilm=idfilm))

    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("UPDATE Movies SET Nume=%s, durata=%s WHERE idfilm=%s", (nume, durata_int, idfilm))
        conn.commit()
        flash("Film modificat.", "ok")
    except Error as e:
        flash(f"Eroare la modificare film: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("movies_list"))


@app.route("/movies/<int:idfilm>/delete", methods=["POST"])
def movies_delete(idfilm):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        # Șterge filmul; MN cade automat si FK cu ON DELETE CASCADE
        cur.execute("DELETE FROM Movies WHERE idfilm=%s", (idfilm,))
        conn.commit()
        flash("Film șters.", "ok")
    except Error as e:
        flash(f"Eroare la ștergere film: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("movies_list"))


# ===================== LOCATIONS =====================
@app.route("/locations")
def locations_list():
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT idlocatie, Nume, capacitate FROM Locations ORDER BY idlocatie ASC")
        locations = cur.fetchall()
        return render_template("locations.html", locations=locations)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return render_template("locations.html", locations=[])
    finally:
        if conn:
            conn.close()


@app.route("/locations/add", methods=["POST"])
def locations_add():
    nume = (request.form.get("nume") or "").strip()
    capacitate = (request.form.get("capacitate") or "").strip()

    if not nume:
        flash("Numele locației e obligatoriu.", "error")
        return redirect(url_for("locations_list"))

    try:
        cap_int = int(capacitate)
        if cap_int <= 0:
            raise ValueError
    except ValueError:
        flash("Capacitatea trebuie să fie număr întreg > 0.", "error")
        return redirect(url_for("locations_list"))

    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("INSERT INTO Locations (Nume, capacitate) VALUES (%s, %s)", (nume, cap_int))
        conn.commit()
        flash("Locație adăugată.", "ok")
    except Error as e:
        flash(f"Eroare la adăugare locație: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("locations_list"))


@app.route("/locations/<int:idlocatie>/edit")
def locations_edit(idlocatie):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT idlocatie, Nume, capacitate FROM Locations WHERE idlocatie=%s", (idlocatie,))
        location = cur.fetchone()
        if not location:
            flash("Locație inexistentă.", "error")
            return redirect(url_for("locations_list"))
        return render_template("location_edit.html", location=location)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return redirect(url_for("locations_list"))
    finally:
        if conn:
            conn.close()


@app.route("/locations/<int:idlocatie>/edit", methods=["POST"])
def locations_update(idlocatie):
    nume = (request.form.get("nume") or "").strip()
    capacitate = (request.form.get("capacitate") or "").strip()

    if not nume:
        flash("Numele locației e obligatoriu.", "error")
        return redirect(url_for("locations_edit", idlocatie=idlocatie))

    try:
        cap_int = int(capacitate)
        if cap_int <= 0:
            raise ValueError
    except ValueError:
        flash("Capacitatea trebuie să fie număr întreg > 0.", "error")
        return redirect(url_for("locations_edit", idlocatie=idlocatie))

    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("UPDATE Locations SET Nume=%s, capacitate=%s WHERE idlocatie=%s", (nume, cap_int, idlocatie))
        conn.commit()
        flash("Locație modificată.", "ok")
    except Error as e:
        flash(f"Eroare la modificare locație: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("locations_list"))


@app.route("/locations/<int:idlocatie>/delete", methods=["POST"])
def locations_delete(idlocatie):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("DELETE FROM Locations WHERE idlocatie=%s", (idlocatie,))
        conn.commit()
        flash("Locație ștearsă.", "ok")
    except Error as e:
        flash(f"Eroare la ștergere locație: {e}", "error")
    finally:
        if conn:
            conn.close()
    return redirect(url_for("locations_list"))


# ===================== MN (LINKS) =====================
@app.route("/mn")
def mn_list():
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)

        # list links (cu nume)
        cur.execute("""
            SELECT MN.idfilm, MN.idlocatie, m.Nume AS film, l.Nume AS locatie
            FROM MN
            JOIN Movies m ON m.idfilm = MN.idfilm
            JOIN Locations l ON l.idlocatie = MN.idlocatie
            ORDER BY m.Nume ASC, l.Nume ASC
        """)
        links = cur.fetchall()

        # dropdown data
        cur.execute("SELECT idfilm, Nume FROM Movies ORDER BY Nume ASC")
        movies = cur.fetchall()

        cur.execute("SELECT idlocatie, Nume FROM Locations ORDER BY Nume ASC")
        locations = cur.fetchall()

        return render_template("mn.html", links=links, movies=movies, locations=locations)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return render_template("mn.html", links=[], movies=[], locations=[])
    finally:
        if conn:
            conn.close()


@app.route("/mn/add", methods=["POST"])
def mn_add():
    idfilm = (request.form.get("idfilm") or "").strip()
    idlocatie = (request.form.get("idlocatie") or "").strip()

    try:
        idfilm_i = int(idfilm)
        idloc_i = int(idlocatie)
    except ValueError:
        flash("Selectează film și locație valide.", "error")
        return redirect(url_for("mn_list"))

    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("INSERT INTO MN (idfilm, idlocatie) VALUES (%s, %s)", (idfilm_i, idloc_i))
        conn.commit()
        flash("Legătură MN adăugată.", "ok")
    except Error as e:
        flash(f"Eroare la adăugare legătură: {e}", "error")
    finally:
        if conn:
            conn.close()

    return redirect(url_for("mn_list"))


@app.route("/mn/<int:idfilm>/<int:idlocatie>/edit")
def mn_edit(idfilm, idlocatie):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor(dictionary=True)

        cur.execute("""
            SELECT MN.idfilm, MN.idlocatie, m.Nume AS film, l.Nume AS locatie
            FROM MN
            JOIN Movies m ON m.idfilm = MN.idfilm
            JOIN Locations l ON l.idlocatie = MN.idlocatie
            WHERE MN.idfilm=%s AND MN.idlocatie=%s
        """, (idfilm, idlocatie))
        link = cur.fetchone()
        if not link:
            flash("Legătură inexistentă.", "error")
            return redirect(url_for("mn_list"))

        cur.execute("SELECT idfilm, Nume FROM Movies ORDER BY Nume ASC")
        movies = cur.fetchall()

        cur.execute("SELECT idlocatie, Nume FROM Locations ORDER BY Nume ASC")
        locations = cur.fetchall()

        return render_template("mn_edit.html", link=link, movies=movies, locations=locations)
    except Error as e:
        flash(f"Eroare DB: {e}", "error")
        return redirect(url_for("mn_list"))
    finally:
        if conn:
            conn.close()


@app.route("/mn/<int:idfilm>/<int:idlocatie>/edit", methods=["POST"])
def mn_update(idfilm, idlocatie):
    new_film = (request.form.get("idfilm") or "").strip()
    new_loc = (request.form.get("idlocatie") or "").strip()

    try:
        new_film_i = int(new_film)
        new_loc_i = int(new_loc)
    except ValueError:
        flash("Selectează film și locație valide.", "error")
        return redirect(url_for("mn_edit", idfilm=idfilm, idlocatie=idlocatie))

    # pentru PK compus: update = delete + insert
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("DELETE FROM MN WHERE idfilm=%s AND idlocatie=%s", (idfilm, idlocatie))
        cur.execute("INSERT INTO MN (idfilm, idlocatie) VALUES (%s, %s)", (new_film_i, new_loc_i))
        conn.commit()
        flash("Legătură MN modificată.", "ok")
    except Error as e:
        if conn:
            conn.rollback()
        flash(f"Eroare la modificare legătură: {e}", "error")
    finally:
        if conn:
            conn.close()

    return redirect(url_for("mn_list"))


@app.route("/mn/<int:idfilm>/<int:idlocatie>/delete", methods=["POST"])
def mn_delete(idfilm, idlocatie):
    conn = None
    try:
        conn = get_conn()
        cur = conn.cursor()
        cur.execute("DELETE FROM MN WHERE idfilm=%s AND idlocatie=%s", (idfilm, idlocatie))
        conn.commit()
        flash("Legătură MN ștearsă.", "ok")
    except Error as e:
        flash(f"Eroare la ștergere legătură: {e}", "error")
    finally:
        if conn:
            conn.close()

    return redirect(url_for("mn_list"))


if __name__ == "__main__":
    app.run(debug=True, host="127.0.0.1", port=5000)
