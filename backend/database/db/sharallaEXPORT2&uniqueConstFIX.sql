-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 16 déc. 2024 à 17:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sharalla`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertRandomUsers` ()   BEGIN
    DECLARE i INT DEFAULT 11;
    WHILE i <= 100 DO
        INSERT INTO users (
            id, email, username, password, last_login, rank, avatar, signup_date, auth_token
        ) VALUES (
            DEFAULT,
            CONCAT('user', i, '@example.com'), -- email
            CONCAT('user', i),                -- username
            CONCAT('password', i),            -- password
            DEFAULT,                          -- last_login
            3,                           -- rank
            CONCAT('avatar', i, '.png'),      -- avatar
            CURRENT_TIMESTAMP,                -- signup_date
            NULL                              -- auth_token
        );

        INSERT INTO status (
            id, label_id, user_id
        ) VALUES (
            DEFAULT, 
            2, 
            (SELECT id FROM users WHERE username = CONCAT('user', i))
        );
        
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content` varchar(2000) NOT NULL,
  `author_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content_types`
--

CREATE TABLE `content_types` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content_types`
--

INSERT INTO `content_types` (`id`, `label`) VALUES
(1, 'image');

-- --------------------------------------------------------

--
-- Structure de la table `extension`
--

CREATE TABLE `extension` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `extension`
--

INSERT INTO `extension` (`id`, `label`) VALUES
(1, 'jpg');

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `size` int(11) NOT NULL COMMENT 'files size',
  `path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uploader_id` int(11) NOT NULL,
  `extension_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'reference content type id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `files`
--

INSERT INTO `files` (`id`, `title`, `description`, `size`, `path`, `upload_date`, `uploader_id`, `extension_id`, `type_id`) VALUES
(1, 'Avatar1', 'Avatar of user 1', 85871, 'files/Users/1/Avatar1.jpg', '2024-12-16 16:54:12', 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `file_tags`
--

CREATE TABLE `file_tags` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `followed`
--

CREATE TABLE `followed` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `followed_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liked_content`
--

CREATE TABLE `liked_content` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Count like on given file id';

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `content` varchar(2000) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `send_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `power` int(11) NOT NULL DEFAULT 0 COMMENT '0-10 invite\r\n11-20 user\r\n>50 moderator\r\n100 admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ranks`
--

INSERT INTO `ranks` (`id`, `label`, `power`) VALUES
(1, 'ADMIN', 100),
(2, 'MODERATOR', 50),
(3, 'USER', 20),
(4, 'BANNED_USER', 15),
(5, 'INVITE', 10);

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `status`
--

INSERT INTO `status` (`id`, `label_id`, `user_id`) VALUES
(1, 1, 1),
(3, 2, 3),
(4, 2, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 2, 8),
(9, 2, 11),
(10, 2, 12),
(11, 2, 13),
(12, 2, 14),
(13, 2, 15),
(14, 2, 16),
(15, 2, 17),
(16, 2, 18),
(17, 2, 19),
(18, 2, 20),
(19, 2, 21),
(20, 2, 22),
(21, 2, 23),
(22, 2, 24),
(23, 2, 25),
(24, 2, 26),
(25, 2, 27),
(26, 2, 28),
(27, 2, 29),
(28, 2, 30),
(29, 2, 31),
(30, 2, 32),
(31, 2, 33),
(32, 2, 34),
(33, 2, 35),
(34, 2, 36),
(35, 2, 37),
(36, 2, 38),
(37, 2, 39),
(38, 2, 40),
(39, 2, 41),
(40, 2, 42),
(41, 2, 43),
(42, 2, 44),
(43, 2, 45),
(44, 2, 46),
(45, 2, 47),
(46, 2, 48),
(47, 2, 49),
(48, 2, 50),
(49, 2, 51),
(50, 2, 52),
(51, 2, 53),
(52, 2, 54),
(53, 2, 55),
(54, 2, 56),
(55, 2, 57),
(56, 2, 58),
(57, 2, 59),
(58, 2, 60),
(59, 2, 61),
(60, 2, 62),
(61, 2, 63),
(62, 2, 64),
(63, 2, 65),
(64, 2, 66),
(65, 2, 67),
(66, 2, 68),
(67, 2, 69),
(68, 2, 70),
(69, 2, 71),
(70, 2, 72),
(71, 2, 73),
(72, 2, 74),
(73, 2, 75),
(74, 2, 76),
(75, 2, 77),
(76, 2, 78),
(77, 2, 79),
(78, 2, 80),
(79, 2, 81),
(80, 2, 82),
(81, 2, 83),
(82, 2, 84),
(83, 2, 85),
(84, 2, 86),
(85, 2, 87),
(86, 2, 88),
(87, 2, 89),
(88, 2, 90),
(89, 2, 91),
(90, 2, 92),
(91, 2, 93),
(92, 2, 94),
(93, 2, 95),
(94, 2, 96),
(95, 2, 97),
(96, 2, 98),
(97, 2, 99),
(98, 2, 100),
(99, 2, 101),
(100, 2, 102),
(101, 2, 103),
(102, 2, 104),
(103, 2, 105),
(104, 2, 106),
(105, 2, 107),
(106, 2, 108),
(107, 2, 109),
(108, 2, 110);

-- --------------------------------------------------------

--
-- Structure de la table `status_label`
--

CREATE TABLE `status_label` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `status_label`
--

INSERT INTO `status_label` (`id`, `label`) VALUES
(1, 'ONLINE'),
(2, 'OFFLINE'),
(3, 'AWAY'),
(4, 'NOTDISTURB');

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rank` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `signup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `auth_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `last_login`, `rank`, `avatar`, `signup_date`, `auth_token`) VALUES
(1, 'admin@admin.admin', 'admin', '$2y$10$9Bb15YxW.Cs7t6VQSP0Cce7EorbQkKXTFoIjyPUsRkFxx4HSY8bfK', '2024-12-16 16:01:53', 1, 'admin', '2024-11-16 09:14:53', '49ebcc4c44a56080d889fc65070b92e57c021565121e0048c01ac8d7da885a'),
(3, 'user@user.fr', 'user', '$2y$10$of1h9ew47sE0yimGB8ebD.FMLIMHj.ySnphpjuwmOvQcpihbUYqoq', '2024-11-16 11:26:17', 3, 'placeholder', '2024-11-16 11:26:17', NULL),
(4, 'prydwen@prydwen.fr', 'prydwen', '$2y$10$bYRgXXYT/FkWih4wl5gbL.0d/OrJGdKZlXDDG3/aDfZDfmDYDJ9aK', '2024-11-16 11:29:56', 3, 'placeholder', '2024-11-16 11:29:56', NULL),
(5, 'alex@alex.fr', 'alex', '$2y$10$B8wnFg.eC4OrAj5jSH/ecOFNn3mqrhud7hSP7hRHzReOX9TrjWAJu', '2024-11-16 11:30:58', 3, 'placeholder', '2024-11-16 11:30:58', NULL),
(6, 'bobsample@gmail.com', 'bobsample', '$2y$10$xwt4o1bLa1BuVgMdtO94PeTsRYM0GMP6MUaWqKJ/kDVnF1hbqTzU.', '2024-11-16 11:34:54', 3, 'placeholder', '2024-11-16 11:34:54', NULL),
(7, 'dummy@dummy.fr', 'dummy', '$2y$10$wOasLnzGrCSX6AlrbZBv3eFutJacO7syFOzZ2.TyZMSa0yydK9AT.', '2024-11-17 08:16:09', 3, 'placeholder', '2024-11-17 08:16:09', NULL),
(8, 'snake@outerHeaven.solid', 'Snake', '$2y$10$BFjntrptDxZiNj6oDvnKx.5Lok2BSSy3B4ooIU2yvzK0Vgdj/kxdi', '2024-11-30 15:36:28', 3, 'placeholder', '2024-11-28 15:16:50', NULL),
(11, 'user1@example.com', 'user1', 'password1', '2024-12-04 16:07:51', 3, 'avatar1.png', '2024-12-04 16:07:51', NULL),
(12, 'user2@example.com', 'user2', 'password2', '2024-12-04 16:07:51', 3, 'avatar2.png', '2024-12-04 16:07:51', NULL),
(13, 'user3@example.com', 'user3', 'password3', '2024-12-04 16:07:51', 3, 'avatar3.png', '2024-12-04 16:07:51', NULL),
(14, 'user4@example.com', 'user4', 'password4', '2024-12-04 16:07:51', 3, 'avatar4.png', '2024-12-04 16:07:51', NULL),
(15, 'user5@example.com', 'user5', 'password5', '2024-12-04 16:07:51', 3, 'avatar5.png', '2024-12-04 16:07:51', NULL),
(16, 'user6@example.com', 'user6', 'password6', '2024-12-04 16:07:51', 3, 'avatar6.png', '2024-12-04 16:07:51', NULL),
(17, 'user7@example.com', 'user7', 'password7', '2024-12-04 16:07:51', 3, 'avatar7.png', '2024-12-04 16:07:51', NULL),
(18, 'user8@example.com', 'user8', 'password8', '2024-12-04 16:07:51', 3, 'avatar8.png', '2024-12-04 16:07:51', NULL),
(19, 'user9@example.com', 'user9', 'password9', '2024-12-04 16:07:51', 3, 'avatar9.png', '2024-12-04 16:07:51', NULL),
(20, 'user10@example.com', 'user10', 'password10', '2024-12-04 16:07:51', 3, 'avatar10.png', '2024-12-04 16:07:51', NULL),
(21, 'user11@example.com', 'user11', 'password11', '2024-12-04 16:09:29', 3, 'avatar11.png', '2024-12-04 16:09:29', NULL),
(22, 'user12@example.com', 'user12', 'password12', '2024-12-04 16:09:29', 3, 'avatar12.png', '2024-12-04 16:09:29', NULL),
(23, 'user13@example.com', 'user13', 'password13', '2024-12-04 16:09:29', 3, 'avatar13.png', '2024-12-04 16:09:29', NULL),
(24, 'user14@example.com', 'user14', 'password14', '2024-12-04 16:09:29', 3, 'avatar14.png', '2024-12-04 16:09:29', NULL),
(25, 'user15@example.com', 'user15', 'password15', '2024-12-04 16:09:29', 3, 'avatar15.png', '2024-12-04 16:09:29', NULL),
(26, 'user16@example.com', 'user16', 'password16', '2024-12-04 16:09:29', 3, 'avatar16.png', '2024-12-04 16:09:29', NULL),
(27, 'user17@example.com', 'user17', 'password17', '2024-12-04 16:09:29', 3, 'avatar17.png', '2024-12-04 16:09:29', NULL),
(28, 'user18@example.com', 'user18', 'password18', '2024-12-04 16:09:29', 3, 'avatar18.png', '2024-12-04 16:09:29', NULL),
(29, 'user19@example.com', 'user19', 'password19', '2024-12-04 16:09:29', 3, 'avatar19.png', '2024-12-04 16:09:29', NULL),
(30, 'user20@example.com', 'user20', 'password20', '2024-12-04 16:09:29', 3, 'avatar20.png', '2024-12-04 16:09:29', NULL),
(31, 'user21@example.com', 'user21', 'password21', '2024-12-04 16:09:29', 3, 'avatar21.png', '2024-12-04 16:09:29', NULL),
(32, 'user22@example.com', 'user22', 'password22', '2024-12-04 16:09:29', 3, 'avatar22.png', '2024-12-04 16:09:29', NULL),
(33, 'user23@example.com', 'user23', 'password23', '2024-12-04 16:09:29', 3, 'avatar23.png', '2024-12-04 16:09:29', NULL),
(34, 'user24@example.com', 'user24', 'password24', '2024-12-04 16:09:29', 3, 'avatar24.png', '2024-12-04 16:09:29', NULL),
(35, 'user25@example.com', 'user25', 'password25', '2024-12-04 16:09:29', 3, 'avatar25.png', '2024-12-04 16:09:29', NULL),
(36, 'user26@example.com', 'user26', 'password26', '2024-12-04 16:09:29', 3, 'avatar26.png', '2024-12-04 16:09:29', NULL),
(37, 'user27@example.com', 'user27', 'password27', '2024-12-04 16:09:29', 3, 'avatar27.png', '2024-12-04 16:09:29', NULL),
(38, 'user28@example.com', 'user28', 'password28', '2024-12-04 16:09:29', 3, 'avatar28.png', '2024-12-04 16:09:29', NULL),
(39, 'user29@example.com', 'user29', 'password29', '2024-12-04 16:09:29', 3, 'avatar29.png', '2024-12-04 16:09:29', NULL),
(40, 'user30@example.com', 'user30', 'password30', '2024-12-04 16:09:29', 3, 'avatar30.png', '2024-12-04 16:09:29', NULL),
(41, 'user31@example.com', 'user31', 'password31', '2024-12-04 16:09:29', 3, 'avatar31.png', '2024-12-04 16:09:29', NULL),
(42, 'user32@example.com', 'user32', 'password32', '2024-12-04 16:09:29', 3, 'avatar32.png', '2024-12-04 16:09:29', NULL),
(43, 'user33@example.com', 'user33', 'password33', '2024-12-04 16:09:29', 3, 'avatar33.png', '2024-12-04 16:09:29', NULL),
(44, 'user34@example.com', 'user34', 'password34', '2024-12-04 16:09:29', 3, 'avatar34.png', '2024-12-04 16:09:29', NULL),
(45, 'user35@example.com', 'user35', 'password35', '2024-12-04 16:09:29', 3, 'avatar35.png', '2024-12-04 16:09:29', NULL),
(46, 'user36@example.com', 'user36', 'password36', '2024-12-04 16:09:29', 3, 'avatar36.png', '2024-12-04 16:09:29', NULL),
(47, 'user37@example.com', 'user37', 'password37', '2024-12-04 16:09:29', 3, 'avatar37.png', '2024-12-04 16:09:29', NULL),
(48, 'user38@example.com', 'user38', 'password38', '2024-12-04 16:09:29', 3, 'avatar38.png', '2024-12-04 16:09:29', NULL),
(49, 'user39@example.com', 'user39', 'password39', '2024-12-04 16:09:29', 3, 'avatar39.png', '2024-12-04 16:09:29', NULL),
(50, 'user40@example.com', 'user40', 'password40', '2024-12-04 16:09:29', 3, 'avatar40.png', '2024-12-04 16:09:29', NULL),
(51, 'user41@example.com', 'user41', 'password41', '2024-12-04 16:09:29', 3, 'avatar41.png', '2024-12-04 16:09:29', NULL),
(52, 'user42@example.com', 'user42', 'password42', '2024-12-04 16:09:29', 3, 'avatar42.png', '2024-12-04 16:09:29', NULL),
(53, 'user43@example.com', 'user43', 'password43', '2024-12-04 16:09:29', 3, 'avatar43.png', '2024-12-04 16:09:29', NULL),
(54, 'user44@example.com', 'user44', 'password44', '2024-12-04 16:09:29', 3, 'avatar44.png', '2024-12-04 16:09:29', NULL),
(55, 'user45@example.com', 'user45', 'password45', '2024-12-04 16:09:29', 3, 'avatar45.png', '2024-12-04 16:09:29', NULL),
(56, 'user46@example.com', 'user46', 'password46', '2024-12-04 16:09:29', 3, 'avatar46.png', '2024-12-04 16:09:29', NULL),
(57, 'user47@example.com', 'user47', 'password47', '2024-12-04 16:09:29', 3, 'avatar47.png', '2024-12-04 16:09:29', NULL),
(58, 'user48@example.com', 'user48', 'password48', '2024-12-04 16:09:29', 3, 'avatar48.png', '2024-12-04 16:09:29', NULL),
(59, 'user49@example.com', 'user49', 'password49', '2024-12-04 16:09:29', 3, 'avatar49.png', '2024-12-04 16:09:29', NULL),
(60, 'user50@example.com', 'user50', 'password50', '2024-12-04 16:09:29', 3, 'avatar50.png', '2024-12-04 16:09:29', NULL),
(61, 'user51@example.com', 'user51', 'password51', '2024-12-04 16:09:29', 3, 'avatar51.png', '2024-12-04 16:09:29', NULL),
(62, 'user52@example.com', 'user52', 'password52', '2024-12-04 16:09:29', 3, 'avatar52.png', '2024-12-04 16:09:29', NULL),
(63, 'user53@example.com', 'user53', 'password53', '2024-12-04 16:09:29', 3, 'avatar53.png', '2024-12-04 16:09:29', NULL),
(64, 'user54@example.com', 'user54', 'password54', '2024-12-04 16:09:29', 3, 'avatar54.png', '2024-12-04 16:09:29', NULL),
(65, 'user55@example.com', 'user55', 'password55', '2024-12-04 16:09:29', 3, 'avatar55.png', '2024-12-04 16:09:29', NULL),
(66, 'user56@example.com', 'user56', 'password56', '2024-12-04 16:09:29', 3, 'avatar56.png', '2024-12-04 16:09:29', NULL),
(67, 'user57@example.com', 'user57', 'password57', '2024-12-04 16:09:29', 3, 'avatar57.png', '2024-12-04 16:09:29', NULL),
(68, 'user58@example.com', 'user58', 'password58', '2024-12-04 16:09:29', 3, 'avatar58.png', '2024-12-04 16:09:29', NULL),
(69, 'user59@example.com', 'user59', 'password59', '2024-12-04 16:09:29', 3, 'avatar59.png', '2024-12-04 16:09:29', NULL),
(70, 'user60@example.com', 'user60', 'password60', '2024-12-04 16:09:29', 3, 'avatar60.png', '2024-12-04 16:09:29', NULL),
(71, 'user61@example.com', 'user61', 'password61', '2024-12-04 16:09:29', 3, 'avatar61.png', '2024-12-04 16:09:29', NULL),
(72, 'user62@example.com', 'user62', 'password62', '2024-12-04 16:09:29', 3, 'avatar62.png', '2024-12-04 16:09:29', NULL),
(73, 'user63@example.com', 'user63', 'password63', '2024-12-04 16:09:29', 3, 'avatar63.png', '2024-12-04 16:09:29', NULL),
(74, 'user64@example.com', 'user64', 'password64', '2024-12-04 16:09:29', 3, 'avatar64.png', '2024-12-04 16:09:29', NULL),
(75, 'user65@example.com', 'user65', 'password65', '2024-12-04 16:09:29', 3, 'avatar65.png', '2024-12-04 16:09:29', NULL),
(76, 'user66@example.com', 'user66', 'password66', '2024-12-04 16:09:29', 3, 'avatar66.png', '2024-12-04 16:09:29', NULL),
(77, 'user67@example.com', 'user67', 'password67', '2024-12-04 16:09:29', 3, 'avatar67.png', '2024-12-04 16:09:29', NULL),
(78, 'user68@example.com', 'user68', 'password68', '2024-12-04 16:09:29', 3, 'avatar68.png', '2024-12-04 16:09:29', NULL),
(79, 'user69@example.com', 'user69', 'password69', '2024-12-04 16:09:29', 3, 'avatar69.png', '2024-12-04 16:09:29', NULL),
(80, 'user70@example.com', 'user70', 'password70', '2024-12-04 16:09:29', 3, 'avatar70.png', '2024-12-04 16:09:29', NULL),
(81, 'user71@example.com', 'user71', 'password71', '2024-12-04 16:09:29', 3, 'avatar71.png', '2024-12-04 16:09:29', NULL),
(82, 'user72@example.com', 'user72', 'password72', '2024-12-04 16:09:29', 3, 'avatar72.png', '2024-12-04 16:09:29', NULL),
(83, 'user73@example.com', 'user73', 'password73', '2024-12-04 16:09:29', 3, 'avatar73.png', '2024-12-04 16:09:29', NULL),
(84, 'user74@example.com', 'user74', 'password74', '2024-12-04 16:09:29', 3, 'avatar74.png', '2024-12-04 16:09:29', NULL),
(85, 'user75@example.com', 'user75', 'password75', '2024-12-04 16:09:29', 3, 'avatar75.png', '2024-12-04 16:09:29', NULL),
(86, 'user76@example.com', 'user76', 'password76', '2024-12-04 16:09:29', 3, 'avatar76.png', '2024-12-04 16:09:29', NULL),
(87, 'user77@example.com', 'user77', 'password77', '2024-12-04 16:09:29', 3, 'avatar77.png', '2024-12-04 16:09:29', NULL),
(88, 'user78@example.com', 'user78', 'password78', '2024-12-04 16:09:29', 3, 'avatar78.png', '2024-12-04 16:09:29', NULL),
(89, 'user79@example.com', 'user79', 'password79', '2024-12-04 16:09:29', 3, 'avatar79.png', '2024-12-04 16:09:29', NULL),
(90, 'user80@example.com', 'user80', 'password80', '2024-12-04 16:09:29', 3, 'avatar80.png', '2024-12-04 16:09:29', NULL),
(91, 'user81@example.com', 'user81', 'password81', '2024-12-04 16:09:29', 3, 'avatar81.png', '2024-12-04 16:09:29', NULL),
(92, 'user82@example.com', 'user82', 'password82', '2024-12-04 16:09:29', 3, 'avatar82.png', '2024-12-04 16:09:29', NULL),
(93, 'user83@example.com', 'user83', 'password83', '2024-12-04 16:09:29', 3, 'avatar83.png', '2024-12-04 16:09:29', NULL),
(94, 'user84@example.com', 'user84', 'password84', '2024-12-04 16:09:29', 3, 'avatar84.png', '2024-12-04 16:09:29', NULL),
(95, 'user85@example.com', 'user85', 'password85', '2024-12-04 16:09:29', 3, 'avatar85.png', '2024-12-04 16:09:29', NULL),
(96, 'user86@example.com', 'user86', 'password86', '2024-12-04 16:09:29', 3, 'avatar86.png', '2024-12-04 16:09:29', NULL),
(97, 'user87@example.com', 'user87', 'password87', '2024-12-04 16:09:29', 3, 'avatar87.png', '2024-12-04 16:09:29', NULL),
(98, 'user88@example.com', 'user88', 'password88', '2024-12-04 16:09:29', 3, 'avatar88.png', '2024-12-04 16:09:29', NULL),
(99, 'user89@example.com', 'user89', 'password89', '2024-12-04 16:09:29', 3, 'avatar89.png', '2024-12-04 16:09:29', NULL),
(100, 'user90@example.com', 'user90', 'password90', '2024-12-04 16:09:29', 3, 'avatar90.png', '2024-12-04 16:09:29', NULL),
(101, 'user91@example.com', 'user91', 'password91', '2024-12-04 16:09:29', 3, 'avatar91.png', '2024-12-04 16:09:29', NULL),
(102, 'user92@example.com', 'user92', 'password92', '2024-12-04 16:09:29', 3, 'avatar92.png', '2024-12-04 16:09:29', NULL),
(103, 'user93@example.com', 'user93', 'password93', '2024-12-04 16:09:29', 3, 'avatar93.png', '2024-12-04 16:09:29', NULL),
(104, 'user94@example.com', 'user94', 'password94', '2024-12-04 16:09:29', 3, 'avatar94.png', '2024-12-04 16:09:29', NULL),
(105, 'user95@example.com', 'user95', 'password95', '2024-12-04 16:09:29', 3, 'avatar95.png', '2024-12-04 16:09:29', NULL),
(106, 'user96@example.com', 'user96', 'password96', '2024-12-04 16:09:29', 3, 'avatar96.png', '2024-12-04 16:09:29', NULL),
(107, 'user97@example.com', 'user97', 'password97', '2024-12-04 16:09:29', 3, 'avatar97.png', '2024-12-04 16:09:29', NULL),
(108, 'user98@example.com', 'user98', 'password98', '2024-12-04 16:09:29', 3, 'avatar98.png', '2024-12-04 16:09:29', NULL),
(109, 'user99@example.com', 'user99', 'password99', '2024-12-04 16:09:29', 3, 'avatar99.png', '2024-12-04 16:09:29', NULL),
(110, 'user100@example.com', 'user100', 'password100', '2024-12-04 16:09:29', 3, 'avatar100.png', '2024-12-04 16:09:29', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Index pour la table `content_types`
--
ALTER TABLE `content_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `unique_label` (`label`);

--
-- Index pour la table `extension`
--
ALTER TABLE `extension`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `unique_label` (`label`);

--
-- Index pour la table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `uploader_id` (`uploader_id`),
  ADD KEY `extension_id` (`extension_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Index pour la table `file_tags`
--
ALTER TABLE `file_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Index pour la table `followed`
--
ALTER TABLE `followed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Index pour la table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Index pour la table `liked_content`
--
ALTER TABLE `liked_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Index pour la table `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `label_id` (`label_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `status_label`
--
ALTER TABLE `status_label`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `rank` (`rank`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `content_types`
--
ALTER TABLE `content_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `extension`
--
ALTER TABLE `extension`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `file_tags`
--
ALTER TABLE `file_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `followed`
--
ALTER TABLE `followed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `liked_content`
--
ALTER TABLE `liked_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT pour la table `status_label`
--
ALTER TABLE `status_label`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`extension_id`) REFERENCES `extension` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `content_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `file_tags`
--
ALTER TABLE `file_tags`
  ADD CONSTRAINT `file_tags_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `file_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `followed`
--
ALTER TABLE `followed`
  ADD CONSTRAINT `followed_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `followed_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `liked_content`
--
ALTER TABLE `liked_content`
  ADD CONSTRAINT `liked_content_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `liked_content_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `status_ibfk_1` FOREIGN KEY (`label_id`) REFERENCES `status_label` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `status_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rank`) REFERENCES `ranks` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
