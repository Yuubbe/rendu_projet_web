-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           12.1.2-MariaDB - MariaDB Server
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour projet_web
DROP DATABASE IF EXISTS `projet_web`;
CREATE DATABASE IF NOT EXISTS `projet_web` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `projet_web`;

-- Listage de la structure de table projet_web. detail_location
DROP TABLE IF EXISTS `detail_location`;
CREATE TABLE IF NOT EXISTS `detail_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prix_jour` decimal(6,2) NOT NULL,
  `film_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DETAIL_FILM` (`film_id`),
  KEY `IDX_DETAIL_LOCATION` (`location_id`),
  CONSTRAINT `FK_DETAIL_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DETAIL_LOCATION` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.detail_location : ~3 rows (environ)
INSERT INTO `detail_location` (`id`, `prix_jour`, `film_id`, `location_id`) VALUES
	(1, 4.00, 1, 1),
	(2, 5.50, 2, 1),
	(3, 4.00, 4, 2),
	(4, 3.60, 2, 3),
	(5, 5.00, 5, 4);

-- Listage de la structure de table projet_web. doctrine_migration_versions
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table projet_web.doctrine_migration_versions : ~1 rows (environ)
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20260130174346', '2026-01-30 17:43:53', 100);

-- Listage de la structure de table projet_web. film
DROP TABLE IF EXISTS `film`;
CREATE TABLE IF NOT EXISTS `film` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `annee` int(11) NOT NULL,
  `duree` int(11) NOT NULL,
  `synopsis` varchar(255) NOT NULL,
  `prix_location_defaut` int(11) NOT NULL,
  `affiche` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.film : ~5 rows (environ)
INSERT INTO `film` (`id`, `titre`, `annee`, `duree`, `synopsis`, `prix_location_defaut`, `affiche`) VALUES
	(1, 'Inception', 2010, 148, 'Un voleur qui infiltre les rêves pour voler des secrets.', 5, '/images/inception.jpg'),
	(2, 'Le Seigneur des Anneaux : La Communauté de l\'Anneau', 2001, 178, 'Un hobbit se lance dans une quête pour détruire un anneau maléfique.', 4, '/images/lotr1.jpg'),
	(3, 'Interstellar', 2014, 169, 'Une équipe traverse un trou de ver pour trouver un nouveau foyer à l\'humanité.', 6, '/images/interstellar.jpg'),
	(4, 'Your Name', 2016, 106, 'Deux lycéens échangent mystérieusement leurs corps.', 4, '/images/yourname.jpg'),
	(5, 'Avengers: Endgame', 2019, 181, 'Les Avengers tentent d\'inverser les dégâts causés par Thanos.', 5, '/images/endgame.jpg');

-- Listage de la structure de table projet_web. film_genre
DROP TABLE IF EXISTS `film_genre`;
CREATE TABLE IF NOT EXISTS `film_genre` (
  `film_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  PRIMARY KEY (`film_id`,`genre_id`),
  KEY `IDX_FILM_GENRE_FILM` (`film_id`),
  KEY `IDX_FILM_GENRE_GENRE` (`genre_id`),
  CONSTRAINT `FK_FILM_GENRE_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FILM_GENRE_GENRE` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.film_genre : ~10 rows (environ)
INSERT INTO `film_genre` (`film_id`, `genre_id`) VALUES
	(1, 1),
	(1, 4),
	(2, 1),
	(2, 3),
	(3, 3),
	(3, 4),
	(4, 3),
	(4, 5),
	(5, 1),
	(5, 4);

-- Listage de la structure de table projet_web. genre
DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.genre : ~5 rows (environ)
INSERT INTO `genre` (`id`, `nom`) VALUES
	(1, 'Action'),
	(2, 'Comédie'),
	(3, 'Drame'),
	(4, 'Science-Fiction'),
	(5, 'Animation');

-- Listage de la structure de table projet_web. location
DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_location` date NOT NULL,
  `location_prix_final` decimal(8,2) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_LOCATION_USER` (`utilisateur_id`),
  CONSTRAINT `FK_LOCATION_USER` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.location : ~0 rows (environ)
INSERT INTO `location` (`id`, `date_location`, `location_prix_final`, `utilisateur_id`) VALUES
	(1, '2026-01-15', 9.50, 1),
	(2, '2026-02-01', 4.00, 3),
	(3, '2026-02-01', 3.60, 3),
	(4, '2026-02-01', 5.00, 3);

-- Listage de la structure de table projet_web. messenger_messages
DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Listage des données de la table projet_web.messenger_messages : ~0 rows (environ)

-- Listage de la structure de table projet_web. tarif
DROP TABLE IF EXISTS `tarif`;
CREATE TABLE IF NOT EXISTS `tarif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jour_semaine` varchar(20) NOT NULL,
  `coefficient` double NOT NULL,
  `film_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_TARIF_FILM` (`film_id`),
  CONSTRAINT `FK_TARIF_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.tarif : ~10 rows (environ)
INSERT INTO `tarif` (`id`, `jour_semaine`, `coefficient`, `film_id`) VALUES
	(1, 'lundi', 0.8, 1),
	(2, 'mardi', 0.8, 1),
	(3, 'vendredi', 1.1, 1),
	(4, 'samedi', 1.2, 1),
	(5, 'dimanche', 1.2, 1),
	(6, 'lundi', 0.9, 2),
	(7, 'vendredi', 1.1, 2),
	(8, 'samedi', 1.2, 2),
	(9, 'mercredi', 1, 3),
	(10, 'samedi', 1.3, 3);

-- Listage de la structure de table projet_web. user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_USER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.user : ~2 rows (environ)
INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
	(1, 'jean.dupont@example.com', '[]', 'password'),
	(2, 'claire.martin@example.com', '[]', 'password'),
	(3, 'yuumie31@gmail.com', '[]', '$2y$13$QdbKqeVwFnNMBsWkZSmXn.3MKFsIDatKaTD8sublp18.PtUOzGk6i');

-- Listage de la structure de table projet_web. user_film
DROP TABLE IF EXISTS `user_film`;
CREATE TABLE IF NOT EXISTS `user_film` (
  `user_id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`film_id`),
  KEY `IDX_USER_FILM_USER` (`user_id`),
  KEY `IDX_USER_FILM_FILM` (`film_id`),
  CONSTRAINT `FK_USER_FILM_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_USER_FILM_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projet_web.user_film : ~3 rows (environ)
INSERT INTO `user_film` (`user_id`, `film_id`) VALUES
	(1, 1),
	(1, 3),
	(2, 2),
	(3, 3);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
