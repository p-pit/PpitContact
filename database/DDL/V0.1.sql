-- phpMyAdmin SQL Dump
-- version 4.1.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 22 Février 2016 à 21:57
-- Version du serveur :  5.6.15
-- Version de PHP :  5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Structure de la table `contact_community`
--

CREATE TABLE IF NOT EXISTS `contact_community` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `root_document` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;


-- --------------------------------------------------------

--
-- Structure de la table `contact_community_function`
--

CREATE TABLE IF NOT EXISTS `contact_community_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `customer_community_id` int(11) DEFAULT NULL,
  `supplyer_community_id` int(11) DEFAULT NULL,
  `function` varchar(255) DEFAULT NULL,
  `opening_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Structure de la table `contact_vcard`
--

CREATE TABLE IF NOT EXISTS `contact_vcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `n_title` varchar(255) DEFAULT NULL,
  `n_first` varchar(255) DEFAULT NULL,
  `n_last` varchar(255) DEFAULT NULL,
  `n_fn` varchar(255) DEFAULT NULL,
  `org` varchar(255) DEFAULT NULL,
  `tel_work` varchar(255) DEFAULT NULL,
  `tel_cell` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `properties` text,
  `photo_link_id` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=153 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

