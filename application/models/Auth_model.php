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

/* ============================================================================================
 *
 * AUTH (AUTHENTIFICATION) MODEL
 *
 * ============================================================================================ */

class Auth_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();

        $this->load->library('user_agent');
        $this->load->helper('cookie');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Enregistrer une tentative de connexion infructueuse
     *
     * -------------------------------------------------------------------------------------------- */
    function _tentative_connexion($courriel)
    {
        $data = array(
            'courriel'   => $courriel,
            'adresse_ip' => $_SERVER['REMOTE_ADDR'],
            'epoch'      => $this->now_epoch,
            'date'       => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->insert('securite_connexion_tentatives', $data);

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier les tentatives de connexion abusives 
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_tentatives_connexion_abusives($courriel)
    {
        //
        // Verifier l'existence des parametres requis.
        //
        
        $parametres = array(
            'securite_tentatives_connexion_prevention',
            'securite_tentatives_connexion_max',
            'securite_tentatives_connexion_periode',
            'securite_tentatives_connexion_periode_blocage'
        );

        foreach($parametres as $p)
        {
            if ( ! (${$p} = $this->config->item($p)))
            {
                return TRUE;
            }
        }

        // 
        // Est-ce que la prevention est activee?
        //

        if ( ! $securite_tentatives_connexion_prevention)
        {
            // Elle n'est pas activee.
            return;
        }

        $adresse_ip_bloquee = FALSE;

        //
        // Extraire les blocages actuels
        //

		$this->db->from  ('securite_connexion_blocages');
		$this->db->where ('courriel', $courriel);
        $this->db->where ('adresse_ip', $_SERVER['REMOTE_ADDR']);
		$this->db->where ('expiration_epoch >', $this->now_epoch);
		$this->db->order_by ('expiration_epoch', 'desc');
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $adresse_ip_bloquee = TRUE;
        }
        else
		{
            //
            // 1. Extraire toutes les tentatives dans la periode de temps allouee
            // 2. Bloquer une adresse si le nombre de tentatives excede le nombre alloue
            //

			$this->db->from  ('securite_connexion_tentatives');
			$this->db->where ('courriel', $courriel);
            $this->db->where ('adresse_ip', $_SERVER['REMOTE_ADDR']);
            $this->db->where ('epoch >=', ($this->now_epoch - ($securite_tentatives_connexion_periode * 60)));

            $query = $this->db->get();

            if ($query->num_rows() >= $securite_tentatives_connexion_max)
			{
                // Ajouter le blocage d'un courriel / adresse ip

                $expiration_epoch = $this->now_epoch + ($securite_tentatives_connexion_periode_blocage * 60);
                $expiration_date  = date_humanize($expiration_epoch, TRUE);

				$data = array(
					'courriel'		   => $courriel,
                    'adresse_ip'       => $_SERVER['REMOTE_ADDR'],
                    'expiration_epoch' => $expiration_epoch,
                    'expiration_date'  => $expiration_date
                );

                $this->db->insert('securite_connexion_blocages', $data);

                $adresse_ip_bloquee = TRUE;
            }
        }

        //
        // Rediriger l'adresse ip bloquee vers une autre page
        //
    
        if ($adresse_ip_bloquee == TRUE)
        {
            redirect(base_url() . 'erreur/spec/connexion');
            exit;
        }

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner le groupe selon le sous-domaine.
     *
     * -------------------------------------------------------------------------------------------- */
    function determination_groupe($sous_domaine)
    {
        if (empty($sous_domaine))
        {
            return array();     
        }

		//
		// Le groupe par default (www)
		//

        if ($sous_domaine == 'www')
        {
			$groupe = $this->config->item('groupe_www');

			// Je ne peux entrer cette info facilement dans la config,
			// alors je la parametre ici.
			if (empty($groupe['groupe_url']))
			{
				$groupe['groupe_url'] = $this->config->item('main_url');
			}

            return $groupe;
        }

        //
        // Extraire les donnees du groupe
        //

        $groupes_t = $this->config->item('database_tables')['groupes'];

        $this->db->from  ($groupes_t . ' as g');
        $this->db->where ('g.sous_domaine', $sous_domaine);
        $this->db->where ('g.efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $groupe = $query->row_array();

        return $groupe;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner l'ecole selon le groupe.
     *
     * -------------------------------------------------------------------------------------------- */
    function determination_ecole($ecole_id)
    {
        if (empty($ecole_id))
        {
			$ecole = $this->config->item('ecole_www');

			// Je ne peux entrer cette info facilement dans la config,
			// alors je la parametre ici.
			if (empty($ecole['ecole_url']))
			{
				$ecole['ecole_url'] = $this->config->item('main_url');
			}

            return $ecole;
		}

        //
        // Extraire les donnees de l'ecole
        //

        $ecoles_t = $this->config->item('database_tables')['ecoles'];

        $this->db->from  ($ecoles_t . ' as e');
        $this->db->where ('e.ecole_id', $ecole_id);
        $this->db->where ('e.actif', 1);
        $this->db->where ('e.efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();
                                                                                                                                                                                                                                  
        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Connecter un enseignant ou un etudiant par formulaire
     *
     * -------------------------------------------------------------------------------------------- */
    function connexion_formulaire($data = array())
    {
        if ( ! isset($data) || ! is_array($data))
        {
            // 
            // Les donnees de connexion provenant du formulaire sont manquante.
            //

            return FALSE;
        }

        //
        // Reinitialiser les temoins et la session.
        //

        delete_all_cookies();

        //
        // Champs obligatoires
        //

        $champs = array('email', 'password');

        foreach($champs as $c)
        {
            if ( ! array_key_exists($c, $data) || empty($data[$c]))
            {
                return FALSE;
            }
        }

		$courriel   = $data['email'];
		$password_f = $data['password']; // plain text

        //
        // Verifier les tentatives de connexion abusives
        //

        $this->verifier_tentatives_connexion_abusives($courriel);

		//
		// Est-ce un ETUDIANT ?
		//

        $type          = NULL; // etudiant ou enseignant
        $type_id       = NULL; // etudiant_id ou enseignant_id
        $type_t        = NULL; // etudiants ou enseignants table
        $compte_existe = FALSE;

        if ( ! $compte_existe)
        {
			$this->db->where('courriel', $courriel);
			$this->db->where('efface', 0);
			$this->db->limit(1);
            $query = $this->db->get($this->etudiants_t);

            if ($query->num_rows())
            {
                $type    = 'etudiant';
                $type_id = 'etudiant_id';
                $type_t  = $this->etudiants_t;

                $compte_existe = TRUE;
            }
        }

        if ( ! $compte_existe)
        {
            $this->db->where('courriel', $courriel);
			$this->db->where('efface', 0);
            $this->db->limit(1);

            $query = $this->db->get($this->enseignants_t);

            if ($query->num_rows())
            {
                $type    = 'enseignant';
                $type_id = 'enseignant_id';
                $type_t  = $this->enseignants_t;

                $compte_existe = TRUE;
            }
        }

        //
        // Ce compte n'existe pas.
        //
        
        if ( ! $compte_existe)
        {
            //
            // Verifier si ce compte est en procedure d'inscription.
            //

            $this->db->from  ('inscriptions');
            $this->db->where ('courriel', $courriel);
            $this->db->where ('clef_activation_expiration >', $this->now_epoch);
            $this->db->where ('efface', 0);
            $this->db->limit (1);
             
            $query = $this->db->get();
             
            if ($query->num_rows() > 0)
            {
                return 'inscription-en-cours';
            }

            return 'introuvable';
        }

        $usager = $query->row_array();

        //
        // Verifier que le compte est actif
        //

        if ( ! $usager['actif'])
        {
            return 'inactif';
        }

		//
		// Verifier l'autorisation
		//

        if ( ! $this->verifier_motdepasse($usager['salt'], $password_f, $usager['password']))
        {
            // Le mot-de-passe entre est errone.
            // Enregistrer la tentative de connexion.

            $this->_tentative_connexion($courriel);

            return 'mauvais-mot-de-passe';
        }

        //
        // Genere le mot-de-passe pour les cookies
        //

        // Ceci cause des problemes avec CloudFlare parce que l'adresse IP est dans le hash du cookie. (2024-06-25)
        // $password_c = hash('sha256', $this->agent->agent_string() . $this->input->ip_address() . substr($usager['salt'], 12) . $usager['password']);

        $password_c = hash('sha256', $this->agent->agent_string() . substr($usager['salt'], 12) . $usager['password']);

        // Ceci est peut-etre la bonne facon mais c'est trop lent (100ms) !
		// $password_c = password_hash($this->agent->agent_string() . $this->input->ip_address() . $usager['password'], PASSWORD_BCRYPT);

		//
		// Installer les cookies
		//

        $this->input->set_cookie(
            array(
                'name'   => $this->config->item('email_cookie_name', 'cookies'),
                'value'  => $courriel,
                'expire' => $this->config->item('connexion_expiration')
            )
        );

        $this->input->set_cookie(
            array(
                'name'   => $this->config->item('password_cookie_name', 'cookies'),
                'value'  => $password_c,
                'expire' => $this->config->item('connexion_expiration')
            )
        );

        $this->input->set_cookie(
            array(
                'name'   => $this->config->item('type_cookie_name', 'cookies'),
                'value'  => $type,
                'expire' => $this->config->item('connexion_expiration')
            )
        );

		set_cookie($this->config->item('email_cookie_name',    'cookies'), $courriel,   $this->config->item('connexion_expiration'));
        set_cookie($this->config->item('password_cookie_name', 'cookies'), $password_c, $this->config->item('connexion_expiration'));
        set_cookie($this->config->item('type_cookie_name',     'cookies'), $type,       $this->config->item('connexion_expiration'));

        return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Connecter un enseignant ou un etudiant par cookie
     *
     * -------------------------------------------------------------------------------------------- */
	function connexion_cookie()
    {
        //
        // Verifier l'usurpation
        //

        if ($this->usurp)
        {
            $type = $this->usurp['type'];
        }
        else
        {
            //
            // Verifier les cookies
            //

            if (
                (($courriel   = get_cookie($this->config->item('email_cookie_name',    'cookies'), TRUE)) === NULL) ||
                (($password_c = get_cookie($this->config->item('password_cookie_name', 'cookies'), TRUE)) === NULL) ||
                (($type       = get_cookie($this->config->item('type_cookie_name',     'cookies'), TRUE)) === NULL)           
              )
            {
                return FALSE;
            }
        }

		//
		// Est-ce un ETUDIANT ou un ENSEIGNANT ?
		//

        if ( ! in_array($type, array('etudiant', 'enseignant')))
        {
            return FALSE;
        }

        if ($type == 'etudiant')
        {
            $type_id = 'etudiant_id';
            $type_t  = $this->config->item('database_tables')['etudiants'];
        }
        else
        {
            $type_id = 'enseignant_id';
            $type_t  = $this->config->item('database_tables')['enseignants'];
        }

		//
		// Extraire les donnees de l'enseignant ou de l'etudiant
        //

        if ($this->usurp)
        {
            $this->db->where ($type_id, $this->usurp['id']);
            $this->db->where ('actif', 1);
            $this->db->limit (1);
 
            $query = $this->db->get($type_t);

            if ( ! $query->num_rows())
            {
                return FALSE;
            }
            
            $usager = $query->row_array();
        }
        else
        {
            $this->db->where ('courriel', $courriel);
            $this->db->where ('actif', 1);
            $this->db->limit (1);

            $query = $this->db->get($type_t);

            if ( ! $query->num_rows())
            {
                return FALSE;
            }
            
            $usager = $query->row_array();

            //
            // Verifier l'autorisation par cookie
            //

            // Ceci cause des problemes avec CloudFlare parce que l'adresse IP est dans le hash du cookie. (2024-06-25)
            // if (hash('sha256', $this->agent->agent_string() . $this->input->ip_address() . substr($usager['salt'], 12) . $usager['password']) != $password_c)

            if (hash('sha256', $this->agent->agent_string() . substr($usager['salt'], 12) . $usager['password']) != $password_c)
            {
                delete_all_cookies();
                return FALSE;
            }

            //
            // L'ancienne facon de verifier le mot-de-passe.
            // C'etait peut-etre la bonne facon mais c'etait trop lent... > 100ms !
            //
            /*
            if ( ! password_verify($this->agent->agent_string() . $this->input->ip_address() . $usager['password'], $password_c))
            {
                delete_all_cookies();
                return FALSE;
            }
            */
        }

        //
        // Rendre les informations disponible aux autres instances
        //

        $data = array(
            $type_id            => $usager[$type_id],
            'type'              => $type,
            'nom'               => $usager['nom'],
            'prenom'            => $usager['prenom'],
            'courriel'          => $usager['courriel'],
            'genre'             => $usager['genre'],
            'inscription_epoch' => $usager['inscription_epoch'],
            'actif'             => $usager['actif'],
            'activite_compteur' => $usager['activite_compteur']
        );

        if ($type == 'etudiant')
        {
            return array_merge($data,
                array(
                    'montrer_rang_cours'      => $usager['montrer_rang_cours'],
                    'montrer_rang_evaluation' => $usager['montrer_rang_evaluation']
                )
            );
        }

        elseif ($type == 'enseignant')
        {
            return array_merge($data,
                array(
                    'privilege'			            => $usager['privilege'],
                    'cacher_evaluation'             => $usager['cacher_evaluation'],
                    'inscription_requise'           => $usager['inscription_requise'],
                    'permettre_fichiers_dangereux'  => $usager['permettre_fichiers_dangereux']
                )
            );
        }

        generer_erreur('AUTH1', "Nous n'avons pu déterminer le type d'usager.");
        exit;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier le mot-de-passe
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_motdepasse($salt, $password_f, $password_e)
    {
        // salt       : le salt
        // password_f : le mot-de-passe en texte
        // password_e : le mot-de-passe encrypte de la base de doneees

		if ( ! password_verify($salt . $password_f, $password_e))
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier l'appartenance a un groupe d'un etudiant ou d'un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
	function appartenance_groupe($groupe_id, $usager)
    {
        if ( ! is_numeric($groupe_id) || empty($usager) || ! is_array($usager))
        {
            return FALSE;
        }

        if ($this->est_etudiant)
        {
            // Les etudiants peuvent entrer dans n'importe quel groupe.

            return TRUE;
        }

        elseif ($this->est_enseignant)
		{
            // Les enseignants

            $this->db->from  ($this->config->item('database_tables')['enseignants'] . '_groupes');
            $this->db->where ('enseignant_id', $usager['enseignant_id']);
            $this->db->where ('groupe_id', $groupe_id);
            $this->db->where ('niveau >', 0);
            $this->db->where ('actif', 1);
            $this->db->limit (1);
            
			$query = $this->db->get();

            if ( ! $query->num_rows() > 0)
			{
                // Les administrateurs
                //
                // Ceci permet aux administrateurs et developpeurs avec un privilege
                // suffisant d'acceder les groupes pour en faciliter la gestion.

                if ($this->enseignant['privilege'] > 89)
                {
                    return array(
                        'groupe_id'   => $groupe_id,
                        'niveau'      => 99,
                        'semestre_id' => NULL
                    );
                }

                return FALSE;
            }
                                                                                                                                                                                                                                      
			return $query->row_array();
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     * 
     * Ajouter une inscription (etudiant ou enseignant)
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_inscription($type, $post_data, $status)
    {
        if ( ! is_array($post_data) || empty($post_data))
            return FALSE;

        if ( ! ($type == 'etudiant' || $type == 'enseignant'))
            return FALSE;

        //
        // Champs obligatoires
        //
    
        $champs_obligatoires = array(
            'nom', 'prenom', 'courriel', 'password1', 'password2'
        );
        
        //
        // Verifier les champs obligatoires
        //
        
        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data) || empty($post_data[$c]))
                return FALSE;
        }

        //
        // Extraire le courriel
        //

        $courriel = $post_data['courriel'];

        if ( ! filter_var($courriel, FILTER_VALIDATE_EMAIL)) 
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6712',
                    'desc'  => "Cette adresse courriel est invalide.",
                    'extra' => 'courriel: ' . $courriel
                )
            );
            exit;
        }

        if (empty($courriel))
        {
            generer_erreur2(
                array(
                    'code' => 'IS6713',
                    'desc' => "Nous n'avons pu déterminer votre courriel pour l'inscription."
                )
            );
            exit;
        }

        //
        // Verifier que le courriel est inexistant dans la base de données.
        //

        // Enseignant

        $this->db->from  ('enseignants');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
         
        $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6714',
                    'desc'  => "Un usager avec ce courriel est déjà inscrit.",
                    'extra' => 'courriel: ' . $courriel
                )
            );
            exit;
        }

        // Etudiant

        $this->db->from  ('etudiants');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
         
        $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6715',
                    'desc'  => "Un usager avec ce courriel est déjà inscrit.",
                    'extra' => 'courriel: ' . $courriel
                )
            );
            exit;
        }

        //
        // Verifier que cet usager n'est pas deja en procedure d'inscription
        //

        $this->db->from  ('inscriptions');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('clef_activation_expiration >', $this->now_epoch);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
         
        $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            log_alerte(
                array(
                    'code'       => 'IS67156',
                    'desc'       => "Cet usager est présentement en procédure d'inscription. Veuillez vérifier vos courriels et pourriels.",
                    'importance' => 1,
                    'extra'      => 'courriel: ' . $courriel
                )
            );

            redirect(base_url() . 'erreur/spec/IS67156');
            exit;
        }

        //
        // Preparation
        //

		$this->load->helper('string');

        if ($post_data['password1'] !== $post_data['password2'])
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6716',
                    'desc'  => "Les mot-de-passes entrés ne sont pas identiques.",
                    'extra' => 'courriel: ' . $courriel
                )
            );
            exit;
        }

        if (($password_data = create_password($post_data['password1'], 1)) === FALSE)
        {
            return FALSE;
        }

        extract($password_data);

		// Generer la clef d'activation pour confirmer le courriel.

        $clef 		= random_string('alpha', 10); 
        $hash	    = hash('md5', $clef . $courriel); 
        $expiration = date('U') + $this->config->item('inscription_expiration');

        //
        // Verifier si le matricule est requis.
        //

        $numero_da = NULL;

        if ($type == 'etudiant' && $this->groupe_id != 0)
        {
            $numero_da = (array_key_exists('numero_da', $post_data)) ? $post_data['numero_da'] : NULL;
        }

		//
        // Ajouter l'inscription
		//

        $data = array(
            'etudiant'                    => ($type == 'etudiant' ? 1 : 0),
            'enseignant'                  => ($type == 'enseignant' ? 1 : 0),
            // 'reference_enseignant_id'     => $reference_enseignant_id,
            'courriel'                    => trim($courriel),
            'clef_activation'             => $clef,
            'clef_activation_hash'        => $hash,
            'clef_activation_expiration'  => $expiration,
			'nom'				          => $post_data['nom'],
            'prenom'			          => $post_data['prenom'],
            'genre'                       => $post_data['genre'] ?: 'M',
            'numero_da'                   => trim($numero_da),
            'salt'             	          => $salt,
            'password'                    => $hashed_password,
            'recaptcha_score'             => $post_data['recaptcha_score'],
            'inscription_epoch'           => $this->now_epoch,
            'inscription_date'            => date_humanize($this->now_epoch, TRUE),
        );

		$this->db->trans_begin();

        $this->db->insert('inscriptions', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return $clef;
    }

    /* --------------------------------------------------------------------------------------------
     * 
     * Ajouter un usager (etudiant ou enseignant)
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_usager($type, $data)
    {
        if ( ! is_array($data) || empty($data))
            return FALSE;

        if ( ! ($type == 'etudiant' || $type == 'enseignant'))
            return FALSE;

        //
        // Champs obligatoires
        //

        $champs_obligatoires = array(
            'courriel', 'nom', 'prenom', 'genre', 
            'salt', 'password',
            'inscription_epoch', 'inscription_date'
        );
    
        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $data) || empty($data[$c]))
            {
                return FALSE;
            }
        }

        //
        // Verifier que le courriel est inexistant.
        //

        $this->db->from  ('etudiants as et');
        $this->db->where ('courriel', $data['courriel']);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
         
         $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6712', 
                    'desc'  => "Ce courriel est déjà inscrit.",
                    'extra' => 'courriel: ' . $data['courriel']
                )
            );
            exit;
        }

        $this->db->from  ('enseignants');
        $this->db->where ('courriel', $data['courriel']);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
         
         $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            generer_erreur2(
                array(
                    'code'  => 'IS6713', 
                    'desc'  => "Ce courriel est déjà inscrit.",
                    'extra' => 'courriel: ' . $data['courriel']
                )
            );
            exit;
        }

		//
        // Ajouter l'usager
		//

        $data = array(
            'courriel'              => $data['courriel'],
            'courriel_confirmation' => 1,
			'nom'                   => $data['nom'],
            'prenom'                => $data['prenom'],
            'genre'                 => $data['genre'] ?: 'M',
            'salt'                  => $data['salt'],
            'password'              => $data['password'],
            'inscription_epoch'     => $data['inscription_epoch'],
            'inscription_date'      => $data['inscription_date'],
            'actif'                 => 1
        );

        /*
        if ($type == 'enseignant')
        {
            $data['reference_enseignant_id'] = $reference_enseignant_id;
        }
        */

		$this->db->trans_begin();

        $this->db->insert($type . 's', $data);

        $usager_id = $this->db->insert_id();

		//
		// Creer le groupe personnel du nouvel enseignant
		//
        
        if ($type == 'enseignant' && ! empty($usager_id))
        {
            $data = array(
                'enseignant_id' => $usager_id,
                'groupe_id'		=> 0,
                'actif'			=> 1,
                'ajout_date'    => date_humanize($this->now_epoch, TRUE),
                'ajout_epoch'   => $this->now_epoch
            );

            $this->db->insert('enseignants_groupes', $data);
        }

        $this->db->trans_commit();

        return $usager_id;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Editer le mot-de-passe
     *
     * -------------------------------------------------------------------------------------------- */
    function editer_password($type, $type_id, $password)
    {
		// type    = enseignant ou etudiant
		// type_id = id de l'enseignant, ou,  id de l'etudiant 

		if ( ! in_array($type, array('etudiant', 'enseignant')))
		{
			generer_erreur('CRJ98', "Erreur de type lors de l'édition du mot-de-passe.");
			die;
		}

		if ( ! is_numeric($type_id))
		{
			generer_erreur('CRJ99', "Erreur du type_id lors de l'édition du mot-de-passe.");
			die;
		}

        $password_data = create_password($password, 1);

        $data = array(
            'password' => $password_data['hashed_password'],
            'salt'     => $password_data['salt']
        );

        $this->db->where ($type . '_id', $type_id);
        $this->db->update($type . 's', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        //
        // Mettre a jour le cookie, autrement l'usager devra s'authentifer de nouveau
        //

        $password_c = hash('sha256', $this->agent->agent_string() . $this->input->ip_address() . substr($data['salt'], 12) . $data['password']);

        set_cookie($this->config->item('password_cookie_name', 'cookies'), $password_c, $this->config->item('connexion_expiration'));

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Creer le lien d'autorisation pour confirmer le courriel
     *
     * -------------------------------------------------------------------------------------------- */
    function envoie_clef_autorisation($courriel, $clef)
    {
		//
        // Verifier que ce courriel est dans la base de donnees et qu'il est unique.
		//

        $this->db->from  ('inscriptions');
        $this->db->where ('courriel', $courriel);
		$this->db->where ('clef_activation_expiration >', $this->now_epoch);
		$this->db->where ('efface', 0);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 1)
        {
            // Plusieurs courriels (au moins 2, ou plus) ont ete trouves dans la base de donnees.

            generer_erreur('IS7123', "Plusieurs entrées ont été trouvées pour ce courriel dans la base de données.");
			return;
        }

        if ( ! $query->num_rows() > 0)
        {
            // Aucun courriel correspondant n'a ete trouve dans la base de donnees.

            generer_erreur('IS7124', "Votre courriel n'a pu être trouvé dans la base de données.");
            return;
        }

		//
        // Envoyer un courriel avec la clef d'activation.
		//

        if (
            $this->Courriel_model->envoyer_courriel(
                array(
                    'destination_courriel' => $courriel,
                    'sujet'                => 'Confirmation de votre adresse courriel',
                    'contenu'              => 'inscription/inscription_clefenvoyee_email2',
                    'contenu_data'         => array('clef' => $clef),
                    'raison'               => 'inscription'
                )
            )
        )
        {
            return TRUE;
        }

		generer_erreur('IR8210', "Il y a eu un problème avec l'envoi du courriel pour confirmer votre adresse courriel.");
		return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Creer le lien de reinitialisaton d'un mot-de-passe et l'envoyer a l'enseignant.
     *
     * Version 2 : 2020-04-06
     *
     * -------------------------------------------------------------------------------------------- */
    function envoie_clef_reinitialisation2($courriel)
    {
        //
		// Verifier que le courriel existe
        //

        $est_etudiant   = FALSE;
        $est_enseignant = FALSE;

        //
        // Est-ce un etudiant ?
        //

	    $this->db->select('etudiant_id, courriel');
        $this->db->from  ('etudiants');
        $this->db->where ('courriel', $courriel);
        $this->db->limit (1);
         
        $query = $this->db->get();
         
        if ($query->num_rows() > 0)
        {
            $est_etudiant = TRUE;
            $etudiant = $query->row_array();
        }

        //
        // Est-ce un enseignant ?
        //

        if ( ! $est_etudiant)
        {
            $this->db->select('enseignant_id, courriel');
            $this->db->from  ('enseignants');
            $this->db->where ('courriel', $courriel);
            $this->db->limit (1);
             
            $query = $this->db->get();
             
            if ($query->num_rows() > 0)
            {
                $est_enseignant = TRUE;
                $enseignant = $query->row_array();
            }
        }

        if ( ! $est_etudiant && ! $est_enseignant)
        {
            // Aucun courriel correspondant n'a ete trouve dans la base de donnees.
            // Cet enseignant n'existe pas ou n'est pas inscrit.
            return array(
                'message' => 'Ce courriel est inexistant.',
                'alert'   => 'danger'
            );
		}

		//
		// Verifier qu'une clef de reinitialisation n'est pas deja presente
		//

		$this->db->from  ('usagers_oubli_motdepasse');
		$this->db->where ('courriel', $courriel);
		$this->db->where ('clef_reinitialisation_expiration >', $this->now_epoch);
		$this->db->where ('efface', 0);

		$query = $this->db->get();
		
        if ($query->num_rows() > 0)
        {
            // Une clef de reinitialisation a deja ete envoyee.
            return array(
                'message' => "Une clef de réinitilisation de mot-de-passe a déjà été envoyée. Veuillez attendre qu'elle expire (24h) pour en demander une autre.",
                'alert'   => 'danger'
            );
		}

		//
        // Generer une clef, envoyer un courriel, puis inserer la clef dans la base de donnee.
		//

        $clef 		= random_string('alpha', 10); 
        $hash 		= hash('md5', $clef . $courriel); 
        $expiration = $this->now_epoch + 60*60*24; // 24h

        if (
            $this->Courriel_model->envoyer_courriel(
                array(
                    'destination_courriel' => $courriel,
                    'sujet'                => 'Demande de réinitialisation de mot-de-passe',
                    'contenu'              => 'connexion/connexion_clefenvoyee_email',
                    'contenu_data'         => array('clef' => $clef),
                    'raison'               => 'oubli_mot_de_passe'
                )
            )
        )
        {
			$data = array(
				'courriel'						   => $courriel,
				'clef_reinitialisation' 		   => $clef,
				'clef_reinitialisation_hash' 	   => $hash,
				'clef_reinitialisation_expiration' => $expiration
			);
			
            if ($est_etudiant)
            {
                $data['etudiant_id'] = $etudiant['etudiant_id'];
            }

            if ($est_enseignant)
            {
                $data['enseignant_id'] = $enseignant['enseignant_id'];
            }

			$this->db->insert('usagers_oubli_motdepasse', $data);

            return TRUE;
        }

		generer_erreur('CR29120', "Il y a eu un problème avec l'envoi du courriel réinitialisation de mot-de-passe.");
		die;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier la validite d'une clef d'activation, puis confirmer le courriel d'un nouvel usager.
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_clef_confirmer_courriel($clef)
	{
		if ( ! ctype_alpha($clef))
		{
			return array(
				'status'  => FALSE,
				'no'	  => 'IS9801',
				'message' => "Il y un problème avec la clef de confirmation.",
				'alert'   => 'danger'
            );
		}

		//
		// Extraire les donnees relies a la clef.
		//

		$this->db->from  ('inscriptions');
		$this->db->where ('clef_activation', $clef);
		$this->db->where ('clef_activation_expiration >', $this->now_epoch);
		$this->db->where ('efface', 0);
		$this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
			return array(
				'status'  => FALSE,
				'no'	  => 'IS9812',
				'message' => "Cette clef d'activation est introuvable ou expirée. Veuillez recommencer l'inscription.",
				'alert'   => 'danger'
            );
		}

        $clef_data = $query->row_array();

        $hash = hash('md5', $clef . $clef_data['courriel']); 

		if ($hash !== $clef_data['clef_activation_hash'])
        {
			return array(
				'status'  => FALSE,
				'no'	  => 'IS4512',
				'message' => "Cette clef d'activation est invalide.",
				'alert'   => 'danger'
            );
        }

		//
		// Ajouter un usager
		//

        if ( ! empty($clef_data['enseignant']))
        {
            $type = 'enseignant';
        }
        else
        {
            $type = 'etudiant';
        }

        $id = $this->ajouter_usager($type, $clef_data);

        //
        // Ajouter le matricule (numero DA) s'il est present
        //

        if (array_key_exists('numero_da', $clef_data) && ! empty($clef_data['numero_da']))
        {
            $this->db->insert('etudiants_numero_da', 
                array(
                    'etudiant_id'   => $id,
                    'groupe_id'     => $this->groupe_id,
                    'numero_da'     => $clef_data['numero_da']
                )
            );
        } 

		//
		// Desactiver la clef d'activation
		//

		$this->db->where  ('inscription_id', $clef_data['inscription_id']);
        $this->db->update ('inscriptions', 
			array(
				'clef_utilisee_date'  => date_humanize($this->now_epoch, TRUE),
				'clef_utilisee_epoch' => $this->now_epoch,
				'efface' 			  => 1
			)
		);
	
        if ( ! $this->db->affected_rows())
        {
			return array(
				'status'  => FALSE,
				'no'	  => 'IS2288',
				'message' => "Il n'a pas été possible de changer le paramètre de confirmation de courriel dans la base de données.",
				'alert'   => 'danger'
            );
        }

        log_alerte(
            array(
                'code'       => 'NOUV44',
                'desc'       => "Un nouvel " . ($type == 'etudiant' ? 'étudiant' : $type) . " s'est inscrit et a confirmé son courriel !",
                'importante' => 0,
                'extra'      => 
                    $type . '_id = ' . $id . 
                    ', nom = '       . $clef_data['prenom'] . ' ' . $clef_data['nom'] .
                    ', courriel = '  . $clef_data['courriel']
            )
        );

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return array(
            'status'     => TRUE,
            'etudiant'   => $clef_data['etudiant'],
            'enseignant' => $clef_data['enseignant']
        );
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier la validite d'une clef de reinitialisation (de mot-de-passe)
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_clef_reinitialisation($clef)
	{
		if ( ! ctype_alpha($clef))
		{
			generer_erreur('CR29120', "Il y a eu un problème avec la clef de confirmation.");
			die;
		}

		//
		// Extraire la clef
		//

		$this->db->from  ('usagers_oubli_motdepasse');
		$this->db->where ('clef_reinitialisation', $clef);
		$this->db->where ('clef_reinitialisation_expiration >', $this->now_epoch);
		$this->db->where ('efface', 0);
		$this->db->limit (1);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
			return array(
				'status'  => FALSE,
				'message' => "La clef de réinitialisation est introuvable.",
				'alert'   => 'danger'
			);
		}

        $row = $query->row_array();
	
		$courriel = $row['courriel'];

        $hash = hash('md5', $clef . $courriel); 

		if ($hash !== $row['clef_reinitialisation_hash'])
		{
			return array(
				'status'  => FALSE,
				'message' => "La clef de réinitialisation est invalide.",
				'alert'   => 'danger'
			);
		}

		if ($this->now_epoch > $row['clef_reinitialisation_expiration'])
		{
			return array(
				'status'  => FALSE,
				'message' => "La clef de réinitialisation est expirée",
				'alert'   => 'danger'
			);
		}	

		return array_merge(array('status' => TRUE), $row);
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Effacement d'une clef de reinitialisation
     *
     * -------------------------------------------------------------------------------------------- */
	function effacer_clef_reinitialisation($clef)
	{
		$this->db->from  ('usagers_oubli_motdepasse');
		$this->db->where ('clef_reinitialisation', $clef);
		$this->db->where ('clef_reinitialisation_expiration >', $this->now_epoch);
		$this->db->where ('efface', 0);
		$this->db->limit (1);
        
        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
			return array(
				'status'  => FALSE,
				'message' => "La clef de réinitialisation est introuvable.",
				'alert'   => 'danger'
			);
		}

        $row = $query->row_array();

		$this->db->where ('oubli_id', $row['oubli_id']);
        $this->db->update('usagers_oubli_motdepasse',
			array(
				'clef_utilisee_date'  => date_humanize($this->now_epoch, TRUE),
				'clef_utilisee_epoch' => $this->now_epoch,
				'efface' 			  => 1
			)
		);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des courriels acceptes
     *
     * -------------------------------------------------------------------------------------------- */
	function liste_courriels_acceptes()
	{
        $this->db->from   ('ecoles as e');
        $this->db->select ('e.ecole_id, e.courriel_domaine, e.ecole_nom');
        $this->db->where  ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

}
