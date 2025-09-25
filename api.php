<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once __DIR__ . '/controller/PeliculaController.php';

$controller = new PeliculaController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'peliculas':
        $peliculas = $controller->obtenerPeliculasDeAPI();
        echo json_encode($peliculas);
        break;
    case 'favoritos':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $favoritos = $controller->obtenerFavoritos();
            echo json_encode($favoritos);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            try {
                if (isset($data['action']) && $data['action'] === 'add') {
                    $controller->agregarFavorito(
                        $data['imdb_id'],
                        $data['nombre'],
                        $data['genero'],
                        $data['anio'],
                        $data['descripcion'],
                        $data['poster']
                    );
                    echo json_encode(['success' => true]);
                } elseif (isset($data['action']) && $data['action'] === 'delete') {
                    $controller->eliminarFavorito($data['imdb_id']);
                    echo json_encode(['success' => true]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage(), 'data' => $data]);
            }
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}


?>