CREATE DATABASE IF NOT EXISTS `imdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `imdb`;

CREATE TABLE `peliculas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imdb_id` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `poster` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;