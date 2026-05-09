-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : ven. 08 mai 2026 à 16:31
-- Version du serveur : 8.0.45
-- Version de PHP : 8.3.30

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
('DoctrineMigrations\\Version20260507133355', '2026-05-07 13:34:18', 165);

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
(1, '2026-03-20 08:20:00', '2026-03-21 20:00:00', 20.00, 1, 2);

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
(6, 'jean@free.fr', '[\"ROLE_USER\"]', '$2y$13$lyyavwY5mAV5RlUi/Vui3e1ViWIzTIKi6RKwr21COfrZ6CeLQFXla', 'jean', 'boude', NULL);

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
(2, 'Charle Leclerc', 'Charre lourd', 'https://external-content.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.guer-coetquidan-broceliande.fr%2Fbisto%2Fcoet%2Fcampchars%2Fleclerc_3.jpg&f=1&nofb=1&ipt=ce47e9552b67b286d11d94233ed78aecfb37878be3663a50221e91cb678b7fbc', 'Neuf', 'Char de l\'armée française', 10000, 10, 1),
(3, 'Leopard', 'Char jsp', 'https://external-content.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.guer-coetquidan-broceliande.fr%2Fbisto%2Fcoet%2Fcampchars%2Fleclerc_3.jpg&f=1&nofb=1&ipt=ce47e9552b67b286d11d94233ed78aecfb37878be3663a50221e91cb678b7fbc', 'Endommagé', 'Char de la guerre des chorizo', 1000, 10, 1);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
