<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* --------------------------------------------------------------------------
 *
 * CHARGER LES DONNEES CONFIDENTIELLES
 *
 * -------------------------------------------------------------------------- */

require(APPPATH . '/../../config_secure.php');

/* --------------------------------------------------------------------------
 *
 * PARAMETRES DU SITE
 *
 * -------------------------------------------------------------------------- */

//
// Les enseignants doivent se connecter a chaque deux semaines.
//

$config['connexion_expiration'] = 60*60*24*14; // 2 semaines
$config['session_expiration']   = 60*60*24;    // 1 jour

//
// Les longueurs de chaine de caracteres pour diverses references
//

$config['len_evaluation_reference'] = 6;
$config['len_soumission_reference'] = 8;

/* --------------------------------------------------------------------------
 *
 * PARAMETRES DU SITES - COOKIES
 *
 * -------------------------------------------------------------------------- */

//
// Definir le nom des cookies.
//

$config['cookies'] = array(
	'email_cookie_name'    => 'courriel',
    'password_cookie_name' => 'motdepasse',
    'type_cookie_name'     => 'type',
);

/* --------------------------------------------------------------------------
 *
 * PARAMETRES DU SITES - COURRIELS
 *
 * -------------------------------------------------------------------------- */

$config['courriel_options'] = array(
	'from'		 => 'KOVAO',
	'from-email' => 'nepasrepondre@kovao.com'
);

/* --------------------------------------------------------------------------
 *
 * SECURITE
 *
 * -------------------------------------------------------------------------- */

//
// Les IP a ne pas logger (whitelist)
//
$config['ips_whitelist'] = array(
    '107.159.86.150'
);

/* --------------------------------------------------------------------------
 *
 * BASE DE DONNEES - LE NOM DES TABLES
 *
 * --------------------------------------------------------------------------
 *
 * Ces clefs sont incompletes et sous-utilisees.
 *
 * -------------------------------------------------------------------------- */

$config['database_tables'] = array(
//  clef                       nom de la table
	'activite'				=> 'activite',
	'documents'				=> 'documents',
	'documents_etudiants'   => 'documents_etudiants',
    'ecoles'                => 'ecoles',
    'enseignants'           => 'enseignants',
    'enseignants_groupes'   => 'enseignants_groupes',
    'etudiants'             => 'etudiants',
    'etudiants_numero_da'   => 'etudiants_numero_da',
    'etudiants_traces'      => 'etudiants_traces',
    'evaluations'           => 'evaluations',
    'inscriptions'          => 'inscriptions',
    'groupes'               => 'groupes',
	'parametres'            => 'parametres',
	'semestres'             => 'semestres',
    'scrutins'              => 'scrutins',
    'scrutins_choix'        => 'scrutins_choix',
    'scrutins_documents'    => 'scrutins_documents',
	'scrutins_participants' => 'scrutins_participants',
	'soumissions'			=> 'soumissions'
);

/* --------------------------------------------------------------------------
 *
 * ECOLE & GROUPE PAR DEFAULT (WWW)
 *
 * ------------------------------------------------------------------------- */

$config['ecole_www'] = array(
    'ecole_id'          => 0,
    'ecole_nom'         => 'Personnel',
    'ecole_nom_court'   => 'Personnel',
    'ecole_url'         => '',
    'numero_da_nom'     => 'Matricule',
    'numero_da_desc'    => 'Entrez votre matricule',
    'plateforme'        => NULL,
    'actif'             => 1,
    'efface'            => 0
);

$config['groupe_www'] = array(
    'groupe_id'           => 0,
    'ecole_id'            => 0,
    'groupe_code'         => 'P',
    'groupe_nom'          => 'Personnel',
    'groupe_nom_court'    => 'Personnel',
    'groupe_url'          => '',
    'denomination'        => NULL,
    'sous_domaine'        => 'perso',
    'admin_enseignant_id' => NULL,
    'inscription_code'    => NULL,
    'inscription_permise' => 1,
    'actif'               => 1,
    'efface'              => 0
);

/* --------------------------------------------------------------------------
 *
 * LES PRIVILEGES GLOBLAUX
 *
 * -------------------------------------------------------------------------- */

// @TODO

/* --------------------------------------------------------------------------
 *
 * LES NIVEAUX D'UN GROUPE
 *
 * -------------------------------------------------------------------------- */

$config['niveaux'] = array(
    'sysop'                 => 100, // administrateur du systeme
    'devel'                 => 90,  // developpeur
    'admin'                 => 75,  // admin general

    'admin_ecole'           => 60,  // admin d'une ecole
    'admin_groupe_sup'      => 50,  // enseignant : admin d'un departement en chef
    'admin_groupe'          => 40,  // enseignant : admin d'un departement
    'admin_lab'             => 30,  // enseignant : admin des evaluations de type 'laboratoire'
    'admin_evaluations'     => 20,  // enseignant : admin des evaluations du departement
    'enseignant_permanent'  => 5,   // enseignant permanent
    'enseignant_precaire'   => 3,   // enseignant precaire
    'enseignant'            => 1,   // enseignant
    'non_enregistre'        => 0
);

$config['niveaux_desc'] = array(
    1   => "Enseignant(e)",
    3   => "Enseignant(e) précaire",
    5   => "Enseignant(e) permanent(e)",
    20  => "Admin des évaluations",
    40  => "Admin du département",
    50  => "Admin en chef du département"
    /*
    60  => "Admin de l'école",
    75  => "Admin général",
    99  => "Développeur",
    100 => "Sysop"
    */
);

/* --------------------------------------------------------------------------
 *
 * LES TYPES DE QUESTIONS
 *
 * -------------------------------------------------------------------------- */

//
// Retrocompatibilite pour avant 2020-05-01
//

/* Les anciens noms :
$config['questions_types'] = array(
    1 => 'Question à choix unique',
    2 => 'Question à développement',
    3 => 'Question à coefficients variables', // question à choix unique avec équation
    4 => 'Question à choix multiples',
    5 => 'Question à réponse numérique entière',
    6 => 'Question à réponse numérique',
    7 => 'Question à réponse littérale courte'
);
*/

//
// Suggestion pour l'ordre des questions :
//

//  1. Question à développement
//  2. Question a choix unique
//  3. Question a choix unique avec equation
//  4. Question a choix multiples
//  5. Question à choix multiples stricte | Tous les choix doivent être justes pour obtenir les points.
//  6. Question a reponse numerique entiere
//  7. Question a reponse numerique entiere avec equation (bientot)
//  8. Question a reponse numerique
//  9. Question a reponse numerique avec equation (bientot)
// 10. Question a reponse litterale courte
// 11. Question a reponse par documents (bientot)

//
// Les types de questions
// (!) La key doit absolument etre le type pour acceder directement aux proprietes de la question.
//

$config['questions_types'] = array(

// Type
    1 => array(
        'type'      => 1, // numero unique du type de question
        'ordre'     => 1, // ordre de la question dans le menu deroulant
        'priv'      => 1, // privilege requis pour utiliser ce type de question
        'desc'      => 'Question à choix unique', // description du type de question
        'selecteur' => 99, // nombre de reponses a partir duquel le selecteur sera affiche automatiquement
        'selecteur_option' => 6 // nombre de reponses a partir duquel l'enseignant peut choisir d'activer le selecteur
    ),

    2 => array(
        'type'      => 2, 
        'ordre'     => 10, 
        'priv'      => 1, 
        'desc'      => 'Question à développement'
    ),

    3 => array(
        'type'      => 3, 
        'ordre'     => 2, 
        'priv'      => 1, 
        'desc'      => 'Question à choix unique par équations' // Question à coefficients variables
    ), 

    4 => array(
        'type'      => 4, 
        'ordre'     => 3, 
        'priv'      => 1, 
        'desc'      => 'Question à choix multiples'
    ),

    5 => array(
        'type'      => 5, 
        'ordre'     => 5, 
        'priv'      => 1, 
        'desc'      => 'Question à réponse numérique entière'
    ),

    6 => array(
        'type'      => 6, 
        'ordre'     => 7, 
        'priv'      => 1, 
        'desc'      => 'Question à réponse numérique',
        'tolerances' => TRUE
    ),

    7 => array(
        'type'      =>  7, 
        'ordre'     =>  9, 
        'priv'      =>  1, 
        'desc'      => 'Question à réponse littérale courte', 
        'similarite'     => 92, // similarite par default en pourcentage (max = 100)
        'similarite_min' => 10  // similarite maximum
    ), 

    9 => array(
        'type'      =>  9, 
        'ordre'     =>  8, 
        'priv'      =>  1, 
        'desc'      => 'Question à réponse numérique par équation',
        'tolerances' => TRUE
    ),

    10 => array(
        'type'      => 10, 
        'ordre'     => 13, 
        'priv'      =>  1, 
        'desc'      => 'Question à répondre par téléversement de documents', 
        'docs_max'   => 5, 
        'taille_max' => 5, // en Mo
        'val' => array(
            1 => 'Je vais téléverser un ou plusieurs documents contenant ma réponse.',
            9 => 'Je ne vais pas répondre à cette question.'
        )
    ),

    11 => array(
        'type'      => 11, 
        'ordre'     => 4, 
        'priv'      => 1, 
        'desc'      => 'Question à choix multiples stricte'
    ),

    12 => array(
        'type'      => 12, 
        'ordre'     => 12, 
        'priv'      => 1, 
        'desc'      => 'Question à développement court'
    ),

    13 => array(
        'type'      => 13,
        'ordre'     => 11,
        'priv'      => 99, // seulement l'admin pour l'instant
        'desc'      => 'Question à développement (ChatGPT)'
    )
);

/* --------------------------------------------------------------------------
 *
 * LABORATOIRES
 *
 * -------------------------------------------------------------------------- */
    
$config['lab_champs_types_icons'] = array(
	'standard'      => 'box-arrow-in-down',
	'comparaison'   => 'arrows',
	'calcul'        => 'calculator',
	'precision'     => 'record-circle',
	'exactitude'    => 'bullseye',
	'validite'      => 'check2-circle',
	'absorbance_d'  => 'align-middle'
);

$config['lab_parametres_initiaux'] = array(
    'precorrection' => 1,
    'precorrection_essais' => 5,
    'precorrection_penalite' => 1
);

/* --------------------------------------------------------------------------
 *
 * MODELES IA
 *
 * -------------------------------------------------------------------------- */

$config['modeles_ia'] = array(
    1 => array(
        'desc' => 'ChatGPT 3.5 Turbo',
        'nom'  => 'gpt-3.5-turbo',
        'cout' => '~ 0,0001$CAD / 100 caractères',
        'cout_float' => 0,0003 // par 1K tokens
    )
);

/* --------------------------------------------------------------------------
 *
 * GRILLES DE CORRECTION
 *
 * -------------------------------------------------------------------------- */

$config['elements_types'] = array(
    1 => 'additif',
    2 => 'déductif',
);

/* --------------------------------------------------------------------------
 *
 * DOCUMENTS (PDF & IMAGES)
 *
 * -------------------------------------------------------------------------- */

//
// Les chemins sur le disque pour acceder les fichiers
// Ils doivent se terminer par un '/' (slash).
//

$config['documents_path']   = 'storage/';   // fichiers des evaluations (enseignants)
$config['documents_path_s'] = 'storage_s/'; // fichiers des soumissions (etudiants)

// 
// Images
//

$config['image_qualite'] = 75; // en % pour les JPG

// La taille (en px) maximale de la hauteur et largeur d'une image avant de considerer
// sa compression.

$config['image_max_hw'] = '1600';

//
// Images Thumbnails
//

$config['image_tn_taille']  = 150; // en px
$config['image_tn_qualite'] = 75;  // en %

//
// Les formats acceptes des documents (mime types)
//

/*
$config['documents_mime_types'] = array(
    'image/jpeg', 
    'image/gif', 
    'image/png', 
    'application/pdf',
    'application/msword', // .doc
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    'application/vnd.ms-excel', // .xls
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
);
*/

$config['documents_mime_types_properties'] = array(

    'image/jpeg' => 
        array(
            'extension' => 'jpg', 
            'tn'        => TRUE, // Un thumbnail a ete genere? oui = TRUE
            'danger'    => FALSE
        ),

    'image/gif' => 
        array(
            'extension' => 'gif', 
            'tn'        => TRUE,
            'danger'    => FALSE
        ),

    'image/png' => 
        array(
            'extension' => 'png', 
            'tn'        => TRUE,
            'danger'    => FALSE
        ),

    'application/pdf' => 
        array(
            'extension' => 'pdf', 
            'tn'        => TRUE,
            'danger'    => FALSE
        ),

    'application/msword' => 
        array(
            'extension'  => 'doc', 
            'tn'         => FALSE, 
            'tn_fichier' => 'icon_doc.png',
            'danger'     => TRUE
        ),

    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' =>
        array(
            'extension'  => 'docx', 
            'tn'         => FALSE, 
            'tn_fichier' => 'icon_doc.png',
            'danger'     => TRUE
        ),

    'application/vnd.msexcel' => 
        array(
            'extension'  => 'xls',
            'tn'         => FALSE,          // thumbnail existe
            'tn_fichier' => 'icon_xls.png', // fichier du thumbnail
            'danger'     => TRUE
        ),

    'application/vnd.ms-excel' => 
        array(
            'extension'  => 'xls',
            'tn'         => FALSE,          // thumbnail existe
            'tn_fichier' => 'icon_xls.png', // fichier du thumbnail
            'danger'     => TRUE
        ),

    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' =>
        array(
            'extension'  => 'xlsx',
            'tn'         => FALSE,
            'tn_fichier' => 'icon_xls.png',
            'danger'     => TRUE
        ),
);

$config['documents_mime_types'] = array_keys($config['documents_mime_types_properties']);

