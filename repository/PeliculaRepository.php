<?php
require_once __DIR__ . '/../config/db.php';

class PeliculaRepository {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function agregarFavorito($pelicula) {
        $stmt = $this->conn->prepare("INSERT INTO peliculas (imdb_id, nombre, genero, anio, descripcion, poster) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $pelicula->getImdbId(), $pelicula->getNombre(), $pelicula->getGenero(), $pelicula->getAnio(), $pelicula->getDescripcion(), $pelicula->getPoster());
        if (!$stmt->execute()) {
            throw new Exception($stmt->error ?: 'Execute failed');
        }
        $stmt->close();
    }

    public function eliminarFavorito($imdb_id) {
        $stmt = $this->conn->prepare("DELETE FROM peliculas WHERE imdb_id = ?");
        $stmt->bind_param("s", $imdb_id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error ?: 'Execute failed');
        }
        $stmt->close();
    }

    public function obtenerFavoritos() {
        $result = $this->conn->query("SELECT * FROM peliculas");
        $favoritos = [];
        while ($row = $result->fetch_assoc()) {
            $favoritos[] = new Pelicula($row['imdb_id'], $row['nombre'], $row['genero'], $row['anio'], $row['descripcion'], $row['poster'], $row['id']);
        }
        return $favoritos;
    }

    public function esFavorito($imdb_id) {
        $stmt = $this->conn->prepare("SELECT id FROM peliculas WHERE imdb_id = ?");
        $stmt->bind_param("s", $imdb_id);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
?>