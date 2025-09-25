<?php
require_once __DIR__ . '/../model/Pelicula.php';
require_once __DIR__ . '/../repository/PeliculaRepository.php';

class PeliculaController {
    private $repository;

    public function __construct() {
        $this->repository = new PeliculaRepository();
    }

    public function obtenerPeliculasDeAPI() {
        // Llamada a la API de IMDb
        $url = "https://api.imdbapi.dev/titles?types=MOVIE&startYear=2021&languageCodes=es&minVoteCount=1000&minAggregateRating=6.0&sortBy=SORT_BY_POPULARITY&sortOrder=DESC&limit=50";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $peliculas = [];
        if (isset($data['titles'])) {
            foreach ($data['titles'] as $title) {
                $genero = isset($title['genres']) ? implode(', ', $title['genres']) : null;
                $anio = $title['startYear'] ?? null;
                $descripcion = $title['plot'] ?? null;
                $poster = isset($title['primaryImage']['url']) ? $title['primaryImage']['url'] : null;
                $peliculas[] = [
                    'imdb_id' => $title['id'],
                    'nombre' => $title['primaryTitle'],
                    'genero' => $genero,
                    'anio' => $anio,
                    'descripcion' => $descripcion,
                    'poster' => $poster
                ];
            }
        }
        return $peliculas;
    }

    public function agregarFavorito($imdb_id, $nombre, $genero, $anio, $descripcion, $poster) {
        $pelicula = new Pelicula($imdb_id, $nombre, $genero, $anio, $descripcion, $poster);
        $this->repository->agregarFavorito($pelicula);
    }

    public function eliminarFavorito($imdb_id) {
        $this->repository->eliminarFavorito($imdb_id);
    }

    public function obtenerFavoritos() {
        $favoritos = $this->repository->obtenerFavoritos();
        return array_map(function($fav) {
            return $fav->toArray();
        }, $favoritos);
    }

    public function esFavorito($imdb_id) {
        return $this->repository->esFavorito($imdb_id);
    }
}
?>