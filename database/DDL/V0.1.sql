
-- --------------------------------------------------------

--
-- Structure de la table `contact_event`
--

CREATE TABLE `contact_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` varchar(2047) DEFAULT NULL,
  `comment` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `contact_vcard`
--

CREATE TABLE `contact_vcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `n_title` varchar(255) DEFAULT NULL,
  `n_first` varchar(255) DEFAULT NULL,
  `n_last` varchar(255) DEFAULT NULL,
  `n_fn` varchar(255) DEFAULT NULL,
  `org` varchar(255) DEFAULT NULL,
  `tel_work` varchar(255) DEFAULT NULL,
  `tel_cell` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `contact_vcard`
--

INSERT INTO `contact_vcard` (`id`, `n_title`, `n_first`, `n_last`, `n_fn`, `org`, `tel_work`, `tel_cell`, `email`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(1, 'M.', 'bruno', 'superadmin', 'superadmin, bruno', 'P-PIT', '+33 4 86 40 84 19', NULL, 'postmaster@confianceit.com', '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0);

-- --------------------------------------------------------

--
-- Structure de la table `contact_vcard_property`
--

CREATE TABLE `contact_vcard_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL,
  `blob_value` blob,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `contact_vcard_property`
--

INSERT INTO `contact_vcard_property` (`id`, `vcard_id`, `order`, `name`, `type`, `text_value`, `blob_value`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(1, 1, 5, 'ADR_street', 'work', '546, rue Baruch de Spinoza', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0),
(2, 1, 6, 'ADR_extended', 'work', 'Site Agroparc', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0),
(3, 1, 7, 'ADR_post_office_box', 'work', 'BP 51224', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0),
(4, 1, 8, 'ADR_zip', 'work', '84911', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0),
(5, 1, 9, 'ADR_city', 'work', 'AVIGNON Cedex 9', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0),
(6, 1, 10, 'ADR_country', 'work', '', NULL, '2015-06-12 13:14:19', 0, '2015-06-12 13:14:19', 0);

