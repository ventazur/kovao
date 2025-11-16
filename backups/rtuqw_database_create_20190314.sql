-- Create syntax for TABLE 'activite'
CREATE TABLE `activite` (
      `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
      `enseignant_id` int(11) unsigned DEFAULT NULL,
      `semestre_id` int(11) unsigned DEFAULT NULL,
      `ecole_id` int(11) unsigned DEFAULT NULL,
      `groupe_id` int(11) unsigned DEFAULT NULL,
      `adresse_ip` varchar(15) DEFAULT NULL,
      `identite` varchar(100) DEFAULT NULL,
      `unique_id` varchar(64) DEFAULT NULL,
      `agent_string` varchar(250) DEFAULT NULL,
      `plateforme` varchar(50) DEFAULT NULL,
      `fureteur` varchar(50) DEFAULT NULL,
      `mobile` varchar(50) DEFAULT NULL,
      `date` varchar(19) DEFAULT NULL,
      `epoch` int(11) unsigned DEFAULT NULL,
      `referencement` varchar(250) DEFAULT NULL,
      `uri` varchar(50) DEFAULT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41883 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'alertes'
CREATE TABLE `alertes` (
      `alerte_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `epoch` int(11) unsigned NOT NULL,
      `date` varchar(19) NOT NULL DEFAULT '',
      `adresse_ip` varchar(15) NOT NULL DEFAULT '',
      `code` varchar(10) NOT NULL DEFAULT '',
      `desc` varchar(250) NOT NULL DEFAULT '',
      `uri` varchar(80) DEFAULT '',
      `extra` mediumtext,
      PRIMARY KEY (`alerte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'blocs'
CREATE TABLE `blocs` (
      `bloc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `evaluation_id` int(10) unsigned NOT NULL,
      `bloc_label` varchar(1) NOT NULL DEFAULT '',
      `bloc_points` decimal(10,2) NOT NULL,
      `bloc_nb_questions` tinyint(3) unsigned NOT NULL DEFAULT '0',
      `bloc_desc` varchar(150) DEFAULT NULL,
      `bloc_actif` tinyint(1) NOT NULL DEFAULT '1',
      `efface` tinyint(1) NOT NULL DEFAULT '0',
      `efface_epoch` int(11) unsigned DEFAULT NULL,
      `efface_date` varchar(19) DEFAULT NULL,
      PRIMARY KEY (`bloc_id`),
      KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'ci_sessions'
CREATE TABLE `ci_sessions` (
      `id` varchar(128) NOT NULL,
      `ip_address` varchar(45) NOT NULL,
      `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
      `data` blob NOT NULL,
      PRIMARY KEY (`id`,`ip_address`),
      KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'cours'
CREATE TABLE `cours` (
      `cours_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `groupe_id` int(11) unsigned DEFAULT NULL,
      `cours_code` varchar(10) DEFAULT NULL,
      `cours_code_court` varchar(5) DEFAULT NULL,
      `cours_nom` varchar(250) DEFAULT NULL,
      `cours_nom_court` varchar(30) DEFAULT NULL,
      `cours_url` varchar(200) DEFAULT NULL,
      `actif` tinyint(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`cours_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'documents'
CREATE TABLE `documents` (
      `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `groupe_id` int(10) unsigned NOT NULL,
      `question_id` int(11) unsigned NOT NULL,
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
      PRIMARY KEY (`doc_id`),
      KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=448 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'ecoles'
CREATE TABLE `ecoles` (
      `ecole_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `ecole_nom` varchar(100) DEFAULT NULL,
      `ecole_nom_court` varchar(10) DEFAULT NULL,
      `actif` tinyint(1) NOT NULL DEFAULT '1',
      `ecole_url` varchar(200) DEFAULT NULL,
      `numero_da_nom` varchar(15) DEFAULT NULL,
      `numero_da_desc` varchar(100) DEFAULT NULL,
      `plateforme` varchar(15) DEFAULT NULL,
      PRIMARY KEY (`ecole_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'ecoles_ips'
CREATE TABLE `ecoles_ips` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `ecole_id` int(11) unsigned NOT NULL,
      `adresse_ip` varchar(15) NOT NULL DEFAULT '',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'eleves'
CREATE TABLE `eleves` (
      `eleve_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `enseignant_id` int(11) unsigned NOT NULL,
      `semestre_id` int(11) unsigned NOT NULL,
      `cours_id` int(11) unsigned NOT NULL,
      `groupe_id` int(11) unsigned NOT NULL,
      `cours_groupe` varchar(10) NOT NULL DEFAULT '',
      `eleve_prenom` varchar(30) DEFAULT '',
      `eleve_nom` varchar(50) DEFAULT NULL,
      `numero_da` varchar(20) DEFAULT NULL,
      `programme_code` varchar(10) DEFAULT NULL,
      `code_permanent` varchar(15) DEFAULT NULL,
      PRIMARY KEY (`eleve_id`),
      KEY `enseignant_id` (`enseignant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2299 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'enseignants'
CREATE TABLE `enseignants` (
      `enseignant_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    ue_id` (`unique_id`),
      KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'evaluations_securite_chargements'
CREATE TABLE `evaluations_securite_chargements` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `unique_id` varchar(64) NOT NULL DEFAULT '',
      `evaluation_id` int(11) NOT NULL,
      `epoch` int(11) NOT NULL,
      `date` varchar(19) DEFAULT NULL,
      `expiration_epoch` int(11) NOT NULL,
      `expiration_date` varchar(19) DEFAULT NULL,
      `adresse_ip` varchar(15) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `unique_id` (`unique_id`),
      KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=725 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'groupes'
CREATE TABLE `groupes` (
      `groupe_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `ecole_id` int(11) unsigned DEFAULT NULL,
      `groupe_code` varchar(25) DEFAULT NULL,
      `groupe_nom` varchar(50) DEFAULT NULL,
      `groupe_nom_court` varchar(10) DEFAULT NULL,
      `groupe_ur varchar(19) NOT NULL,
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

-- Create syntax for TABLE 'semestres'
CREATE TABLE `semestres` (
      `semestre_id` int(11) unsigned NOT NULL AUTO_IN
