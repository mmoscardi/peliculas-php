<?php
class Pelicula {
    private $id;
    private $imdb_id;
    private $nombre;
    private $genero;
    private $anio;
    private $descripcion;
    private $poster;

    public function __construct($imdb_id, $nombre, $genero = null, $anio = null, $descripcion = null, $poster = null, $id = null) {
        $this->id = $id;
        $this->imdb_id = $imdb_id;
        $this->nombre = $nombre;
        $this->genero = $genero;
        $this->anio = $anio;
        $this->descripcion = $descripcion;
        $this->poster = $poster;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getImdbId() { return $this->imdb_id; }
    public function getNombre() { return $this->nombre; }
    public function getGenero() { return $this->genero; }
    public function getAnio() { return $this->anio; }
    public function getDescripcion() { return $this->descripcion; }
    public function getPoster() { return $this->poster; }

    public function toArray() {
        return [
            'id' => $this->id,
            'imdb_id' => $this->imdb_id,
            'nombre' => $this->nombre,
            'genero' => $this->genero,
            'anio' => $this->anio,
            'descripcion' => $this->descripcion,
            'poster' => $this->poster
        ];
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setImdbId($imdb_id) { $this->imdb_id = $imdb_id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setGenero($genero) { $this->genero = $genero; }
    public function setAnio($anio) { $this->anio = $anio; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setPoster($poster) { $this->poster = $poster; }
}
?>