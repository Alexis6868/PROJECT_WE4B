-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : sam. 09 mai 2026 à 21:47
-- Version du serveur : 8.0.46
-- Version de PHP : 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tank`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260505113434', '2026-05-05 11:35:41', 69),
('DoctrineMigrations\\Version20260507133355', '2026-05-07 13:34:18', 165),
('DoctrineMigrations\\Version20260607222153', '2026-06-07 22:21:53', 120);

-- --------------------------------------------------------

--
-- Structure de la table `entrepot`
--

CREATE TABLE `entrepot` (
  `id` int NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `capacite` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entrepot`
--

INSERT INTO `entrepot` (`id`, `adresse`, `capacite`) VALUES
(1, '12 rue du muguet, Wintzenheim 68000', 499);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `id_user_id` int NOT NULL,
  `id_vehicule_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `date_debut`, `date_fin`, `prix`, `id_user_id`, `id_vehicule_id`) VALUES
(3, '2026-08-10 12:00:00', '2026-09-12 20:00:00', 34000.00, 8, 15);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `tel` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `tel`) VALUES
(1, 'alexis.zimmermann@utbm.fr', '[\"ROLE_ADMIN\"]', '123', 'Zimmermann', 'Alexis', NULL),
(3, 'alexis.zimmermann@uha.fr', '[\"ROLE_ADMIN\"]', '$2y$13$JTV.xSnGdDdMS5EaR.Vv7uJJ3L/1ykjumlSFZjionG/TPz.vW0q1K', 'Zimmermann', 'Alexis', NULL),
(5, 'zimmermann.pro@proton.me', '[\"ROLE_USER\"]', '$2y$13$vBu94c1mX6MlXA2/vnkCXO1nhZze3fu3OyYgm/mTsOhGsAfoDm/Ha', 'Jean', 'cultamere', '95959505'),
(6, 'jean@free.fr', '[\"ROLE_USER\"]', '$2y$13$lyyavwY5mAV5RlUi/Vui3e1ViWIzTIKi6RKwr21COfrZ6CeLQFXla', 'jean', 'boude', NULL),
(7, 'yolo@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$I2S/fEeRwZ4V7i35Z7/upekfjNEzY8rWmcPAx1h8YIARRMDsA7h22', 'jean', 'marie', '22222222222222222'),
(8, 'Mull@yahoo.fr', '[\"ROLE_USER\"]', '$2y$13$stIJ1j2pQ4IWgmh539rVueTFMI1WjX4XARuV3n9TnVJ4XKv9pgeLi', 'Müller', 'Alphonse', '05487652389');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `id` int NOT NULL,
  `nom` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `image` varchar(1024) NOT NULL,
  `etat` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `masse` int NOT NULL,
  `indice_maintenance` int NOT NULL,
  `entrepot_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `nom`, `type`, `image`, `etat`, `description`, `masse`, `indice_maintenance`, `entrepot_id`) VALUES
(2, 'Charle Leclerc', 'Char moyen', 'https://external-content.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.guer-coetquidan-broceliande.fr%2Fbisto%2Fcoet%2Fcampchars%2Fleclerc_3.jpg&f=1&nofb=1&ipt=ce47e9552b67b286d11d94233ed78aecfb37878be3663a50221e91cb678b7fbc', 'Opérationnel', 'Char de l\'armée française dit AMX-56', 56, 89, 1),
(3, 'Leopard 1 A4', 'Char moyen', 'https://2img.net/r/hpimg4/pics/855275leopardl1.jpg', 'En panne', 'Leopard 1 version A4', 42, 25, 1),
(4, 'M4A2', 'Char moyen', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/M4A3_Sherman_medium_tank_-_Collings_Foundation_-_Massachusetts_-_DSC07120-001.jpg/1920px-M4A3_Sherman_medium_tank_-_Collings_Foundation_-_Massachusetts_-_DSC07120-001.jpg', 'En maintenance', 'M4A2 sur flan arrière droit', 26, 48, 1),
(5, 'Panzer II \"Luchs\"', 'Char léger', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/Panzer_II_L.JPG/960px-Panzer_II_L.JPG', 'Opérationnel', 'Panzer II L', 13, 68, 1),
(6, 'AMX-13/105', 'Char léger', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/AMX_13_%2817264395602%29.jpg/1920px-AMX_13_%2817264395602%29.jpg', 'Opérationnel', 'AMX-13 version 105mm long', 13, 74, 1),
(7, 'AMX-13/75', 'Char léger', 'https://www.armyrecognition.com/images/stories/europe/france/light_armoured/amx-13/amx-13_photo_fiche_technique_640.jpg', 'En panne', 'AMX-13 version 75mm long', 13, 20, 1),
(8, 'Tigre II', 'Char Lourd', 'https://museedesblindes.fr/wp-content/uploads/2024/04/Tigre-II-depart-normandy-61-scaled.jpg', 'Opérationnel', 'Tigre II aus. H  88mm', 68, 87, 1),
(9, 'Moto-chenille Kettenkrad', 'Spécial', 'https://bringatrailer.com/wp-content/uploads/2021/06/1944_nsu_kettenkrad__sd-kfz-2_sonderkraftfahrzeug_2_162508451598f3c19DSC_0220.jpg', 'Opérationnel', 'Motocyclette à chenilles de reconnaissance', 1, 85, 1),
(10, 'Panzer III F', 'Char moyen', 'https://upload.wikimedia.org/wikipedia/commons/c/ca/Panzer_III_Ausf._F_U.S._Army_Armor_%26_Cavalry_Collection.jpg', 'En maintenance', 'Panzer III variant aus. F de 50mm', 23, 56, 1),
(11, 'Vespa 150 TAP', 'Spécial', 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Vespa_militare2.JPG/1920px-Vespa_militare2.JPG', 'Opérationnel', 'Bazooka Vespa des parachutistes français', 1, 90, 1),
(12, 'AML 90', 'Char léger', 'https://upload.wikimedia.org/wikipedia/commons/f/f6/AML-90_DM-SC-91-12078.JPEG', 'Opérationnel', 'Panhard AML version 90 F1 diesel', 5, 86, 1),
(13, 'T90', 'Char moyen', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRxnoKqClYmWnsrad9ygLrIr2WOXXFe7pbu6g&s', 'Opérationnel', 'T90 version A', 47, 77, 1),
(14, 'M3 Half-track', 'Transport', 'https://military-classic-vehicles.fr/wp-content/uploads/2023/11/IMG_4750.jpg', 'Opérationnel', 'Semi-chenillé transport de troupes US', 9, 80, 1),
(15, 'BTR-80', 'Transport', 'http://forcesoperations.com/wp-content/uploads/BTR-82A_and_Tigr-M_assembling_at_AMZ_plant_07-768x512-600x400.jpg', 'Réservé', 'Transport blindé - modèle 80 , 8 roues', 14, 73, 1),
(16, 'AB 43', 'Char léger', 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Autoblindo_AB-43.jpg/960px-Autoblindo_AB-43.jpg', 'Opérationnel', 'Autoblinda Modelo 43, Italie', 7, 61, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `entrepot`
--
ALTER TABLE `entrepot`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_42C8495579F37AE5` (`id_user_id`),
  ADD KEY `IDX_42C849555258F8E6` (`id_vehicule_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_292FFF1D72831E97` (`entrepot_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `entrepot`
--
ALTER TABLE `entrepot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_42C849555258F8E6` FOREIGN KEY (`id_vehicule_id`) REFERENCES `vehicule` (`id`),
  ADD CONSTRAINT `FK_42C8495579F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `FK_292FFF1D72831E97` FOREIGN KEY (`entrepot_id`) REFERENCES `entrepot` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;