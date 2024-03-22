-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 30 août 2023 à 08:08
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `honey`
--

-- --------------------------------------------------------

--
-- Structure de la table `carousel`
--

CREATE TABLE `carousel` (
  `id_carousel` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carousel`
--

INSERT INTO `carousel` (`id_carousel`, `description`, `picture`, `status`) VALUES
(31, 'abeille_face', 'slider/63d563320f8ef-DSC_0394JPG.jpg', 'actif'),
(32, 'travail-des-alveoles', 'slider/63d56351729a3-DSC_0413JPG.jpg', 'actif'),
(33, 'cadre', 'slider/63d563a42690f-DSC_0352JPG.jpg', 'actif'),
(34, 'abeille profil', 'slider/63d563cac888c-DSC_0390JPG.jpg', 'actif'),
(35, 'entrée de la ruche', 'slider/63d563e56074b-DSC_0350JPG.jpg', 'actif'),
(36, 'miel coule des alvéoles', 'slider/63d564333ca66-DSC_0383JPG.jpg', 'actif'),
(37, 'trio sympa', 'slider/63d93e090f674-compo_trio_liquide.png', 'actif'),
(38, 'extra', 'slider/63dade9f92e3c-compo_confiseries.png', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_category` int NOT NULL,
  `categoryName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_category`, `categoryName`, `status`) VALUES
(1, 'Miels', 'actif'),
(2, 'Aromiels', 'actif'),
(3, 'Pains d\'épices', 'actif'),
(4, 'Nougat', 'actif'),
(5, 'Billes de miel', 'actif'),
(6, 'Compositions', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `countries`
--

CREATE TABLE `countries` (
  `id_country` int NOT NULL,
  `countryName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'france'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE `items` (
  `id_item` int NOT NULL,
  `itemRef` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_product` int NOT NULL,
  `pack` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `stock` smallint NOT NULL,
  `id_vat` int NOT NULL,
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `items`
--

INSERT INTO `items` (`id_item`, `itemRef`, `id_product`, `pack`, `price`, `stock`, `id_vat`, `status`) VALUES
(43, 'marais_01', 65, 'pot 125g', 3.8, 10, 1, 'actif'),
(44, 'marais_250', 65, 'pot de 250g', 6, 10, 1, 'actif'),
(47, 'propo', 72, 'pot de 250g', 5, 4, 1, 'actif'),
(48, 'euca', 71, 'pot de 250g', 5, 4, 1, 'actif'),
(49, 'pain_sel_110', 69, '110g', 3.2, 4, 1, 'actif'),
(50, 'pain_sel_325', 69, '325g', 6.8, 4, 1, 'actif'),
(51, 'pain_nat_110', 68, '110g', 3.15, 4, 1, 'actif'),
(52, 'pain_nat_325', 68, '325g', 6.4, 4, 1, 'actif'),
(53, 'nougat_50', 70, 'environ 50g', 2.6, 5, 1, 'actif'),
(54, 'aro_nois', 67, '250g', 9.2, 4, 1, 'actif'),
(55, 'aro_nois100', 67, '125g', 4.95, 4, 1, 'actif'),
(56, 'aron_citron', 66, '250g', 6.65, 4, 1, 'actif'),
(57, 'aron_citron100', 66, '125g', 3.5, 4, 1, 'actif'),
(58, 'miel_foret250', 64, '250g', 6, 4, 1, 'actif'),
(59, 'miel_foret125', 64, '125g', 3.2, 5, 1, 'actif'),
(60, 'meil_boc250', 63, '250g', 6, 4, 1, 'actif'),
(61, 'miel_boc125', 63, '125g', 3.8, 5, 1, 'actif'),
(62, 'miel_chat250', 62, '250g', 6, 5, 1, 'actif'),
(63, 'miel_chat125', 62, '125g', 3.8, 8, 1, 'actif'),
(64, 'miel_prin250', 61, '250g', 6, 8, 1, 'actif'),
(65, 'miel_prin125', 61, '125g', 3.8, 10, 1, 'actif'),
(66, 'miel_crem250', 60, '250g', 6.1, 8, 1, 'inactif'),
(67, 'miel_crem125', 60, '125g', 3.8, 10, 1, 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `id_news` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picture` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` set('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `news`
--

INSERT INTO `news` (`id_news`, `title`, `text`, `picture`, `date`, `status`) VALUES
(34, 'Ceci est une News titre seul', '', '', '2023-01-11 22:30:45', 'actif'),
(35, 'Spécial compositions', 'Les compositions sont disponibles uniquement en direct.\r\nLes différentes variétés de miels étant réalisées en fonction des récoltes merci de nous contacter pour vous assurer de leurs disponibilités.\r\nDes compositions personnalisées peuvent être réalisées, n\'hésitez pas à nous contacter.', 'newsPics/63d7d6dbce3ea-compo_grd_gourmet.png', '2023-01-11 22:31:23', 'actif'),
(37, 'Nos produits y seront !  Venez les découvrir.', '', 'newsPics/63c914d95e84d-affiche_2022.jpg', '2023-01-11 22:32:24', 'actif'),
(38, 'Marchés de Noël 2022', 'Soullans : La Pépinière de la rivière, Samedi et Dimanche 3 et 4 Décembre - 10h à 18h\r\nMouilleron le Captif, Village de Beaupuy, Samedi 10 Décembre de 15h à 21h30,Dimanche 11 Décembre 10h à 18h30\r\nPalluau: Espace de la Gachère (rue André Dorion), Dimanche 11 Décembre -10h à 18h\r\nSaint Hilaire de Riez: Parking de la Baritaudière, Dimanche 11 Décembre - 10h à 18h\r\nBrétignolles sur Mer: Place des Halles, Samedi 17 Décembre de 10h à 20h Dimanche 18 Décembre 10h à 18h', '', '2023-01-12 07:36:40', 'actif'),
(72, 'Que ce passe-t-il en hiver ?', 'Durant la morte saison, les abeilles hivernent. Elles se resserrent \"en grappe\" au centre de la ruche et occupent un espace restreint.\r\nEn agitant leurs ailes, elles produisent de la chaleur qui permet de conserver l’essaim à environ 12°C, quelle que soit la température extérieure.', 'newsPics/63d56c0fcd9b5-rsz_1rucherhiversdeux.jpg', '2023-01-28 18:36:00', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id_order` int NOT NULL,
  `id_user` int NOT NULL,
  `orderDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shipDate` datetime NOT NULL,
  `deliveryDate` datetime NOT NULL,
  `status` enum('en cours de traitement','expédié','livré') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en cours de traitement'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orders_details`
--

CREATE TABLE `orders_details` (
  `id_order_detail` int NOT NULL,
  `id_order` int NOT NULL,
  `id_item` int NOT NULL,
  `quantity` tinyint NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `id_vat` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id_product` int NOT NULL,
  `id_category` int NOT NULL,
  `productName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `productRef` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teaser` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `infos` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id_product`, `id_category`, `productName`, `productRef`, `teaser`, `description`, `infos`, `picture`, `status`) VALUES
(60, 1, 'Miel Crémeux', 'miel_crem', 'Rustique !', 'Miel crémeux aux grains grossiers, au parfum légèrement corsé, laissant découvrir une note de sarasin.', 'existe en 125g, 250g et 500g', 'img_of_products/63d7b62968f4a-miel_cremeux.png', 'actif'),
(61, 1, 'Miel de Printemps', 'miel_prin', 'Subtil et léger', 'Miel onctueux aux grains fins, légèrement parfumé.', 'existe en 125g, 250g et 500g', 'img_of_products/63d7b6b9b151c-miel_de_primptemps.png', 'actif'),
(62, 1, 'Miel de Châtaignier', 'miel_chat', 'Un miel puissant', 'Miel à l\'odeur prononcée, à la saveur boisée et corsée.\r\n\r\n***** Actuellement en rupture *****', 'existe en 125g, 250g', 'img_of_products/63d7b73d3f552-miel_de_chataignier.png', 'actif'),
(63, 1, 'Miel de Bocage', 'miel_boc', 'Notes de pissenlit.', 'Miel liquide,  ambré aux notes de pissenlit.', 'existe en 125g, 250g', 'img_of_products/63d7b7ce36533-miel_de_bocage.png', 'actif'),
(64, 1, 'Miel de Forêt', 'miel_foret', 'Notes d\'acacia, de châtaignier et de ronce.', 'Miel liquide.', 'existe en 125g, 250g', 'img_of_products/63d7b86f2723b-miel_de_foret.png', 'actif'),
(65, 1, 'Miel de Marais', 'miel_marais', 'Réalisé dans le marais vendéen.', 'Miel onctueux.', 'existe en 125g, 250g et 500g', 'img_of_products/63d7b8ca9858e-miel_de_marais.png', 'actif'),
(66, 2, 'Aromiel Citron', 'aro_citron', 'Une touche de pep\'s', 'Aromiel onctueux aux grains fins, parfumé à l\'huile essentielle alimentaire de citron.\r\n\r\n-- Ne convient pas aux femmes enceintes --', 'existe en 125g, 250g et 450g', 'img_of_products/63d7b97625f6f-aromiel_citron.png', 'actif'),
(67, 2, 'Aromiel Noisette', 'aro_noisette', 'Association de gourmandises...', 'Aromiel onctueux aux grains fins auquel a été associé de la pâte de noisette.', 'existe en 125g, 250g et 450g', 'img_of_products/63d7b9d550601-aromiel_noisette.png', 'actif'),
(68, 3, 'Pain d\'épices Nature', 'pain_nature', 'Le bon goût au naturel', 'Ingrédients: \r\nMiel 33%, farine de blé, farine de seigle, lait, sucre, oeufs, levures, épices.', 'conditionnement en 110g et 325g', 'img_of_products/63d7ba9a30065-pain_epice.png', 'actif'),
(69, 3, 'Pain d\'épices au sel', 'pain_sel', 'Complicité du miel et du sel', 'Parfait pour accompagner vos foies gras.\r\nIngrédients: \r\nMiel 33%, farine de blé, farine de seigle, lait, sucre, oeufs, levures, épices, sel 1,2%', 'conditionnement en 110g et 325g', 'img_of_products/63d7badeb5770-pain_epice.png', 'actif'),
(70, 4, 'Nougat', 'nou_100', 'Nougat artisanal maison', 'Ingrédients: \r\nMiel 30%, sucre, amandes, blanc d\'œufs, beurre de cacao', '50g  environ', 'img_of_products/63d7bbd7689b7-nougat.png', 'actif'),
(71, 5, 'Billes de miel à l\'Eucalyptus', 'bille_euca', 'Les bienfaits de l\'eucalyptus sous forme groumande', 'Ingrédents:\r\nsucre, sirop de glucose, miel(20%), huile essentielle d\'eucalyptus.', 'pot de 250g', 'img_of_products/63d7bca1ca035-billes_eucaplyptus.png', 'actif'),
(72, 5, 'Billes de miel à la Propolis', 'bille_pro', 'Vertus anti-infectieuses', 'Ingrédients: sucre, sirop de glucose, miel(20%), propolis(2%), extrait echinacéa bio, arôme naturel.', 'pots de 250 g', 'img_of_products/63d7be93325f0-billes_propiolis.png', 'actif'),
(73, 6, 'Le trio de Miels Crémeux', 'compo_trio_crem', '3 pots de miels de 125g', 'Miel de printemps, miel crémeux et miel de Bocage.', 'Sur commande en direct.', 'img_of_products/63d7caccd1353-compo_primtemps_noisette_cremeux.png', 'actif'),
(74, 6, 'Le trio de Miels Liquides', 'compo_trio_liq', '3 pots de miels de 125g', 'Miel de Marais, \r\nMiel de Forêt, \r\nMiel de châtaignier.', 'Sur commande en direct.', 'img_of_products/63d7cab022726-compo_trio_liquide_2.png', 'actif'),
(75, 6, 'L\'hexagone Confiseries', 'compo_hexa_conf', 'Aromiels / pain d\'épices /nougat', '2 aromiels 125g : Noisette et Citron.\r\n1 petit pain d\'épices nature (110g)\r\n1 Nougat (100g)', 'Sur commande en direct.', 'img_of_products/63d7d24d048b1-compo_confiseries.png', 'actif'),
(76, 6, 'L\'hexagone 100% miels', 'compo_hexa_miels', 'Farandole de miels et aromiels', '5pots de miels de 125g et 2 aromiels de 125g.\r\nMiel de marais, Miel de Bocage, Miel de Chataignier, Miel de printemps, Miel Crémeux, Aromiel Noisette, Aromiel Citron.', 'Sur commande en direct.', 'img_of_products/63d7d384cb771-compo_hexa_miels.png', 'actif'),
(77, 6, 'Coffret Petit Gourmet', 'compo_coffret_petit', '4 pots de miels, 2 pains d\'épices, 1 nougat', '4 pots de miels de 125g: \r\nMiel Crémeux, Miel de Printemps, Miel de Châtaignier, Miel de Marais\r\n2 Petits pains d’épices (110g) (Nature et Sel)\r\n1 Nougat (100g)', 'Sur commande en direct.', 'img_of_products/63d7d42983849-compo_petit_gourmand.png', 'actif'),
(78, 6, 'Coffret Grand Gourmet', 'compo_coffret_grand', '3 miels, 1 pain d\'épices et 2 nougats', '3 pots de miels de 250g\r\nMiel Crémeux, Miel de Marais, Miel de Printemps, \r\n1 Pains d\'épices nature (325g)\r\n2 Nougats (100g)', 'Sur commande  en direct.', 'img_of_products/63d7d50933c8e-compo_grd_gourmet.png', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `firstname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='table des utilisateurs ';

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `firstname`, `lastname`, `email`, `password`, `role`, `status`) VALUES
(1, 'Cyrille', 'TUAL', 'cyrille.tual@gmail.com', '$2y$10$NrONuNi/YuX.7fc3DbXVhOFjEH0EvcXzBWeQt.qVQhsq0yD1ej5GS', 'admin', 'actif'),
(50, 'Maitre', 'YODA', 'gogo@gmail.com', '$2y$10$y/khAl0OW.dO.VBGoqu5IuYMMOIXnpGWnr/awTf4l/HYTxCP9Uj7.', 'admin', 'actif'),
(60, 'Jean', 'DUPONT', 'jean@gmail.com', '$2y$10$H4v4xsndIkoB90fCVrtp5eIT95isTsHY/uuajb9nzEbMq9ZQL/P1e', 'client', 'inactif');

-- --------------------------------------------------------

--
-- Structure de la table `users_details`
--

CREATE TABLE `users_details` (
  `id_userDetails` int NOT NULL,
  `id_user` int NOT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adressExt` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` mediumint(5) UNSIGNED ZEROFILL NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_country` int NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='details des clients ';

-- --------------------------------------------------------

--
-- Structure de la table `vat`
--

CREATE TABLE `vat` (
  `id_vat` int NOT NULL,
  `rate` decimal(4,2) NOT NULL,
  `name` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ex 19.6% ',
  `comment` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vat`
--

INSERT INTO `vat` (`id_vat`, `rate`, `name`, `comment`, `status`) VALUES
(1, '5.50', '5.5 %', 'Vinaigre de miel, sirop contenant du miel, pollen, miel, gelée royale, glace au miel, pain d’épice, confiture au miel, propolis (si assimilée à un additif alimentaire)', 'actif'),
(2, '10.00', '10%', 'Essaims, de reines, de cellules royales, de cire…', 'actif'),
(3, '20.00', '20%', 'Hydromel, bonbons au miel, nougat…', 'actif');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id_carousel`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Index pour la table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id_country`);

--
-- Index pour la table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `items&products` (`id_product`),
  ADD KEY `items & vat` (`id_vat`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id_news`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `orders_details`
--
ALTER TABLE `orders_details`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `id_order` (`id_order`,`id_item`),
  ADD KEY `id_vat` (`id_vat`),
  ADD KEY `orders_details & iems` (`id_item`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `id_category` (`id_category`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Index pour la table `users_details`
--
ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id_userDetails`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `users_details & countries` (`id_country`);

--
-- Index pour la table `vat`
--
ALTER TABLE `vat`
  ADD PRIMARY KEY (`id_vat`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id_carousel` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `countries`
--
ALTER TABLE `countries`
  MODIFY `id_country` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `items`
--
ALTER TABLE `items`
  MODIFY `id_item` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `id_news` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `orders_details`
--
ALTER TABLE `orders_details`
  MODIFY `id_order_detail` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT pour la table `users_details`
--
ALTER TABLE `users_details`
  MODIFY `id_userDetails` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vat`
--
ALTER TABLE `vat`
  MODIFY `id_vat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items & vat` FOREIGN KEY (`id_vat`) REFERENCES `vat` (`id_vat`),
  ADD CONSTRAINT `items&products` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders & users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Contraintes pour la table `orders_details`
--
ALTER TABLE `orders_details`
  ADD CONSTRAINT `orders_details & iems` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`),
  ADD CONSTRAINT `orders_details & orders` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `orders_details & vat` FOREIGN KEY (`id_vat`) REFERENCES `vat` (`id_vat`);

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products & categories` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`);

--
-- Contraintes pour la table `users_details`
--
ALTER TABLE `users_details`
  ADD CONSTRAINT `users & users_details` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `users_details & countries` FOREIGN KEY (`id_country`) REFERENCES `countries` (`id_country`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
