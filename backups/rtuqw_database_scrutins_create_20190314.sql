-- Create syntax for TABLE 'scrutins'
CREATE TABLE `scrutins` (
      `scrutin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `groupe_id` int(10) unsigned NOT NULL,
      `enseignant_id` int(11) unsigned NOT NULL,
      `scrutin_texte` mediumtext NOT NULL,
      `anonyme` tinyint(1) unsigned NOT NULL DEFAULT '0',
      `echeance_date` varchar(19) DEFAULT NULL,
      `echeance_epoch` int(11) unsigned DEFAULT NULL,
      `lance` tinyint(1) NOT NULL DEFAULT '0',
      `lance_scrutin_reference` varchar(12) DEFAULT NULL,
      `creation_epoch` int(11) unsigned NOT NULL,
      `creation_date` varchar(19) NOT NULL,
      `efface` tinyint(1) NOT NULL DEFAULT '0',
      `efface_epoch` int(11) unsigned DEFAULT NULL,
      `efface_date` varchar(19) DEFAULT NULL,
      PRIMARY KEY (`scrutin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_choix'
CREATE TABLE `scrutins_choix` (
      `choix_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `scrutin_id` int(11) NOT NULL,
      `choix_texte` varchar(250) NOT NULL DEFAULT '',
      PRIMARY KEY (`choix_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_documents'
CREATE TABLE `scrutins_documents` (
      `scrutin_doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `groupe_id` int(11) unsigned NOT NULL,
      `scrutin_id` int(11) unsigned NOT NULL,
      `doc_filename` varchar(100) NOT NULL DEFAULT '',
      `doc_caption` varchar(250) DEFAULT NULL,
      `doc_sha256` varchar(64) DEFAULT NULL,
      `doc_sha256_file` varchar(64) DEFAULT NULL,
      `doc_filesize` float unsigned NOT NULL,
      `doc_is_image` tinyint(1) NOT NULL,
      `doc_size_h` int(11) unsigned DEFAULT NULL,
      `doc_size_w` int(11) unsigned DEFAULT NULL,
      `doc_mime_type` varchar(50) DEFAULT NULL,
      `ajout_date` varchar(19) DEFAULT NULL,
      `ajout_epoch` int(11) unsigned DEFAULT NULL,
      `ajout_par_enseignant_id` int(10) unsigned NOT NULL,
      `efface` tinyint(1) unsigned NOT NULL DEFAULT '0',
      `efface_epoch` int(11) unsigned DEFAULT NULL,
      `efface_date` varchar(19) DEFAULT NULL,
      PRIMARY KEY (`scrutin_doc_id`),
      KEY `question_id` (`scrutin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_lances'
CREATE TABLE `scrutins_lances` (
      `scrutin_lance_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `scrutin_id` int(11) unsigned DEFAULT NULL,
      `scrutin_reference` varchar(8) DEFAULT NULL,
      `choix_data` mediumtext,
      `enseignant_id` int(11) unsigned DEFAULT NULL,
      `anonyme` tinyint(1) NOT NULL,
      `lance_epoch` int(11) unsigned NOT NULL,
      `lance_date` varchar(19) NOT NULL DEFAULT '',
      `echeance_date` varchar(19) DEFAULT NULL,
      `echeance_epoch` int(11) unsigned DEFAULT NULL,
      `termine` tinyint(1) NOT NULL DEFAULT '0',
      `termine_date` varchar(19) DEFAULT '',
      `termine_epoch` int(11) unsigned DEFAULT NULL,
      `efface` tinyint(1) NOT NULL DEFAULT '0',
      `efface_date` varchar(19) DEFAULT NULL,
      `efface_epoch` int(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`scrutin_lance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_lances_participants'
CREATE TABLE `scrutins_lances_participants` (
      `vote_participant_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `scrutin_reference` varchar(8) NOT NULL DEFAULT '',
      `enseignant_id` int(1) NOT NULL,
      `vote_termine` tinyint(1) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`vote_participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=581 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_lances_votes'
CREATE TABLE `scrutins_lances_votes` (
      `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `scrutin_reference` varchar(8) DEFAULT NULL,
      `vote_salt` varchar(12) DEFAULT NULL,
      `vote_sha256` varchar(64) NOT NULL DEFAULT '',
      `enseignant_id` int(11) unsigned DEFAULT NULL,
      `choix_id` int(11) unsigned NOT NULL,
      `date` varchar(19) NOT NULL DEFAULT '',
      `epoch` int(11) NOT NULL,
      PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'scrutins_participants'
CREATE TABLE `scrutins_participants` (
      `participant_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `scrutin_id` int(11) unsigned NOT NULL,
      `enseignant_id` int(11) unsigned NOT NULL,
      PRIMARY KEY (`participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
