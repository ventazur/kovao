-- Create syntax for TABLE 'activite'
CREATE TABLE `activite` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned DEFAULT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `semestre_id` int unsigned DEFAULT NULL,
  `ecole_id` int unsigned DEFAULT NULL,
  `groupe_id` int unsigned DEFAULT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  `identite` varchar(100) DEFAULT NULL,
  `unique_id` varchar(64) DEFAULT NULL,
  `agent_string` varchar(250) DEFAULT NULL,
  `plateforme` varchar(50) DEFAULT NULL,
  `fureteur` varchar(50) DEFAULT NULL,
  `fureteur_id` varchar(64) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `date` varchar(19) DEFAULT NULL,
  `epoch` int unsigned DEFAULT NULL,
  `referencement` varchar(250) DEFAULT NULL,
  `uri` varchar(50) DEFAULT NULL,
  `annee` smallint unsigned DEFAULT NULL,
  `mois` mediumint unsigned DEFAULT NULL,
  `jour` int unsigned DEFAULT NULL,
  `heure` varchar(2) DEFAULT NULL,
  `minute` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epoch` (`epoch`),
  KEY `etudiant_id` (`etudiant_id`),
  KEY `semestre_id` (`semestre_id`),
  KEY `enseignant_id` (`enseignant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=326293 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'activite_debug'
CREATE TABLE `activite_debug` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned DEFAULT NULL,
  `enseignant_id` int unsigned DEFAULT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `evaluation_id` int unsigned DEFAULT NULL,
  `evaluation_reference` int unsigned DEFAULT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `msg` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `msg_l` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `msg_json` json DEFAULT NULL,
  `class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `function` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fureteur` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `epoch` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22597 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create syntax for TABLE 'activite_evaluation'
CREATE TABLE `activite_evaluation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `semestre_id` int unsigned DEFAULT NULL,
  `etudiant_id` int unsigned NOT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(12) NOT NULL DEFAULT '',
  `soumission_id` int unsigned DEFAULT NULL,
  `soumission_reference` varchar(12) DEFAULT NULL,
  `question_id` int unsigned DEFAULT NULL,
  `action` varchar(500) NOT NULL,
  `action_court` varchar(100) DEFAULT NULL,
  `action_data` varchar(500) DEFAULT NULL,
  `date` varchar(19) NOT NULL DEFAULT '',
  `epoch` int unsigned NOT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  `fureteur_id` varchar(64) DEFAULT NULL,
  `fureteur` varchar(50) DEFAULT NULL,
  `plateforme` varchar(50) DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=214926 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'activite_terminer_evaluations'
CREATE TABLE `activite_terminer_evaluations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `groupe_id` int unsigned DEFAULT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(12) NOT NULL DEFAULT '',
  `terminaisons_forcees` int unsigned NOT NULL DEFAULT '0',
  `soumission_ids` mediumtext,
  `soumission_references` mediumtext,
  `date` varchar(19) NOT NULL DEFAULT '',
  `epoch` int unsigned NOT NULL,
  `cli` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5710 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'alertes'
CREATE TABLE `alertes` (
  `alerte_id` int unsigned NOT NULL AUTO_INCREMENT,
  `epoch` int unsigned NOT NULL,
  `date` varchar(19) NOT NULL DEFAULT '',
  `adresse_ip` varchar(15) NOT NULL DEFAULT '',
  `groupe_id` int DEFAULT NULL,
  `importance` tinyint(1) NOT NULL DEFAULT '3',
  `code` varchar(10) NOT NULL DEFAULT '',
  `desc` varchar(250) NOT NULL DEFAULT '',
  `uri` varchar(80) DEFAULT '',
  `extra` mediumtext,
  `enseignant_id_concerne` int DEFAULT NULL,
  PRIMARY KEY (`alerte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7801 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'blocs'
CREATE TABLE `blocs` (
  `bloc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `evaluation_id` int unsigned NOT NULL,
  `bloc_label` varchar(1) NOT NULL DEFAULT '',
  `bloc_points` decimal(10,2) NOT NULL,
  `bloc_nb_questions` tinyint unsigned NOT NULL DEFAULT '0',
  `bloc_desc` varchar(150) DEFAULT NULL,
  `bloc_actif` tinyint(1) NOT NULL DEFAULT '1',
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`bloc_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9993 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'ci_sessions'
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'courriels_envoyes'
CREATE TABLE `courriels_envoyes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `epoch` int unsigned NOT NULL,
  `date` varchar(19) DEFAULT NULL,
  `fournisseur_id` smallint NOT NULL,
  `courriel` varchar(100) NOT NULL DEFAULT '',
  `raison` varchar(50) DEFAULT NULL,
  `raison_data` mediumtext,
  `status_code` smallint DEFAULT '0',
  `erreur_msg` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70434 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'courriels_fournisseurs'
CREATE TABLE `courriels_fournisseurs` (
  `fournisseur_id` int unsigned NOT NULL AUTO_INCREMENT,
  `fournisseur_nom` varchar(50) DEFAULT NULL,
  `priorite` smallint DEFAULT '1',
  `limite` tinyint(1) NOT NULL DEFAULT '0',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`fournisseur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'courriels_fournisseurs_limites'
CREATE TABLE `courriels_fournisseurs_limites` (
  `fournisseur_id` int unsigned NOT NULL,
  `periode` varchar(1) NOT NULL DEFAULT '',
  `max` int unsigned NOT NULL,
  `calendrier` tinyint unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'cours'
CREATE TABLE `cours` (
  `cours_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned DEFAULT NULL,
  `groupe_id` int unsigned DEFAULT NULL,
  `cours_code` varchar(10) DEFAULT NULL,
  `cours_code_court` varchar(5) DEFAULT NULL,
  `cours_nom` varchar(250) DEFAULT NULL,
  `cours_nom_court` varchar(30) DEFAULT NULL,
  `cours_url` varchar(200) DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `desuet` tinyint unsigned NOT NULL DEFAULT '0',
  `actif` tinyint unsigned NOT NULL DEFAULT '1',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cours_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'documents'
CREATE TABLE `documents` (
  `doc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `question_id` int unsigned NOT NULL,
  `s3` tinyint unsigned NOT NULL DEFAULT '0',
  `doc_filename` varchar(100) NOT NULL DEFAULT '',
  `doc_caption` varchar(250) DEFAULT NULL,
  `doc_sha256` varchar(64) DEFAULT NULL,
  `doc_sha256_file` varchar(64) DEFAULT NULL,
  `doc_filesize` float unsigned NOT NULL,
  `doc_is_image` tinyint(1) NOT NULL,
  `doc_size_h` int unsigned DEFAULT NULL,
  `doc_size_w` int unsigned DEFAULT NULL,
  `doc_mime_type` varchar(128) DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `ajout_par_enseignant_id` int unsigned NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21466 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'documents_etudiants'
CREATE TABLE `documents_etudiants` (
  `doc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `etudiant_session_id` varchar(128) DEFAULT NULL,
  `evaluation_id` int unsigned DEFAULT NULL,
  `evaluation_reference` varchar(12) DEFAULT NULL,
  `soumission_id` int unsigned DEFAULT NULL,
  `soumission_reference` varchar(12) DEFAULT NULL,
  `question_id` int unsigned NOT NULL,
  `s3` tinyint unsigned NOT NULL DEFAULT '0',
  `doc_filename` varchar(100) NOT NULL DEFAULT '',
  `doc_sha256_file` varchar(64) DEFAULT NULL,
  `doc_filesize` float unsigned NOT NULL,
  `doc_is_image` tinyint(1) NOT NULL,
  `doc_size_h` int unsigned DEFAULT NULL,
  `doc_size_w` int unsigned DEFAULT NULL,
  `doc_mime_type` varchar(128) DEFAULT NULL,
  `doc_tn_filename` varchar(100) DEFAULT NULL,
  `doc_tn_sha256_file` varchar(64) DEFAULT NULL,
  `doc_tn_filesize` float unsigned DEFAULT NULL,
  `doc_tn_is_image` tinyint(1) NOT NULL DEFAULT '1',
  `doc_tn_size_h` int DEFAULT NULL,
  `doc_tn_size_w` int DEFAULT NULL,
  `doc_tn_mime_type` varchar(128) DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `modif_date` varchar(19) DEFAULT NULL,
  `modif_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24253 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'ecoles'
CREATE TABLE `ecoles` (
  `ecole_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ecole_nom` varchar(100) DEFAULT NULL,
  `ecole_nom_court` varchar(10) DEFAULT NULL,
  `ecole_url` varchar(200) DEFAULT NULL,
  `courriel_domaine` varchar(50) DEFAULT NULL,
  `numero_da_nom` varchar(15) DEFAULT NULL,
  `numero_da_desc` varchar(100) DEFAULT NULL,
  `plateforme` varchar(15) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `visible` tinyint unsigned NOT NULL DEFAULT '1',
  `actif` tinyint unsigned NOT NULL DEFAULT '1',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ecole_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'ecoles_ips'
CREATE TABLE `ecoles_ips` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ecole_id` int unsigned NOT NULL,
  `adresse_ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'eleves'
CREATE TABLE `eleves` (
  `eleve_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `semestre_id` int unsigned NOT NULL,
  `cours_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `cours_groupe` varchar(10) NOT NULL DEFAULT '',
  `eleve_prenom` varchar(30) DEFAULT '',
  `eleve_nom` varchar(50) DEFAULT NULL,
  `numero_da` varchar(20) DEFAULT NULL,
  `programme_code` varchar(10) DEFAULT NULL,
  `code_permanent` varchar(15) DEFAULT NULL,
  `temps_supp` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`eleve_id`),
  KEY `enseignant_id` (`enseignant_id`),
  KEY `semestre_id` (`semestre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17393 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'enseignants'
CREATE TABLE `enseignants` (
  `enseignant_id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_enseignant_id` int unsigned DEFAULT NULL,
  `courriel` varchar(100) NOT NULL,
  `courriel_confirmation` tinyint(1) NOT NULL DEFAULT '0',
  `nom` varchar(75) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `genre` varchar(1) NOT NULL DEFAULT 'M',
  `salt` varchar(64) DEFAULT NULL,
  `password` varchar(96) DEFAULT NULL,
  `privilege` tinyint NOT NULL DEFAULT '1',
  `cacher_evaluation` tinyint unsigned NOT NULL DEFAULT '0',
  `inscription_requise` tinyint unsigned NOT NULL DEFAULT '1',
  `montrer_rang` tinyint unsigned NOT NULL DEFAULT '1',
  `montrer_ecart_moy` tinyint unsigned NOT NULL DEFAULT '0',
  `permettre_fichiers_dangereux` tinyint(1) NOT NULL DEFAULT '0',
  `inscription_epoch` int unsigned NOT NULL,
  `inscription_date` varchar(19) DEFAULT NULL,
  `derniere_activite_date` varchar(19) DEFAULT NULL,
  `derniere_activite_epoch` int unsigned DEFAULT NULL,
  `activite_compteur` int unsigned NOT NULL DEFAULT '0',
  `actif` tinyint unsigned NOT NULL DEFAULT '0',
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`enseignant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'enseignants_groupes'
CREATE TABLE `enseignants_groupes` (
  `enseignant_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `semestre_id` int unsigned DEFAULT NULL,
  `niveau` tinyint unsigned NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'enseignants_groupes_demandes'
CREATE TABLE `enseignants_groupes_demandes` (
  `joindre_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL DEFAULT '0',
  `demande_date` varchar(19) DEFAULT NULL,
  `demande_epoch` int unsigned DEFAULT NULL,
  `demande_expiration` int unsigned DEFAULT NULL,
  `acceptee` tinyint NOT NULL DEFAULT '0',
  `refusee` tinyint unsigned NOT NULL DEFAULT '0',
  `traitement` tinyint(1) DEFAULT '0',
  `traitement_epoch` int DEFAULT NULL,
  `traitement_date` varchar(19) DEFAULT NULL,
  `traitement_expiration_epoch` int unsigned DEFAULT NULL,
  `traitement_expiration_date` varchar(19) DEFAULT NULL,
  `efface` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`joindre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'enseignants_traces'
CREATE TABLE `enseignants_traces` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned DEFAULT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `lab` tinyint(1) NOT NULL DEFAULT '0',
  `data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `data_modification_epoch` int unsigned DEFAULT NULL,
  `data_modification_date` varchar(19) DEFAULT NULL,
  `expiration_epoch` int unsigned DEFAULT NULL,
  `expiration_date` varchar(19) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49656 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants'
CREATE TABLE `etudiants` (
  `etudiant_id` int unsigned NOT NULL AUTO_INCREMENT,
  `courriel` varchar(100) DEFAULT NULL,
  `courriel_confirmation` tinyint(1) NOT NULL DEFAULT '0',
  `nom` varchar(75) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `genre` varchar(1) DEFAULT NULL,
  `salt` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(96) NOT NULL DEFAULT '',
  `inscription_epoch` int unsigned NOT NULL,
  `inscription_date` varchar(19) NOT NULL DEFAULT '',
  `derniere_activite_date` varchar(19) DEFAULT NULL,
  `derniere_activite_epoch` int unsigned DEFAULT NULL,
  `activite_compteur` int unsigned DEFAULT NULL,
  `courriel_evaluation_envoyee` tinyint(1) NOT NULL DEFAULT '1',
  `montrer_rang_cours` tinyint(1) NOT NULL DEFAULT '1',
  `montrer_rang_evaluation` tinyint(1) NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '0',
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `efface` tinyint DEFAULT '0',
  PRIMARY KEY (`etudiant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6502 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants_cours'
CREATE TABLE `etudiants_cours` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `etudiant_id` int unsigned NOT NULL,
  `cours_id` int unsigned NOT NULL,
  `semestre_id` int unsigned NOT NULL,
  `cours_groupe` varchar(10) DEFAULT NULL,
  `numero_da` varchar(20) DEFAULT NULL,
  `ajout_date` varchar(19) NOT NULL DEFAULT '',
  `ajout_epoch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6668 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants_evaluations_messages'
CREATE TABLE `etudiants_evaluations_messages` (
  `message_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(12) NOT NULL DEFAULT '',
  `semestre_id` int unsigned NOT NULL,
  `message` varchar(512) NOT NULL,
  `date` varchar(19) DEFAULT NULL,
  `epoch` int unsigned NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `evaluation_reference` (`evaluation_reference`)
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants_evaluations_notifications'
CREATE TABLE `etudiants_evaluations_notifications` (
  `notification_id` int unsigned NOT NULL AUTO_INCREMENT,
  `etudiant_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `semestre_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(12) DEFAULT NULL,
  `message_id` int unsigned NOT NULL,
  `extrait` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1123 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants_numero_da'
CREATE TABLE `etudiants_numero_da` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `etudiant_id` int unsigned NOT NULL,
  `ecole_id` int unsigned DEFAULT NULL,
  `groupe_id` int unsigned NOT NULL,
  `numero_da` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6369 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'etudiants_traces'
CREATE TABLE `etudiants_traces` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) DEFAULT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `semestre_id` int unsigned NOT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(12) NOT NULL DEFAULT '',
  `soumission_debut_epoch` int unsigned NOT NULL,
  `soumission_debut_date` varchar(19) NOT NULL,
  `lab` tinyint(1) NOT NULL DEFAULT '0',
  `lab_etudiant2_id` int unsigned DEFAULT NULL,
  `lab_etudiant3_id` int unsigned DEFAULT NULL,
  `data` mediumtext NOT NULL,
  `data_modification_epoch` int unsigned DEFAULT NULL,
  `data_modification_date` varchar(19) DEFAULT NULL,
  `secondes_en_redaction` int unsigned NOT NULL DEFAULT '0',
  `activite_epoch` int unsigned DEFAULT NULL,
  `activite_date` varchar(19) DEFAULT NULL,
  `soumission_id` int unsigned DEFAULT NULL,
  `soumission_reference` varchar(12) DEFAULT NULL,
  `evaluation_terminee` tinyint(1) NOT NULL DEFAULT '0',
  `evaluation_envoyee` tinyint(1) NOT NULL DEFAULT '0',
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_par_etudiant` tinyint(1) NOT NULL DEFAULT '0',
  `DESUET_modification_epoch` int unsigned DEFAULT NULL,
  `DESUET_modification_date` varchar(19) DEFAULT NULL,
  `DESUET_expiration_epoch` int NOT NULL,
  `DESUET_expiration_date` varchar(19) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluation_id` (`evaluation_id`),
  KEY `evaluation_reference` (`evaluation_reference`)
) ENGINE=InnoDB AUTO_INCREMENT=62551 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'evaluations'
CREATE TABLE `evaluations` (
  `evaluation_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `cours_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `public` tinyint(1) DEFAULT '1',
  `lab` tinyint(1) NOT NULL DEFAULT '0',
  `lab_parametres` json DEFAULT NULL,
  `lab_valeurs` json DEFAULT NULL,
  `lab_points` json DEFAULT NULL,
  `lab_corr_controller` varchar(100) DEFAULT NULL,
  `lab_vue` varchar(200) DEFAULT NULL,
  `lab_prefix` varchar(12) DEFAULT NULL,
  `ordre` float DEFAULT '99',
  `evaluation_titre` varchar(153) NOT NULL DEFAULT '',
  `evaluation_desc` mediumtext,
  `questions_aleatoires` tinyint(1) DEFAULT '0',
  `DESUET_inscription_requise` tinyint(1) NOT NULL DEFAULT '1',
  `DESUET_inscription_non_requise` tinyint(1) NOT NULL DEFAULT '0',
  `temps_en_redaction` tinyint(1) DEFAULT '0',
  `formative` tinyint(1) DEFAULT '0',
  `cadenas` tinyint(1) NOT NULL DEFAULT '0',
  `instructions` mediumtext,
  `archive` tinyint unsigned NOT NULL DEFAULT '0',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`evaluation_id`),
  KEY `groupe_id` (`groupe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2287 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'evaluations_ponderations'
CREATE TABLE `evaluations_ponderations` (
  `ponderation_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned DEFAULT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `semestre_id` int unsigned NOT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `ponderation` float DEFAULT NULL,
  `date` varchar(19) NOT NULL DEFAULT '',
  `epoch` int unsigned NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ponderation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1273 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'evaluations_securite_blocages'
CREATE TABLE `evaluations_securite_blocages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) NOT NULL DEFAULT '',
  `evaluation_id` int NOT NULL,
  `blocage_expiration_epoch` int NOT NULL,
  `blocage_expiration_date` varchar(19) DEFAULT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unique_id` (`unique_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'evaluations_securite_chargements'
CREATE TABLE `evaluations_securite_chargements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) NOT NULL DEFAULT '',
  `session_id` varchar(128) DEFAULT NULL,
  `evaluation_id` int NOT NULL,
  `evaluation_reference` varchar(12) DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `identite_presumee` varchar(100) DEFAULT NULL,
  `secondes_en_redaction` int unsigned NOT NULL DEFAULT '0',
  `epoch` int NOT NULL,
  `date` varchar(19) DEFAULT NULL,
  `expiration_epoch` int NOT NULL,
  `expiration_date` varchar(19) DEFAULT NULL,
  `activite_epoch` int DEFAULT NULL,
  `activite_date` varchar(19) DEFAULT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unique_id` (`unique_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79183 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'forums_commentaires'
CREATE TABLE `forums_commentaires` (
  `commentaire_id` int unsigned NOT NULL AUTO_INCREMENT,
  `commentaire_id_parent` int unsigned DEFAULT NULL,
  `message_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `json` tinyint(1) NOT NULL DEFAULT '1',
  `commentaire_contenu` mediumtext NOT NULL,
  `ajout_epoch` int NOT NULL,
  `ajout_date` varchar(19) NOT NULL DEFAULT '',
  `edite` tinyint(1) NOT NULL DEFAULT '0',
  `edite_epoch` int unsigned DEFAULT NULL,
  `edite_date` varchar(19) DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentaire_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'forums_messages'
CREATE TABLE `forums_messages` (
  `message_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `json` tinyint(1) NOT NULL DEFAULT '1',
  `titre` varchar(300) NOT NULL DEFAULT '',
  `contenu` mediumtext NOT NULL,
  `permettre_commentaires` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_epoch` int NOT NULL,
  `ajout_date` varchar(19) NOT NULL DEFAULT '',
  `edite` tinyint NOT NULL DEFAULT '0',
  `edite_epoch` int unsigned DEFAULT NULL,
  `edite_date` varchar(19) DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'forums_notifications_messages_lus'
CREATE TABLE `forums_notifications_messages_lus` (
  `enseignant_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `message_id` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'forums_notifications_messages_suivis'
CREATE TABLE `forums_notifications_messages_suivis` (
  `enseignant_id` int unsigned NOT NULL,
  `message_id` int unsigned NOT NULL,
  `derniere_lecture_date` varchar(19) NOT NULL DEFAULT '',
  `derniere_lecture_epoch` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'groupes'
CREATE TABLE `groupes` (
  `groupe_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ecole_id` int unsigned DEFAULT NULL,
  `groupe_code` varchar(25) DEFAULT NULL,
  `groupe_nom` varchar(50) DEFAULT NULL,
  `groupe_nom_court` varchar(15) DEFAULT NULL,
  `sous_domaine` varchar(20) DEFAULT NULL,
  `groupe_url` varchar(200) DEFAULT NULL,
  `denomination` varchar(50) DEFAULT NULL,
  `denomination_genre` varchar(1) DEFAULT 'M',
  `admin_enseignant_id` int unsigned DEFAULT NULL,
  `creation_enseignant_id` int unsigned DEFAULT NULL,
  `creation_epoch` int unsigned DEFAULT NULL,
  `creation_date` varchar(19) DEFAULT NULL,
  `inscription_permise` tinyint(1) NOT NULL DEFAULT '1',
  `inscription_code` varchar(15) DEFAULT '',
  `visible` tinyint unsigned NOT NULL DEFAULT '1',
  `actif` tinyint unsigned NOT NULL DEFAULT '1',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'inscriptions'
CREATE TABLE `inscriptions` (
  `inscription_id` int unsigned NOT NULL AUTO_INCREMENT,
  `etudiant` tinyint(1) NOT NULL DEFAULT '0',
  `enseignant` tinyint(1) NOT NULL DEFAULT '0',
  `reference_enseignant_id` int unsigned DEFAULT NULL,
  `courriel` varchar(100) NOT NULL,
  `clef_activation` varchar(10) NOT NULL DEFAULT '',
  `clef_activation_hash` varchar(32) NOT NULL DEFAULT '',
  `clef_activation_expiration` int unsigned NOT NULL,
  `clef_utilisee_date` varchar(19) DEFAULT NULL,
  `clef_utilisee_epoch` int DEFAULT NULL,
  `nom` varchar(75) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `genre` varchar(1) NOT NULL DEFAULT 'M',
  `numero_da` varchar(20) DEFAULT NULL,
  `salt` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(96) NOT NULL DEFAULT '',
  `recaptcha_score` float DEFAULT NULL,
  `inscription_date` varchar(19) NOT NULL DEFAULT '',
  `inscription_epoch` int unsigned NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`inscription_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7716 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'inscriptions_courriels_jetables'
CREATE TABLE `inscriptions_courriels_jetables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domaine` varchar(100) NOT NULL DEFAULT '',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=206635 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'inscriptions_invitations'
CREATE TABLE `inscriptions_invitations` (
  `invitation_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `courriel` varchar(100) NOT NULL DEFAULT '',
  `invitation_hash` varchar(16) NOT NULL DEFAULT '',
  `expiration_epoch` int unsigned NOT NULL,
  `expiration_date` varchar(19) NOT NULL DEFAULT '',
  `accepte` tinyint unsigned NOT NULL DEFAULT '0',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`invitation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'parametres'
CREATE TABLE `parametres` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `clef` varchar(128) NOT NULL DEFAULT '',
  `valeur` varchar(50) NOT NULL DEFAULT '',
  `niveau` tinyint unsigned DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `desc` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'questions'
CREATE TABLE `questions` (
  `question_id` int unsigned NOT NULL AUTO_INCREMENT,
  `evaluation_id` int unsigned DEFAULT NULL,
  `ordre` float NOT NULL DEFAULT '99',
  `bloc_id` int unsigned DEFAULT NULL,
  `question_texte` mediumtext,
  `question_type` tinyint DEFAULT NULL,
  `question_points` decimal(10,2) DEFAULT NULL,
  `sondage` tinyint(1) NOT NULL DEFAULT '0',
  `reponses_aleatoires` tinyint(1) NOT NULL DEFAULT '1',
  `selecteur` tinyint(1) NOT NULL DEFAULT '0',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_par_enseignant_id` int DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  KEY `evaluation_id` (`evaluation_id`),
  KEY `bloc_id` (`bloc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67988 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'questions_grilles_correction'
CREATE TABLE `questions_grilles_correction` (
  `grille_id` int unsigned NOT NULL AUTO_INCREMENT,
  `evaluation_id` int unsigned DEFAULT NULL,
  `question_id` int unsigned NOT NULL,
  `grille_affichage` tinyint(1) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_date` varchar(19) NOT NULL DEFAULT '',
  `ajout_epoch` int NOT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`grille_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5258 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'questions_grilles_correction_elements'
CREATE TABLE `questions_grilles_correction_elements` (
  `element_id` int unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int unsigned DEFAULT NULL,
  `grille_id` int unsigned NOT NULL,
  `element_type` tinyint(1) NOT NULL DEFAULT '1',
  `element_desc` varchar(500) DEFAULT NULL,
  `element_ordre` float unsigned NOT NULL DEFAULT '99',
  `element_pourcent` float unsigned NOT NULL,
  `actif` tinyint unsigned NOT NULL DEFAULT '1',
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_date` varchar(19) DEFAULT NULL,
  `efface_epoch` int unsigned DEFAULT NULL,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53061 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'questions_similarites'
CREATE TABLE `questions_similarites` (
  `similarite_id` int unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int unsigned NOT NULL,
  `similarite` tinyint NOT NULL,
  `variation` tinyint DEFAULT NULL,
  PRIMARY KEY (`similarite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1717 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'questions_tolerances'
CREATE TABLE `questions_tolerances` (
  `tolerance_id` int unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int unsigned NOT NULL,
  `tolerance` float NOT NULL,
  `type` tinyint(1) NOT NULL,
  `penalite` int NOT NULL,
  PRIMARY KEY (`tolerance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5450 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'rel_enseignants_cours'
CREATE TABLE `rel_enseignants_cours` (
  `relec_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `semestre_id` int unsigned NOT NULL,
  `cours_id` int unsigned NOT NULL,
  PRIMARY KEY (`relec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'rel_enseignants_evaluations'
CREATE TABLE `rel_enseignants_evaluations` (
  `relee_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `semestre_id` int unsigned NOT NULL,
  `cours_id` int unsigned DEFAULT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `evaluation_reference` varchar(6) DEFAULT NULL,
  `lab` tinyint(1) NOT NULL DEFAULT '0',
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `debut_date` varchar(19) DEFAULT NULL,
  `debut_epoch` int unsigned NOT NULL DEFAULT '0',
  `fin_date` varchar(19) DEFAULT NULL,
  `fin_epoch` int unsigned NOT NULL DEFAULT '0',
  `temps_limite` int unsigned DEFAULT NULL,
  `inscription_requise` tinyint unsigned NOT NULL DEFAULT '1',
  `cacher` tinyint unsigned NOT NULL DEFAULT '0',
  `bloquer` tinyint unsigned NOT NULL DEFAULT '0',
  `filtre_enseignant` tinyint unsigned NOT NULL DEFAULT '1',
  `filtre_enseignant_autorisation` tinyint unsigned NOT NULL DEFAULT '0',
  `filtre_cours` tinyint unsigned NOT NULL DEFAULT '0',
  `filtre_cours_autorisation` tinyint unsigned NOT NULL DEFAULT '0',
  `filtre_groupe` varchar(10) DEFAULT NULL,
  `filtre_groupe_autorisation` varchar(10) DEFAULT NULL,
  `filtre_etudiants` mediumtext,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_date` varchar(19) DEFAULT NULL,
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_par_cli` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`relee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=631 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'reponses'
CREATE TABLE `reponses` (
  `reponse_id` int unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int unsigned DEFAULT NULL,
  `question_type` tinyint DEFAULT NULL,
  `reponse_texte` mediumtext,
  `equation` tinyint(1) NOT NULL DEFAULT '0',
  `cs` tinyint(1) DEFAULT NULL,
  `unites` varchar(50) DEFAULT NULL,
  `notsci` tinyint(1) NOT NULL DEFAULT '0',
  `reponse_correcte` tinyint(1) NOT NULL DEFAULT '0',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ajout_par_enseignant_id` int unsigned DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`reponse_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=244557 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins'
CREATE TABLE `scrutins` (
  `scrutin_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `scrutin_texte` mediumtext NOT NULL,
  `echeance_date` varchar(19) DEFAULT NULL,
  `echeance_epoch` int unsigned DEFAULT NULL,
  `anonyme` tinyint unsigned NOT NULL DEFAULT '0',
  `code_morin` tinyint(1) NOT NULL DEFAULT '0',
  `lance` tinyint unsigned NOT NULL DEFAULT '0',
  `lance_scrutin_lance_id` int unsigned DEFAULT NULL,
  `lance_scrutin_reference` varchar(10) DEFAULT NULL,
  `creation_epoch` int unsigned NOT NULL,
  `creation_date` varchar(19) NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`scrutin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_choix'
CREATE TABLE `scrutins_choix` (
  `choix_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_id` int NOT NULL,
  `choix_texte` varchar(250) NOT NULL DEFAULT '',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_date` varchar(19) DEFAULT NULL,
  `efface_epoch` int unsigned DEFAULT NULL,
  PRIMARY KEY (`choix_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_documents'
CREATE TABLE `scrutins_documents` (
  `scrutin_doc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL,
  `scrutin_id` int unsigned NOT NULL,
  `doc_filename` varchar(100) NOT NULL DEFAULT '',
  `doc_caption` varchar(250) DEFAULT NULL,
  `doc_sha256` varchar(64) DEFAULT NULL,
  `doc_sha256_file` varchar(64) DEFAULT NULL,
  `doc_filesize` float unsigned NOT NULL,
  `doc_is_image` tinyint(1) NOT NULL,
  `doc_size_h` int unsigned DEFAULT NULL,
  `doc_size_w` int unsigned DEFAULT NULL,
  `doc_mime_type` varchar(50) DEFAULT NULL,
  `ajout_date` varchar(19) DEFAULT NULL,
  `ajout_epoch` int unsigned DEFAULT NULL,
  `ajout_par_enseignant_id` int unsigned NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`scrutin_doc_id`),
  KEY `question_id` (`scrutin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_lances'
CREATE TABLE `scrutins_lances` (
  `scrutin_lance_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_id` int unsigned NOT NULL,
  `groupe_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `scrutin_reference` varchar(10) NOT NULL DEFAULT '',
  `scrutin_texte` mediumtext NOT NULL,
  `lance_epoch` int unsigned NOT NULL,
  `lance_date` varchar(19) NOT NULL DEFAULT '',
  `echeance_date` varchar(19) DEFAULT NULL,
  `echeance_epoch` int unsigned DEFAULT NULL,
  `anonyme` tinyint(1) NOT NULL,
  `code_morin` tinyint(1) NOT NULL DEFAULT '0',
  `proposeur_enseignant_id` int DEFAULT NULL,
  `proposeur_epoch` int DEFAULT NULL,
  `proposeur_date` varchar(19) DEFAULT NULL,
  `appuyeur_enseignant_id` int unsigned DEFAULT NULL,
  `appuyeur_epoch` int unsigned DEFAULT NULL,
  `appuyeur_date` varchar(19) DEFAULT NULL,
  `termine` tinyint(1) NOT NULL DEFAULT '0',
  `termine_date` varchar(19) DEFAULT '',
  `termine_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`scrutin_lance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_lances_choix'
CREATE TABLE `scrutins_lances_choix` (
  `scrutin_lance_choix_id` int NOT NULL AUTO_INCREMENT,
  `scrutin_lance_id` int NOT NULL,
  `choix_texte` varchar(250) NOT NULL DEFAULT '',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`scrutin_lance_choix_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_lances_documents'
CREATE TABLE `scrutins_lances_documents` (
  `scrutin_lance_doc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_lance_id` int unsigned NOT NULL,
  `doc_filename_original` varchar(100) NOT NULL DEFAULT '',
  `doc_filename` varchar(100) NOT NULL DEFAULT '',
  `doc_caption` varchar(250) DEFAULT NULL,
  `doc_sha256` varchar(64) DEFAULT NULL,
  `doc_sha256_file` varchar(64) DEFAULT NULL,
  `doc_filesize` float unsigned NOT NULL,
  `doc_is_image` tinyint(1) NOT NULL,
  `doc_size_h` int unsigned DEFAULT NULL,
  `doc_size_w` int unsigned DEFAULT NULL,
  `doc_mime_type` varchar(50) DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`scrutin_lance_doc_id`),
  KEY `question_id` (`scrutin_lance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_lances_participants'
CREATE TABLE `scrutins_lances_participants` (
  `vote_participant_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_lance_id` int unsigned NOT NULL,
  `enseignant_id` int NOT NULL,
  `vote_termine` tinyint unsigned NOT NULL DEFAULT '0',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`vote_participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_lances_votes'
CREATE TABLE `scrutins_lances_votes` (
  `vote_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_reference` varchar(10) NOT NULL DEFAULT '',
  `scrutin_lance_id` int unsigned NOT NULL,
  `vote_salt` varchar(12) DEFAULT NULL,
  `vote_sha256` varchar(64) NOT NULL DEFAULT '',
  `enseignant_id` int unsigned DEFAULT NULL,
  `scrutin_lance_choix_id` int unsigned NOT NULL,
  `date` varchar(19) NOT NULL DEFAULT '',
  `epoch` int unsigned NOT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'scrutins_participants'
CREATE TABLE `scrutins_participants` (
  `participant_id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrutin_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_date` varchar(19) DEFAULT NULL,
  `efface_epoch` int DEFAULT NULL,
  PRIMARY KEY (`participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'securite_connexion_blocages'
CREATE TABLE `securite_connexion_blocages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `courriel` varchar(100) DEFAULT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  `expiration_epoch` int DEFAULT NULL,
  `expiration_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'securite_connexion_tentatives'
CREATE TABLE `securite_connexion_tentatives` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `courriel` varchar(100) DEFAULT NULL,
  `adresse_ip` varchar(19) DEFAULT NULL,
  `epoch` int DEFAULT NULL,
  `date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19949 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'semestres'
CREATE TABLE `semestres` (
  `semestre_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int DEFAULT NULL,
  `groupe_id` int unsigned DEFAULT NULL,
  `semestre_nom` varchar(25) DEFAULT NULL,
  `semestre_code` varchar(5) DEFAULT NULL,
  `semestre_debut_epoch` int unsigned DEFAULT NULL,
  `semestre_debut_date` varchar(19) DEFAULT NULL,
  `semestre_fin_epoch` int unsigned DEFAULT NULL,
  `semestre_fin_date` varchar(19) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`semestre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'soumissions'
CREATE TABLE `soumissions` (
  `soumission_id` int unsigned NOT NULL AUTO_INCREMENT,
  `groupe_id` int unsigned NOT NULL DEFAULT '0',
  `semestre_id` int unsigned DEFAULT NULL,
  `cours_id` int unsigned DEFAULT NULL,
  `evaluation_id` int unsigned NOT NULL,
  `enseignant_id` int unsigned NOT NULL,
  `etudiant_id` int unsigned DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `unique_id` varchar(64) DEFAULT NULL,
  `adresse_ip` varchar(15) DEFAULT NULL,
  `evaluation_reference` varchar(6) DEFAULT NULL,
  `soumission_reference` varchar(12) NOT NULL DEFAULT '',
  `empreinte` varchar(12) DEFAULT NULL,
  `soumission_data` text,
  `soumission_debut_epoch` int unsigned DEFAULT NULL,
  `soumission_date` varchar(19) NOT NULL DEFAULT '',
  `soumission_epoch` int unsigned NOT NULL,
  `prenom_nom` varchar(100) DEFAULT NULL,
  `numero_da` varchar(20) DEFAULT NULL,
  `courriel` varchar(100) DEFAULT NULL,
  `evaluation_data_gz` blob,
  `cours_data_gz` blob,
  `questions_data_gz` mediumblob,
  `images_data_gz` mediumblob,
  `documents_data_gz` mediumblob,
  `ajustements_data` mediumtext,
  `grilles_data` mediumtext,
  `commentaires_data_gz` mediumblob,
  `lab` tinyint(1) NOT NULL DEFAULT '0',
  `lab_etudiant2_id` int unsigned DEFAULT NULL,
  `lab_etudiant3_id` int unsigned DEFAULT NULL,
  `lab_data` json DEFAULT NULL,
  `lab_valeurs` json DEFAULT NULL,
  `lab_points` json DEFAULT NULL,
  `lab_points_champs` json DEFAULT NULL,
  `lab_points_tableaux` json DEFAULT NULL,
  `extra_data` mediumtext,
  `points_obtenus` decimal(10,2) DEFAULT '0.00',
  `points_total` decimal(10,2) DEFAULT '0.00',
  `points_evaluation` decimal(10,2) DEFAULT NULL,
  `corrections_manuelles` tinyint(1) NOT NULL DEFAULT '0',
  `corrections_terminees` tinyint(1) DEFAULT NULL,
  `permettre_visualisation` tinyint(1) DEFAULT '0',
  `permettre_visualisation_expiration` int unsigned NOT NULL DEFAULT '0',
  `vues` int unsigned DEFAULT '0',
  `version` tinyint NOT NULL DEFAULT '1',
  `non_terminee` tinyint(1) NOT NULL DEFAULT '0',
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`soumission_id`),
  KEY `enseignant_id` (`enseignant_id`),
  KEY `semestre_id` (`semestre_id`),
  KEY `reference` (`soumission_reference`),
  KEY `cours_id` (`cours_id`),
  KEY `etudiant_id` (`etudiant_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79405 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'soumissions_consultees'
CREATE TABLE `soumissions_consultees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `soumission_id` int unsigned NOT NULL,
  `soumission_reference` varchar(12) NOT NULL DEFAULT '',
  `etudiant_id` int unsigned DEFAULT NULL,
  `consulte_par_etudiant_id` int unsigned DEFAULT NULL,
  `enseignant_id` int unsigned DEFAULT NULL,
  `identite` varchar(100) DEFAULT NULL,
  `data` text,
  `date` varchar(19) NOT NULL DEFAULT '',
  `epoch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9919 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'soumissions_partagees'
CREATE TABLE `soumissions_partagees` (
  `soumission_p_id` int unsigned NOT NULL AUTO_INCREMENT,
  `soumission_id` int unsigned NOT NULL,
  `etudiant_id` int unsigned NOT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`soumission_p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create syntax for TABLE 'usagers_oubli_motdepasse'
CREATE TABLE `usagers_oubli_motdepasse` (
  `oubli_id` int unsigned NOT NULL AUTO_INCREMENT,
  `enseignant_id` int unsigned NOT NULL DEFAULT '0',
  `etudiant_id` int unsigned NOT NULL DEFAULT '0',
  `courriel` varchar(100) NOT NULL DEFAULT '',
  `clef_reinitialisation` varchar(10) NOT NULL DEFAULT '',
  `clef_reinitialisation_hash` varchar(32) NOT NULL DEFAULT '',
  `clef_reinitialisation_expiration` int unsigned DEFAULT NULL,
  `clef_utilisee_date` varchar(19) DEFAULT NULL,
  `clef_utilisee_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`oubli_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1850 DEFAULT CHARSET=utf8mb3;

-- Create syntax for TABLE 'variables'
CREATE TABLE `variables` (
  `variable_id` int unsigned NOT NULL AUTO_INCREMENT,
  `evaluation_id` int unsigned NOT NULL,
  `label` varchar(1) NOT NULL DEFAULT '',
  `minimum` float NOT NULL,
  `maximum` float NOT NULL,
  `decimales` int NOT NULL,
  `ns` tinyint(1) NOT NULL DEFAULT '0',
  `cs` tinyint NOT NULL DEFAULT '0',
  `variable_desc` varchar(150) DEFAULT NULL,
  `modification_epoch` int unsigned DEFAULT NULL,
  `efface` tinyint unsigned NOT NULL DEFAULT '0',
  `efface_epoch` int unsigned DEFAULT NULL,
  `efface_date` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`variable_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1606 DEFAULT CHARSET=utf8mb3;

