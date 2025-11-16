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
 * ETUDIANT MODEL
 *
 * ============================================================================ */

class Etudiant_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire des etudiants
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiants($options = array())
    {
    	$options = array_merge(
            array(
                'etudiant_ids'  => array(),
                'actif'         => NULL,
                'limite'        => NULL
            ),
            $options
       	);

        $this->db->from ('etudiants as e');

        if ($options['actif'])
        {
            $this->db->where ('e.actif', 1);
        }

        $this->db->where    ('e.efface', 0);
        $this->db->order_by ('inscription_epoch', 'desc');

		if (is_array($options['etudiant_ids']) && ! empty($options['etudiant_ids']))
		{
			$this->db->where_in ('e.etudiant_id', $options['etudiant_ids']);
		}

        if ( ! empty($options['limite']) && is_numeric($options['limite']))
        {
            $this->db->limit($options['limite']);
        }

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
		{
             return array();
		}

        return array_keys_swap($query->result_array(), 'etudiant_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire un etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant($etudiant_id, $options = array())
    {
    	$options = array_merge(
            array(
                'inclure_motdepasse' => FALSE,
                'inclure_numero_da'  => TRUE,
                'groupe_id'          => @$this->groupe_id
            ),
            $options
        );

        $select = 'e.etudiant_id, e.courriel, e.nom, e.prenom, e.genre, e.inscription_epoch' 
                  . ', e.courriel_evaluation_envoyee, e.montrer_rang_cours, e.montrer_rang_evaluation';
        $from   = $this->etudiants_t . ' as e';

        // 
        // Ceci est necessaire pour changer le profil de l'etudiant
        //

        if ($options['inclure_motdepasse'])
        {
            $select .= ', e.password, e.salt';
        }

        //
        // Ceci est necessaire pour changer le profil de l'etudiant
        // (ne pas chercher le numero_da pour le groupe_id = 0)
        //

        $this->db->from     ($from);
        $this->db->select   ($select);
		$this->db->where    ('e.etudiant_id', $etudiant_id);
		$this->db->where	('e.efface', 0);
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $etudiant = $query->row_array();

        if ($options['groupe_id'] && $options['inclure_numero_da']) 
        {
            $this->db->from  ('etudiants_numero_da as eda');
            $this->db->select('eda.numero_da');
            $this->db->where ('eda.etudiant_id', $etudiant_id);
            $this->db->where ('eda.groupe_id', $options['groupe_id']);
            $this->db->limit (1);

            $query = $this->db->get();

            if ( ! $query->num_rows())
            {
                $etudiant['numero_da'] = NULL;

                // Ceci est un champ special pour determiner s'il faut creer une nouvelle entree pour cet etudiant lors
                // de la modification de son profil (encore un hack).
                $etudiant['numero_da_inexistant'] = TRUE; 
            }
            else
            {
                $etudiant['numero_da'] = $query->row_array()['numero_da']; 
            }
        }

        return $etudiant;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le numero_da de l'etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant_numero_da()
    {
        if ( ! $this->etudiant_id && ! $this->groupe_id)
        {
            return NULL;
        }

        $this->db->select   ('numero_da');
        $this->db->from     ('etudiants_numero_da');
        $this->db->where    ('etudiant_id', $this->etudiant_id);
        $this->db->where    ('groupe_id', $this->groupe_id);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return NULL;
                                                                                                                                                                                                                                  
        return $query->row_array()['numero_da'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le temps supplementaire de l'etudiant (a partir de son numero da)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant_temps_supp($numero_da, $options = array())
    {
    	$options = array_merge(
            array(
                'cours_id'    => @$this->cours_id ?? NULL,
                'groupe_id'   => @$this->groupe_id ?? NULL,
                'semestre_id' => @$this->semestre_id ?? NULL
            ),
            $options
       	);

        $this->db->from     ('eleves');
        $this->db->where    ('numero_da',   $numero_da);

        if ( ! empty($options['cours_id']) && is_numeric($options['cours_id']))
        {
            $this->db->where ('cours_id', $options['cours_id']);
        }

        if ( ! empty($options['groupe_id']) && is_numeric($options['groupe_id']))
        {
            $this->db->where ('groupe_id', $options['groupe_id']);
        }

        if ( ! empty($options['semestre_id']) && is_numeric($options['semestre_id']))
        {
            $this->db->where ('semestre_id', $options['semestre_id']);
        }

        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return 0;
                                                                                                                                                                                                                                  
        return $query->row_array()['temps_supp'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le temps supplementaire de l'etudiant (a partir de son etudiant_id)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant_id_temps_supp($etudiant_id, $options = array())
    {
    	$options = array_merge(
            array(
                'cours_id'    => @$this->cours_id ?? NULL,
                'groupe_id'   => @$this->groupe_id ?? NULL,
                'semestre_id' => @$this->semestre_id ?? NULL
            ),
            $options
       	);

        //
        // Extraire le numero_da de l'etudiant
        //

        $this->db->from     ('etudiants_numero_da');
        $this->db->where    ('etudiant_id', $etudiant_id);

        if ( ! empty($options['groupe_id']) && is_numeric($options['groupe_id']))
        {
            $this->db->where ('groupe_id', $options['groupe_id']);
        }

        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
            return 0; // 0 minute de temps supp
        
        $numero_da = $query->row_array()['numero_da'];

        return $this->extraire_etudiant_temps_supp(
            $numero_da,
            array(
                'cours_id' => $options['cours_id'],
                'groupe_id' => $options['groupe_id'],
                'semestre_id' => $options['semestre_id']
            )
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le profil d'un etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_profil_etudiant($etudiant_id)
    {
        $this->db->from     ('etudiants as e');
        $this->db->select   ('e.etudiant_id, e.courriel, e.nom, e.prenom, e.genre'
                             . ', e.courriel_evaluation_envoyee, e.montrer_rang_cours, e.montrer_rang_evaluation');
        $this->db->where    ('e.etudiant_id', $etudiant_id);

        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $profil = $query->row_array();

        //
        // Extraire le Numero DA du present groupe
        //

        $profil['numero_da'] = NULL;

        if ($this->groupe_id) 
        {
            $this->db->from  ('etudiants_numero_da as eda');
            $this->db->where ('eda.etudiant_id', $etudiant_id);
            $this->db->where ('eda.groupe_id', $this->groupe_id);

            $this->db->limit (1);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $profil['numero_da'] = $query->row_array()['numero_da']; 
            }
        }
        
        return $profil;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : identite
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_identite($etudiant_id, $post_data)
    {
        $etudiant = $this->extraire_etudiant($etudiant_id);

        //
        // Permettre la modification des champs pertinents uniquement
        //

        $champs_pertinents = array(
            'nom', 'prenom', 'genre', 'numero_da'
        );

        //
        // Exclure les champs
        //

        $data = array();

        foreach($post_data as $champ => $val)
        {
            if ( ! in_array($champ, $champs_pertinents))
            {
                continue;
            }
    
            if ($etudiant[$champ] == $val)
            {
                continue;
                
            }

            $data[$champ] = $val;
        }

        // Le numero DA (matricule) doit etre traite separement

        $numero_da = FALSE;

        if (array_key_exists('numero_da', $data))
        {
            $numero_da = $data['numero_da'];

            unset($data['numero_da']);
        }

        //
        // Faire les modifications appropriees
        //

        if ((empty($data) || ! is_array($data)) && $numero_da === FALSE)
        {
            return 'aucun';
        }

        $resultat = FALSE;

        //
        // Changer l'identite
        //

        if ( ! empty($data) && is_array($data))
        {
            $this->db->where ('etudiant_id', $etudiant_id);
            $this->db->update('etudiants', $data);

            $resultat = TRUE;
        }

        //
        // Changer le numero_da
        //

        if ($numero_da !== FALSE)
        {
            $data = array(
                'numero_da' => trim($numero_da)
            );

            if (array_key_exists('numero_da_inexistant', $etudiant))
            {
                $data['etudiant_id'] = $etudiant_id;
                $data['groupe_id']   = $this->groupe_id;

                $this->db->insert ('etudiants_numero_da', $data);
            }
            else
            {
                $this->db->where  ('etudiant_id', $etudiant_id);
                $this->db->where  ('groupe_id',   $this->groupe_id);
                $this->db->update ('etudiants_numero_da', $data);
            }

            $resultat = TRUE;
        }

        return $resultat;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : mot-de-passe
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_motdepasse($etudiant_id, $post_data)
    {
        $etudiant = $this->extraire_etudiant($etudiant_id,
            array(
                'inclure_motdepasse' => TRUE
            )
        );

        $champs_obligatoires = array(
            'password0', // mot-de-passe actuel
            'password1', // nouveau mot-de-passe
            'password2'  // confirmation du nouveau mot-de-passe
        );

        foreach($champs_obligatoires as $champ)
        {
            if ( ! array_key_exists($champ, $post_data) || empty($post_data[$champ]))
            {
                return FALSE;
            }
        }

        //
        // Verifier le mot-de-passe actuel
        //

        if ( ! $this->Auth_model->verifier_motdepasse($etudiant['salt'], $post_data['password0'], $etudiant['password']))
        {
            return 'mauvais_motdepasse';
        }     

        //
        // Faire les modifications au mot-de-passe
        //

        if ($this->Auth_model->editer_password('etudiant', $etudiant_id, $post_data['password1']) !== TRUE)
        {
            return FALSE;
        }

        log_alerte(
            array(
                'code'  => 'PRF8900',
                'desc'  => $etudiant['prenom'] . ' ' . $etudiant['nom'] . ' a changé son mot-de-passe.',
                'extra' => 'etudiant_id = ' . $etudiant['etudiant_id']
            )
        );

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : parametres
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_parametres($etudiant_id, $post_data)
    {
        $etudiant = $this->extraire_etudiant($etudiant_id);

        //
        // Permettre la modification des champs pertinents uniquement
        //

        $champs_pertinents = array(
            'courriel_evaluation_envoyee', 'montrer_rang_cours', 'montrer_rang_evaluation'
        );

        //
        // Initialiser les champs (standard behaviour is the value is only sent if the checkbox is checked)
        //
        // absent  : 0
        // present : on => 1
        //

        foreach($champs_pertinents as $champ)
        {
            if ( ! array_key_exists($champ, $post_data))
            {
                $post_data[$champ] = 0;
            }
            else
            {
                $post_data[$champ] = 1;
            }
        }

        //
        // Exclure les champs non pertinents
        //

        $data = array();

        foreach($post_data as $champ => $val)
        {
            if ( ! in_array($champ, $champs_pertinents))
            {
                continue;
            }
    
            if ($etudiant[$champ] == $val)
            {
                continue;
            }

            $data[$champ] = $val;
        }

        //
        // Faire les modifications appropriees
        //

        if (empty($data) || ! is_array($data))
        {
            return 'aucun';
        }

        $this->db->where ('etudiant_id', $etudiant_id);
        $this->db->update('etudiants', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les soumissions de l'etudiant, pour le groupe en cours
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Extraire en ordre decroissant de remplissage
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumissions($etudiant_id, $options = array())
    {
    	$options = array_merge(
            array(
                'semestre_id'  => NULL
            ),
            $options
       	);

        if (empty($etudiant_id) || ! ctype_digit($etudiant_id))
        {
            return array();
        }

        //
        // Extraire les soumissions
        //

        $soumissions = array();

        $this->db->from     ('soumissions as s');
        $this->db->where    ('s.etudiant_id', $etudiant_id);
        $this->db->where    ('s.groupe_id', $this->groupe_id);
        $this->db->where    ('s.efface', 0);

        if ($options['semestre_id'])
        {
            $this->db->where('s.semestre_id', $options['semestre_id']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $soumissions = $query->result_array();
        }

        //
        // Extraire les soumissions ou l'etudiant est un partenaire de laboratoire
        //

        $this->db->select   ('s.*');
        $this->db->from     ('soumissions as s');
        $this->db->where    ('s.lab', 1);

        $this->db->group_start();
            $this->db->where    ('s.lab_etudiant2_id', $etudiant_id);
            $this->db->or_where ('s.lab_etudiant3_id', $etudiant_id);
        $this->db->group_end();

        $this->db->where    ('s.groupe_id', $this->groupe_id);
        $this->db->where    ('s.efface', 0);

        if ($options['semestre_id'])
        {
            $this->db->where('s.semestre_id', $options['semestre_id']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $soumissions = array_merge($soumissions, $query->result_array());
        }

        //
        // Extraire les soumissions partagees
        //
        // *** Ceci sera rendu OBSOLETE a la session H2025, ainsi que la table 'soumissions_partagees'.
        //

        $soumissions_p = array();

        $this->db->select   ('s.*');
        $this->db->from     ('soumissions as s, soumissions_partagees as sp');
        $this->db->where    ('sp.etudiant_id', $etudiant_id);
        $this->db->where    ('sp.soumission_id = s.soumission_id');
        $this->db->where    ('s.groupe_id', $this->groupe_id);
        $this->db->where    ('s.efface', 0);

        if ($options['semestre_id'])
        {
            $this->db->where('s.semestre_id', $options['semestre_id']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $soumissions = array_merge($soumissions, $query->result_array());
        }

        //
        // Order by date
        //

		usort($soumissions, function($a, $b) 
		{
    		return $b['soumission_epoch'] - $a['soumission_epoch'];
		});

        return $soumissions;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les etudiants en train de remplir des evaluations de l'enseignant
     *
     * Version 2 : 2020/04/17
     * Version 3 : 2020/12/20
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiants_redaction($evaluations_references, $min_epoch = 0, $options = array())
    {
    	$options = array_merge(
            array(
                'notifications' => FALSE
            ),
            $options
        );

        $this->db->from     ('etudiants_traces as et');
        $this->db->where    ('et.evaluation_envoyee', 0);
        $this->db->where    ('et.efface', 0);
        $this->db->where    ('et.efface_par_etudiant', 0);
        $this->db->where    ('et.semestre_id', $this->semestre_id);
        $this->db->where_in ('et.evaluation_reference', $evaluations_references);
        $this->db->order_by ('et.activite_epoch', 'desc');

        // $this->db->where ('et.activite_epoch !=', NULL);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $etudiants = $query->result_array(); // Inclus les etudiants inscrits et NON inscrits

		//
		// Extraire les informations (nom, prenom, genre) des etudiants inscrits
		//

        $etudiant_ids = array();

        foreach($etudiants as $e)
        {
            $etudiant_ids[] = $e['etudiant_id'];

            if ($e['lab'])
            {
                if ( ! empty($e['lab_etudiant2_id']))
                    $etudiant_ids[] = $e['lab_etudiant2_id'];

                if ( ! empty($e['lab_etudiant3_id']))
                    $etudiant_ids[] = $e['lab_etudiant3_id'];
            }
        }

		$etudiants_info = array();

		if ( ! empty($etudiant_ids))
		{
			$etudiants_info = $this->extraire_etudiants(array('etudiant_ids' => $etudiant_ids));
        }

        //
        // Determiner les etudiants en redaction
        //

        foreach($etudiants as $k => $e)
        {
            $evaluation_id        = $e['evaluation_id'];
            $evaluation_reference = $e['evaluation_reference'];

            //
            // Le temps en redaction en minutes
            //

            $secondes_en_redaction = $e['secondes_en_redaction'];
            $minutes_en_redaction  = ceil($secondes_en_redaction / 60);

            //
            // Le nom des partenaires de laboratoire
            //

            if ($e['lab'])
            {
                if ( ! empty($e['lab_etudiant2_id']) && array_key_exists($e['lab_etudiant2_id'], $etudiants_info))
                {
                    $etudiants[$k]['lab_etudiant2_nom'] = $etudiants_info[$e['lab_etudiant2_id']]['prenom'];
                    $etudiants[$k]['lab_etudiant2_nom'] .= ' ' . $etudiants_info[$e['lab_etudiant2_id']]['nom'];
                }

                if ( ! empty($e['lab_etudiant3_id']) && array_key_exists($e['lab_etudiant3_id'], $etudiants_info))
                {
                    $etudiants[$k]['lab_etudiant3_nom'] = $etudiants_info[$e['lab_etudiant3_id']]['prenom'];
                    $etudiants[$k]['lab_etudiant3_nom'] .= ' ' . $etudiants_info[$e['lab_etudiant3_id']]['nom'];
                }
            }

            //
            // Tous les etudiants
            // 

            /*
             *
             * Je pense qu'il vaut mieux, pour les etudiants inscrits, de voir tout ceux qui
             * ont charge l'evaluation, et pour combien de temps. (2020-10-01)
             *
            // Ne pas inclure les etudiants curieux ayant seulement ouvert l'evaluation,
            // ce qui correspond aux chargements dont l'inactivite depasse 15 minutes et dont le temps
            // en redaction de l'etudiant s'est limite a 10 minutes ou moins.

            if ( ($minutes_en_redaction <= 10) && ($e['activite_epoch'] < ($this->now_epoch - 60*15)) )
            {
                unset($etudiants[$k]);	
                continue;
            }
            */

            //
            // Les etudiants INSCRITS		
            //

			if ( ! empty($e['etudiant_id']))
            {
                $etudiant_id = $e['etudiant_id'];

                //
                // Extraire les traces de cet etudiant/evaluation
                //
                // Si l'etudiant a decide d'effacer son evaluation, alors
                // effacer son entree dans celle des etudiants en redaction.
                //

                $t = array();

                if ( ! empty($traces_inscrits))
                {
                    foreach($traces_inscrits as $ti)
                    {
                        if ($ti['etudiant_id'] != $etudiant_id)
                            continue;

                        if ($ti['evaluation_reference'] != $evaluation_reference)
                            continue;

                        $t = $ti;
                        break;
                    }

                    if ( ! empty($t) && $t['efface_par_etudiant'])
                    {
                        unset($etudiants[$k]);
                        continue;
                    }
                }

                //
                // Effacer les zombies
                //
                // Si les etudiants ont moins de 5 minutes en redaction, et a fait plus d'une semaine,
                // ne pas montrer leur entree.
                //

                if ($minutes_en_redaction < 6 && ($this->now_epoch - $e['activite_epoch']) > 60*60*24*7)
                {
                    unset($etudiants[$k]);	
                    continue;
                }

                //
				// Incorporer les informations de l'etudiant
                //

				$etudiants[$k]['nom']    = $etudiants_info[$etudiant_id]['nom'];
				$etudiants[$k]['prenom'] = $etudiants_info[$etudiant_id]['prenom'];
				$etudiants[$k]['genre']  = $etudiants_info[$etudiant_id]['genre'];

				continue;
            }

            //
            // Les etudiants NON INSCRITS
            //

			else
            {
                // Ne pas inclure les etudiants ayant abandonne la redaction, 
                // ce qui correspond aux etudiants ayant une inactivite superieure a deux heures.

                if ($e['activite_epoch'] < ($this->now_epoch - 60*60*2))
                {
                    unset($etudiants[$k]);	
                    continue;
                }

                //
                // Detecter l'identite presumee de l'etudiant
                //

                // Obtenir le nom des traces

                $traces_arr = unserialize($e['data']);
        
                if (array_key_exists('nom', $traces_arr) && ! empty($traces_arr['nom']))
                {
                    $etudiants[$k]['nom']    = NULL;
                    $etudiants[$k]['prenom'] = $traces_arr['nom'] . ' ＊';
                }

                // Obtenir le nom a partir des temoins de connexion (admin seulement)
                // L'identite presume n'est pas enregistree dans les traces des etudiants NON inscrits.

                /*
                if ($this->enseignant['privilege'] >= 90 && empty($etudiants[$k]['nom']) && empty($etudiants[$k]['prenom']))
                {
                    if ($e['identite_presumee'] != NULL)
                    {
                        $etudiants[$k]['nom']    = NULL;
                        $etudiants[$k]['prenom'] = $e['identite_presumee'] . ' Ψ';
                    }
                }
                */

			} // else
        }

        //
        // Extraire les notifications
        //

        if ( ! empty($etudiant_ids) && $options['notifications'])
        {
            $notifications = array();

            $evaluation_references = array_column($etudiants, 'evaluation_reference');
            $evaluation_references = array_unique($evaluation_references);

            if ( ! empty($evaluation_references))
            {
                $this->db->from     ('etudiants_evaluations_messages');
                $this->db->where    ('enseignant_id', $this->enseignant_id);
                $this->db->where    ('semestre_id', $this->semestre_id);
                $this->db->where_in ('evaluation_reference', $evaluation_references);
                
                $query = $this->db->get();
                
                if ($query->num_rows() > 0)
                {
                    $messages = $query->result_array();
                    $message_ids = array_column($messages, 'message_id');
                            
                    $this->db->from     ('etudiants_evaluations_notifications');
                    $this->db->where    ('extrait', 0);
                    $this->db->where_in ('message_id', $message_ids);
                    
                    $query = $this->db->get();
                    
                    if ($query->num_rows() > 0)
                    {
                        $notifications = $query->result_array();
                    }
                }
            }

            //
            // Organisation les notifications
            //

            if ( ! empty($notifications))
            {
                foreach($notifications as $n)
                {
                    foreach($etudiants as &$e)
                    {
                        if (empty($e['etudiant_id']))
                            continue;

                        if ( ! array_key_exists('notifications', $e))
                        {
                            $e['notifications'] = array();
                        } 

                        if (
                            $n['etudiant_id'] == $e['etudiant_id'] &&
                            $n['evaluation_reference'] == $e['evaluation_reference']
                           )
                        {
                            if ( ! in_array($n['message_id'], $e['notifications']))
                            {
                                $e['notifications'][] = $n['message_id'];
                            }
                        }
                    } // foreach $e
                } // foreach $n
            }

        } // notifications

        return $etudiants;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les traces par session_id(s) (des etudiants NON inscrits)
     *
     * -------------------------------------------------------------------------------------------- */
    function traces_session_ids($session_ids = array(), $min_epoch = 0)
    {
        if (empty($session_ids))
        {
            return array();
        }

        $this->db->from     ('traces');
        $this->db->select   ('session_id, data');
        $this->db->where    ('soumission_debut_epoch >=', $min_epoch);
        $this->db->where_in ('session_id', $session_ids);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'session_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les informations de chargement de l'evaluation pour une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_chargement_soumission($evaluation_reference, $etudiant_id = NULL, $session_id = NULL)
    {
        $this->db->from     ('evaluations_securite_chargements as esc');
        $this->db->where    ('esc.evaluation_reference', $evaluation_reference);
        $this->db->where    ('esc.efface', 0);
        $this->db->where    ('esc.activite_epoch !=', NULL);
        $this->db->where_in ('esc.evaluation_reference', $evaluation_reference);

        // Un etudiant inscrit
        
        if ( ! empty($etudiant_id))
        {
            $this->db->where ('esc.etudiant_id', $etudiant_id);
        }

        // Un etudiant NON inscrit

        else
        {
            $this->db->where ('esc.session_id', $session_id ?: session_id());
        }

        $this->db->limit (1);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les traces completes
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ceci inclus les donnees (data = traces) ainsi que les autres informations.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_traces_completes($evaluation_reference, $evaluation_id, $session_id = NULL)
    {
        $this->db->from  ('etudiants_traces as et');
        $this->db->where ('et.evaluation_reference', $evaluation_reference);
        $this->db->where ('et.evaluation_id', $evaluation_id);
        $this->db->where ('et.evaluation_envoyee', 0);
        $this->db->where ('et.efface', 0);
        $this->db->limit (1);

        //
        // Un etudiant inscrit
        //

        if ($this->est_etudiant)
        {
            $this->db->where ('et.etudiant_id', $this->etudiant_id);
        }

        //
        // Un etudiant NON inscrit
        //

        else
        {
            $this->db->where ('et.session_id', $session_id ?: session_id());
        }
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter une soumission aux resultats d'un etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_soumission_resultats($reference, $empreinte)
    {
        $soumission = $this->Evaluation_model->extraire_soumission_reference($reference);

        if (empty($soumission))
        {
            // Cette soumission est introuvable.
            return FALSE;
        }

        if ( ! empty($soumission['etudiant_id']))
        {
            // Cette soummision a deja ete ajoutee dans le compte d'un etudiant.
            return 2;
        }

        if ($soumission['groupe_id'] != $this->groupe_id)
        {
            // Cette soumission n'appartient pas a ce groupe.
            return 4;
        }

        if (strtolower($empreinte) == $soumission['empreinte'])
        {
            $data = array(
                'etudiant_id' => $this->etudiant_id
            );

            $this->db->where('soumission_id', $soumission['soumission_id']);
            $this->db->update('soumissions', $data);

            log_alerte(
                array(
                    'code'       => 'ETUAJS1', 
                    'desc'       => "Un étudiant a ajouté dans les résultats de son compte une soumission envoyée précédemment.", 
                    'importance' => 1
                )
            );

            return TRUE;
        }

        // La combinaison reference/empreinte est inexistante.
        return 1; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Suggerer les evaluations a remplir
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluations_a_suggerer()
    {
        //
        // L'etudiant doit avoir configure son Numero DA.
        //

        if (empty($this->etudiant['numero_da']))
        {
            return array();
        }

        //
        // Assainir le Numero DA.
        //

        if ( ! ctype_alnum($this->etudiant['numero_da']))
        {
            return array();
        }

        //
        // Le semestre doit etre en vigueur.
        //

        if ($this->semestre_id == 0)
        {
            return array();
        }

        //
        // Extraire les cours de l'etudiant
        //

        // $this->db->from  ('eleves as e, cours as c');
        $this->db->from  ('eleves as e');

        $this->db->select ('e.*');

        // $this->db->where ('e.cours_id = e.cours_id');
        $this->db->where ('e.numero_da', $this->etudiant['numero_da']);
        $this->db->where ('e.groupe_id', $this->groupe_id);
        $this->db->where ('e.semestre_id', $this->semestre_id);

        // $this->db->order_by ('c.cours_nom', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            // L'enseignant n'a probablement pas encore entre ses listes d'eleves.
            return array();
        }

        $data = array();

        foreach($query->result_array() as $cours)
        {
            $cours_id = $cours['cours_id'];

            $evaluations = $this->Evaluation_model->lister_evaluations_selectionnees(
                    $cours['enseignant_id'],
                    $this->semestre_id,
                    $cours_id,
                    TRUE,
                    array(
                        'cacher_cachees'  => TRUE, 
                        'cacher_bloquees' => TRUE,
                        'respecter_date'  => TRUE
                    )
            );

            if ( ! empty($evaluations))
            {
                if ( ! array_key_exists($cours_id, $data))
                    $data[$cours_id] = [];

               $data[$cours_id] = $data[$cours_id] + $evaluations;
            }
        }

        return $data;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les traces completes d'une evaluation debutee par une etudiant inscrit
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluation_reference_traces($etudiant_id, $evaluation_reference)
    {
        $this->db->from  ('etudiants_traces');
        $this->db->where ('etudiant_id', $etudiant_id);
        $this->db->where ('semestre_id', $this->semestre_id);
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Nombre de soumissions envoyees par etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function nombre_soumissions_etudiants($etudiant_ids)
    {
        $this->db->from     ('soumissions as s');
        $this->db->where    ('s.efface', 0);
        $this->db->where_in ('etudiant_id', $etudiant_ids);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $nombre_s = array();

        foreach($query->result_array() as $row)
        {
            if ( ! array_key_exists($row['etudiant_id'], $nombre_s))
            {
                $nombre_s[$row['etudiant_id']] = 1;
                continue;
            }

            $nombre_s[$row['etudiant_id']] += 1; 
        }

        return $nombre_s;   
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire comptes des etudiants avec leur numero DAs
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_comptes_etudiants($numero_das)
    {
        $this->db->from     ('etudiants_numero_da as en, etudiants as e');
        $this->db->select   ('e.etudiant_id, e.nom, e.prenom, en.numero_da, e.courriel');
        $this->db->where    ('e.etudiant_id = en.etudiant_id');
        $this->db->where_in ('en.numero_da', $numero_das);
        $this->db->where    ('en.groupe_id', $this->groupe_id);
        $this->db->where    ('e.actif', 1);
        $this->db->where    ('e.efface', 0);
        $this->db->where    ('e.test', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return array_keys_swap($query->result_array(), 'etudiant_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les comptes autorises (associes a vos etudiants)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_comptes_autorises($semestre_id, $options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id'  => $this->est_enseignant ? $this->enseignant_id : NULL,
                'cours_id'       => NULL
            ),
            $options
       	);

        //
        // Champs obligatoires
        //

        if (empty($options['enseignant_id']))
        {
            return array();
        }

        $this->db->from  ('etudiants_cours');
        $this->db->where ('enseignant_id', $options['enseignant_id']);
        $this->db->where ('semestre_id',   $semestre_id);

        if ( ! empty($option['cours_id']))
        {
            $this->db->where('cours_id', $options['cours_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $comptes = $query->result_array();

        //
        // Demande pour un seul cours, 
        // donc on retoure un tableau simple
        //

        if ( ! empty($options['cours_id']))
        {
            return array_keys_swap($comptes, 'etudiant_id');
        }

        //
        // Demande pour plusieurs cours, 
        // donc il faut creer un tableau (cours_id) de tableau
        //

        if ( ! empty($comptes) && is_array($comptes))
        {
            $comptes_out = array(); // le tableau (cours_id) de tableau de retour

            // Organiser le tableau par cours
            // On assume qu'un etudiant ne peut pas etre dans deux groupes differents d'un meme cours
            
            foreach($comptes as $c)
            {
                if ( ! array_key_exists($c['cours_id'], $comptes_out))
                {
                    $comptes_out[$c['cours_id']] = array();
                }

                $comptes_out[$c['cours_id']][$c['etudiant_id']] = $c;
            }

            return $comptes_out;
        }

        return array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Associer le compte trouve a un etudiant d'une liste d'etudiants
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_compte_autorise($semestre_id, $etudiant_id)
    {
        $this->db->from  ('etudiants_cours');
        $this->db->where ('semestre_id', $semestre_id);
        $this->db->where ('etudiant_id', $etudiant_id);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Associer le compte trouve a un etudiant d'une liste d'etudiants
     *
     * -------------------------------------------------------------------------------------------- */
    function associer_compte($post_data)
    {
        // Verifier l'existence de cette entree dans la base de donnees

        $this->db->from  ('etudiants_cours');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('etudiant_id',   $post_data['etudiant_id']);
        $this->db->where ('cours_id',      $post_data['cours_id']);
        $this->db->where ('semestre_id',   $post_data['semestre_id']);
        $this->db->where ('cours_groupe',  $post_data['cours_groupe']);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            // Une entree a ete trouvee, ne pas ajouter deux fois la meme entree.
            return FALSE;
        }

        $data = array(
            'enseignant_id' => $this->enseignant_id, 
            'etudiant_id'   => $post_data['etudiant_id'],
            'cours_id'      => $post_data['cours_id'],
            'semestre_id'   => $post_data['semestre_id'],
            'cours_groupe'  => $post_data['cours_groupe'],
            'numero_da'     => $post_data['numero_da'],
            'ajout_date'    => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'   => $this->now_epoch
        );

        $this->db->insert('etudiants_cours', $data);

        if ($this->db->affected_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Dissocier le compte d'un etudiant a l'etudiant de votre liste
     *
     * -------------------------------------------------------------------------------------------- */
    function dissocier_compte($post_data)
    {
        // Verifier l'existence de cette entree dans la base de donnees

        $this->db->from  ('etudiants_cours');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('etudiant_id',   $post_data['etudiant_id']);
        $this->db->where ('cours_id',      $post_data['cours_id']);
		$this->db->where ('semestre_id',   $post_data['semestre_id']);

		// Ceci causait un probleme lorsque le meme etudiant, dans le meme cours, appartient a deux groupes differents.
        // Par exemple, lorsqu'il y a un groupe cours et le cours est separe en deux groupes pour les laboratoires. 2022-06-01
        // Je l'ai reajoute parce que ca causait un autre probleme (celui de France) pour une raison obscure. 2023-02-07.
            
        $this->db->where ('cours_groupe',  $post_data['cours_groupe']);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
			// Aucune entree trouvee
			// echo json_encode('aucune entree trouvee');
            return FALSE;
        }

        $row = $query->row_array();

        $this->db->where  ('id', $row['id']);
        $this->db->delete ('etudiants_cours');

        if ($this->db->affected_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Mettre a jour le cours_groupe des comptes autorises
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ceci est necessaire si l'enseignant efface une liste d'eleves ou il y avait deja des
     * comptes autorises. Il faut mettre a jour le cours_groupe qui aurait peut-etre change.
     *
     * -------------------------------------------------------------------------------------------- */
    function rafraichir_comptes_autorises($semestre_id, $cours_id, $cours_groupe)
    {
        $data = array(
            'cours_groupe' => $cours_groupe
        );

        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('semestre_id',   $semestre_id);
        $this->db->where ('cours_id',      $cours_id);

        $this->db->update ('etudiants_cours', $data);

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'activite de l'etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant_activite($etudiant_id, $from_epoch = 0)
    {
        $this->db->from     ('activite');
        $this->db->where    ('etudiant_id', $etudiant_id);
        $this->db->where    ('epoch >=', $from_epoch);
        $this->db->order_by ('id', 'asc');
        // $this->db->order_by ('epoch', 'desc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner la performance de l'etudiant pour chacune de ses soumissions
     *
     * - Le rang pour chaque evaluation
     * - L'ecart a la moyenne pour chaque evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function determiner_performance($soumissions)
    {
        $cache_key = strtolower(__CLASS__) . '_' . strtolower(__FUNCTION__) . '_' . @$this->etudiant_id . '_' . md5(serialize(array_keys($soumissions)));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        //
        // Extraire les evaluation_ids et les enseignant_ids des soumissions
        //

        $evaluation_ids = array();
        $enseignant_ids = array();

        foreach($soumissions as $s)
        {
            if ( ! in_array($s['evaluation_id'], $evaluation_ids))
            {
                $evaluation_ids[] = $s['evaluation_id'];
            }

            if ( ! in_array($s['enseignant_id'], $enseignant_ids))
            {
                $enseignant_ids[] = $s['enseignant_id'];
            }
        }

        //
        // Extraire la preference des enseignants concernes
        //

        $enseignants_montrer_rang = array();
        $enseignants_montrer_ecart_moy = array();

        $this->db->select   ('e.enseignant_id, e.montrer_rang, e.montrer_ecart_moy');
        $this->db->from     ('enseignants as e');
        $this->db->where_in ('e.enseignant_id', $enseignant_ids);
        $this->db->where    ('e.efface', 0);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $r)
            {
                if ($this->est_enseignant && $this->enseignant['privilege'] > 89)
                {
                    //
                    // Ceci est pour calculer les rangs pour l'admin.
                    //

                    $enseignants_montrer_rang[$r['enseignant_id']]      = TRUE;
                    $enseignants_montrer_ecart_moy[$r['enseignant_id']] = TRUE;
                }
                else
                {
                    $enseignants_montrer_rang[$r['enseignant_id']]      = $r['montrer_rang'];
                    $enseignants_montrer_ecart_moy[$r['enseignant_id']] = $r['montrer_ecart_moy'];
                }
            }
        }            
        
        //
        // Extraire toutes les soumissions, de tous les etudiants
        //
        
        $this->db->select   ('s.soumission_id, s.evaluation_reference, s.soumission_reference, s.points_obtenus, s.corrections_terminees, s.evaluation_id, s.semestre_id, s.ajustements_data');
        $this->db->from     ('soumissions as s');
        $this->db->where_in ('s.evaluation_id', $evaluation_ids);
        $this->db->where    ('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $toutes_soumissions = $query->result_array();

        $notes     = array(); // Les notes de TOUS les etudiants
        $mes_notes = array(); // Les notes de l'etudiant
        $notes_max = array(); // Le nombre de point de l'evaluation (ou la note max que l'etudiant pourrait avoir)

        $enseignants_evaluation = array();

        //
        // Determiner la performance a partir des resultats de l'etudiant
        //

        foreach($soumissions as $s)
        {
            // Ne pas tenir compte des soumissions dont les corrections manuelles ne sont pas terminees

            if ( ! $s['corrections_terminees'])
                continue;

            $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];

            // Enregistrer l'enseignant pour chaque evaluation

            $enseignants_evaluation[$label] = $s['enseignant_id'];

            if ( ! array_key_exists($label, $notes))
            {
                $notes[$label] = array();
            } 

            $points_obtenus = $s['points_obtenus'];

            //
            // Verifier si la note de la soumission a ete ajustee par l'enseignant
            //

            if ( ! empty($s['ajustements_data']))
            {
                $ajustements = unserialize($s['ajustements_data']);

                if (array_key_exists('total', $ajustements))
                { 
                    $points_obtenus = $ajustements['total'];
                }
            }

            $notes[$label][$s['soumission_id']] = $points_obtenus;

            $mes_notes[$label] = $points_obtenus;
            $notes_max[$label] = $s['points_evaluation'];
        }

        //
        // Inserer les resultats des autres etudiants dans les notes
        //

        foreach($toutes_soumissions as $s)
        {
            //
            // Ne pas tenir compte des soumissions dont les corrections manuelles ne sont pas terminees
            //

            if ( ! $s['corrections_terminees'])
                continue;

            //
            // Ne pas tenir compte des soumissions envoyees par les enseignants lors d'une previsualisation
            //

            if (empty($s['evaluation_reference']))
                continue;

            $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];

            if ( ! array_key_exists($label, $notes))
                continue;

            $points_obtenus = $s['points_obtenus'];

            //
            // Verifier si la note de la soumission a ete ajustee par l'enseignant
            //

            if ( ! empty($s['ajustements_data']))
            {
                $ajustements = unserialize($s['ajustements_data']);

                if (array_key_exists('total', $ajustements))
                { 
                    $points_obtenus = $ajustements['total'];
                }
            }

            $notes[$label][$s['soumission_id']] = $points_obtenus;
        }

        //
        // Moyennes
        //

        $moyennes = array();

        //
        // - Ordonner de facon decroissante tous les resultats
        // - Calculer la moyenne pour chaque evaluation
        //

        foreach($notes as $evaluation => $n)
        {
            $moyennes[$evaluation] = array_sum($n) / count($n);

            arsort($n);
            $notes[$evaluation] = $n;
        }

        //
        // - Determiner le rang de l'etudiant pour chaque evaluation
        // - Determiner l'ecart a la moyenne pour chaque evaluation
        //
        
        $perf = array();

        $soumission_ids = array_keys($soumissions);

        foreach($notes as $evaluation => $n)
        {
            if ( ! array_key_exists($evaluation, $perf))
            {
                $perf[$evaluation] = array();
            }

            //
            // Extraire l'enseignant
            //

            $enseignant_id = 0;

            if (array_key_exists($evaluation, $enseignants_evaluation))
            {
                $enseignant_id = $enseignants_evaluation[$evaluation];
            }

            //
            // Determiner le rang
            //

            if ($enseignants_montrer_rang[$enseignant_id])
            {
                // 
                // Le nombre total d'etudiants pour cette evaluation
                //

                $perf[$evaluation]['rang_max'] = count($n);

                $i = 0;

                $note_precedente = NULL;
                $note_precedente_fois = 1;

                foreach($n as $soumission_id => $note)
                {
                    $i++;

                    if ($note_precedente == NULL || $note != $note_precedente)
                    {
                        $note_precedente = $note;
                        $rang = $i;
                    }

                    if ( ! in_array($soumission_id, $soumission_ids))
                        continue;

                    if ($enseignants_montrer_rang[$soumissions[$soumission_id]['enseignant_id']])
                    {
                        $perf[$evaluation]['rang'] = $rang;
                    }
                }
        
            }  // rang

            //
            // Determiner l'ecart a la moyenne
            //

            if ($enseignants_montrer_ecart_moy[$enseignant_id])
            {
                // $rangs[$evaluation]['note']     = $mes_notes[$evaluation];
                // $rangs[$evaluation]['note_max'] = $notes_max[$evaluation];
                // $rangs[$evaluation]['moy']      = $moyennes[$evaluation];

                if ($notes_max[$evaluation] > 0)
                {
                    $perf[$evaluation]['ecart_moy'] = ($mes_notes[$evaluation] / $notes_max[$evaluation] * 100) - ($moyennes[$evaluation] / $notes_max[$evaluation] * 100);
                }
                else
                {
                    $perf[$evaluation]['ecart_moy'] = 0;
                }
            }
        }

        //
        // Verifier au moins un rang et au moins un ecart_moy a ete trouve
        //

        $champs_recherches = array('rang', 'ecart_moy');

        foreach($champs_recherches as $c)
        {
            $perf['_' . $c] = FALSE;

            foreach($perf as $p)
            {
                if ( ! is_array($p))
                    continue;

                if (array_key_exists($c, $p))
                {
                    $perf['_' . $c] = TRUE;
                }
            }
        }

        $this->kcache->save($cache_key, $perf, 'resultats', 30);

        return $perf;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner les rangs de l'etudiant pour chacun de ses cours
     *
     * --------------------------------------------------------------------------------------------
     *
     * Il faut considerer le semestre car un etudiant aurait pu faire le meme cours a deux 
     * semestres differents, dans le cas d'un echec la premiere fois par exemple.
     *
     * -------------------------------------------------------------------------------------------- */
    function determiner_rangs_cours($etudiant_id)
    {
        $cache_key = strtolower(__CLASS__) . '_' . strtolower(__FUNCTION__) . '_' . $etudiant_id;

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        //
        // Extraire toutes les soumissions de l'etudiant
        //

        $this->db->select ('soumission_id, enseignant_id, etudiant_id, evaluation_id, evaluation_reference, semestre_id, cours_id, points_obtenus, points_evaluation, ajustements_data');
        $this->db->from   ('soumissions');
        $this->db->where  ('corrections_terminees', 1);
        $this->db->where  ('etudiant_id', $etudiant_id);
        $this->db->where  ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        $soumissions = $query->result_array();

        //
        // A partir des soumissions de l'etudiant :
        //
        // - Determiner la relation semestres/cours
        //
        // - Determiner la relation cours/enseignants
        //   On veut faire un rang seulement pour le meme enseignant
        //   sinon ca pourrait etre injuste pour ceux ayant des enseignants plus severes.
        //
        //   @TODO Eventuellement, pour creer un classement inter-enseignant, on pourrait 
        //         tenir compte de la moyenne et de faire un ajustement a la hausse 
        //         (ou a la baisse) selon l'enseignant.
        //
        
        $semestres_cours   = array(); // semestre_id => $cours_id
        $cours_enseignants = array(); // cours_id => enseignant_id
        
        foreach($soumissions as $s)
        {
            if ( ! array_key_exists($s['semestre_id'], $semestres_cours))
            {
                $semestres_cours[$s['semestre_id']] = array();
            }

            if ( ! in_array($s['cours_id'], $semestres_cours[$s['semestre_id']]))
            {
                $semestres_cours[$s['semestre_id']][] = $s['cours_id'];
            }

            if ( ! array_key_exists($s['cours_id'], $cours_enseignants))
            {
                $cours_enseignants[$s['cours_id']] = $s['enseignant_id'];
            }
        }

        //
        // Extraire toutes les soumissions (de tous les etudiants) 
        // de tous les semestres et de tous les cours dont l'etudiant a envoye au moins une soumission
        //

        $soumissions_semestres_cours = array();

        foreach($semestres_cours as $semestre_id => $cours_ids)
        {
            foreach($cours_ids as $cours_id)
            {
                $this->db->select ('soumission_id, etudiant_id, cours_id, evaluation_id, evaluation_reference, semestre_id, points_obtenus, points_evaluation, ajustements_data');
                $this->db->from   ('soumissions');
                $this->db->where  ('etudiant_id !=', NULL);
                $this->db->where  ('corrections_terminees', 1);
                $this->db->where  ('cours_id', $cours_id);
                $this->db->where  ('enseignant_id', $cours_enseignants[$cours_id]);
                $this->db->where  ('semestre_id', $semestre_id);
                $this->db->where  ('efface', 0);
                
                $query = $this->db->get();
                
                if ( ! $query->num_rows() > 0)
                {
                    continue;
                }

                $soumissions_semestres_cours[$semestre_id][$cours_id] = $query->result_array();
            }
        }

        //
        // Determiner le pointage de chaque etudiant pour chaque semestre/cours
        //

        $rangs = array();

        $etudiants_points_obtenus = array();
        $etudiants_points_totaux  = array();

        $ponderations = array();

        foreach($soumissions_semestres_cours as $semestre_id => $soumissions_cours)
        {
            foreach($soumissions_cours as $cours_id => $ss)
            {
                //
                // Extraire les ponderations
                //

                $ponderations = array_merge($ponderations, $this->Evaluation_model->extraire_ponderations_par_soumissions($ss));

                //
                // Iterer a travers les soumissions
                //

                $etu_resultats = array();
                $pourcentages  = array();

                $points_accumules = array();

                foreach($ss as $s)
                { 
                    // Un semestre a la fois
                    if ($s['semestre_id'] != $semestre_id)
                        continue;

                    // Un cours a la fois 
                    if ($s['cours_id'] != $cours_id)
                        continue;

                    $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];

                    //
                    // Ne pas considerer les soumissions (evaluations) dont la ponderation est inconnue.
                    //

                    if ( ! array_key_exists($label, $ponderations))
                        continue;
    
                    //
                    // Ne pas considerer les essais des enseignants
                    // 
                    // - Ce sont les soumissions l'evaluation_reference est NULL
                    //

                    if (empty($s['evaluation_reference']))
                        continue;

                    //
                    // L'etudiant_id
                    //

                    $e_id = $s['etudiant_id'];

                    //
                    // Initialiser les variables
                    //

                    if ( ! array_key_exists($e_id, $etu_resultats))
                    {
                        $etu_resultats[$e_id] = array(
                            'p_obtenus'     => 0,
                            'p_totaux'      => 0,
                            'p_pourcentage' => 0
                        );

                        $pourcentages[$e_id] = 0;
                    }

                    //
                    // Considerer les ajustements a la note par l'enseignant
                    //

                    $points_obtenus = $s['points_obtenus'];

                    if ( ! empty($s['ajustements_data']))
                    {
                        $ajustements = unserialize($s['ajustements_data']);

                        if (array_key_exists('total', $ajustements))
                        { 
                            $points_obtenus = $ajustements['total'];
                        }
                    }

                    //
                    // Determiner le pointage
                    //

                    if ($s['points_evaluation'] > 0 && $ponderations[$label]['ponderation'] > 0)
                    {
                        //
                        // Calculer les points obtenus selon la ponderation
                        //

                        $p = $points_obtenus / $s['points_evaluation'] * $ponderations[$label]['ponderation'];


                        //
                        // Addition ces points au pointage actuel (le nouveau pointage)
                        //

                        $etu_resultats[$e_id]['p_obtenus'] += $p;
                        $etu_resultats[$e_id]['p_totaux']  += $ponderations[$label]['ponderation'];

                        //
                        // Recalculer le pourcentage en tenant compte du nouveau pointage
                        //

                        $etu_resultats[$e_id]['p_pourcentage'] = $etu_resultats[$e_id]['p_obtenus'] / $etu_resultats[$e_id]['p_totaux'] * 100;

                        //
                        // Enregistrer le pourcentage de l'etudiant dans un tableau pour manipulation ulterieure
                        //

                        $pourcentages[$e_id] = $etu_resultats[$e_id]['p_pourcentage'];
                    }

                    //
                    // Determiner le nombre de points maximum accumulable si un etudiant avait obtenu 100% a toutes ses evaluations de ce cours
                    // 
                    // Ceci permettra d'exclure les etudiants n'ayant pas completes un nombre suffisant d'evaluation pour faire parti 
                    // du classement. Par exemple, un etudiant ayant fait une seule evaluation et aurait obtenu 100% se retrouverait
                    // toujours premier au classement alors meme qu'il aurait abandonne le cours.
                    //

                    if ( ! array_key_exists($label, $points_accumules))
                    {
                        $points_accumules[$label] = 0;
                    }

                    $points_accumules[$label] = $ponderations[$label]['ponderation'];
                }

                //
                // Determiner le rang de l'etudiant pour ce cours
                //

                if ( ! empty($pourcentages))
                {
                    // Le pointage maximum ayant pu etre obtenu (accumulable)

                    $points_max = array_sum($points_accumules);

                    // Ordonner les pourcentages inversement

                    arsort($pourcentages);

                    //
                    // Determiner le classement de l'etudiant
                    //
                    // Ceci tient compte de pourcentage identique, et si c'etait le cas,
                    // le meme rang est attribue aux deux etudiants.
                    //

                    $i = 0;
                    $p_precedent = NULL; 

                    foreach($pourcentages as $e_id => $p)
                    {
                        //
                        // Il faut avoir complete au moins la moitie des points totaux accumulables
                        // pour etre considere comme un etudiant faisant parti du classement.
                        //

                        if ($etu_resultats[$e_id]['p_totaux'] < ($points_max / 2))
                        {
                            unset($pourcentages[$e_id]);
                            continue; 
                        }

                        $i++;

                        //
                        // On increment le rang actuel si la note precedente est differente de la note suivante.
                        //

                        if ($p_precedent == NULL || $p != $p_precedent)
                        {
                            $p_precente = $p;
                            $rang = $i;
                        }
                        
                        //
                        // On enregistre la note pour la prochaine iteration
                        //

                        $p_precedent = $p;

                        //
                        // Il ne s'agit pas de l'etudiant pour lequel on veut connaitre le rang.
                        //

                        if ($e_id != $etudiant_id)
                        {
                            continue;
                        }

                        //
                        // Le rang actuel correspond au rang de l'etudiant.
                        //

                        $rangs[$semestre_id][$cours_id]['rang'] = $rang;

                        break;
                    }

                    //
                    // Le nombre d'etudiants participant au cours (rang max)
                    //

                    if (array_key_exists($semestre_id, $rangs) &&
                        array_key_exists($cours_id, $rangs[$semestre_id]) &&
                        array_key_exists('rang', $rangs[$semestre_id][$cours_id]))
                    {
                        $rangs[$semestre_id][$cours_id]['rang_max'] = count($pourcentages);
                    }

                } // if ! empty $poucentages

            } // foreach cours
        } // foreach semestre

        $this->kcache->save($cache_key, $rangs, 'resultats', 60);

        return $rangs;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner les rangs de l'etudiant pour chacun de ses cours, tous les enseignants confondus
     *
     * --------------------------------------------------------------------------------------------
     *
     * Il faut considerer le semestre car un etudiant aurait pu faire le meme cours a deux 
     * semestres differents, dans le cas d'un echec la premiere fois par exemple.
     *
     * complet = tous les enseignants
     *
     * -------------------------------------------------------------------------------------------- */
    function determiner_rangs_cours_complet($etudiant_id)
    {
        $cache_key = strtolower(__CLASS__) . '_' . strtolower(__FUNCTION__) . '_' . $etudiant_id;

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        //
        // Extraire les soumissions de l'etudiant
		//
		// Ceci permettra de determiner les enseignants et les cours pertinents pour cet etudiant.
        //

        $this->db->select ('soumission_id, enseignant_id, etudiant_id, numero_da, evaluation_id, evaluation_reference, semestre_id, cours_id'
                           . ', points_obtenus, points_evaluation, ajustements_data');
        $this->db->from   ('soumissions');
        $this->db->where  ('corrections_terminees', 1);
        $this->db->where  ('etudiant_id', $etudiant_id);
        $this->db->where  ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        $soumissions = $query->result_array();

        //
        // A partir des soumissions de l'etudiant :
        //
        // - Determiner la relation semestres/cours
        //
        // - Determiner la relation cours/enseignants
        //   On veut faire un rang seulement pour le meme enseignant
        //   sinon ca pourrait etre injuste pour ceux ayant des enseignants plus severes.
        //
        //   @TODO Eventuellement, pour creer un classement inter-enseignant, on pourrait 
        //         tenir compte de la moyenne et de faire un ajustement a la hausse 
        //         (ou a la baisse) selon l'enseignant.
        //
        
        $semestres_cours = array(); // semestre_id => $cours_id => $enseignant_id

        foreach($soumissions as $s)
        {
            if ( ! array_key_exists($s['semestre_id'], $semestres_cours))
            {
                $semestres_cours[$s['semestre_id']] = array();
            }

            if ( ! in_array($s['cours_id'], $semestres_cours[$s['semestre_id']]))
            {
                $semestres_cours[$s['semestre_id']][$s['cours_id']] = array();
            }

            if ( ! in_array($s['enseignant_id'], $semestres_cours[$s['semestre_id']][$s['cours_id']]))
            {
                $semestres_cours[$s['semestre_id']][$s['cours_id']][] = $s['enseignant_id'];
            }
        }

        //
        // Extraire toutes les soumissions (de tous les etudiants) 
        // pour tous les enseignants, tous les semestres et tous les cours 
        // dont l'etudiant a envoye au moins une soumission
        //

        $soumissions_toutes = array();
        $eleves             = array();

        foreach($semestres_cours as $semestre_id => $cours)
        {
            $c_ids = array_keys($cours); 

            //
            // Extraire les eleves
            //

            $this->db->from     ('eleves');
            $this->db->where    ('semestre_id', $semestre_id);
            $this->db->where_in ('cours_id', $c_ids);
            $this->db->where    ('groupe_id', $this->groupe_id);
            $this->db->order_by ('eleve_id', 'asc');

            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                $eleves = array_merge($eleves, $query->result_array());
            }

            //
            // Extraire les soumissions
            //

            $this->db->select   ('soumission_id, enseignant_id, etudiant_id, numero_da, cours_id, evaluation_id, evaluation_reference, semestre_id, points_obtenus, points_evaluation, ajustements_data');
            $this->db->from     ('soumissions');
            $this->db->where    ('etudiant_id !=', NULL);
            $this->db->where    ('corrections_terminees', 1);
            $this->db->where    ('semestre_id', $semestre_id);
            $this->db->where_in ('cours_id', $c_ids);
            $this->db->where    ('efface', 0);
            
            $query = $this->db->get();
            
            if ( ! $query->num_rows() > 0)
            {
                $soumissions_semestres[$semestre_id] = array();
                continue;
            }

            $soumissions_toutes = array_merge($soumissions_toutes, $query->result_array());
        }

        //
        // Eleves - Creer un index pour les groupes/numero_da
        //

        $eleves_g = array();

        if ( ! empty($eleves))
        {
            foreach($eleves as $e)
            {
                // label3 : enseignant - semetre - cours
                $label3 = 'e' . $e['enseignant_id'] . 's' . $e['semestre_id'] . 'c' . $e['cours_id'];

                if ( ! array_key_exists($label3, $eleves_g))
                {
                    $eleves_g[$label3] = array();
                }

                if ( ! array_key_exists($e['cours_groupe'], $eleves_g[$label3]))
                {
                    $eleves_g[$label3][$e['cours_groupe']] = array();
                }

                $eleves_g[$label3][$e['cours_groupe']][] = $e['numero_da'];
            }
        }

        //
        // Extraire les ponderations
        //

        $ponderations = $this->Evaluation_model->extraire_ponderations_par_soumissions($soumissions_toutes);

        //
        // Compiler les notes des etudiants
        //

        $notes = array();
        $enseignants = array();

        foreach($soumissions_toutes as $s)
        {
            $label  = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];
            $label2 = 's' . $s['semestre_id'] . 'c' . $s['cours_id'];
			$label3 = 'e' . $s['enseignant_id'] . 's' . $s['semestre_id'] . 'c' . $s['cours_id']; 

            //
            // Exclure les soumissions provenant de previsualisation 
            // (generalement des tests des enseignants)
            //

            if (empty($s['evaluation_reference']))
                continue;

            //
            // Ne pas considerer les soumissions (evaluations) dont la ponderation est inconnue.
            //

            if ( ! array_key_exists($label, $ponderations))
                continue;
            
            //
            // Ne pas considerer les evaluations sont la ponderation est zero
            //
            
            if ( ! ($s['points_evaluation'] > 0 && $ponderations[$label]['ponderation'] > 0))
                continue;

            //
            // Les ajustements au pointage obtenu de la soumission
            //

            $points_obtenus = $s['points_obtenus'];

            if ( ! empty($s['ajustements_data']))
            {
                $ajustements = unserialize($s['ajustements_data']);

                if (array_key_exists('total', $ajustements))
                { 
                    $points_obtenus = $ajustements['total'];
                }
            }

            //
            // Initialiser les tableaux
            //

            if ( ! array_key_exists($s['etudiant_id'], $notes))
            {
                $notes[$s['etudiant_id']] = array();
            }

            //
            // Determiner le groupe
            //
           
			$groupe = array_search_recursive($s['numero_da'], $eleves_g[$label3])[0] ?? 9999;

            if ( ! array_key_exists($label2, $notes[$s['etudiant_id']]))
            {
                $notes[$s['etudiant_id']][$label2] = array(
                    'numero_da'      => $s['numero_da'],
                    'semestre_id'    => $s['semestre_id'],
                    'cours_id'       => $s['cours_id'],
                    'groupe'         => array_search_recursive($s['numero_da'], $eleves_g[$label3])[0] ?? 9999,
					'enseignant_id'  => $s['enseignant_id'],
                    'points_obtenus' => 0, // points ponderes obtenus
                    'points_totaux'  => 0, // points ponderes totaux
                    'pourcentage'    => 0  // pourcentage
                );
            }

            if ( ! array_key_exists($s['enseignant_id'], $enseignants))
            {
                $enseignants[$s['enseignant_id']] = array();
            }

            if ( ! array_key_exists($label2, $enseignants[$s['enseignant_id']]))
            {
                $enseignants[$s['enseignant_id']][$label2] = array(
                    'semestre_id'    	 => $s['semestre_id'],
                    'cours_id'       	 => $s['cours_id'],
                    'notes'          	 => array(), // etudiant_id => note (pourcentage)
                    'evaluations'    	 => array(),
					'evaluations_points' => array(),
					'points_obtenus' 	 => 0,
					'points_totaux'	 	 => 0,
                    'points_max'     	 => array(), // par groupes
                    'std_dev'        	 => 0,
                    'moy'            	 => 0
                );
            }

            $n  = &$notes[$s['etudiant_id']][$label2];
            $en = &$enseignants[$s['enseignant_id']][$label2];

			$groupe = $n['groupe'];

            //
            // Calculer les points obtenus selon la ponderation
            //

            $p = ($points_obtenus / $s['points_evaluation']) * $ponderations[$label]['ponderation'];

            $n['points_obtenus'] += $p;
            $n['points_totaux']  += $ponderations[$label]['ponderation'];
            $n['pourcentage']     = $n['points_obtenus'] / $n['points_totaux'] * 100;

            $en['notes'][$s['etudiant_id']] = $n['pourcentage'];

			if ( ! array_key_exists($groupe, $en['points_max']))
			{
				$en['evaluations_points'][$groupe] = array();
				$en['evaluations'][$groupe] = array();
				$en['points_max'][$groupe] = 0;
			}

			// La meme evaluation peut avoir ete demandee pour deux groupes differentes.

			if ( ! in_array($s['evaluation_id'], $en['evaluations'][$groupe]))
			{
				$en['evaluations'][$groupe][] = $s['evaluation_id'];
				$en['points_max'][$groupe]    += $ponderations[$label]['ponderation'];

				$en['evaluations_points'][$groupe][$s['evaluation_id']] = $ponderations[$label]['ponderation'];
			}

            if ( ! array_key_exists($groupe, $en['points_max']))
            {
                $en['points_max'][$groupe] = 0;
            }

			$en['points_obtenus'] += $p;
			$en['points_totaux']  += $ponderations[$label]['ponderation'];

			$en['moy'] = $en['points_obtenus'] / $en['points_totaux'] * 100;	

        }

		//
        // Exclure les etudiants qui n'ont pas complete assez d'evaluations (en points)
        // Il faut avoir completer au moins la moitie des points.
        //

        foreach($notes as $etu_id => $cours)
        {
            foreach($cours as $label2 => $e)
            {
                $points_max = $enseignants[$e['enseignant_id']][$label2]['points_max'][$e['groupe']];

                if ($e['groupe'] == 9999 || $points_max == 0 || ($e['points_totaux'] < ($points_max * 0.25)))
                {
                    unset($notes[$etu_id][$label2]);
                    unset($enseignants[$e['enseignant_id']][$label2]['notes'][$etu_id]);
                }
            }
        }

        //
        // Calculer la cote Z
        //

		$cote_z_cours = array();
		$cote_z_cours_complet = array();

		foreach($notes as $etu_id => $cours)
		{		
			foreach($cours as $label2 => $e)
			{
				$en_id = $e['enseignant_id'];
				$en    = &$enseignants[$en_id][$label2];

				//
				// Calculer la deviation standard pour l'enseignant si ce n'est pas deja fait
				//

				if (empty($en['std_dev']))
				{
					$en['std_dev'] = std_dev($en['notes']);
				}

				//
				// Calculer la cote Z
				//

                if ( ! empty($en['std_dev']))
                {
                    $cote_z = ($e['pourcentage'] - $en['moy']) / ($en['std_dev']);

                    if (in_array($e['enseignant_id'], $semestres_cours[$e['semestre_id']][$e['cours_id']]))
                    {
                        $cote_z_cours[$e['semestre_id']][$e['cours_id']][$etu_id] = $cote_z;
                    }

                    $cote_z_cours_complet[$e['semestre_id']][$e['cours_id']][$etu_id] = $cote_z;
                }
			}
		}

        //
        // Les rangs de l'etudiant
        //

        $rangs = array();

		foreach($semestres_cours as $semestre_id => $cours)
		{
			foreach($cours as $cours_id => $c)
			{
				//
				// Cours	
				//

				if (array_key_exists($semestre_id, $cote_z_cours) && array_key_exists($cours_id, $cote_z_cours[$semestre_id]))
				{
					$zz = $cote_z_cours[$semestre_id][$cours_id];
					arsort($zz);

					$i = 0;
					$rang = 0;
					$z_precedent = NULL;

					foreach($zz as $etu_id => $z)
					{
						$i++;

						//
						// On increment le rang actuel si la note precedente est differente de la note suivante.
						//

						if ($z_precedent == NULL || $z != $z_precedent)
						{
							$z_precente = $z;
							$rang = $i;
						}
						
						//
						// On enregistre la note pour la prochaine iteration
						//

						$z_precedent = $z;

						//
						// Il ne s'agit pas de l'etudiant pour lequel on veut connaitre le rang.
						//

						if ($etu_id != $etudiant_id)
						{
							continue;
						}

						$rangs[$semestre_id][$cours_id]['rang'] = $rang;
						$rangs[$semestre_id][$cours_id]['rang_max'] = count($zz);

						break;
					} // rang cours
				} // cours

				//
				// Cours COMPLET
				//

				if (array_key_exists($semestre_id, $cote_z_cours_complet) && array_key_exists($cours_id, $cote_z_cours_complet[$semestre_id]))
				{
					$zz = $cote_z_cours_complet[$semestre_id][$cours_id];
					arsort($zz);

					$i = 0;
					$rang = 0;
					$z_precedent = NULL;

					foreach($zz as $etu_id => $z)
					{
						$i++;

						//
						// On increment le rang actuel si la note precedente est differente de la note suivante.
						//

						if ($z_precedent == NULL || $z != $z_precedent)
						{
							$z_precente = $z;
							$rang = $i;
						}
						
						//
						// On enregistre la note pour la prochaine iteration
						//

						$z_precedent = $z;

						//
						// Il ne s'agit pas de l'etudiant pour lequel on veut connaitre le rang.
						//

						if ($etu_id != $etudiant_id)
						{
							continue;
						}

						$rangs[$semestre_id][$cours_id]['rang_complet'] = $rang;
						$rangs[$semestre_id][$cours_id]['rang_complet_max'] = count($zz);

						break;
					}
				} // cours_complet
			}
		}

        $this->kcache->save($cache_key, $rangs, 'resultats', 60);

        return $rangs;

    } // rang_cours_complet2

    /* --------------------------------------------------------------------------------------------
     *
     * Derniere connexion
     *
     * -------------------------------------------------------------------------------------------- */
    function derniere_connexion($etudiant_id)
    {
        $this->db->from     ('activite');
        $this->db->where    ('etudiant_id', $etudiant_id);
        $this->db->order_by ('epoch', 'desc');
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;
                                                                                                                                                                                                                                  
        return $query->row_array();
    } 

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire etudiant a partir de son eleve_id
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction sert a extraire etudiant_id a partir de eleve_id, base sur le matricule.
     *
     * Mais comme le matricule est entre par un eleve, il faut aussi verifier la similarite avec son
     * nom au cas ou un autre eleve aurait entre le meme matricule par megarde.
     *
     * Dans le cas ou l'etudiant a plusieurs comptes avec des noms identiques, il est assume que le
     * compte principal de l'etudiant est le dernier utilise (selon le champ derniere_activite_epoch).
     *
     * @date 2024-09-07
     * @update 2024-11-22
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_etudiant_de_eleve($eleve_id)
    {
        $this->db->from     ('eleves');
        $this->db->where    ('eleve_id', $eleve_id);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        $row = $query->row_array();

        $matricule = $row['numero_da'];

        //
        // On ne peut pas chercher un matricule vide.
        //

        if (empty($matricule))
        {
            return array();
        }

        $eleve_nom    = $row['eleve_nom'];
        $eleve_prenom = $row['eleve_prenom'];

        //
        // Trouver tous les etudiants correspondant a ce matricule.
        // Il est suppose en avoir qu'un seul mais...
        //

        $this->db->select   ('e.etudiant_id, e.nom, e.prenom, e.activite_compteur, e.derniere_activite_epoch');
        $this->db->from     ('etudiants_numero_da as em, etudiants as e');
        $this->db->where    ('em.numero_da', $matricule);
        $this->db->where    ('em.etudiant_id = e.etudiant_id');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $results = $query->result_array();

        $etudiants = array();

        foreach ($results as $row)
        {
            //
            // Il n'y a qu'un choix, alors assumons qu'il correspond bien a l'etudiant.
            //

            if (count($results) == 1)
            {
                return $row;
            }

            //
            // S'il n'y a aucune activite au compteur, ca veut dire que l'etudiant ne s'est jamais connecte.
            //

            $derniere_activite = $row['derniere_activite_epoch'];

            if (empty($derniere_activite))
                continue;

            // La somme des similarites du nom et du prenom, en %, doit etre superieure a 160.
            // Parfois les etudiants n'ecrient pas leur prenom au complet.

            $sim_nom = similar_text($eleve_nom, $row['nom'], $sim_nom_p);
            $sim_prenom = similar_text($eleve_prenom, $row['prenom'], $sim_prenom_p);

            if (($sim_nom_p + $sim_prenom_p) > 160)
            {
                $etudiants[$derniere_activite] = $row;
                continue;
            } 
        }

        if (empty($etudiants))
        {
            // Ceci cause un probleme car l'eleve n'a pu etre associe a aucun etudiant.
            return array();
        }

        if (count($etudiants) > 1)
        {
            // Le compte qui a le plus d'activite au compteur devrait etre le compte principal de l'etudiant.

            krsort($etudiants);

            return array_shift($etudiants);
        }
        else
        {
            return array_shift($etudiants);
        }

        return array();
    }
}
