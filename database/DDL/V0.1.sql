
-- --------------------------------------------------------

--
-- Structure de la table `contact_event`
--

CREATE TABLE IF NOT EXISTS `contact_event` (
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
  PRIMARY KEY (`id`),
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `contact_vcard`
--

CREATE TABLE IF NOT EXISTS `contact_vcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) DEFAULT NULL,
  `n_title` varchar(255) DEFAULT NULL,
  `n_first` varchar(255) DEFAULT NULL,
  `n_last` varchar(255) DEFAULT NULL,
  `n_fn` varchar(255) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `contact_vcard_property`
--

CREATE TABLE IF NOT EXISTS `contact_vcard_property` (
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
