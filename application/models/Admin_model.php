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

/* ============================================================================
 *
 * ADMIN MODEL
 *
 * ============================================================================ */

class Admin_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Log les messages pour aider le debogage
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * UniqueID est une tentative de generer un identifiant unique pour chaque etudiant (connexion).
     *
     * -------------------------------------------------------------------------------------------- */
    public function debogage($options = array())
    {
        // debogage_niveau pas encore utilise

    	$options = array_merge(
            array(
                'groupe_id'     => @$this->groupe_id ?? NULL,
                'enseignant_id' => @$this->enseignant_id ?? NULL,
                'etudiant_id'   => @$this->etudiant_id ?? NULL,
                'evaluation_id' => NULL,
                'evaluation_reference' => NULL,
                'code'          => NULL,
                'msg'           => NULL,
                'msg_l'         => NULL,
                'adresse_ip'    => $this->input->ip_address(),
                'fureteur'      => $this->input->user_agent(),
                'date'          => date_humanize(date('U'), TRUE),
                'epoch'         => date('U')
           ),
           $options
        );

        p($options);

        $this->db->insert('activite_debug', $options);

        return TRUE;        
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Generation du UniqueID
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * UniqueID est une tentative de generer un identifiant unique pour chaque etudiant (connexion).
     *
     * -------------------------------------------------------------------------------------------- */
    public function generer_unique_id()
    {
        return hash('sha256',
            $_SERVER['REMOTE_ADDR'] .
            $_SERVER['HTTP_ACCEPT'] . 
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] . 
            $_SERVER['HTTP_ACCEPT_ENCODING'] .
            $_SERVER['HTTP_USER_AGENT']
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Generation du FureteurID
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * FureteurID est une tentative de generer un identifiant unique pour le fureteur de l'etudiant,
     * tout en preservant ses informations personnelles.
     *
     * -------------------------------------------------------------------------------------------- */
    public function generer_fureteur_id()
    {
        return hash('sha256',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] . 
            $_SERVER['HTTP_ACCEPT_ENCODING'] .
            $_SERVER['HTTP_USER_AGENT']
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner l'identite de l'etudiant
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * A partir des informations dans les temoins de connexion, determiner l'identite de
     * l'etudiant. Cette identite a ete enregistree auparavant lors de la soumission d'une evaluation.
     *
     * -------------------------------------------------------------------------------------------- */
    public function determiner_identite($output = 'str')
    {
        //
        // $output peut etre 'string' ou 'array'
        //

        if ($this->logged_in)
        {
            // L'identite est deja connue car l'usager est inscrit au site.
            return NULL;
        }

        $identite = NULL;

        $etudiant_data = get_cookie('adata');

        if (empty($etudiant_data))
        {
            return NULL;
        }

        $etudiant_serialized   = $this->encryption->decrypt($etudiant_data);
        $etudiant_unserialized = (array) unserialize($etudiant_serialized);

        // Ceci va permettre d'aller chercher precisement des informations sur le cours.
  
        $cours_data = (array) json_decode($etudiant_unserialized['cours_data']);

        if ($output == 'array')
        {
            return $etudiant_unserialized;
        }

        return
            html_entity_decode(@$etudiant_unserialized['prenom_nom']) . ';' .
            @$etudiant_unserialized['numero_da'] . ';' .
            @$cours_data['cours_code_court'] . ';' .
            @$cours_data['semestre_code'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Enregistrer la derniere activite sur le site
     *
     * -------------------------------------------------------------------------------------------- */
    public function log_activite()
	{
		//
		// Ne pas logger l'usurpation
		//

		if ($this->usurp !== FALSE)
			return;	

        //
        // Ne pas logger les requetes AJAX.
        //

        if ($this->input->is_ajax_request())
            return;

        //
        // Ne pas logger les requetes CLI
        //

        if (is_cli())
            return;

        //
        // Ne pas logger l'admin du systeme.
        //

        if ($this->logged_in && $this->est_enseignant && $this->enseignant['privilege'] >= 99)
            return;

        //
        // Ne pas logger certains IPs.
        //

        if (in_array($_SERVER['REMOTE_ADDR'], $this->config->item('ips_whitelist')))
            return;

        //
        // Ne pas logger les robots (Google, Bing).
        //

        if ($this->agent->is_robot())
            return;

        //
        // Ne pas laisser les plateformes inconnues acceder le site.
        //

        if ( ! $this->agent->platform() || $this->agent->platform() == 'Unknown Platform')
        {
            log_alerte(
                array(
                    'code'       => 'PLFSUP',
                    'desc'       => "Cette plateforme n'est pas supportée.",
                    'importance' => 1,
                    'extra'      => 'plateforme = ' . $this->agent->plateform() . ', fureteur = ' . $this->agent->browser()
                )
            );

            redirect(base_url() . 'erreur/spec/plateforme');
            die;
        }

        //
        // Identite de l'etudiant(e)
        //

        $identite = $this->determiner_identite();

        //
        // Generer un identifiant unique (tentative)
        //

        $unique_id = $this->generer_unique_id();

        //
        // Fureteur
        //

        $fureteur = NULL;

        if ($this->agent->is_browser())
        {
            $fureteur = $this->agent->browser();

            if ($version = $this->agent->version())
            {
                $fureteur = $fureteur . ' ' . $version;
            }
        }

        $fureteur_id = $this->generer_fureteur_id();

        //
        // Referrer
        //

        $referrer = $this->agent->referrer();

        $data = array(
            'date'          => date_humanize($this->now_epoch, TRUE),
            'epoch'         => $this->now_epoch,
            'enseignant_id' => @$this->enseignant_id ?: NULL,
            'etudiant_id'   => @$this->etudiant_id ?:  NULL,
            'ecole_id'      => @$this->ecole_id ?: 0,
            'groupe_id'     => @$this->groupe_id ?: 0,
            'semestre_id'   => @$this->semestre_id ?: NULL,
            'adresse_ip'    => $_SERVER['REMOTE_ADDR'],
            'identite'      => $identite,
            'unique_id'     => $unique_id,
            'plateforme'    => $this->agent->platform() ?: NULL,
            'fureteur'      => $fureteur,
            'fureteur_id'   => $fureteur_id,
            'mobile'        => $this->agent->mobile() ?: NULL,
            'referencement' => $referrer,
            'uri'           => uri_string() ?: '/',

            'annee'         => date('Y', $this->now_epoch),
            'mois'          => date('Ym', $this->now_epoch),
            'jour'          => date('Ymd', $this->now_epoch),
            'heure'         => date('H', $this->now_epoch),
            'minute'        => date('i', $this->now_epoch)
        );

        $this->db->insert('activite', $data);

        //
        // Logger la derniere activite des usagers
        //

        if ($this->est_enseignant)
        {
            $activite_compteur = $this->enseignant['activite_compteur'] ?: 0;

            $this->db->where ('enseignant_id', $this->enseignant_id);
            $this->db->update('enseignants', 
                array(
                    'derniere_activite_date'  => date_humanize($this->now_epoch, TRUE),
                    'derniere_activite_epoch' => $this->now_epoch,
                    'activite_compteur'       => ++$activite_compteur
                )
            );
        }

        if ($this->est_etudiant)
        {
            $activite_compteur = $this->etudiant['activite_compteur'] ?: 0;

            $this->db->where ('etudiant_id', $this->etudiant_id);
            $this->db->update('etudiants', 
                array(
                    'derniere_activite_date'  => date_humanize($this->now_epoch, TRUE),
                    'derniere_activite_epoch' => $this->now_epoch,
                    'activite_compteur'       => ++$activite_compteur
                )
            );
        }
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Voir l'activite du site
     *
     * -------------------------------------------------------------------------------------------- */
    public function voir_activite($options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id'   => NULL, 
                'limite'      => NULL,
                'apres_epoch' => NULL
           ),
           $options
       );

        $this->db->from   ('activite as a');
        $this->db->select ('a.id, a.enseignant_id, a.etudiant_id, a.groupe_id, a.adresse_ip, a.identite, a.unique_id,
                            a.epoch, a.referencement, a.uri');

        if ( ! empty($options['groupe_id']))
        {
            $this->db->where ('a.groupe_id', $options['groupe_id']);
        }

        if ( ! empty($options['apres_epoch']))
        {
            $this->db->where ('a.epoch >', $this->now_epoch - $options['apres_epoch']);
        }

        if ( ! empty($options['limite']))
        {
            $this->db->limit ($options['limite']);
        }
        
        $this->db->order_by ('epoch', 'desc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array(
                // 'complete'     => array(),
                'non_connectes' => array(),
                'enseignants'   => array(),
                'etudiants'     => array()
            );
        }

        //
        // Determiner le nom de l'ecole et du groupe
        //
    
        $ecoles  = $this->Ecole_model->lister_ecoles();
        $groupes = $this->Groupe_model->lister_groupes();

        //
        // Determiner le nom des enseigannts
        //

        $enseignants = $this->Enseignant_model->lister_enseignants_tous();

        //
        // Determiner le nom des etudiants
        //

        $etudiants = $this->Etudiant_model->extraire_etudiants();
       
        //
        // Iterer a travers l'activite
        //

        $activites = array(
            // 'complete'     => array(),
            'non_connectes' => array(),
            'enseignants'   => array(),
            'etudiants'     => array()
        );

        foreach($query->result_array() as $r)
        {
            //
            // Ne pas inclure l'activite des IPs de la liste blanche (whitelist)
            //

            if (in_array($r['adresse_ip'], $this->config->item('ips_whitelist')))
            {
                continue;
            }

            //
            // Determiner le sous-domaine de l'activite
            //

            if ($r['groupe_id'] == 0)
            {
               $r['groupe_sous_domaine'] = 'www';
            }

            elseif ($r['groupe_id'] && array_key_exists($r['groupe_id'], $groupes)) 
            {
               $r['groupe_sous_domaine'] = $groupes[$r['groupe_id']]['sous_domaine'];
            }

            //
            // Classer l'activite selon s'il s'agit d'etudiants, d'enseignants ou d'utilisateurs non-connectes
            //

            if ( ! empty($r['enseignant_id']) && array_key_exists($r['enseignant_id'], $enseignants))
            {
                $r['nom']    = $enseignants[$r['enseignant_id']]['nom'];
                $r['prenom'] = $enseignants[$r['enseignant_id']]['prenom'];

                $activites['enseignants'][] = $r;
            }

            elseif ( ! empty($r['etudiant_id']) && array_key_exists($r['etudiant_id'], $etudiants))
            {
                $r['nom']    = $etudiants[$r['etudiant_id']]['nom'];
                $r['prenom'] = $etudiants[$r['etudiant_id']]['prenom'];

                $activites['etudiants'][] = $r;
            }

            else
            {
                $activites['non_connectes'][] = $r;
            }
        }

        return $activites;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Derniere activite d'un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    public function derniere_activite($enseignant_id)
    {
        $this->db->from     ('activite as a');

        $this->db->where    ('a.enseignant_id', $enseignant_id);
        $this->db->order_by ('epoch', 'desc');
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;

        return $query->row_array()['date'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les alertes
     *
     * -------------------------------------------------------------------------------------------- */
    public function extraire_alertes($options = array())
    {
    	$options = array_merge(
            array(
                'apres_epoch' => NULL,
                'groupe_id'   => NULL,
                'importance'  => 3,
                'limite'      => NULL,
           ),
           $options
       );

        $this->db->from     ('alertes as a');
        $this->db->order_by ('a.epoch', 'desc');

        if ( ! empty($options['apres_epoch']))
        {
            $this->db->where ('a.epoch >', $options['apres_epoch']);
        }

        if ( ! empty($options['groupe_id']))
        {
            $this->db->where ('a.groupe_id', $options['groupe_id']);
        }

        if ( ! empty($options['importance']))
        {
            $this->db->where ('a.importance >=', $options['importance']);
        }       

        if ( ! empty($options['limite']))
        {
            $this->db->limit ($options['limite']);
        }

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Compter alertes
     *
     * -------------------------------------------------------------------------------------------- */
    public function compter_alertes($options = array())
    {
    	$options = array_merge(
            array(
                'importance'  => 3,
                'apres_epoch' => NULL
           ),
           $options
        );

        $this->db->from     ('alertes as a');
        $this->db->order_by ('a.epoch', 'desc');

        if ( ! empty($options['apres_epoch']))
        {
            $this->db->where ('a.epoch >', $options['apres_epoch']);
        }

        if ( ! empty($options['importance']))
        {
            $this->db->where ('a.importance >=', $options['importance']);
        }       

        return $this->db->count_all_results();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le nombre de soumissions par enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    public function extraire_nombre_soumissions_enseignants($enseignant_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'groupe_id' => $this->groupe_id
           ),
           $options
        );

        $soumissions_total    = array();
        $soumissions_semestre = array();

        $semestre_id = $this->Semestre_model->extraire_dernier_semestre() ?? NULL;

        $this->db->from     ('soumissions as s');
        $this->db->select   ('s.soumission_id, s.semestre_id, s.enseignant_id');
        $this->db->where    ('s.groupe_id', $options['groupe_id']);
        $this->db->where_in ('s.enseignant_id', $enseignant_ids);
        $this->db->where    ('s.efface', 0);
         
        $query = $this->db->get();
         
        if ( ! $query->num_rows() > 0)
        {
            return array(
                 'soumissions_total'    => array(),
                 'soumissions_semestre' => array()
            );
        }

        foreach ($query->result_array() as $row)
        {
            if ( ! array_key_exists($row['enseignant_id'], $soumissions_total))
            {
                $soumissions_total[$row['enseignant_id']] = 0;
                $soumissions_semestre[$row['enseignant_id']] = 0;
            }

            $soumissions_total[$row['enseignant_id']]++;

            if ($row['semestre_id'] == $semestre_id)
            {
                $soumissions_semestre[$row['enseignant_id']]++;
            }
        }

        return array(
             'soumissions_total' => $soumissions_total,
             'soumissions_semestre' => $soumissions_semestre
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Historique de l'activite
     *
     * -------------------------------------------------------------------------------------------- */
    function activite_historique()
    {
        $signet = 0;
        $surete = 0;

        $records = 0;

        $historique = array();

        while($surete < 100)
        {
            $this->db->select('a.epoch');
            $this->db->from  ('activite as a');
            $this->db->where ('a.epoch >', $signet);
            $this->db->limit (50000);
            
            $query = $this->db->get();
            
            if ( ! $query->num_rows() > 0)
            {
                break;
            }

            $records += $query->num_rows();

            foreach ($query->result_array() as $row)
            {
                if (empty($row['epoch']))
                    continue;

                $signet = $row['epoch'];

                $d = str_replace('-', '', date_humanize($row['epoch']));

                if ( ! array_key_exists($d, $historique))
                {
                    $historique[$d] = 1;
                    continue;
                }

                $historique[$d] += 1;
            }

            $surete++;
        }

        return $historique;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Historique de l'activite
     * version 2
     *
     * -------------------------------------------------------------------------------------------- */
    function activite_historique2()
    {
        // Notes :
        // $this->db->select   ('jour, count(jour) AS total');
        // $this->db->group_by ('jour');

        $historique = array();

        //
        // Historique des meilleurs mois, jours
        //

        $champs = array('mois', 'jour');

        foreach($champs as $c)
        {
            $this->db->from     ('activite');
            $this->db->select   ($c . ', count(' . $c . ') AS ' . $c . '_total');
            $this->db->where    ($c . ' !=', NULL);
            $this->db->group_by ($c);
            $this->db->order_by ($c . '_total', 'desc');
            
            $query = $this->db->get();
            
            $historique[$c] = $query->result_array();
        }

        return $historique;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les parametres dynamiques
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_parametres_dynamiques()
    {
        $this->db->from ('parametres');
        $this->db->order_by ('clef', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer un parametre dynamique
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_parametre_dynamique($clef, $valeur)
    {
        if ($this->enseignant['privilege'] < 90)
        {
            return FALSE;
        }

        $this->db->where('clef', $clef);
        $this->db->update('parametres', array('valeur' => $valeur));

        if ($this->db->affected_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Calculer le temps passe par vos etudiants a rediger des evaluations
     *
     * -------------------------------------------------------------------------------------------- */
    function temps_passe_en_redaction($enseignant_id = NULL, $semestre_id = NULL)
    {
        $temps_total = 0;

        //
        // Verifier la presence des soumissions
        //

        $this->db->from ('soumissions');

        if ($enseignant_id != NULL)
        {
            $this->db->where('enseignant_id', $enseignant_id);
        }

        if ($semestre_id != NULL)
        {
            $this->db->where ('semestre_id', $semestre_id);
        }

        $this->db->where ('efface', 0);
        
        $soumissions_compte = $this->db->count_all_results();

        $i = 0;
        $max = 500;

        $debut_id   = 1;
        $dernier_id = 0;

        if ($soumissions_compte > 0)
        {
            while ($i < $soumissions_compte)
            {   
                // Une protection contre les boucles infinies

                if ($debut_id == $dernier_id)
                {
                    break;
                }

                $debut_id = $dernier_id + 1;

                $this->db->from ('soumissions');

                if ($enseignant_id != NULL)
                {
                    $this->db->where('enseignant_id', $enseignant_id);
                }

                if ($semestre_id != NULL)
                {
                    $this->db->where ('semestre_id', $semestre_id);
                }

                $this->db->where ('efface', 0);
                $this->db->where ('soumission_id >=', $debut_id);
                $this->db->limit ($max);

                $query = $this->db->get();

                if ( ! $query->num_rows() > 0)
                {
                    break;
                }

                $soumissions = $query->result_array();

                $i = $i + count($soumissions);

                foreach($soumissions as $s)
                {
                    if ($s['soumission_id'] > $dernier_id)
                    {
                        $dernier_id = $s['soumission_id'];
                    }

                    if (empty($s['soumission_debut_epoch']))
                    {
                        continue;
                    }

                    $soumission_ids_lues[] = $s['soumission_id'];

                    $temps_total += ($s['soumission_epoch'] - $s['soumission_debut_epoch']);
                }

                // $documents_manquants_s = array_unique($documents_manquants_s);

            } // while
        } // if

        return array(
            'soumissions_lues'  => $i,
            'temps_passe_epoch' => $temps_total,
            'temps_passe'       => calculer_longue_duree(0, $temps_total)
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les etudiants relies
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_etudiants_relies($options = array())
    {
        if (empty($this->semestre_id))
        {
            return array();
        }

    	$options = array_merge(
        	array(
                'enseignant_id' => NULL // Limiter aux etudiants de cet enseignant
           ),
           $options
        );

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        //
        // Extraire les adresses IP de l'ecole
        //

        $ecole_ips = array();

        $this->db->select   ('adresse_ip');
        $this->db->from     ('ecoles_ips');
        $this->db->where    ('ecole_id', $this->ecole_id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row)
            {
                $ecole_ips[] = $row['adresse_ip'];
            }
        }

        //
        // Extraire les etudiants de l'enseignant
        //

        $etudiant_ids = array();

        if ( ! empty($options['enseignant_id']))
        {
            $this->db->select ('etudiant_id');
            $this->db->from   ('soumissions as s');
            $this->db->where  ('s.enseignant_id', $options['enseignant_id']);
            $this->db->where  ('s.semestre_id', $this->semestre_id);
            $this->db->where  ('s.soumission_epoch >=', $this->semestres[$this->semestre_id]['semestre_debut_epoch']);
            $this->db->where  ('s.efface', 0);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                foreach ($query->result_array() as $row)
                {
                    if (empty($row['etudiant_id']))
                        continue;

                    if ( ! in_array($row['etudiant_id'], $etudiant_ids))
                    {
                        $etudiant_ids[] = $row['etudiant_id'];
                    }
                }
            }
        }

        //
        // Extraire l'activite des etudiants
        //

        $this->db->select   ('a.etudiant_id, a.adresse_ip, a.date, a.epoch, a.semestre_id, a.identite, e.courriel, e.nom, e.prenom, e.genre, e.etudiant_id');
        $this->db->from     ('activite as a, etudiants as e');
        $this->db->where    ('a.groupe_id', $this->groupe_id);
        $this->db->where    ('a.etudiant_id !=', NULL);
        $this->db->where    ('a.etudiant_id = e.etudiant_id');
        $this->db->where    ('a.semestre_id', $this->semestre_id);
        $this->db->where    ('a.epoch >=', $this->semestres[$this->semestre_id]['semestre_debut_epoch']);
        $this->db->where_not_in ('a.adresse_ip', $ecole_ips);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $connexions = array();

        foreach ($query->result_array() as $row)
        {
            $ip   = $row['adresse_ip'];
            $e_id = $row['etudiant_id'];

            if (in_array($e_id, array(1, 248)))
                continue;

            if ( ! array_key_exists($ip, $connexions))
            {
                $connexions[$ip] = array();
            }

            if ( ! array_key_exists($e_id, $connexions[$ip]))
            {
                $connexions[$ip][$e_id] = $row;
            }
        }

        $relations = array();

        foreach($connexions as $ip => $c)
        {
            if (count($c) > 1)
            {
                $relations[$ip] = $c;
            }
        }

        //
        // Extraire seulement les etudiants pertinents a l'enseignant
        //

        if ( ! empty($options['enseignant_id']))
        {
            if (empty($etudiant_ids))
                return array();

            $relations_enseignants = array();

            foreach($relations as $ip => $r)
            {
                $trouve = FALSE;

                foreach($r as $etudiant)
                {
                    if (in_array($etudiant['etudiant_id'], $etudiant_ids))
                        $trouve = TRUE;
                }

                if ($trouve == TRUE)
                {
                    $relations_enseignants[$ip] = $r;
                }
            }

            $this->kcache->save($cache_key, $relations_enseignants, 'outils', 60);

            return $relations_enseignants;
        }

        $this->kcache->save($cache_key, $relations, 'outils', 60);

        return $relations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Statistiques > Combien d'etudiants se sont connectes a chaque jour
     *
     * -------------------------------------------------------------------------------------------- */
    function stats_etudiants_connexions($options = array())
    {
        $activite = array();

        $i = 211591;
        $limite = 10000;
        $charge = FALSE;

        while( ! $charge)
        {
            $this->db->select   ('a.id, a.jour, a.etudiant_id');
            $this->db->from     ('activite as a');
            $this->db->where    ('etudiant_id !=', NULL);
            $this->db->where    ('id >', $i);
            $this->db->order_by ('epoch', 'asc');
            $this->db->limit    ($limite);

            $query = $this->db->get();

            if ( ! $query->num_rows() > 0)
            {
                break;
            }

            foreach ($query->result_array() as $row)
            {
                $i = $row['id'];

                $jour = $row['jour'];

                if ( ! array_key_exists($jour, $activite))
                {
                    $activite[$jour] = array();
                }

                if ( ! in_array($row['etudiant_id'], $activite[$jour]))
                {
                    $activite[$jour][] = $row['etudiant_id'];
                }
            }
        }

        $journees = array();

        foreach($activite as $jour => $a)
        {
            if ( ! array_key_exists($jour, $journees))
            {
                $journees[$jour] = count($a);
            }
        }

        echo "Le nombre d'étudiants différents qui se sont connectés à chaque journée : (" . $i . " entrées lues)";

        arsort($journees);

        p($journees);
        die;
	}

    /* --------------------------------------------------------------------------------------------
     *
	 * Effacer les etudiants inactifs
	 *
     * -------------------------------------------------------------------------------------------- */
    function effacer_etudiants_inactifs($options = array())
	{
    	$options = array_merge(
			array(
				'mois' => 3*12 // defaut
           ),
           $options
		);

		// Exclure ces etudiants (etudiant_id)

		$etudiant_ids_exclure = [0, 1, 248];

		// Drapeau pour effacement
		
		$data = ['efface' => 1, 'efface_epoch' => date('U'), 'efface_date' => date_humanize(date('U'), TRUE)];

		// Obtenir la date du debut de la periode d'inactivite

		try 
		{
			$maintenant = new DateTimeImmutable();
			$intervalle = new DateInterval('P' . $options['mois'] . 'M'); // P36M

			$date_passe = $maintenant->sub($intervalle);

			$epoch = $date_passe->getTimestamp();
		} 
		catch (Exception $e) 
		{
			echo 'Erreur de date : ' . $e->getMessage();
		}

		$this->db->trans_begin();
		$this->benchmark->mark('code_start');
		
		//
		// Initialisation du rapport
		//

		$rapport = ['g' => 
			[
				'etudiants' => 0,
				'soumissions_sans_etudiant_id' => 0
			]
		];

		//
		// compteurs
		//

		//
		// Effacer les soumissions sans etudiant_id
		// (auparavant il n'etait pas necessaire de s'inscrire)
		//
		
		$this->db->where('efface', 0);
		$this->db->where('etudiant_id', NULL);
		$this->db->where('soumission_epoch <', $epoch);

		$this->db->update($this->soumissions_t, $data);

		if ($this->db->affected_rows() > 0)
		{
			$count_soumissions += $this->db->affected_rows();

			$rapport['g']['soumissions_sans_etudiant_id'] = $this->db->affected_rows();
		}

		//
		// Effacer les etudiants inactifs
		//

		// Extraire les etudiants a effacer

		$etudiants = $this->Etudiant_model->extraire_etudiants_inactifs($epoch);

		if (count($etudiants) > 0)
		{
			// Exclure certains etudiants

			foreach($etudiant_ids_exclure as $etudiant_id)
			{
				if (array_key_exists($etudiant_id, $etudiants))
					unset($etudiants[$etudiant_id]);
			}		

			$etudiant_ids = array_keys($etudiants);

			$rapport = $rapport + $etudiants;

			if (empty($etudiant_ids))
			{
				echo '0 etudiant inactif a effacer';
				return;
			}

			//
			// Effacer les etudiants inactifs de differentes tables
			//

			$tables = [
				'activite',
				'activite_evaluation',
				'documents_etudiants',
				'etudiants',
				'etudiants_cours',
				'etudiants_evaluations_notifications',
				'etudiants_numero_da',
				'etudiants_traces',
				'evaluations_ponderations',
				'evaluations_securite_chargements',
				'soumissions',
				'soumissions_consultees',
				'soumissions_partagees',
				'usagers_oubli_motdepasse'
			];

			foreach ($tables as $t)
			{
				$this->db->where('efface', 0);
				$this->db->where_in('etudiant_id', $etudiant_ids);
				$this->db->update($t, $data);

				if ($this->db->affected_rows() > 0)
				{
					$rapport['g'][$t] = $this->db->affected_rows();
				}
			}

			//
			// Effacer les donnees de ces etudiants reliees a leur courriel
			//

			$courriels = array_column($etudiants, 'courriel');

			$tables = [
				'courriels_envoyes',
				'inscriptions',
				'inscriptions_invitations',
				'securite_connexion_blocages',
				'securite_connexion_tentatives'
			];

			foreach ($tables as $t)
			{
				$count = 0;

				$chunk_size = 500;
				$chunks = array_chunk($courriels, $chunk_size);

				foreach($chunks as $c)
				{	
					// $this->db->where('efface', 0);
					$this->db->where_in('courriel', $c);
					$this->db->update($t, $data);

					$count += $this->db->affected_rows();
				}

				if ($count > 0)
				{
					$rapport['g'][$t] = $count;
				}
			}

			//
			// Quelques cas particuliers
			//

			//
			// soumissions (partenaire de laboratoire)
			// soumissions_consultees (consulte_par_etudiant_id)
			//

			$count1 = 0; // soumissions
			$count2 = 0; // soumissions_consultees

			$chunk_size = 500;
			$chunks = array_chunk($etudiant_ids, $chunk_size);

			foreach($chunks as $c)
			{	
				$this->db->where   ('efface', 0);
				$this->db->where_in('lab_etudiant2_id', $c);
				$this->db->update  ('soumissions', ['lab_etudiant2_id' => NULL]);

				$count1 += $this->db->affected_rows();

				$this->db->where   ('efface', 0);
				$this->db->where_in('lab_etudiant3_id', $c);
				$this->db->update  ('soumissions', ['lab_etudiant3_id' => NULL]);

				$count1 += $this->db->affected_rows();

				$this->db->where   ('efface', 0);
				$this->db->where_in('consulte_par_etudiant_id', $c);
				$this->db->update  ('soumissions_consultees', ['consulte_par_etudiant_id' => NULL]);

				$count2 += $this->db->affected_rows();
			}

			if ($count1 > 0)
			{
				$rapport['g']['soumissions_partenaire_labo'] = $count1;
			}

			if ($count2 > 0)
			{
				$rapport['g']['soumissions_consultees_par_etudiant_id'] = $count2;
			}

		} // if count $etudiants > 0

		$this->benchmark->mark('code_end');

		$rapport['g']['benchmark'] = $this->benchmark->elapsed_time('code_start', 'code_end');

		// Ecrire le rapport de maintenance pour verification ulterieure
		// en cas de problematiques.
		// Etant donne que ce rapport contient des renseignants personnels,
		// il sera eventuellement efface et purge apres un certain temps.

		$options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;
		$json = json_encode($rapport, $options);

		$rapport_data = [
			'enseignant_id' => @$this->enseignant_id ?: NULL,
			'cli'			=> is_cli() ? 1 : 0,
			'action'		=> 'effacer_etudiants_inactifs',
			'data'			=> $json,
			'epoch'			=> date('U'),
			'date'			=> date_humanize(date('U'), TRUE)
		];

		$this->db->insert('rapports_maintenance', $rapport_data);

        if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();

			$rapport_data['data'] = NULL;
			$rapport_data['erreur'] = 1;

			$this->db->insert('rapports_maintenance', $rapport_data);

            return FALSE;
		}

		if ( ! is_cli())
		{
			echo 'succes';
			echo '<pre>' . $json . '</pre>';
		}

		$this->db->trans_commit();

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
	 * Effacer les etudiants inactifs (rapport)
	 *
     * -------------------------------------------------------------------------------------------- */
    function effacer_etudiants_inactifs_rapports($options = array())
	{
    	$options = array_merge(
			array(
				'mois' => 3*12 // defaut de 36 mois
           ),
           $options
		);

		$this->db->where('action', 'effacer_etudiants_inactifs');

		$query = $this->db->get('rapports_maintenance');

        if ( ! $query->num_rows() > 0)
        {
            return array();
		}

		$rapports = $query->result_array();

		$epochs = array_column($rapports, 'epoch');
		array_multisort($epochs, SORT_DESC, SORT_NUMERIC, $rapports);

		return $rapports;
	}
}
