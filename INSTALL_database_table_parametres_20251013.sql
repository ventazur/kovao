# ************************************************************
# Sequel Ace SQL dump
# Version 20095
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump de la table parametres
# ------------------------------------------------------------

DROP TABLE IF EXISTS `parametres`;

CREATE TABLE `parametres` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `clef` varchar(128) NOT NULL DEFAULT '',
  `valeur` varchar(50) NOT NULL DEFAULT '',
  `niveau` tinyint unsigned DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `desc` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

LOCK TABLES `parametres` WRITE;
/*!40000 ALTER TABLE `parametres` DISABLE KEYS */;

INSERT INTO `parametres` (`id`, `clef`, `valeur`, `niveau`, `type`, `desc`)
VALUES
	(1,'corrections_max_vues','5',NULL,'number','Le nombre maximum de fois qu\'un étudiant peut consulter une évaluation corrigée.'),
	(3,'evaluations_chargement_prevention','1',NULL,'boolean','Activer la prévention de chargement abusif des évaluations.'),
	(4,'evaluations_chargement_max','2',NULL,'number','Le nombre maximum de fois qu\'une évaluation peut être chargée (regénérée) par le même unique_id.'),
	(5,'evaluations_chargement_periode','15',NULL,'number','La période de temps (en minutes) que evaluations_chargement_max reste en vigueur.'),
	(6,'evaluations_chargement_periode_blocage','120',NULL,'number','La période de temps (en minutes) que l\'évaluation chargée (regénérée) abusivement est bloquée.'),
	(7,'evaluations_chargement_whitelist','1',NULL,'boolean','Désactiver le blocage des chargements abusifs pour les IP dans la whitelist.'),
	(8,'cache_actif','1',NULL,'boolean','Activer le cache.'),
	(9,'cache_actif_dev','0',NULL,'boolean','Activer le cache en mode développement.'),
	(10,'cache_ttl','60',NULL,'number','Le temps de survie du cache avant son autodestruction (en secondes).'),
	(11,'inscription_permise','1',NULL,'boolean','Permettre les nouvelles inscriptions.'),
	(12,'inscription_code','',NULL,'text','Ajouter un code pour limiter les inscriptions.'),
	(13,'maintenance','0',NULL,'boolean','Activer la page pour indiquer la maintenance du site (mode stricte, pour tout le monde sans exception).'),
	(14,'verifier_numero_da','1',NULL,'boolean','Vérifier que le numéro DA entré possède un numéro correspondant dans la liste des étudiants de l\'enseignant.'),
	(15,'securite_tentatives_connexion_prevention','1',NULL,'boolean','Activer le blocage suite à des tentatives de connexio infructueuses.'),
	(16,'securite_tentatives_connexion_max','10',NULL,'number','Le nombre de fois qu\'une connexion peut etre infructueuse avant le blocage.'),
	(17,'securite_tentatives_connexion_periode','15',NULL,'number','La période de temps (en minutes) que le maximum de tentatives est en vigueur.'),
	(18,'securite_tentatives_connexion_periode_blocage','30',NULL,'number','La période de temps (en minutes) qu\'une adresse IP est bloquée pour toute connexion.'),
	(19,'maintenance_admin','0',NULL,'boolean','Activer la page pour indiquer la maintenance du site, mais laissez l\'accès aux administrateurs, et laissez les étudiants envoyer leur soumission.'),
	(20,'inscription_permise_etudiant','1',NULL,'boolean','Permettre les nouvelles inscriptions d\'étudiant(e)s.'),
	(21,'inscription_expiration','3600',NULL,'number','Le temps maximal pour confirmer son courriel suite à l\'inscription (en secondes) (minimum : 3600, en multiple de 3600)'),
	(22,'inscription_permise_enseignant','1',NULL,'boolean','Permettre les nouvelles inscriptions d\'enseignant(e)s.'),
	(23,'inscription_invitation_expiration','259200',NULL,'number','Le temps maximal pour accepter une invitation d\'inscription (en secondes).'),
	(24,'evaluation_confirmation_courriel','1',NULL,'boolean','Permettre aux étudiants de recevoir une confirmation par courriel suite à l\'envoi de leur évaluation.'),
	(25,'alertes_importance','1',NULL,'number','L\'importance par défaut des alertes.'),
	(26,'alertes_importantes','5',NULL,'number','L\'importance à partir de laquelle les alertes sont considérées importantes.'),
	(27,'ping_etudiant_evaluation','1',NULL,'boolean','Détecter l\'activité d\'un étudiant lorqu\'il remplit une évaluation.'),
	(28,'ping_etudiant_evaluation_intervalle','7',NULL,'number','L\'intervalle en secondes entre les pings de l\'activité des étudiants en rédaction.'),
	(29,'forums','0',NULL,'boolean','Activer les forums.'),
	(30,'forums_commentaires','0',NULL,'boolean','Activer les commentaires dans les forums.'),
	(31,'scrutins','0',NULL,'boolean','Activer les scrutins.'),
	(32,'forums_commentaires_effacement_delai','259200',NULL,'number','Le délai permis pour effacer un commentaire par son auteur (secondes).'),
	(33,'forums_effacement_delai','259200',NULL,'number','Le délai permis pour effacer un message par son auteur (secondes)'),
	(34,'forums_intervalle_max','604800',NULL,'number','L\'intervalle maximum pour présenter les messages comme nouveaux (secondes).'),
	(35,'geolocalisation','0',NULL,'boolean','Activer la géolocalisation lors des évaluations (INACTIF).'),
	(36,'evaluation_montrer_rang','1',NULL,'boolean','Montrer le rang de l\'étudiant parmi les autres étudiants ayant fait la même évaluation.'),
	(37,'utiliser_s3','1',NULL,'boolean','Utiliser le service de storage d\'Amazon S3.'),
	(38,'evaluation_montrer_ecart_moyenne','1',NULL,'boolean','Montrer l\'écart à la moyenne des résultats de l\'étudiant pour une même évaluation.'),
	(39,'evaluation_montrer_rang_cours','1',NULL,'boolean','Montrer le rang de l\'étudiant par cours, pour un même enseignant.'),
	(40,'evaluation_ponderation','1',NULL,'boolean','Permettre les pondérations des évaluations.'),
	(41,'evaluations_non_terminees','1',NULL,'boolean','Envoyer (et corriger) les évaluations non terminées.'),
	(42,'courriels_dev','0',NULL,'boolean','Envoyer des courriels en mode développement.'),
	(43,'evaluation_activite_log','1',NULL,'boolean','Enregistrer l\'activité de l\'étudiant pendant son évaluation.'),
	(44,'app_version','1799',NULL,'number','Enregistrer la version de l\'application pour forcer un reload des pages avec des Javascrits (git commit number).'),
	(45,'kovahoot','0',NULL,'boolean','Activer les kovahoot.'),
	(46,'permettre_fichiers_dangereux','1',NULL,'boolean','Permettre les fichiers dangereux (doc, docx, xls, xlsx).'),
	(47,'ws_actif','0',NULL,'boolean','Activer les WebSockets.'),
	(48,'debogage_niveau','9',NULL,'number','Le niveau de débogage (1 à 9), 1 = peu et 9 = beaucoup.');

/*!40000 ALTER TABLE `parametres` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
