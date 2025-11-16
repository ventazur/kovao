<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * KOVAO - Système d’évaluation open source
 * Copyright (C) 2018–2025 KOVAO Project
 *
 * FR : Ce fichier fait partie du projet KOVAO.
 * Distribué sous licence GNU AGPL-3.0 avec conditions additionnelles.
 * Les versions dérivées peuvent être distribuées sous un autre nom,
 * mais doivent mentionner leur origine dans le projet KOVAO.
 * Voir le fichier LICENSE pour les détails.
 *
 * EN: This file is part of the KOVAO project.
 * Licensed under GNU AGPL-3.0 with additional terms.
 * Derivative versions may be distributed under another name,
 * but must credit the original KOVAO project.
 * See LICENSE for details.
 */

// ==================================================================
//
// CONFIGURATION SECURE
//
// ------------------------------------------------------------------
//
// ATTENTION :
//
// Les informations sensibles, comme les mot-de-passes, doivent etre
// enregistrees dans ce fichier, et ce fichier doit etre en dehors
// du chemin accessible par le serveur web.
// 
// Le chemin d'acces de ce fichier est indique dans config/config_site.php
// Ce fichier est le meme pour les deux environnements :
//
// - production
// - dev
//
// INSTALLATION :
//
// 1. Modifier le fichier en generant les codes necessaires.
// 2. Enlever la portion '_skel' du fichier.
// 3. Deplacer le fichier hors du chemin accessible par le server web.
// 4. Modifier le chemin dans application/config/config_site.php.
//
// ==================================================================

// ------------------------------------------------------------------
//
// Encryption
//
// Un clef aleatoire de 32 caracteres, lettres et chiffres.
//
// $ openssl rand -base64 24 | cut -c1-32
// ou
// $ head /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 32
//
// ------------------------------------------------------------------

$key = '00000000000000000000000000000000';

// ------------------------------------------------------------------
//
// Encryption Library
//
// ------------------------------------------------------------------

$config['encryption_settings'] = array(
    'driver' => 'mcrypt',
    'cipher' => 'aes-128',
    'mode'   => 'ctr',
    'key'    => hex2bin($key)
);

// ------------------------------------------------------------------
//
// Databases
//
// Les mot-de-passes de la base de donnes.
//
// ------------------------------------------------------------------

$config['database_settings'] = array(
    'active_group' => 'default', // ceci n'est pas un mot-de-passe, ca indique le groupe par defaut
    'default'	   => '',        // mot-de-passe
    'dev'   	   => ''         // mot-de-passe
);

// ------------------------------------------------------------------
//
// Empreinte pour les soumissions
//
// Le clef de 20 caracteres generee aleatoirement ne doit pas contenir 
// de chiffres, seulement des lettres majuscules et minuscules.
//
// $ head /dev/urandom | tr -dc 'a-zA-Z' | head -c 20
//
// ------------------------------------------------------------------

$config['empreinte_clef'] = array(
    1 => 'aaaaaaaaaaAAAAAAAAAA'
);

// ------------------------------------------------------------------
//
// Mailgun
//
// ------------------------------------------------------------------

$config['mailgun'] = array(
    'protocol'  => 'smtp',
    'smtp_host' => 'smtp.mailgun.org',
    'smtp_user' => '',
    'smtp_pass' => '',
    'api_url'   => 'https://api.mailgun.net/v3/domaine.com',
    'api_key'   => ''
);

// ------------------------------------------------------------------
//
// SendGrid
//
// ------------------------------------------------------------------

$config['sendgrid'] = array(
    'api_key' => ''
);

// ------------------------------------------------------------------
//
// Mailjet
//
// ------------------------------------------------------------------

$config['mailjet'] = array(
    'api_key'    => '',
    'api_secret' => ''
);

// ------------------------------------------------------------------
//
// Amazon SDK
//
// ------------------------------------------------------------------

$config['amazon'] = array(
    'api_user'    => '',
    'api_key'     => '',
    'api_secret'  => '',
    'region'      => '', // ex: us-east-1
    's3_url'      => 'https://xxxxxx.s3.amazonaws.com/', // xxxxxx = nom du bucket
    's3_dossiers' => array(
        'evaluations' => 'evaluations',
        'forums'      => 'forums',
        'scrutins'    => 'scrutins',
        'soumissions' => 'soumissions'
    )
);

// ------------------------------------------------------------------
//
// Sendinblue
//
// ------------------------------------------------------------------

$config['sendinblue'] = array(
    'api_url' => 'https://api.sendinblue.com/v3/smtp/email',
    'api_key' => ''
);

// ------------------------------------------------------------------
//
// Google reCAPTCHA
//
// ------------------------------------------------------------------

$config['google_recaptcha'] = array(
    'api_uri'  => 'https://www.google.com/recaptcha/api/siteverify',
    'pub_key'  => '',
    'priv_key' => ''
);

// ------------------------------------------------------------------
//
// Usurpation *** seulement dans la version DEV ***
//
// ------------------------------------------------------------------
//
// Le usurp_code de 16 caracteres doit contenir des chiffres et lettres.
// Il sert de securite lorsque l'identite d'un usager est usurpe 
// a des fins de debogage.
//
// $ head /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 16
//
// ------------------------------------------------------------------

$config['usurp_code']       = '';      // chaine de caracteres aleatoire
$config['usurp_expiration'] = 60*60*3; // 3 heures

