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
 * EVALUATION MODEL
 *
 * ============================================================================ */

use jlawrence\eos\Parser;

class Evaluation_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* ------------------------------------------------------------------------
     *
     * Permission de charger une evaluation
     *
     * ------------------------------------------------------------------------
     *
     * Cette function verifie les demandes de chargement abusives.
     *
     * ------------------------------------------------------------------------ */
    function permission_charger_evaluation($evaluation_id, $evaluation_reference = NULL)
    {
        //
        // Verifier l'existence des parametres requis.
        // Ces parametres sont necessaires pour statuer sur la permission.
        //
        
        $parametres = array(
            'evaluations_chargement_prevention',
            'evaluations_chargement_max',
            'evaluations_chargement_periode',
            'evaluations_chargement_periode_blocage',
            'evaluations_chargement_whitelist'
        );

        foreach($parametres as $p)
        {
            if ( ! (${$p} = $this->config->item($p)))
            {
                return TRUE;
            }
        }

        // 
        // Est-ce que la prevention de chargement abusif est activee?
        //

        if ( ! $evaluations_chargement_prevention)
        {
            // Elle n'est pas activee.
            return TRUE;
        }

        //
        // Est-ce que l'adresse IP de l'usager est dans une liste blanche ?
        // 

        if ($evaluations_chargement_whitelist)
        {
            //
            // La liste blanche (whitelist) du site
            // 

            if (in_array($_SERVER['REMOTE_ADDR'], $this->config->item('ips_whitelist')))
            {
                // L'adresse IP est dans la liste blanche (whitelist) du site.
                return TRUE;
            }

            //
            // La liste blanche (whitelist) de l'ecole
            //
            // Les ecoles ont plusieurs postes publics ou les etudiants peuvent acceder les evaluations.
            // Il est difficile de differencier entre ces postes donc tous les chargements sont autorises
            // lorsqu'ils proviennent de ces postes.
            // 

            if ($this->groupe_id != 0)
            {
                $this->db->from  ('ecoles as e, ecoles_ips as ei');
                $this->db->where ('e.ecole_id = ei.ecole_id');
                $this->db->where ('ei.ecole_id', $this->ecole_id);
                $this->db->where ('e.actif', 1);
                $this->db->where ('e.efface', 0);
                
                $query = $this->db->get();
                
                if ($query->num_rows() > 0)
                {
                    $resultats = $query->result_array();

                    foreach($resultats as $r)
                    {
                        if ($r['adresse_ip'] == $_SERVER['REMOTE_ADDR'])
                        {
                            // L'adresse IP est dans la liste blanche de l'ecole.
                            return TRUE;
                        }
                    }
                }

            } // groupe_id != 0
        }

        //
        // Generer un identifiant unique (UniqueID).
        //

        $unique_id = $this->Admin_model->generer_unique_id();

        //
        // Verifier que cet UniqueID n'est pas bloque.
        //

        $this->db->from  ('evaluations_securite_blocages');
        $this->db->where ('unique_id', $unique_id);
		$this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('blocage_expiration_epoch >', $this->now_epoch);
        $this->db->limit (1);
        
        $query = $this->db->get();

        if ($query->num_rows())
        {
            $row = $query->row_array();

            return round(($row['blocage_expiration_epoch'] - $this->now_epoch) / 60);
        } 

        //
        // Verifier que le chargement max n'est pas excede.
        //

        $this->db->from  ('evaluations_securite_chargements');
        $this->db->where ('unique_id', $unique_id);
        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('expiration_epoch >', $this->now_epoch);
        
        $query = $this->db->get();

        if ($query->num_rows() > ($evaluations_chargement_max - 1))
        {
            //
            // Bannir le UniqueID
            //

            $blocage_expiration_epoch = $this->now_epoch + ($evaluations_chargement_periode_blocage * 60);

            $data = array(
                'unique_id'                => $unique_id,
				'evaluation_id'            => $evaluation_id,
				'evaluation_reference'	   => $evaluation_reference,
                'blocage_expiration_epoch' => $blocage_expiration_epoch,
                'blocage_expiration_date'  => date_humanize($blocage_expiration_epoch, TRUE),
                'adresse_ip'               => $_SERVER['REMOTE_ADDR']
            );

            $this->db->insert('evaluations_securite_blocages', $data);

			log_alerte(
				array(
					'code'       => 'EVV7997',
                    'desc'       => "Un étudiant a excédé le nombre de tentatives maximum de chargement d'une évaluation.",
                    'extra'      => 'unique_id = ' . $unique_id . ', evaluation_id = ' . $evaluation_id,
                    'importance' => 5
				)
            );

            return $evaluations_chargement_periode_blocage;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Enregistrer la demande de chargement d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Cette fonction tente d'eviter les chargements abusifs des evaluations, en enregistrant
     * chaque demande avec l'identifiant unique du demandeur.
     *
     * -------------------------------------------------------------------------------------------- */
    function demande_chargement_evaluation($evaluation_id, $evaluation_reference = NULL)
    {
        //
        // Verifier l'existence des parametres requis.
        //
        
        $parametres = array(
            'evaluations_chargement_prevention',
            'evaluations_chargement_periode',
            'evaluations_chargement_periode_blocage',
            'evaluations_chargement_whitelist'
        );

        //
        // Creer des variables a partir des items de la configuration,
        // pour faciliter leur utilisation dans le code ci-bas.
        //

        foreach($parametres as $p)
        {
            ${$p} = $this->config->item($p);
        }

        //
        // Ne pas enregistrer les chargements pour le site de developpement
        // (Ceci cause des desagrements lors du testing)
        //

        if ($this->is_DEV)
        {
            return;
        }

        //
        // Ne pas enregistrer les chargements de la liste blanche.
        //

        if ($evaluations_chargement_whitelist)
        {
            //
            // La liste blanche (whitelist) du site
            // 

            if (in_array($_SERVER['REMOTE_ADDR'], $this->config->item('ips_whitelist')))
            {
                // L'adresse IP est dans la liste blanche (whitelist) du site.
                return;
            }

            //
            // La liste blanche (whitelist) de l'ecole
            //
            // Les ecoles ont plusieurs postes publics ou les etudiants peuvent acceder les evaluations.
            // Il est difficile de differencier entre ces postes donc tous les chargements sont autorises
            // lorsqu'ils proviennent de ces postes.
            // 

            // Enregistrer ces chargements meme s'ils ne sont pas controller avec la function
            // 'permission_charger_evaluation'.
        }

        //
        // Generer un identifiant unique (UniqueID).
        //

        $unique_id = $this->Admin_model->generer_unique_id();

        //
        // Determiner la periode d'effectivite de ce chargement.
        //

        $expiration_epoch = $this->now_epoch + ($evaluations_chargement_periode * 60);

        //
        // Determiner l'identite de l'etudiant persumee
        //

        $identite_str = NULL;

        if (( ! $this->est_etudiant) && ($identite = $this->Admin_model->determiner_identite('array')) !== NULL)
        {
            if (array_key_exists('prenom_nom', $identite) && ! empty($identite['prenom_nom']))
            {
                $identite_str = $identite['prenom_nom']; 
            }
        }

        $data = array(
            'unique_id'            => $unique_id,
            'session_id'           => session_id(),
            'evaluation_id'        => $evaluation_id,
            'evaluation_reference' => $evaluation_reference,
            'etudiant_id'          => $this->est_etudiant ? $this->etudiant_id : NULL,
            'identite_presumee'    => $identite_str,
            'epoch'                => $this->now_epoch,
            'date'                 => date_humanize($this->now_epoch, TRUE),
            'expiration_epoch'     => $expiration_epoch,
            'expiration_date'      => date_humanize($expiration_epoch, TRUE),
            'adresse_ip'           => $_SERVER['REMOTE_ADDR']
        );

        $this->db->insert('evaluations_securite_chargements', $data);

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier le chargement de l'evaluation
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Ceci verifie que la demande de chargement enregistree correspond a la session_id de
     * l'etudiant qui charge les questions partir de sa session. Si ce n'est pas le cas, il 
     * faut mettre a jour ses informations de chargement.
     *
     * 2020-04-18
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_chargement_evaluation($evaluation_id, $evaluation_reference, $min_epoch)
    {
        $this->db->from  ('evaluations_securite_chargements');
        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('session_id', session_id());
        $this->db->where ('epoch >', $min_epoch); 
        $this->db->order_by ('epoch', 'desc'); // la plus recente
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return;
        }

        // Introuvable !
        // Essayons de chercher l'enregistrement avec le unique_id.
        //
        // Attention, ceci pourrait ne pas fonctionner si l'etudiant a ouvert
        // son evluation partir d'un poste de travail du college, car ceux-ci
        // ont souvent le meme unique_id.
        
        $this->db->from  ('evaluations_securite_chargements');
        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('unique_id', $this->Admin_model->generer_unique_id());
        $this->db->where ('epoch >', $min_epoch); 
        $this->db->order_by ('epoch', 'desc');
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            // Introuvable, il n'y a rien a faire pour detecter cet etudiant.
            return;
        }

        // Trouve !
        // Il faut mettre a jour l'enregistrement avec le nouveau session_id.

        $chargement = $query->row_array();

        $data = array(
            'session_id' => session_id()
        );

        $this->db->where ('id', $chargement['id']);
        $this->db->update('evaluations_securite_chargements', $data);

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier l'association numero DA et enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_numero_da($enseignant_id, $numero_da)
    {
        // Verifier que l'enseignant a entre ses listes d'étudiants, 
        // et extraire les numeros DA pertinents

        $this->db->select   ('numero_da');
        $this->db->from     ('eleves');
        $this->db->where    ('enseignant_id', $enseignant_id);
        $this->db->where    ('semestre_id', $this->semestre_id);

        // $this->db->where    ('LOWER(numero_da)', strtolower($numero_da));
        // $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            // L'enseignant n'a pas entre ses listes d'etudiants pour le semestre demande,
            // donc ne pas verifier le numero_da.
            return TRUE;
        }

        $results = $query->result_array();

        foreach($results as $row)
        {
            if (strtolower($row['numero_da']) == strtolower($numero_da))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Permissions accordees pour effectuer des changements sur une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function permissions_evaluation($evaluation_id, $evaluation = NULL)
    {
        // Note :
        // Si les donnees de l'evaluation ont deja ete extraites, il faut simplement
        // les passer en arguement dans $evaluation.

        //
        // Les permissions
        //

        $permissions = array(
            'lire',
            'ajouter_question',
            'importer_question',
            'modifier', // modifier titre, activer/desactiver, changer l'ordre, ajouter/modifier instructions, ajouter/modifier variables et blocs
            'changer_responsable',
            'effacer'
        );

        //
        // Les permissions accordees par default (aucune)
        //

        $permissions_accordees = array();

        //
        // Extraire l'evaluation
        //

        if ( ! 
             ( ! empty($evaluation) && array($evaluation) && array_key_exists('enseignant_id', $evaluation) && array_key_exists('cadenas', $evaluation))
           )
        {
            $this->db->from  ('evaluations as e');
            $this->db->where ('e.evaluation_id', $evaluation_id);
            $this->db->where ('e.groupe_id', $this->enseignant['groupe_id']);
            $this->db->where ('e.efface', 0);
            $this->db->limit (1);

            $query = $this->db->get();

            if ( ! $query->num_rows())
            {
                return array();
            }

            $evaluation = $query->row_array();
        }

        foreach($permissions as $permission)
        {
            //
            // Le responsable de l'evaluation peut tout faire.
            //

            if ($evaluation['enseignant_id'] == $this->enseignant['enseignant_id'])
            {
                $permissions_accordees[] = $permission;
                continue;
            }

            //
            // Les permissions speciales pour l'admin.
            //
            
            if (permis('admin'))
            {
                $permissions_accordees[] = $permission;
                continue;
            }

            //
            // Si ce n'est pas une evaluation du departement, 
            // ne donner aucune permission sauf a l'enseignant responsable.
            //

            if ( ! $evaluation['public'])
            {
                continue;
            }

            //
            // Les evaluations du departement
            //
 
            if (permis('editeur'))
            {
                // Toutes les permissions
                $permissions_accordees[] = $permission;
                continue;
            }
            
            switch($permission)
            {
                case 'lire' :

                    // Tout le monde peut lire l'evaluation
                    $permissions_accordees[] = $permission;
                    break;

                case 'ajouter_question' :
                case 'importer_question' :

                    if ( ! $evaluation['cadenas'])
                    {
                        $permissions_accordees[] = $permission;
                    }
                    break;
            }
        }

        return $permissions_accordees;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Creer une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function creer_evaluation($post_data)
    {
        if (empty($post_data['evaluation_cours_id']) || empty($post_data['evaluation_titre']))
        {
            return FALSE;
        }

        $data = array(
            'groupe_id'        => $this->groupe_id,
            'cours_id'         => $post_data['evaluation_cours_id'],
            'enseignant_id'    => $this->enseignant_id,
            'lab'              => 0,
            'evaluation_titre' => htmlentities(mb_strimwidth(strip_tags($post_data['evaluation_titre']), 0, 153, '...')),
            'public'           => $this->groupe_id == 0 ? 0 : (array_key_exists('public', $post_data) ? $post_data['public'] : 1),
            'actif'            => 0, // Une evaluation debute en mode desactive.
            'ajout_date'       => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'      => $this->now_epoch
        );

        //
        // Laboratoire
        //

        if ($post_data['est_laboratoire'])
        {
            $data['lab']            = 1;
            $data['lab_prefix']	    = substr(bin2hex(random_bytes(4)), 0, 7);
            $data['lab_parametres'] = json_encode($this->config->item('lab_parametres_initiaux'));
        }

        $this->db->insert($this->evaluations_t, $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Importer, Exporter, Copier (version 2)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction a quatre capacites :
     *
     * 1) Importer une evaluation du departement vers 'Mes evaluations'.
     * 2) Exporter mon evaluation vers les 'Evaluations du departement'.
     * 3) Copier une evaluation vers un autre cours.
     *    ATTN : Une evaluation conserve son status public ou prive suite au copiage.
     *           De plus, cours_id_cible doit etre specifie.
     * 4) Dupliquer une evaluation (copier vers le meme cours) (depuis 2020/03/20)
     *
     * Parametres
     * ----------
     *
     * evaluation_id : l'evaluation d'origine
     * cours_id      : le cours cible si on copie l'evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function importer_exporter_copier_evaluation($evaluation_id, $cours_id_cible = NULL)
    {
        //
        // Cours cible
        //
        
        // Si on importe ou exporte une evaluation, le cours_id_cible est le meme que celui
		// de l'evaluation.
		// Si on copie une evaluation, alors ce cours_id_cible est different et doit etre specifie.

        // Si le cours_id_cible est specifie, 
		// il faut verifier que le cours_id appartient au groupe ou a l'enseignant s'il s'agit de son groupe personnel.

		$groupes = $this->Groupe_model->lister_groupes2();

        if ( ! empty($cours_id_cible) && is_numeric($cours_id_cible))
        {
            $cours = $this->Cours_model->extraire_cours(array('cours_id' => $cours_id_cible));

            if (empty($cours) || $cours == FALSE)
            {
				return array(
					'code'     => '',
					'message'  => "Le cours spécifié n'a pu être trouvé.",
					'solution' => "Veuillez ajouter ou activer une question."
				);
            }

            // En attenant que 'extraire_cours' prenne en charge automatiquement cette verifircation :

			if ($cours['groupe_id'] == 0)
			{
				if ($cours['enseignant_id'] != $this->enseignant_id)
				{
					echo "Le cours cible ne vous appartient pas.";
					return FALSE;
				}
			}
			else
			{
				if ( ! array_key_exists($cours['groupe_id'], $groupes))
				{
					echo "Le cours specifie n'appartient pas au groupe de l'enseignant.";
					return FALSE;
				}
			}
        } 

        //
        // Evaluation
        //

        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
        {
            // L'evaluation est introuvable.

            echo "L'evaluation est introuvable.";
            return FALSE;
        }

        //
        // Verifier la permission d'utiliser cette fonction
        //

		if ($evaluation['groupe_id'] == 0)
		{
			if ($evaluation['enseignant_id'] != $this->enseignant_id)
			{
				echo "Vous n'avez pas la permission d'importer, d'exporter ou de copier cette evaluation
					  car elle ne vous appartient pas.";
				return FALSE;
			}
		}
		else
		{
			if ( ! array_key_exists($evaluation['groupe_id'], $groupes))
			{
				echo "Vous n'avez pas la permission d'importer, d'exporter ou de copier cette evaluation
					  car elle n'appartient pas a votre groupe.";
				return FALSE;
			}

			if ($evaluation['public'] == 0)
			{
				if (($evaluation['enseignant_id'] != $this->enseignant_id) && $this->enseignant['privilege'] < 90)
				{
					echo "Vous n'avez pas la permission d'exporter ou de copier une evaluation privee qui ne vous appartient pas.";
					return FALSE;
				}
			}
		}

        //
        // Questions
        //

        $questions    = $this->Question_model->lister_questions($evaluation_id);
        $question_ids = array_keys($questions);

        //
        // Documents
        //

        $images = $this->Document_model->extraire_images($question_ids);

        //
        // Reponses
        //

        $reponses = array();

        if ( ! empty($question_ids))
        {
            $this->db->from     ('reponses as r');
            $this->db->where_in ('r.question_id', $question_ids);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $reponses = array_keys_swap($query->result_array(), 'reponse_id');
            }
        }

        //
        // Tolerances
        //

        $tolerances = array();

        if ( ! empty($question_ids))
        {
            foreach($question_ids as $question_id)
            {
                $tol = $this->Question_model->extraire_tolerances($question_id);

                if ( ! empty($tol))
                {
                    $tolerances[$question_id] = $tol;
                }
            }
        }

        //
        // Similarites
        //

        $similarites = array();

        if ( ! empty($question_ids))
        {
            foreach($question_ids as $question_id)
            {
                if ($questions[$question_id]['question_type'] != 7)
                    continue;

                $sim = $this->Question_model->extraire_similarite($question_id);

                if ( ! empty($sim))
                {
                    $similarites[$question_id] = $sim;
                }
            }
        }

        //
        // Variables
        //

        $variables = $this->extraire_variables($evaluation_id);

        //
        // Blocs
        //
        
        $blocs = $this->Question_model->extraire_blocs($evaluation_id);

        //
        // Grilles de corrections
        //

        $grilles = $this->Question_model->extraire_grilles_correction_par_evaluation_id($evaluation_id);

        // ---------------------------------------------------------------------
        //
        // Proceder au copiage, a l'importation ou l'exportation de l'evaluation
        //
        // ---------------------------------------------------------------------

        $this->db->trans_begin();

        $data = array();

        $data = $evaluation;

        unset($data['evaluation_id']);

        $data['actif']         = 0; // Les evaluations importees ou exportees debutent desativees
        $data['cadenas']       = 0; // Les evaluations importees ou exportees debutent debarrees.
        $data['ajout_date']    = date_humanize($this->now_epoch, TRUE);
        $data['ajout_epoch']   = $this->now_epoch;
        $data['enseignant_id'] = $this->enseignant_id;
        $data['lab_prefix']    = substr(bin2hex(random_bytes(4)), 0, 7);

        //
        // Copiage
        //

        if ($cours_id_cible != NULL)
        {
            $data['public']    = $evaluation['public'];
            $data['cours_id']  = $cours['cours_id'];  // $cours provient de $cours_id_cible
			$data['groupe_id'] = $cours['groupe_id'];
        }

        //
        // Importation / Exportation
        //

        else
        {
            if ($evaluation['public'])
            {
                // Importation

                $data['public']     = 0;
                $data['formative']  = 0;
                // $data['inscription_requise'] = 0; // depuis 2023-01-14
            }
            else
            {
                // Exportation

                $data['public']     = 1;
                $data['formative']  = 0;
                // $data['inscription_requise'] = 0;  // depuis 2023-01-14
            }
        }

        //
        // Enlever les champs inexistants
        //

        $champs = $this->db->list_fields('evaluations');

        foreach($data as $k => $val)
        {
            if ( ! in_array($k, $champs))
            {
                unset($data[$k]);
            }
        }

        $this->db->insert('evaluations', $data);

        $n_evaluation_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            
            echo "Une erreur lors de la copie de l'evaluation provenant de la base de donnees a ete signalee.";
            return FALSE;
        }

        //
        // Copier les blocs
        //

        $ref_bloc_ids = array(); // index: vieux bloc_id, valeur: nouveau bloc_id

        $champs = $this->db->list_fields('blocs');

        if ( ! empty($blocs))
        {
            foreach($blocs as $bloc_id => $b)
            {
                $data = $b;

                unset($data['bloc_id']);

                $data['evaluation_id'] = $n_evaluation_id;

                // Enlever les champs inexistants
                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                    {
                        unset($data[$k]);
                    }
                }

                $this->db->insert('blocs', $data);

                $ref_bloc_ids[$bloc_id] = $this->db->insert_id();

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des blocs provenant de la base de donnees ete signalee.";
                    return FALSE;
                }
            }
        }

        //
        // Copier les questions
        // 

        // References entre les question_ids avant et apres l'insertion.
        $ref_question_ids = array();

        $champs = $this->db->list_fields('questions');

        if ( ! empty($question_ids))
        {
            foreach($question_ids as $question_id)
            {
                $data = array();
                $data = $questions[$question_id];

                unset($data['question_id']);

                if ( ! empty($data['bloc_id']))
                {
                    // Assigner le nouveau bloc_id correspondant a l'ancien.
                    $data['bloc_id'] = $ref_bloc_ids[$data['bloc_id']];
                }

                $data['evaluation_id'] = $n_evaluation_id;
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;
                $data['ajout_date'] = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch'] = $this->now_epoch;

                foreach($data as $k => $val)
                {
                    // Enlever les champs inexistants
                    if ( ! in_array($k, $champs))
                    {
                        unset($data[$k]);
                    }
                }

                $this->db->insert('questions', $data);

                $ref_question_ids[$question_id] = $this->db->insert_id();

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des questions provenant de la base de donnees ete signalee.";
                    return FALSE;
                }
            }
        }

        //
        // Copier les variables
        //

        if ( ! empty($variables))
        {
            foreach($variables as $v)
            {
                $v['evaluation_id'] = $n_evaluation_id;

                unset($v['variable_id']);

                $this->db->insert('variables', $v);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des variables provenant de la base de donnees ete signalee.";
                    return FALSE;
                }
            }
        }

        //
        // Copier les images
        //

        $images_copiees_doc_ids = array();

        $champs = $this->db->list_fields('documents');

        if ( ! empty($images))
        {
            foreach($images as $question_id => $i)
            {
                $data = array();
                $data = $i;

                unset($data['doc_id']);

                $data['question_id'] = $ref_question_ids[$question_id];
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;
                $data['ajout_date'] = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch'] = $this->now_epoch;

                // Enlever les champs inexistants
                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                    {
                        unset($data[$k]);
                    }
                }

                $this->db->insert('documents', $data);

                $images_copiees_doc_ids[] = $this->db->insert_id();

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des documents (images) provenant de la base de donnees ete signalee.";
                    return FALSE;
                }
            }
        } 

        //
        // Copier les reponses
        //

        if ( ! empty($reponses))
        {
            $champs = $this->db->list_fields('reponses');

            foreach($reponses as $reponse_id => $r)
            {
                $data = array();
                $data = $r;

                $question_id = $r['question_id'];

                unset($data['reponse_id']);

                $data['question_id']             = $ref_question_ids[$question_id];
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;
                $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch']             = $this->now_epoch;

                // Enlever les champs inexistants
                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                    {
                        unset($data[$k]);
                    }
                }

                $this->db->insert('reponses', $data);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des reponses provenant de la base de donnees ete signalee.";
                    return FALSE;
                }
            }
        }

        //
        // Copier les tolerances
        //

        if ( ! empty($tolerances))
        {
            $champs = $this->db->list_fields('questions_tolerances');

            foreach($tolerances as $question_id => $tol)
            {
                foreach($tol as $t)
                {
                    $data = array();
                    $data = $t;

                    unset($data['tolerance_id']);

                    $data['question_id'] = $ref_question_ids[$question_id];

                    foreach($data as $k => $val)
                    {
                        if ( ! in_array($k, $champs))
                        {
                            unset($data[$k]);
                        }
                    }

                    $this->db->insert('questions_tolerances', $data);

                    if ( ! $this->db->affected_rows())
                    {
                        $this->db->trans_rollback();

                        echo "Une erreur lors de la copie des tolérances s'est produite.";
                        return FALSE;
                    }
                }
            }
        }

        //
        // Copier les similarites
        //

        if ( ! empty($similarites))
        {
            $champs = $this->db->list_fields('questions_similarites');

            foreach($similarites as $question_id => $sim)
            {
                $data = array();
                $data = $sim;

                if (array_key_exists('similarite_id', $data))
                {
                    unset($data['similarite_id']);
                }

                $data['question_id'] = $ref_question_ids[$question_id];

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                    {
                        unset($data[$k]);
                    }
                }

                $this->db->insert('questions_similarites', $data);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    echo "Une erreur lors de la copie des similarités s'est produite.";
                    return FALSE;
                }
            }
        }

        //
        // Copier les grilles de corrections
        //

        if ( ! empty($grilles) && is_array($grilles))
        {
            foreach($grilles as $question_id => $gc)
            {
                // Copier la grille

                $data = array(
                    'evaluation_id'     => $n_evaluation_id,
                    'question_id'       => $ref_question_ids[$question_id],
                    'grille_affichage'  => $gc['grille_affichage'],
                    'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                    'ajout_epoch'       => $this->now_epoch
                );

                $this->db->insert('questions_grilles_correction', $data);

                if ( ! $this->db->affected_rows())
                {
                    log_alerte(
                        array(
                            'code'  => 'CE9192',
                            'desc'  => "Il n'a pas été possible de copier la grille de correction dans l'evaluation cible.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    $this->db->trans_rollback();
                    return FALSE;
                }

                $n_grille_id = $this->db->insert_id();

                // Copier les elements

                if (array_key_exists('elements', $gc) && ! empty($gc['elements']))
                {
                    $data = array();

                    foreach($gc['elements'] as $e)
                    {
                        $data[] = array(
                            'question_id'       => $ref_question_ids[$question_id],
                            'grille_id'         => $n_grille_id,
                            'element_type'      => $e['element_type'],
                            'element_desc'      => $e['element_desc'],
                            'element_ordre'     => $e['element_ordre'],
                            'element_pourcent'  => $e['element_pourcent'],
                            'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                            'ajout_epoch'       => $this->now_epoch
                        );
                    }

                    $this->db->insert_batch('questions_grilles_correction_elements', $data);

                    if ( ! $this->db->affected_rows())
                    {
                        log_alerte(
                            array(
                                'code'  => 'CPP9193',
                                'desc'  => "Il n'a pas été possible de copier les elements de la grille.",
                                'extra' => $this->db->error()['message']
                            )
                        );

                        $this->db->trans_rollback();
                        return FALSE;
                    }
                }

            } // foreach

        } // copier grilles

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            echo "Une erreur provenant de la base de donnees ete signalee.";
            return FALSE;
        }

        $this->db->trans_commit();

        return $n_evaluation_id;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Copier une question
     *
     * Cette function permet de copier une question d'une evaluation vers une autre evaluation.
     *
     * -------------------------------------------------------------------------------------------- */
    function copier_question($question_id, $evaluation_id_cible, $cours_id_cible)
    {
        //
        // Extraire la question
        //

        $question = $this->Question_model->extraire_question($question_id, $this->groupe_id);

        if (empty($question) || $question == FALSE)
        {
			log_alerte(
				array(
					'code'  => 'CQE2312',
                    'desc'  => "La question à copier n'a pas été trouvée dans la base de donnée.",
                    'extra' => 'question_id=' . $question_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
				)
            );

            return array(
                'status'  => 'error',
                'code'    => 'CQ2E312',
                'message' => "La question à copier n'a pas été trouvée dans la base de donnée."
            );
        }

        $evaluation_id_orig = $question['evaluation_id'];
        $cours_id_orig      = $question['cours_id'];

        //
        // Extraire l'evaluation d'ORIGINE
        //

        $evaluation_orig = $this->extraire_evaluation($evaluation_id_orig); 

        //
        // Extraire l'evaluation CIBLE
        //

        $evaluation_cible = $this->extraire_evaluation($evaluation_id_cible); 

        //
        // Permissions
        //

        // Verifier les permissions de l'evaluation cible.

        if ( ! in_array('ajouter_question', $this->permissions_evaluation($evaluation_id_cible, $evaluation_cible)))
        {
            echo "Vous n'avez pas la permission de copier cette question dans cette évaluation.";
            return FALSE;
        }

        // On ne veut pas qu'un enseignant puisse copier une question d'une evaluation d'un autre groupe.

        if ($evaluation_orig['groupe_id'] != $this->groupe_id)
        {
            log_alerte(
                array(
                    'code'  => 'CQE3311',
                    'desc'  => "Vous ne pouvez copier une question d'une évaluation qui ne provient pas de votre groupe.",
                    'extra' => 'question_id=' . $question_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
                )
            );

            return array(
                'status'  => 'error',
                'code'    => 'CQE3311',
                'message' => "Vous ne pouvez copier une question d'une évaluation qui ne provient pas de votre groupe."
            );
        }

        // On ne veut pas qu'un enseignant puisse copier une question d'une evaluation privee qui ne lui appartient pas.

        if ( ! $evaluation_orig['public'] && $evaluation_orig['enseignant_id'] != $this->enseignant_id)
        {
            log_alerte(
                array(
                    'code'  => 'CQE2316',
                    'desc'  => "Vous n'avez pas la permission de copier une question d'une évaluation d'origine qui ne vous appartient pas.",
                    'extra' => 'question_id=' . $question_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
                )
            );

            return array(
                'status'  => 'error',
                'code'    => 'CQE2316',
                'message' => "Vous n'avez pas la permission de copier une question d'une évaluation d'origine qui ne vous appartient pas."
            );
        }

        //
        // Extraire les reponses
        //

        $reponses = $this->Reponse_model->lister_reponses($question['question_id']);

        //
        // Extraire les tolerances
        //

        $tolerances = array();

        if ($question['question_type'] == 6)
        {
            $tolerances = $this->Question_model->extraire_tolerances($question_id);
        }

        //
        // Extraire la similarite
        //

        $similarite = array();

        if ($question['question_type'] == 7)
        {
            $similarite = $this->Question_model->extraire_similarite($question_id);
        }

        //
        // Extraire les variables de l'evaluation d'origine
        //

        $variables_orig = $this->extraire_variables($evaluation_id_orig);

        //
        // Extraire les variables de l'evaluation cible
        //

        $variables_cible = $this->extraire_variables($evaluation_id_cible);

        //
        // Determiner les etiquettes des variables pertinentes a la question
        //

        $variables_trouvees = array();

        // Verifier dans l'enonce de la question

        if (preg_match_all('/<var>(.+?)<\/var>/', $question['question_texte'], $matches))
        {
            $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
        }

        // Verifier dans les equations, s'il s'agit d'une question a coefficients variables

        if ( ! empty($reponses))
        {
            foreach($reponses as $r)
            {
                if ($question['question_type'] == 3)
                {
                    if (preg_match_all('/([ABCDFGHIJKLMNOPQRSTUVWXYZ]{1})/', $r['reponse_texte'], $matches))
                    {
                        $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
                    }
                }
                else
                {
                    if (preg_match_all('/<var>(.+?)<\/var>/', $r['reponse_texte'], $matches))
                    {
                        $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
                    }
                }
            }
        }

        // Dedoublonne le tableau

        $variables_trouvees = array_unique($variables_trouvees);

        //
        // Verifier que les variables sont definies.
        //

        if ( ! empty($variables_trouvees))
        {
            foreach($variables_trouvees as $v)
            {
                // Verifier que cette variable est bien definie dans l'evaluation d'origine.
                // (Peut-etre qu'elle est utilisee sous forme <var>X</var> mais non definie par exemple.)
                // Si c'est le pas, pas besoin de la copier.

                if ( ! array_key_exists($v, $variables_orig))
                {
                    // Ceci est le code pour enlever un element d'un tableau (lorsqu'on ne connait pas la key).
                    unset($variables_trouvees[array_search($v, $variables_trouvees)]);
                    continue;
                }

                if (array_key_exists($v, $variables_cible))
                {
                    return array(
                        'status'  => 'error',
                        'code'    => 'CQE812',
                        'message' => "Il n'a pas été possible de copier cette question car une variable de même étiquette existe dans l'évaluation cible."
                    );
                }
            }
        }

        //
        // Extraire les reponses
        //
        
        $reponses = $this->Reponse_model->lister_reponses($question_id);

        // 
        // Extraire l'image associee a la question
        //

        $image = $this->Document_model->extraire_image($question_id);

        //
        // Extraire la grille de correction
        //

        $gc = $this->Question_model->extraire_grilles_correction($question_id);

        if (array_key_exists($question_id, $gc))
        {
            $gc = $gc[$question_id]; 
        }

        //
        // Copier la question
        //

        $this->db->trans_begin();

        $data = $question;

        // Enlever les champs inexistants
        $champs = $this->db->list_fields('questions');

        foreach($data as $k => $val)
        {
            if ( ! in_array($k, $champs))
                unset($data[$k]);
        }

        unset($data['question_id']);

        $data['actif']                   = 0;
        $data['ordre']                   = 99;
        $data['evaluation_id']           = $evaluation_id_cible;
        $data['bloc_id']                 = NULL; // enlever l'inclusion dans un bloc
        $data['ajout_par_enseignant_id'] = $this->enseignant_id;
        $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
        $data['ajout_epoch']             = $this->now_epoch;

        $this->db->insert('questions', $data);

        $n_question_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
			log_alerte(
				array(
					'code'  => 'CQE9512',
                    'desc'  => "Il n'a pas été possible de copier la question.",
                    'extra' => $this->db->error()['message']
				)
            );

            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Copier les reponses
        //

        if ( ! empty($reponses))
        {
            $data_batch = array();

            $champs = $this->db->list_fields('reponses');

            foreach($reponses as $r)
            {
                $data = $r;

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                        unset($data[$k]);
                }

                unset($data['reponse_id'], $data['question_id'], $data['ajout_par_enseignant_id'], $data['ajout_date'], $data['ajout_epoch']);
                
                $data['question_id']             = $n_question_id;
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;
                $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch']             = $this->now_epoch;

                $data_batch[] = $data;
            }

            if ( ! empty($data_batch))
            {
                $this->db->insert_batch('reponses', $data_batch);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    log_alerte(
                        array(
                            'code'  => 'CQE9912',
                            'desc'  => "Il n'a pas été possible de copier les réponses.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    return array(
                        'status'  => 'error',
                        'code'    => 'CQE9912',
                        'message' => "Il n'a pas été possible de copier les réponses."
                    );
                }
            }
        }

        //
        // Copier les tolerances
        //

        if ($question['question_type'] == 6 && ! empty($tolerances))
        {
            $data_batch = array();

            $champs = $this->db->list_fields('questions_tolerances');

            foreach($tolerances as $t)
            {
                $data = $t;

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                        unset($data[$k]);
                }

                unset($data['tolerance_id']);
                
                $data['question_id'] = $n_question_id;

                $data_batch[] = $data;
            }

            if ( ! empty($data_batch))
            {
                $this->db->insert_batch('questions_tolerances', $data_batch);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    log_alerte(
                        array(
                            'code'  => 'CQE9913',
                            'desc'  => "Il n'a pas été possible de copier les tolérances.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    return array(
                        'status'  => 'error',
                        'code'    => 'CQE9913',
                        'message' => "Il n'a pas été possible de copier les tolérances."
                    );
                }
            }
        }

        //
        // Copier la similarite
        //

        if ($question['question_type'] == 7 && ! empty($similarite))
        {
            $data = $similarite;

            if (array_key_exists('similarite_id', $data))
            {
                unset($data['similarite_id']);
            }

            $data['question_id'] = $n_question_id;

            $this->db->insert('questions_similarites', $data);

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();

                log_alerte(
                    array(
                        'code'  => 'CQE9914',
                        'desc'  => "Il n'a pas été possible de copier la similarité d'une question",
                        'extra' => $this->db->error()['message']
                    )
                );

                return array(
                    'status'  => 'error',
                    'code'    => 'CQE9914',
                    'message' => "Il n'a pas été possible de copier la similarité d'une question."
                );
            }
        }

        //
        // Copier le document (image)
        //

        if ( ! empty($image))
        {
            $data = $image;

            $champs = $this->db->list_fields('documents');

            foreach($data as $k => $val)
            {
                if ( ! in_array($k, $champs))
                    unset($data[$k]);
            }

            unset($data['doc_id'], $data['question_id'], $data['ajout_par_enseignant_id'], $data['ajout_date'], $data['ajout_epoch']);

            $data['question_id']             = $n_question_id;
            $data['ajout_par_enseignant_id'] = $this->enseignant_id;
            $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
            $data['ajout_epoch']             = $this->now_epoch;

            $this->db->insert('documents', $data);

            if ( ! $this->db->affected_rows())
            {
                log_alerte(
                    array(
                        'code'  => 'CQE9191',
                        'desc'  => "Il n'a pas été possible de copier l'image de la question.",
                        'extra' => $this->db->error()['message']
                    )
                );

                $this->db->trans_rollback();
                return FALSE;
            }
        } 

        //
        // Copier les variables
        //

        if ( ! empty($variables_trouvees) && ! empty($variables_orig))
        {
            $data_batch = array();

            $champs = $this->db->list_fields('variables');

            foreach($variables_trouvees as $etiquette)
            {
                if ( ! array_key_exists($etiquette, $variables_orig))
                    continue; 

                if (array_key_exists($etiquette, $variables_cible))
                    continue;

                $data = $variables_orig[$etiquette];

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                        unset($data[$k]);
                }

                unset($data['variable_id']);

                $data['evaluation_id'] = $evaluation_id_cible;

                $data_batch[] = $data;
            }

            if ( ! empty($data_batch))
            {
                $this->db->insert_batch('variables', $data_batch);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    log_alerte(
                        array(
                            'code'  => 'CQE9501',
                            'desc'  => "Il n'a pas été possible de copier les variables.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    return array(
                        'status'  => 'error',
                        'code'    => 'CQE9501',
                        'message' => "Il n'a pas été possible de copier les variables."
                    );
                }
            }
        } // copier variables

        //
        // Copier la grille de correction et ses elements
        //

        if ( ! empty($gc))
        {
            //
            // Copier la grille
            //

            $data = array(
                'evaluation_id'     => $evaluation_id_cible,
                'question_id'       => $n_question_id,
                'grille_affichage'  => $gc['grille_affichage'],
                'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                'ajout_epoch'       => $this->now_epoch
            );

            $this->db->insert('questions_grilles_correction', $data);

            if ( ! $this->db->affected_rows())
            {
                log_alerte(
                    array(
                        'code'  => 'CPP9192',
                        'desc'  => "Il n'a pas été possible de copier la grille de correction.",
                        'extra' => $this->db->error()['message']
                    )
                );

                $this->db->trans_rollback();
                return FALSE;
            }

            $n_grille_id = $this->db->insert_id();

            // 
            // Copier les elements
            //

            if (array_key_exists('elements', $gc) && ! empty($gc['elements']))
            {
                $data = array();

                foreach($gc['elements'] as $e)
                {
                    $data[] = array(
                        'question_id'       => $n_question_id,
                        'grille_id'         => $n_grille_id,
                        'element_type'      => $e['element_type'],
                        'element_desc'      => $e['element_desc'],
                        'element_ordre'     => $e['element_ordre'],
                        'element_pourcent'  => $e['element_pourcent'],
                        'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                        'ajout_epoch'       => $this->now_epoch
                    );
                }

                $this->db->insert_batch('questions_grilles_correction_elements', $data);

                if ( ! $this->db->affected_rows())
                {
                    log_alerte(
                        array(
                            'code'  => 'CPP9193',
                            'desc'  => "Il n'a pas été possible de copier les elements de la grille.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    $this->db->trans_rollback();
                    return FALSE;
                }
            }

        } // copier grille

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            log_alerte(
                array(
                    'code'  => 'CQE7431',
                    'desc'  => "Une erreur est survenue lors de la copie de la question et/ou de ses composantes."
                )
            );

            return array(
                'status'  => 'error',
                'code'    => 'CQE7431',
                'message' => "Une erreur est survenue lors de la copie de la question et/ou de ses composantes."
            );
        }

        $this->db->trans_commit();

        return $evaluation_id_cible;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le responsable d'une evaluation (dans l'editeur)
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_responsable($evaluation_id, $enseignant_id)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
            return FALSE;

        // Le responsable peut seulement etre changé pour les évaluations publiques.
        if ( ! $evaluation['public'])
            return FALSE;

        //
        // Permissions
        //
        
        if ( ! permis('editeur') && $this->enseignant_id != $evaluation['enseignant_id'])
            return FALSE;

        $data = array(
            'enseignant_id' => $enseignant_id
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);
                
        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_evaluation($evaluation_id)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
        {
            return FALSE;
        }

        //
        // Permission d'effacer cette evaluation
        //

        if ( ! ($this->enseignant_id == $evaluation['enseignant_id'] || permis('editeur_effacer')))
        {
            return FALSE;
        }

        //
        // Questions
        // 

        $questions = $this->Question_model->lister_questions($evaluation_id);
        $question_ids = array_keys($questions);

        //
        // Documents
        //

        $images = $this->Document_model->extraire_images($question_ids); // ATTN: id = question_id
        $doc_ids = array();

        if ( ! empty($images))
        {
            foreach($images as $i)
            {
                $doc_ids[] = $i['doc_id'];
            }
        }

        //
        // Reponses
        //

        $reponses = array();
        $reponse_ids = array();

        if ( ! empty($question_ids))
        {
            $this->db->from   ('reponses as r');
            $this->db->where_in  ('r.question_id', $question_ids);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $reponses = array_keys_swap($query->result_array(), 'reponse_id');
            }
        }

        $reponse_ids = array_keys($reponses);

        //
        // Variables
        //

        $variables = $this->extraire_variables($evaluation_id);

        //
        // Blocs
        //

        $blocs = $this->Question_model->extraire_blocs($evaluation_id); 

        //
        // Grilles
        //

        $grilles = $this->Question_model->extraire_grilles_correction_par_evaluation_id($evaluation_id);

        //
        // Effacement
        //

        $this->db->trans_begin();

        $data_skel = array(
            'efface' => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

        // Effacement des reponses

        if ( ! empty($reponse_ids) && is_array($reponse_ids))
        {
            $this->db->where_in('reponse_id', $reponse_ids);
            $this->db->update  ('reponses', $data_skel);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Effacement des documents/images
        
        if ( ! empty($doc_ids) && is_array($doc_ids))
        {
            $this->db->where_in('doc_id', $doc_ids);
            $this->db->update  ('documents', $data_skel);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Effacement des questions

        if ( ! empty($question_ids) && is_array($question_ids))
        {
            $this->db->where_in('question_id', $question_ids);
            $this->db->update  ('questions', $data_skel);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement des variables
        //

        if ( ! empty($variables))
        {
            $variable_ids = array();

            foreach($variables as $v)
            {
                $variable_ids[] = $v['variable_id'];
            }

            $this->db->where_in('variable_id', $variable_ids);
            $this->db->update  ('variables', $data_skel);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement de la relation enseignant/evaluation
        //

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('efface', 0);
        $this->db->delete('rel_enseignants_evaluations');

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Effacement des blocs
        //

        if ( ! empty($blocs))
        {
            $this->db->where_in('bloc_id', array_keys($blocs));
            $this->db->update  ('blocs', $data_skel);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement des grilles et de leurs elements
        //

        if ( ! empty($grilles) && is_array($grilles))
        {
            //
            // Effacer les grilles
            //

            $data = array(
                'efface'        => 1,
                'efface_date'   => date_humanize($this->now_epoch, TRUE),
                'efface_epoch'  => $this->now_epoch
            );

            $this->db->where ('evaluation_id', $evaluation_id);
            $this->db->where ('efface', 0);
            $this->db->update('questions_grilles_correction', $data);

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }

            //
            // Effacer les elements, s'il y a des questions
            //

            if (is_array($question_ids) && ! empty($question_ids))
            {
                // Il se peut que les grilles ne contiennent aucun element.

                $data = array(
                    'efface'        => 1,
                    'efface_date'   => date_humanize($this->now_epoch, TRUE),
                    'efface_epoch'  => $this->now_epoch
                );

                $this->db->where_in ('question_id', $question_ids);
                $this->db->where    ('efface', 0);
                $this->db->update   ('questions_grilles_correction_elements', $data);
            } 
        }

        //
        // Effacement de l'evaluation
        //

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data_skel);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Purger une evaluation
     *
     * Effacer completement une evaluation de la base de donnees.
     * Cette fonction n'est pas utilisee mais est une copie de l'ancienne fonctionnalite 
     * de 'effacer_evaluation' au cas ou la nouvelle facon de faire ne convienne pas. 2019/01/18
     *
     * -------------------------------------------------------------------------------------------- */
    function purger_evaluation($evaluation_id)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
            return FALSE;

        //
        // Permission d'effacer cette evaluation
        //

        if ( ! ($this->enseignant_id == $evaluation['enseignant_id'] || permis('editeur_effacer')))
        {
            return FALSE;
        }

        //
        // Questions
        // 

        $questions = $this->Question_model->lister_questions($evaluation_id);
        $question_ids = array_keys($questions);

        //
        // Documents
        //

        $images = $this->Document_model->extraire_images($question_ids); // ATTN: id = question_id
        $doc_ids = array();

        if ( ! empty($images))
        {
            foreach($images as $i)
            {
                $doc_ids[] = $i['doc_id'];
            }
        }

        //
        // Reponses
        //

        $reponses = array();
        $reponse_ids = array();

        if ( ! empty($question_ids))
        {
            $this->db->from   ('reponses as r');
            $this->db->where_in  ('r.question_id', $question_ids);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $reponses = array_keys_swap($query->result_array(), 'reponse_id');
            }
        }

        $reponse_ids = array_keys($reponses);

        //
        // Variables
        //

        $variables = $this->extraire_variables($evaluation_id);

        //
        // Blocs
        //

        $blocs = $this->Question_model->extraire_blocs($evaluation_id); 

        //
        // Effacement
        //

        $this->db->trans_begin();

        // Effacement des reponses

        if ( ! empty($reponse_ids) && is_array($reponse_ids))
        {
            $this->db->where_in('reponse_id', $reponse_ids);
            $this->db->delete('reponses');

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Effacement des documents/images
        
        if ( ! empty($doc_ids) && is_array($doc_ids))
        {
            $this->db->where_in('doc_id', $doc_ids);
			$this->db->update  ('documents', array('efface' => 1));

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Effacement des questions

        if ( ! empty($question_ids) && is_array($question_ids))
        {
            $this->db->where_in('question_id', $question_ids);
            $this->db->delete('questions');

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement des variables
        //

        if ( ! empty($variables))
        {
            $variable_ids = array();

            foreach($variables as $v)
            {
                $variable_ids[] = $v['variable_id'];
            }

            $this->db->where_in('variable_id', $variable_ids);
            $this->db->delete('variables');

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement de la relation enseignant/evaluation
        //

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->where('efface', 0);
        $this->db->delete('rel_enseignants_evaluations');

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Effacement des blocs
        //

        if ( ! empty($blocs))
        {
            $this->db->where_in('bloc_id', array_keys($blocs));
            $this->db->delete  ('blocs');

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        //
        // Effacement de l'evaluation
        //

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->delete('evaluations');

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Archiver / Desarchiver une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function archiver_desarchiver_evaluation($evaluation_id)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        //
        // Permission de modifier cette evaluation
        //

        if ($evaluation['groupe_id'] != $this->enseignant['groupe_id'])
		{
            return FALSE;
		}

        if ($evaluation['public'])
        {
            if ( ! (permis('editeur') || ! permis('admin')))
            {
                return FALSE;
            }   
        }

        else 
        {
            if ($this->enseignant_id != $evaluation['enseignant_id'] && permis('admin'))
            {
                return FALSE;
            }
        }

        $data = array(
            'archive' => $evaluation['archive'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Activer / Desactiver une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function activer_desactiver_evaluation($evaluation_id)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        //
        // Permission de modifier cette evaluation
        //

        if ($evaluation['groupe_id'] != $this->enseignant['groupe_id'])
		{
            return FALSE;
		}

        if ($this->enseignant_id != $evaluation['enseignant_id'] && permis('admin'))
        {
            return FALSE;
        }

        if ($evaluation['actif'] == 1)
        {
            // Verifier si l'evaluation est selectionnee
            // pour etre vue par des etudiants (dans la configuration)
            // - Si c'est le cas, il faut l'enlever

            $this->db->from ('rel_enseignants_evaluations as ree');
            $this->db->where('ree.groupe_id', $this->groupe_id);
            $this->db->where('ree.evaluation_id', $evaluation_id);
            $this->db->where('ree.efface', 0);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $this->db->where('evaluation_id', $evaluation_id);
                $this->db->delete('rel_enseignants_evaluations');
            }
        }

        $data = array(
            'actif' => $evaluation['actif'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer questions aleatoires d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_questions_aleatoires($evaluation_id, $checked)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation == FALSE)
            return FALSE;

        $data = array(
            'questions_aleatoires' => $evaluation['questions_aleatoires'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * [OBSOLETE depuis 2023-01-14] Changer le status d'inscription requise
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_changer_inscription_requise($evaluation_id, $checked)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation == FALSE)
            return FALSE;

        $data = array(
            'inscription_requise' => $evaluation['inscription_requise'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le status du temps en redaction
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_temps_en_redaction($evaluation_id, $checked)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation == FALSE)
            return FALSE;

        $data = array(
            'temps_en_redaction' => $evaluation['temps_en_redaction'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le status d'evaluation formative d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_evaluation_formative($evaluation_id, $checked)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation == FALSE)
            return FALSE;

        $data = array(
            'formative' => $evaluation['formative'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Permettre ou interdire les changements a l'evaluation (cadenas)
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_cadenas($evaluation_id, $checked)
    {
        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation == FALSE)
            return FALSE;

        //
        // Permissions
        //

        if ( ! ($evaluation['enseignant_id'] == $this->enseignant_id || permis('editeur')))
            return FALSE;

        $data = array(
            'cadenas' => $evaluation['cadenas'] ? 0 : 1
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister evaluations
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_evaluations($options = array())
    {
    	$options = array_merge(
        	array(
                'enseignant_id' => NULL,
				'groupe_id'     => $this->groupe_id,
                'public'        => NULL,
                'cours_id'      => NULL,
                'archives'      => FALSE,
                'actif'         => TRUE,
           ),
           $options
        );

        $this->db->from ('evaluations as e, cours as c');
        $this->db->select('e.*');

        // Il faut que les cours apparaissent toujours dans le meme ordre dans la liste (depuis 2019/01/13)
        $this->db->where   ('e.cours_id = c.cours_id');
		$this->db->where   ('e.groupe_id', $options['groupe_id']);
        $this->db->where   ('e.efface', 0);
        $this->db->order_by('c.cours_code', 'asc');

        if ($options['enseignant_id'])
        {
            $this->db->where('e.enseignant_id', $options['enseignant_id']);
		}

		if ($this->enseignant['niveau'] < $this->config->item('niveaux')['admin'])
		{
			$this->db->where('e.groupe_id', $this->enseignant['groupe_id']);
		}

        if ($options['public'] === TRUE)
        {
            $this->db->where('e.public', 1);
        }
        else
        {
            $this->db->where('e.public', 0);
        }

        if ($options['cours_id'])
        {
            $this->db->where('e.cours_id', $options['cours_id']);
        }
    
        if ($options['archives'] == TRUE)
        {
            $this->db->where('e.archive', 1);
        }
        else
        {
            $this->db->where('e.archive', 0);
        }

        if ($options['actif'] == TRUE)
        {
            $this->db->where('e.actif', 1);
        }

        $this->db->order_by('e.ordre', 'asc');
        $this->db->order_by('e.evaluation_titre', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
            return array();

        return array_keys_swap($query->result_array(), 'evaluation_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lister les evaluations selectionnees par l'enseignant pour certains cours d'un semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_evaluations_selectionnees($enseignant_id, $semestre_id, $cours_id = NULL, $id_pour_index = TRUE, $options = array())
    {
        $options = array_merge(
            array(
                'cacher_cachees'  => FALSE,  // Cacher les evaluations cachees par l'enseignant (pour les groupes ayant un sous-domaine).
                'cacher_bloquees' => FALSE,  // Cacher les evaluations bloquees par l'enseignant (pour les groupes ayant un sous-domaine).
                'respecter_date'  => FALSE,  // Selectionner seulement ceux disponibles a remplir en respectant les dates planifiees.
                'en_redaction'    => FALSE   // Extraire les evaluations presentement en redaction par l'etudiant
            ), $options
        );

        $cache_key = __FUNCTION__ . $enseignant_id . $semestre_id . $cours_id . $id_pour_index . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $from   = 'rel_enseignants_evaluations as ree, evaluations as e';
        $select = 'ree.*, e.*';

        $this->db->where('ree.enseignant_id', $enseignant_id);
        $this->db->where('ree.semestre_id', $semestre_id);
        $this->db->where('ree.evaluation_id = e.evaluation_id');
        $this->db->where('ree.efface', 0);
        $this->db->where('e.actif', 1);
        $this->db->where('e.efface', 0);

        if ( ! empty($cours_id) && is_numeric($cours_id))
        {
            $from   .= ', cours as c';
            $select .= ', c.cours_code, c.cours_code_court, c.cours_nom, c.cours_nom_court, c.cours_url';

            $this->db->where('e.cours_id = c.cours_id');
            $this->db->where('c.cours_id', $cours_id);
        }

        if ($options['cacher_cachees'] )
        {
            $this->db->where('ree.cacher', 0);
        }

        if ($options['cacher_bloquees'])
        {
            $this->db->where('ree.bloquer', 0);
        }

        if ($options['respecter_date'])
        {
            $this->db->where('ree.debut_epoch <', $this->now_epoch);
        }

        $this->db->order_by('e.ordre', 'asc');
        $this->db->order_by('e.evaluation_titre', 'asc');

        $this->db->from  ($from);
        $this->db->select($select);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $evaluations = $query->result_array();

        if ($id_pour_index)
        {
            // Attention :
            // Ceci ne fonctionne pas avec le javascript car il itere en ordre de id, et non en ordre de listage.

            $evaluations = array_keys_swap($evaluations, 'evaluation_id');
        }

        //
        // Pour les etudiants seulement, determiner si chacune de ces evaluations est en redaction
        //

        $en_redaction = array();

        if ( ! empty($evaluations))
        {
            if ($options['en_redaction'] && $this->est_etudiant)
            {
                $evaluation_ids = array_column($evaluations, 'evaluation_id');

                $this->db->from     ('etudiants_traces');
                $this->db->where    ('etudiant_id', $this->etudiant_id);
                $this->db->where    ('semestre_id', $semestre_id);
                $this->db->where    ('evaluation_terminee', 0);
                $this->db->where    ('efface', 0);

                $this->db->where_in ('evaluation_id', $evaluation_ids);
                
                $query = $this->db->get();
                
                if ($query->num_rows() > 0)
                {
                    $en_redaction = array_keys_swap($query->result_array(), 'evaluation_id');

                    foreach($evaluations as &$e)
                    {
                        $e['en_redaction'] = 0;

                        if (array_key_exists($e['evaluation_id'], $en_redaction))
                        {
                            $e['en_redaction'] = 1;
                        }
                    }
                }
            }
        } // if ! empty($evaluations)

        $this->kcache->save($cache_key, $evaluations, 'bienvenue', 5);

        return $evaluations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une evaluation de la base de donnees
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluation_par_reference($evaluation_reference, $options = array())
    {
        $options = array_merge(
            array(
                'semestre_id' => NULL
            ), $options
		);

        $cache_key = __FUNCTION__ . $evaluation_reference . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->select  ('e.*');
        $this->db->from    ('rel_enseignants_evaluations as ree, evaluations as e');
        $this->db->where   ('ree.evaluation_reference', $evaluation_reference);
        $this->db->where   ('ree.efface', 0);

        if ( ! empty($options['semestre_id']))
        {
            $this->db->where ('ree.semestre_id', $options['semestre_id']);
        }

        $this->db->where   ('ree.evaluation_id = e.evaluation_id');
        $this->db->where   ('e.efface', 0);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

		$r = $query->row_array();

		$this->kcache->save($cache_key, $r, 'evaluation', 30);
                                                                                                                                                                                                                                  
        return $r;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une evaluation de la base de donnees (PING_ETUDIANT)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluation_par_reference_ping($evaluation_reference, $options = array())
    {
        $options = array_merge(
            array(
                'semestre_id' => NULL
            ), $options
		);

        $cache_key = __FUNCTION__ . $evaluation_reference . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->select  ('ree.debut_epoch, ree.cours_id, ree.semestre_id, ree.fin_epoch, ree.temps_limite');
        $this->db->from    ('rel_enseignants_evaluations as ree, evaluations as e');
        $this->db->where   ('ree.evaluation_reference', $evaluation_reference);
        $this->db->where   ('ree.efface', 0);

        if ( ! empty($options['semestre_id']))
        {
            $this->db->where ('ree.semestre_id', $options['semestre_id']);
        }

        $this->db->where   ('ree.evaluation_id = e.evaluation_id');
        $this->db->where   ('e.efface', 0);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $r = $query->row_array();

        //
        // Ajuster le temps limite
        //

        $temps_supp = $this->Etudiant_model->extraire_etudiant_id_temps_supp(
            $this->etudiant_id,
            array(
                'groupe_id' => $this->groupe_id ?? NULL,
                'cours_id'  => $r['cours_id'],
                'semestre_id' => $r['semestre_id']
            )
        );

        if ($temps_supp > 0)
        {
            $r['temps_limite'] = ceil($r['temps_limite'] + ($r['temps_limite'] * $temps_supp/100));
        }

        // Ces informations n'ont pas besoin d'etre transmis.

        unset($r['cours_id']);
        unset($r['semestre_id']);

        // $this->kcache->save($cache_key, $r, 'evaluation', 30);

        return $r;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une evaluation de la base de donnees
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluation($evaluation_id, $options = array())
    {
        if ($evaluation_id == NULL)
        {
            redirect(base_url());
            return;
        }

        $options = array_merge(
            array(
                'evaluation_ids' => array($evaluation_id)
            ), $options
        );

        $evaluations = $this->extraire_evaluations($options);

        if (empty($evaluations))
        {
            return array();
        }

        $evaluation = array_shift($evaluations);

        return $evaluation;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire plusieurs evaluations
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluations($options = array())
    {
    	$options = array_merge(
        	array(
                'evaluation_ids' => array(),
                'actif'          => FALSE,
                'lab_prefix'     => NULL
           ),
           $options
        );

        if (empty($options['evaluation_ids']))
		{
            redirect(base_url());
            exit;
        }

        $this->db->from   ('evaluations as ev, enseignants as en');
        $this->db->select ('ev.*, en.nom as enseignant_nom, en.prenom as enseignant_prenom, en.genre as enseignant_genre');

        if ($options['lab_prefix'])
        {
            $this->db->where('ev.lab_prefix', $options['lab_prefix']);
        }

        if ($options['actif'] === TRUE)
        {
            $this->db->where('ev.actif', 1);
        }

        //
        // Un enseignant ne peut extraire que les evaluations de son groupe.
        //

		if (@$this->est_enseignant && $this->enseignant['privilege'] < 90)
		{
            $this->db->where('ev.groupe_id', $this->enseignant['groupe_id']);
        }

        $this->db->where('ev.enseignant_id = en.enseignant_id');
        $this->db->where('ev.efface', 0);
        $this->db->where_in('ev.evaluation_id', $options['evaluation_ids']);

        $this->db->order_by('ev.ordre', 'asc');
        $this->db->order_by('ev.evaluation_titre', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'evaluation_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une evaluation (simple)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluation_simple($evaluation_id, $options = array())
    {
        if ($evaluation_id == NULL)
        {
            redirect(base_url());
            return;
        }

        $options = array_merge(
            array(
                'evaluation_ids' => array($evaluation_id)
            ), $options
        );

        $evaluations = $this->extraire_evaluations_simple($options);

        if (empty($evaluations))
        {
            return array();
        }

        $evaluation = array_shift($evaluations);

        return $evaluation;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire plusieurs evaluations (simple)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluations_simple($options = array())
    {
    	$options = array_merge(
        	array(
                'evaluation_ids' => array(),
                'actif' => FALSE
           ),
           $options
        );

        if (empty($options['evaluation_ids']))
		{
            redirect(base_url());
            exit;
        }

        $this->db->from ('evaluations as e');

        if ($options['actif'] === TRUE)
        {
            $this->db->where('e.actif', 1);
        }

        $this->db->where    ('e.efface', 0);
        $this->db->where_in ('e.evaluation_id', $options['evaluation_ids']);

        $this->db->order_by ('e.ordre', 'asc');
        $this->db->order_by ('e.evaluation_titre', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'evaluation_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire plusieurs evaluations de la base de donnees, avec le cours_id
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluations_par_cours_ids($options = array())
    {
    	$options = array_merge(
            array(
                'cours_ids' => array()
           ),
           $options
        );

        if (empty($options['cours_ids']) || ! is_array($options['cours_ids']))
            redirect(base_url());

        $this->db->from   ('evaluations as e, cours as c');
        $this->db->select ('e.*');

        $this->db->where_in ('e.cours_id', $options['cours_ids']);
        $this->db->where    ('e.actif', 1);
        $this->db->where    ('e.efface', 0);
        $this->db->where    ('c.actif', 1);
        $this->db->where    ('e.cours_id = c.cours_id');

        // Extraire seulement les evaluations de l'enseignant
        $this->db->where('e.enseignant_id', $this->enseignant_id);
        $this->db->where('e.public', 0);

        $this->db->order_by ('c.cours_code', 'asc');
        $this->db->order_by ('e.ordre', 'asc');
        $this->db->order_by ('e.evaluation_titre', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'evaluation_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire plusieurs evaluations de la base de donnees, avec le cours_id
     *
     * Cette function est concue pour permettre de copier des questions d'une evaluation a une autre,
     * a partir de l'editeur d'evaluations.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluations_pour_select($cours_id, $evaluation_id, $public)
    {
        $this->db->from   ('evaluations as e, cours as c');
        $this->db->select ('e.*');

        $this->db->where ('e.cours_id', $cours_id);
        $this->db->where ('c.actif', 1);
        $this->db->where ('e.cadenas', 0);
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->where ('e.public', $public);
        $this->db->where ('e.efface', 0);

        if ( ! $public)
        {
            $this->db->where ('e.enseignant_id', $this->enseignant_id);
        }

        $this->db->where_not_in('e.evaluation_id', array($evaluation_id));

        $this->db->order_by ('e.ordre', 'asc');
        $this->db->order_by ('e.evaluation_titre', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'evaluation_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  Extraire rel_evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_rel_evaluation($evaluation_reference)
    {
        $this->db->from  ('rel_enseignants_evaluations');
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('efface', 0);
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
     *  Verifier la combinaison enseignant/evaluation
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_enseignant_evaluation($enseignant_id, $evaluation_id)
	{
        $this->db->from ('rel_enseignants_evaluations as ree');
		$this->db->where('ree.enseignant_id', $enseignant_id);
        $this->db->where('ree.evaluation_id', $evaluation_id); 
        $this->db->where('ree.efface', 0);
		$this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
		
		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     *  Verifier la combinaison enseignant/evaluation/semestre
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_enseignant_evaluation_semestre($enseignant_id, $evaluation_id, $semestre_id)
	{
        $this->db->from ('rel_enseignants_evaluations as ree');
		$this->db->where('ree.enseignant_id', $enseignant_id);
		$this->db->where('ree.evaluation_id', $evaluation_id); 
        $this->db->where('ree.semestre_id',   $semestre_id);
        $this->db->where('ree.efface', 0);
		$this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
		
		return $query->row_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     *  Verifier la reference
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_evaluation_reference($evaluation_reference)
	{
        $this->db->from  ('rel_enseignants_evaluations as ree, evaluations as e, groupes as g');
		$this->db->select('ree.*, ree.inscription_requise, g.sous_domaine');
		$this->db->where ('ree.groupe_id = g.groupe_id');
        $this->db->where ('ree.evaluation_reference', $evaluation_reference);
        $this->db->where ('ree.evaluation_id = e.evaluation_id');
        $this->db->where ('ree.efface', 0);
		$this->db->where ('g.actif', 1);
        $this->db->where ('g.efface', 0);

		$this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
	
		return $query->row_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     *  Verifier la reference (simple)
     *
     * -------------------------------------------------------------------------------------------- */
	function verifier_evaluation_reference_simple($evaluation_reference)
    {
        $this->db->from  ('rel_enseignants_evaluations');
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;    
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  evaluation parametres pour la previsualisation, si disponible
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluation_parametres_previsualisation($options = array())
    {
    	$options = array_merge(
        	array(
                'evaluation_id' => NULL,
                'semestre_id'   => NULL
           ),
           $options
        );

        if (empty($options['evaluation_id']) || empty($options['semestre_id']))
        {
            return FALSE;
        }

        $this->db->from  ('rel_enseignants_evaluations as ree, evaluations as e, groupes as g');
		$this->db->select('ree.*, ree.inscription_requise, g.sous_domaine');
		$this->db->where ('ree.groupe_id = g.groupe_id');
        $this->db->where ('ree.evaluation_id', $options['evaluation_id']);
        $this->db->where ('ree.semestre_id', $options['semestre_id']);
        $this->db->where ('ree.evaluation_id = e.evaluation_id');
        $this->db->where ('ree.efface', 0);
		$this->db->where ('g.actif', 1);
        $this->db->where ('g.efface', 0);

		$this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
	
		return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Selectionner ou deselectionner une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function selection_evaluation($semestre_id, $evaluation_id, $cours_id)
    {
        if ( ! is_numeric($semestre_id) || ! is_numeric($evaluation_id))
        {
            return FALSE;
        }

        //
        // Determiner l'operation a effectuer
        //
        // Est-ce une selection ou deselection ?
        //

        $this->db->from ('rel_enseignants_evaluations as ree');
        $this->db->where('ree.enseignant_id', $this->enseignant_id);
        $this->db->where('ree.semestre_id',   $semestre_id);
        $this->db->where('ree.evaluation_id', $evaluation_id);
        $this->db->where('ree.efface', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            //
            // C'est une deselection.
            //

            $evaluation = $query->row_array();
            $evaluation_reference = $evaluation['evaluation_reference'];

            return $this->Soumission_model->mettre_hors_ligne($evaluation_reference);
        }

        //
        // C'est une selection.
        //

        return $this->Soumission_model->mettre_en_ligne($evaluation_id);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verification de l'integrite d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Verifier si l'evaluation comporte des erreurs.
     *
     * Depuis l'introduction des blocs, on ne peut plus verifier l'evaluation seulement lors de la
     * previsualisation car ce n'est pas toutes les questions qui sont selectionnees.
     * Il est donc necessaire d'avoir une fonction specialisee a cette fin. (2019-01-31)
     *
     * parametres
     * ----------
     *
     * evaluation_id :
     * question_ids  : utilise lorsque l'evaluation est verifiee avant une soumission, et
     *                 specifiquement les questions qui seront soumises
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_integrite_evaluation($evaluation_id, $question_ids = array(), $options = array())
    {
        $options = array_merge(
            array(
                // Lors de la soumission, il est inutile de verifier avec des valeurs arbitraires pour les variables,
                // il vaut mieux seulement tester avec les valeurs choisies pour l'evaluation.
                'variables_valeurs' => array()
            ),
            $options
        );

        // Tableau de retour d'erreur
        $erreur = array(
            'status'   => NULL, // (NULL = ERROR), WARNING, TRUE
            'code'     => NULL,
            'message'  => NULL,
            'solution' => NULL,
            'extra'    => array()
        );

        $evaluation = $this->extraire_evaluation_simple($evaluation_id);
    
        //
        // Est-ce un laboratoire ?
        //
    
        $lab = $evaluation['lab'];

        //
        // Extraire toutes les questions, ou seulement les questions choisies
        //

        if ( ! empty($question_ids))
        {
            $questions = $this->Question_model->lister_questions($evaluation_id, 
                array(
                    'question_ids' => $question_ids,
                    'actif' => TRUE
                )
            );
        }
        else
        {
            $questions = $this->Question_model->lister_questions($evaluation_id,
                array(
                    'actif' => TRUE
                )
            );
        }

        if ( ! $lab && empty($questions))
        {
            return array(
                'code'     => 'VIE4477',
                'message'  => "Cette évaluation ne comporte aucune question, ou aucune question activée.",
                'solution' => "Veuillez ajouter ou activer une question."
            );
        }

        //
        // Verifier que meme si certaines questions sont activees, 
        // si ces questions font parties d'un bloc dont aucune question ne peut etre affichee (parametre a 0),
        // alors il faut considerer ces questions comme inactivees.
        //

        $blocs = $this->Question_model->extraire_blocs($evaluation_id);

        if ( ! empty($blocs))
        {
            $question_affichee = FALSE; // Verifier qu'au moins une question sera affichee

            foreach($questions as $question_id => $q)
            {
                if (empty($q['bloc_id']))
                {
                    $question_affichee = TRUE;
                    break;
                }

                if ($blocs[$q['bloc_id']]['bloc_nb_questions'] == 0)
                {
                    continue;
                }

                $question_affichee = TRUE;
                break;
            }

            if ($question_affichee === FALSE)
            {
                return array(
                    'code'     => 'VIE5599',
                    'message'  => "Cette évaluation comporte des questions d'un bloc qui ne permet pas de choisir parmi les questions de ce bloc.",
                    'solution' => "Veuillez paramétrer le bloc pour afficher au moins une question."
                );
            }
        }

        //
        // Verifier les questions
        //

        $variables = $this->extraire_variables($evaluation_id);

        foreach($questions as $question_id => $q)
        {
            //
            // Ceci concerne toutes les questions.
            //

            //
            // Verifier si la question utilise des variables (dans le texte), et si ces variables sont definies.
            //

            if (preg_match_all('/<var>(.+?)<\/var>/', $q['question_texte'], $matches))
            {
                // La question comporte des variables.
                
                $variables_trouvees = $matches[1];

                // Verifier que ces variables sont definies.

                foreach($variables_trouvees as $v)
                {
                    if ( ! array_key_exists($v, $variables))
                    {
                        return array(
                            'code'     => 'VIE5780',
                            'message'  => "Une des questions utilise une variable inexistante.",
                            'solution' => "Veuillez désactiver cette question ou créer la variable " . $v . ".",
                            'extra'    => array('question_id' => $question_id)
                        );
                    }
                }
            } // if preg_match_all

            //
            // Extraire les reponses
            //

            $reponses = $this->Reponse_model->lister_reponses($question_id);

            //
            // Verifier qu'au moins une reponse existe.
            //

            //
            // Ceci concerne les questions :
            //
            // - choix unique (1)
            // - choix unique par equation (3)
            // - choix multiples (4)
            // - reponse numerique entiere (5)
            // - reponse numerique (6)
            // - reponse litterale courte (7)
            // - reponse numerique par equation (9)
            //

            if (in_array($q['question_type'], array(1, 3, 4, 5, 6, 7, 9)))
            {
                if (empty($reponses))
                {
                    if ($q['question_type'] == 5)
                    {
                        if ( ! $q['sondage'])
                        {
                            //
                            // Une reponse numerique entiere est necessaire seulement s'il ne s'agit pas d'un sondage.
                            //

                            return array(
                                'code'     => 'VIE3366',
                                'message'  => "Une " . lcfirst($this->config->item(5, 'questions_types')['desc']) . " n'a aucune réponse.",
                                'solution' => "Veuillez désactiver cette question ou ajouter la réponse correcte.",
                                'extra'    => array('question_id' => $question_id)
                            );
                        }
                    }
                    elseif ($q['question_type'] == 6)
                    {
                        if ( ! $q['sondage'])
                        {
                            //
                            // Une reponse numerique est necessaire seulement s'il ne s'agit pas d'un sondage.
                            //

                            return array(
                                'code'     => 'VIE3367',
                                'message'  => "Une " . lcfirst($this->config->item(6, 'questions_types')['desc']) . " n'a aucune réponse.",
                                'solution' => "Veuillez désactiver cette question ou ajouter la réponse correcte.",
                                'extra'    => array('question_id' => $question_id)
                            );
                        }
                    }
                    elseif ($q['question_type'] == 7)
                    {
                        if ( ! $q['sondage'])
                        {
                            //
                            // Une reponse litterale courte acceptee est necessaire seulement s'il ne s'agit pas d'un sondage.
                            //

                            return array(
                                'code'     => 'VIE3370',
                                'message'  => "Une " . lcfirst($this->config->item(7, 'questions_types')['desc']) . " n'a aucune réponse.",
                                'solution' => "Veuillez désactiver cette question ou ajouter la réponse correcte.",
                                'extra'    => array('question_id' => $question_id)
                            );
                        }
                    }
                    elseif ($q['question_type'] == 9)
                    {
                        return array(
                            'code'     => 'VIE3369',
                            'message'  => "Une des questions à réponse numérique n'a aucune équation pour réponse.",
                            'solution' => "Veuillez désactiver cette question ou ajouter l'équation de la réponse correcte.",
                            'extra'    => array('question_id' => $question_id)
                        );
                    }
                    else
                    {
                        return array(
                            'code'     => 'VIE3368',
                            'message'  => "Une des questions n'a aucune réponse.",
                            'solution' => "Veuillez désactiver cette question ou ajouter au moins deux réponses.",
                            'extra'    => array('question_id' => $question_id)
                        );
                    }
                }
            }

            //
            // Verifier qu'au moins une reponse correcte existe.
            //

            //
            // Ceci concerne les questions :
            //
            // - choix unique (1)
            // - coefficients variables (3)
            // - reponse numerique entiere (5)
            // - reponse numerique (6)
            // - reponse litterale courte (7)
            // - reponse numerique par equation (9)
            //

            if (in_array($q['question_type'], array(1, 3, 5, 6, 7, 9)))
            {
                $reponse_correcte_presente = FALSE;

                foreach($reponses as $r)
                {
                    if ($r['reponse_correcte'])
                    {
                        $reponse_correcte_presente = TRUE;
                        break;
                    }
                }

                if ( ! $reponse_correcte_presente)
                {
                    if (in_array($q['question_type'], array(5, 6, 7)))
                    {
                        $type = $q['question_type'];

                        if ( ! $q['sondage'])
                        {
                            //
                            // Une reponse numerique definie est necessaire seulement s'il ne s'agit pas d'un sondage.
                            //

                            return array(
                                'code'     => 'VIE7799',
                                'message'  => "Une " . lcfirst($this->config->item($type, 'questions_types')['desc']) . " n'a aucune réponse correcte définie.",
                                'solution' => "Veuillez désactiver cette question ou définir une réponse correcte.",
                                'extra'    => array('question_id' => $question_id)
                            );
                        }
                    }
                    else
                    {
                        return array(
                            'code'     => 'VIE7799',
                            'message'  => "Une des questions n'a aucune réponse correcte définie.",
                            'solution' => "Veuillez désactiver cette question ou définir une réponse correcte.",
                            'extra'    => array('question_id' => $question_id)
                        );
                    }
                }
            } // type 1, 3, 5, 6, 7

            //
            // Verifier qu'au moins deux reponses existent.
            //

            //
            // Ceci concerne les questions :
            //
            // - choix unique (1)
            // - coefficients variables (3)
            // - choix multiples (4)
            //

            if (in_array($q['question_type'], array(1, 3, 4)))
            {
                // Depuis 2023-08-27, les questions de type 1 peuvent n'avoir qu'une seule reponse correcte,
                // ceci dans le but de faire des formulaires de consentement a completer (comme les regles de securite).
                if (in_array($q['question_type'], array(3, 4)) && count($reponses) < 2)
                {
                    return array(
                        'code'     => 'VIE6688',
                        'message'  => "Une des questions n'a qu'une seule réponse (le minimum étant de 2).",
                        'solution' => "Veuillez désactiver cette question ou ajouter une autre réponse.",
                        'extra'    => array('question_id' => $question_id)
                    );
                }

                //
                // Verifier l'unicite des reponses, en considerant le texte de la reponse.
                //

                $reponses_uniques = array();

                foreach($reponses as $r)
                {
                    if ( ! in_array($r['reponse_texte'], $reponses_uniques))
                    {
                        $reponses_uniques[] = $r['reponse_texte'];
                    }
                    else
                    {
                        return array(
                            'code'     => 'VIE1991',
                            'message'  => "Une des questions possède deux réponses identiques.",
                            'solution' => "Veuillez désactiver cette question ou modifier l'une des réponses.",
                            'extra'    => array('question_id' => $question_id)
                        );
                    }
                } // foreach;

            } // type 1, 3, 4

            //
            // Verifier l'existence des variables
            // 
            // Au moins une variable doit exister pour ajouter des reponses aux questions a coefficients variables
            //

            //
            // Ceci concerne la question :
            //
            // - choix unique par equations (3)
            // - reposne numerique par equation (9)
            //

            if ($q['question_type'] == 3 || $q['question_type'] == 9)
            {
                $type = $q['question_type'];

                if (empty($variables) || ! is_array($variables))
                {
                    return array(
                        'code'     => 'VIE566101',
                        'message'  => "Il n'existe aucune variable malgré la présence d'une " . lcfirst($this->config->item($type, 'questions_types')['desc']) . ".",
                        'solution' => "Veuillez désactiver la " . lcfirst($this->config->item($type, 'questions_types')['desc']) . " fautive, ou créer une variable.",
                        'extra'    => array('question_id' => $question_id)
                    );
                }

                //
                // Verifier que les variables existantes sont celles utilisees dans les equations.
                //

                foreach($reponses as $r)
                {
                    if (preg_match_all('/([ABCDFGHIJKLMNOPQRSTUVWXYZ]{1})/', $r['reponse_texte'], $matches))
                    {
                        // L'equation comporte un ou plusieurs variables

                        $variables_trouvees = $matches[1];

                        // Verifier que ces variables sont definies.

                        foreach($variables_trouvees as $v)
                        {
                            if ( ! array_key_exists($v, $variables))
                            {
                                return array(
                                    'code'     => 'VIE5781',
                                    'message'  => "Une des équations utilise une variable inexistante.",
                                    'solution' => "Veuillez effacer cette équation ou créer la variable " . $v . ".",
                                    'extra'    => array('question_id' => $question_id)
                                );
                            }
                        }
                    } // if preg_match_all
                }

                //
                // Verifier la qualite des variables et des equations
                //

                $plus_petit_cs = 999; // necessaire pour determiner le plus petit CS des variables
                $iteration     = 0;   // iteration de securite pour eviter une boucle infinie

                //
                // Cette iteration est differente de celle pour montrer l'evaluation.
                // Celle-ci genere X fois (voir iteration ci-bas) des réponses basees sur les equations et 
                // affichera un message d'erreur dès que des réponses identiques sont produites,
                // alors que celle pour montrer l'evaluation represente des tentatives pour generer des reponses uniques.
                //

                while ($iteration < 25)
                {
                    $iteration++;

                    //
                    // Determiner les valeurs des coefficients variables. 
                    //

                    if ( ! empty($options['variables_valeurs']))
                    {
                        $variables_valeurs = $options['variables_valeurs'];

                        //
                        // Il faut verifier que toutes les variables sont presentes.
                        // Ceci est probablement inutile car les variables ont deja ete verifiees avant d'invoquer VIE.
                        //

                        foreach($variables as $label => $v)
                        {
                            if ( ! array_key_exists($label, $variables_valeurs))
                            {
                                return array(
                                    'code'     => 'VIE8811',
                                    'message'  => "Il manque des variables parmi celles fournies",
                                    'solution' => "À déterminer",
                                    'extra'    => array('question_id' => $question_id)
                                );
                            }
                        }
                    }
                    else
                    {
                        // $variables_valeurs = $this->determiner_coefficients_variables($variables);
                        $variables_valeurs = determiner_valeurs_variables($variables);
                    }

                    //
                    // Determiner le plus petit CS de l'operation pour ajuster la reponse de l'equation
                    //

                    foreach($variables_valeurs as $label => $v)
                    {
                        if (cs($v) < $plus_petit_cs)
                        {
                            $plus_petit_cs = cs($v);
                        }
                    }

                    //
                    // Il faut s'assurer que les variables sont des float avant de resoudre l'equation
                    //
                    // * Ceci causait un probleme avec question_id == 10864 (2020-11-28)
                    //

                    foreach($variables_valeurs as &$v)
                    {
                        if (strpos($v, 'E') !== FALSE)
                        {
                            $v = number_format($v, 50);
                        }
                    }

                    //
                    // Verifier l'unicite des reponses, en considerant les equations et l'ajustement des chiffres significatifs
                    //

                    $reponses_uniques = array();
                    $reponses_uniques_avec_cs = array();

                    foreach($reponses as $r)
                    {
                        if ($r['equation'])
                        {
                            $resolu = NULL;

                            try 
                            {
                                $resolu = Parser::solve($r['reponse_texte'], $variables_valeurs); 
                            } 
                            catch (Exception $e) 
                            {
                                return array(
                                    'code'     => 'VIE9900',
                                    'message'  => "Il y a un problème avec une équation, vérifiez le message suivant :" .
                                                  '<br /><br />' .
                                                  '<pre>' . 
                                                  $e->getMessage() . 
                                                  '<br /><br />' .
                                                  json_encode($variables_valeurs) . 
                                                  '</pre>',
                                    'solution' => "Veuillez désactiver cette question ou modifier votre équation pour régler l'erreur rencontrée.",
                                    'extra'    => 'question_id = ' . $question_id
                                );
                            } // try - catch

                            if ( ! in_array($resolu, $reponses_uniques))
                            {
                                $reponses_uniques[] = $resolu;
                            }
                            else
                            {
                                $reponses_uniques[] = $resolu;

                                return array(
                                    'code'     => 'VIE1190',
                                    'message'  => "Les équations d'une des questions générent des réponses identiques.",
                                    'solution' => "Veuillez désactiver cette question ou modifier vos équations pour générer des réponses uniques.",
                                    'extra'    => 'question_id = ' . $question_id . ', iteration = ' . $iteration . ', reponses = ' . $reponses_uniques
                                );
                            }

                            //
                            // Ajustement des chiffres significatifs
                            //

                            if ($r['cs'] != 99)
                            {
                                if ( ! empty($r['cs']))
                                {
                                    $resolu = cs_ajustement($resolu, $r['cs']);
                                }
                                elseif ($plus_petit_cs < 999)
                                {
                                    $resolu = cs_ajustement($resolu, $plus_petit_cs);
                                }

                                $resolu = str_replace('.', ',', $resolu);

                                if ( ! in_array($resolu, $reponses_uniques_avec_cs))
                                {
                                    $reponses_uniques_avec_cs[] = $resolu;
                                }
                                else
                                {
                                    $reponses_uniques_avec_cs[] = $resolu; // On l'ajoute dans le tableau pour l'afficher a l'ecran lors du message d'erreur.

                                    return array(
                                        'code'     => 'VIE1191',
                                        'message'  => "Les équations d'une des questions générent des réponses identiques suite à l'ajustement des chiffres significatifs.",
                                        'solution' => "Veuillez désactiver cette question ou modifier vos équations pour générer des réponses uniques.",
                                        'extra'    => array(
                                            'question_id'      => $question_id, 
                                            'iteration'        => $iteration, 
                                            'reponses'         => $reponses_uniques, 
                                            'reponses_avec_cs' => $reponses_uniques_avec_cs,
                                        )
                                    );
                                }
                            } // if $r['cs'] != 99
                        }

                    } // foreach $reponses

                    //
                    // Il n'est pas necessaire d'iterer lorsque les valeurs des variables sont fournies
                    //

                    if ( ! empty($options['variables_valeurs']))
                    {
                        break;
                    }

                } // while

            } // type 3 et 9
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Soumission deja envoyee?
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Verifier si la soumission a deja ete envoyee pour eviter les duplicats.
     *
     * -------------------------------------------------------------------------------------------- */
    function soumission_envoyee($options = array())
    {
        $options = array_merge(
        	array(
            ),
            $options
        );

        if (empty($options['evaluation_id']))
        {
            generer_erreur('SE888', "Il n'a pas été possible de déterminer le status d'envoi de cette évaluation, votre évaluation ne sera pas envoyée.");
            exit;
        }

        //
        // Le tableau de retour
        //

        $r = array(
            'status'       => TRUE,  // La requete a ete determinee sans erreur
            'envoyee'      => TRUE,  // L'etudiant a deja fait cette evaluation
            'non_terminee' => FALSE, // L'etudiant n'avait pas termine cette evaluation (l'enseignant a force l'envoi)
        );

        //
        // Si l'etudiant est NON INSCRIT
        //

        if ( ! $this->logged_in)
        {
            //
            // Verifier par numero de DA
            //

           if (array_key_exists('numero_da', $options) && ! empty($options['numero_da']))
           {
                $this->db->from   ('soumissions as s');
                $this->db->select ('s.non_terminee');
                $this->db->where  ('s.evaluation_id', $options['evaluation_id']);
                $this->db->where  ('s.numero_da',     $options['numero_da']);
                $this->db->where  ('s.semestre_id',   $this->semestre_id);   // Un etudiant qui recommence le meme cours un autre semestre pourrait le reenvoyer.
                $this->db->where  ('s.efface', 0);
                $this->db->limit  (1);

                $query = $this->db->get();

                if ($query->num_rows())
                {
                    $s = $query->row_array();

                    $r['non_terminee'] = $s['non_terminee'];

                    return $r;
                }
            }

            //
            // Verifier par session_id
            //

            $this->db->from   ('soumissions as s');
            $this->db->select ('s.non_terminee');
            $this->db->where  ('s.evaluation_id',        $options['evaluation_id']);
            $this->db->where  ('s.session_id',           $options['session_id'] ?: session_id());
            $this->db->where  ('s.soumission_epoch >',   date('U') - 60*60*24*3); // Dans les 3 derniers jours
            $this->db->where  ('s.semestre_id',          $this->semestre_id);
            $this->db->where  ('s.efface', 0);
            $this->db->limit  (1);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                log_alerte(
                    array(
                        'code'  => 'ESS781',
                        'desc'  => "Un étudiant a tenté d'envoyer son évaluation avec la même session qu'une session existante.",
                        'extra' => 'session_id=' . session_id()
                    )
                );

                $s = $query->row_array();

                $r['non_terminee'] = $s['non_terminee'];

                return $r;
            }
        }

        //
        // Si l'etudiant est INSCRIT
        //

        if ($this->est_etudiant)
        {
            $this->db->from   ('soumissions as s');
            $this->db->select ('s.non_terminee');
            $this->db->where  ('s.etudiant_id',   $this->etudiant_id);
            $this->db->where  ('s.evaluation_id', $options['evaluation_id']);
            $this->db->where  ('s.semestre_id',   $this->semestre_id); // Un etudiant qui recommence le meme cours un autre semestre pourrait le reenvoyer
            $this->db->where  ('s.efface', 0);
            $this->db->limit  (1);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $s = $query->row_array();

                $r['non_terminee'] = $s['non_terminee'];

                return $r;
            }
        }

        //
        // Cette evaluation n'a jamais ete envoyee.
        //

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Allouer points à une question (d'une evaluation)
     *
     * -------------------------------------------------------------------------------------------- */
    function allouer_points($post_data = array())
    {
        if ( ! array_key_exists('soumission_id', $post_data) ||
             ! array_key_exists('question_id', $post_data) ||
             ! array_key_exists('points', $post_data))
        {
            return FALSE;
        }

        $soumission_id = $post_data['soumission_id'];
        $question_id   = $post_data['question_id'];

        //
        // Extraire la soumission
        //

        $this->db->from ('soumissions as s');
        $this->db->where('s.soumission_id', $soumission_id);
        $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
		$this->db->where('s.efface', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return TRUE;
        }

        $soumission = $query->row_array();

        //
        // Extraire les donnees de la question deja enregistrees dans la soumission
        //

        $questions_data = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);

        //
        // Le tableau qui contient les donnees a mettre a jour
        //

        $data = array();

        //
        // La question n'a jamais ete corrigee
        //
        
        if ($questions_data[$question_id]['corrigee'] != TRUE)
        {
            $questions_data[$question_id]['corrigee']       = TRUE;
            $questions_data[$question_id]['points_obtenus'] = $post_data['points'];

            // (!)
            // Les points obtenus : les points obtenus aux questions
            // Les points total   : les points maximum possibles de la soumission considerant toutes les questions corrigees

            $data['points_obtenus'] = $soumission['points_obtenus'] + $post_data['points'];

            //
            // Cette question etant desormais corrigee, il faut ajouter ses points aux points total.
            //

            $data['points_total'] = $soumission['points_total'] + $questions_data[$question_id]['question_points'];
        }

        //
        // La question a deja ete corrigee
        //

        else
        {
            $points_obtenus_avant_q = $questions_data[$question_id]['points_obtenus'];

            //
            // Il y a changement de pointage a la correction
            //
            // Attention :
            // Si la grille avait ete utilisee, il se pourrait que les points sont identiques
            // mais cela est un hasard et ne doit pas etre considere.
            //

            if ($questions_data[$question_id]['grille'] || ($points_obtenus_avant_q != $post_data['points']))
            {
                $questions_data[$question_id]['points_obtenus'] = $post_data['points'];

                $data['points_obtenus'] = $soumission['points_obtenus'] - $points_obtenus_avant_q + $post_data['points'];
            }

            // 
            // Il y a annulation de la correction (en cliquant sur le meme bouton)
            //
            // Il faut reinitialiser la question comme si elle n'avait jamais ete corrigee
            //
    
            else
            {
                $questions_data[$question_id]['corrigee']       = FALSE;
                $questions_data[$question_id]['points_obtenus'] = 0;

                $data['points_obtenus'] = $soumission['points_obtenus'] - $points_obtenus_avant_q;

                //
                // Cette question n'etant plus corrigee, il faut enlever ses points aux points total.
                //

                $data['points_total'] = $soumission['points_total'] - $questions_data[$question_id]['question_points'];
            }    
        }

        //
        // Verifier s'il y a d'autres questions a corriger dans cette soumission
        //

        $data['corrections_terminees'] = 1;

        foreach($questions_data as $q)
        {
            if ( ! $q['corrigee'])
            {
                $data['corrections_terminees'] = 0;
                break;
            }
        }

        //
        // Ne pas/plus utiliser la grille, meme si des elements sont deja selectionnes
        //

        $questions_data[$question_id]['grille']           = FALSE;
        $questions_data[$question_id]['grille_affichage'] = FALSE;

        //
        // Preparer les donnees des questions
        //

        $data['questions_data_gz'] = gzcompress(json_encode($questions_data), 8);

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data); 

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        $this->kcache->remove_category('corrections');

        return $post_data['points'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Allouer points à une question (d'une evaluation) selon une grille personnalisee
     *
     * -------------------------------------------------------------------------------------------- */
    function allouer_points_grille($post_data = array())
    {
        if ( ! array_key_exists('soumission_id', $post_data) ||
             ! array_key_exists('question_id', $post_data) ||
             ! array_key_exists('points', $post_data))
        {
            return FALSE;
        }

        $soumission_id = $post_data['soumission_id'];
        $question_id   = $post_data['question_id'];

        //
        // Extraire les donnees de la soumission
        //

        $this->db->from ('soumissions as s');
        $this->db->where('s.soumission_id', $soumission_id);
        $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
		$this->db->where('s.efface', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return TRUE;
        }

        $soumission = $query->row_array();

        //
        // Extraire la grille et les elements
        //

        $gc = $this->Question_model->extraire_grilles_correction($question_id);
        $gc = $gc[$question_id];

        $elements   = $gc['elements'];
        $elements_s = array_key_exists('elements_s', $post_data) ? $post_data['elements_s'] : array();

        //
        // Extraire les donnees de la question deja enregistrees dans la soumission
        //

        $questions_data = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);

        //
        // Enregistrer les parametres d'affichage de la grille
        //

        $questions_data[$question_id]['grille_affichage'] = $gc['grille_affichage'];

        //
        // Si un enseignant selectionne un element deductif en premier, les points de la 
        // question seront negatifs. Il faut remettre les points a zero dans ce cas.
        //

        if ($post_data['points'] < 0)
        {
            $post_data['points'] = 0;
        }

        //
        // Le tableau qui contient les donnees a mettre a jour
        //

        $data = array();

        //
        // La question n'a jamais ete corrigee
        //

        if ( ! $questions_data[$question_id]['corrigee'])
        {
            $questions_data[$question_id]['corrigee']       = TRUE;
            $questions_data[$question_id]['points_obtenus'] = $post_data['points'];
            $questions_data[$question_id]['grille']         = TRUE;

            // (!)
            // Les points obtenus : les points obtenus aux questions
            // Les points total   : les points maximum possibles de la soumission considerant toutes les questions corrigees

            $data['points_obtenus'] = $soumission['points_obtenus'] + $post_data['points'];

            //
            // Cette question etant desormais corrigee, il faut ajouter ses points aux points total.
            //

            $data['points_total'] = $soumission['points_total'] + $questions_data[$question_id]['question_points'];
        }

        //
        // La question a deja ete corrigee
        //

        else
        {
            $points_obtenus_avant_q = $questions_data[$question_id]['points_obtenus'];

            //
            // Il n'y a aucun element selectionne.
            // Il faut reinitialiser la question comme si elle n'avait jamais ete corrigee
            //

            if (empty($elements_s))
            {
                $questions_data[$question_id]['corrigee']       = FALSE;
                $questions_data[$question_id]['points_obtenus'] = 0;
                $questions_data[$question_id]['grille']         = FALSE;

                $data['points_obtenus'] = $soumission['points_obtenus'] - $points_obtenus_avant_q;
                $data['points_total']   = $soumission['points_total'] - $questions_data[$question_id]['question_points'];
            }

            //
            // Caculer les nouveaux points selon les changements
            //

            else
            {
                $questions_data[$question_id]['corrigee']       = TRUE;
                $questions_data[$question_id]['points_obtenus'] = $post_data['points'];
                $questions_data[$question_id]['grille']         = TRUE;

                $data['points_obtenus'] = $soumission['points_obtenus'] - $points_obtenus_avant_q + $post_data['points'];
            }
        }

        //
        // Indiquer les elements de la grille ont ete selectionnes
        //

        if ( ! empty($soumission['grilles_data']))
        {
            $grilles_data = unserialize($soumission['grilles_data']);
        }
        else
        {
            $grilles_data = array(); 
        }

        $gc_data = array();

        if (array_key_exists('elements_s', $post_data))
        {
            $gc_data = array(
                'question_id'      => $question_id,
                'grille_affichage' => $gc['grille_affichage'],
                'elements'         => array()
            );

            foreach($elements as $e)
            {
                $d = array(
                    'element_id'       => $e['element_id'],
                    'element_desc'     => $e['element_desc'],
                    'element_type'     => $e['element_type'],
                    'element_ordre'    => $e['element_ordre'],
                    'element_pourcent' => $e['element_pourcent'],
                    'selectionne'      => in_array($e['element_id'], $elements_s) ? TRUE : FALSE
                );

                $gc_data['elements'][] = $d;
            }
        }

        $grilles_data[$question_id] = $gc_data;

        //
        // Verifier s'il y a d'autres questions a corriger dans cette soumission
        //

        $data['corrections_terminees'] = TRUE;

        foreach($questions_data as $q)
        {
            if ( ! $q['corrigee'])
            {
                $data['corrections_terminees'] = FALSE;
                break;
            }
        }

        //
        // Preparer les donnees
        //

        $data['questions_data_gz']     = gzcompress(json_encode($questions_data), 8);
        $data['grilles_data']          = serialize($grilles_data);

        //
        // Mettre a jour la soumission
        //

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data); 

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        $this->kcache->remove_category('corrections');

        return $post_data['points'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Allouer manuellement des points à une question
     *
     * -------------------------------------------------------------------------------------------- */
    function allouer_points_manuel($post_data = array())
    {
        if ( ! array_key_exists('soumission_id', $post_data) ||
             ! array_key_exists('question_id', $post_data) ||
             ! array_key_exists('points', $post_data))
        {
            return FALSE;
        }

        $soumission_id = $post_data['soumission_id'];
        $question_id   = $post_data['question_id'];

        //
        // Extraire les donnees de la soumission
        //

        $this->db->from ('soumissions as s');
        $this->db->where('s.soumission_id', $soumission_id);
        $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
		$this->db->where('s.efface', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return TRUE;
        }

        $soumission = $query->row_array();

        //
        // Extraire les donnees de la question deja enregistrees dans la soumission
        //

        $questions_data = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);

        //
        // Le tableau qui contient les donnees a mettre a jour
        //

        $data = array();

        //
        // La question n'a jamais ete corrigee
        //

        if ( ! $questions_data[$question_id]['corrigee'])
        {
            $questions_data[$question_id]['corrigee']       = TRUE;
            $questions_data[$question_id]['points_obtenus'] = $post_data['points'];

            // (!)
            // Les points obtenus : les points obtenus aux questions
            // Les points total   : les points maximum possibles de la soumission considerant toutes les questions corrigees

            $data['points_obtenus'] = $soumission['points_obtenus'] + $post_data['points'];
            $data['points_total']   = $soumission['points_total'] + $questions_data[$question_id]['question_points'];
        }

        //
        // La question a deja ete corrigee
        //

        else
        {
            $points_obtenus_avant_q = $questions_data[$question_id]['points_obtenus'];

            //
            // Il y a changement a la correction
            //
            // Attention :
            // Si la grille avait ete utilisee, il se pourrait que les points sont identiques
            // mais cela est un hasard et ne doit pas etre considere.
            //

            $questions_data[$question_id]['corrigee']       = TRUE;
            $questions_data[$question_id]['points_obtenus'] = $post_data['points'];

            $data['points_obtenus'] = $soumission['points_obtenus'] - $points_obtenus_avant_q + $post_data['points'];
        }

        //
        // Verifier s'il y a d'autes questions a corriger dans cette soumission
        //

        $data['corrections_terminees'] = TRUE;

        foreach($questions_data as $q)
        {
            if ( ! $q['corrigee'])
            {
                $data['corrections_terminees'] = FALSE;
                break;
            }
        }

        //
        // Ne pas utiliser la grille, meme si des elements sont deja selectionnes
        //

        $questions_data[$question_id]['grille']           = FALSE;
        $questions_data[$question_id]['grille_affichage'] = FALSE;

        //
        // Preparer les donnees des questions
        //

        $data['questions_data_gz'] = gzcompress(json_encode($questions_data), 8);

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data); 

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        $this->kcache->remove_category('corrections');

        return $post_data['points'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Resetter les corrections de la grille d'une question
     *
     * -------------------------------------------------------------------------------------------- */
    function reset_corrections_grille($post_data = array())
    {
        if ( ! array_key_exists('soumission_id', $post_data) ||
             ! array_key_exists('evaluation_id', $post_data) ||
             ! array_key_exists('question_id', $post_data)
           )
        {
            return FALSE;
        }

        $soumission_id = $post_data['soumission_id'];
        $evaluation_id = $post_data['evaluation_id'];
        $question_id   = $post_data['question_id'];

        //
        // Extraire les donnees de la soumission
        //

        $this->db->from ('soumissions as s');
        $this->db->where('s.soumission_id', $soumission_id);
        $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
		$this->db->where('s.efface', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return TRUE;
        }

        $soumission = $query->row_array();

        //
        // Extraire les donnees de la question deja enregistrees dans la soumission
        //

        $questions_data = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);

        //
        // Le tableau qui contient les donnees a mettre a jour
        //

        $data = array();

        //
        // Points presentement assignes a la question
        //
        
        $points_obtenus = $questions_data[$question_id]['points_obtenus'];
        $points         = $questions_data[$question_id]['question_points'];

        //
        // Ne pas utiliser la grille, meme si des elements sont deja selectionnes
        //

        $questions_data[$question_id]['corrigee']         = 0;
        $questions_data[$question_id]['points_obtenus']   = 0;
        $questions_data[$question_id]['grille']           = FALSE;
        $questions_data[$question_id]['grille_affichage'] = FALSE;

        //
        // Preparer les donnees des questions
        //

        $data['questions_data_gz']     = gzcompress(json_encode($questions_data), 8);
        $data['corrections_terminees'] = 0;
        $data['points_obtenus']        = $soumission['points_obtenus'] - $points_obtenus;
        $data['points_total']          = $soumission['points_total'] - $points;

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data); 

        /*
        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }
        */

        $this->kcache->remove_category('corrections');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Generer empreinte d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function generer_empreinte($soumission_reference)
    {
        return substr(hash('sha256', $soumission_reference . '1' . $this->config->item('empreinte_clef')[1]), 0, 12);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Enregistrer la soumission d'une evaluation d'un etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function enregistrer_soumission($post_data)
    {
        $this->load->helper('string');
        
		//
        // La surete permet d'eviter une boucle infinie lors de la generation d'un numero de reference unique.
		//

        $surete = 0;

        while($surete < 100)
        {
            $soumission_reference = strtolower(random_string('alpha', 8));

            //
            // Le numero de reference doit etre unique.
            //

            $this->db->from ('soumissions as s');
            $this->db->where('soumission_reference', $soumission_reference);
            $this->db->limit(1);

            $query = $this->db->get(); 

            if ( ! $query->num_rows())
            {
				// Le numero de reference genere est unique, sortons de cette boucle.
                break;
            }

            $surete++;

			log_alerte(
				array(
					'code'       => 'ESS099',
                    'desc'       => "Ce numéro de référence de soumission (" . $soumission_reference . ") existe déjà, il faut en générer un autre (" . $surete . ").",
                    'importance' => 7
				)
			);

            if ($surete > 90)
            {
                generer_erreur(
                    'ESS100', 
                    "L'incrémentation de sûreté a été activée en tentant de générer plus de 90 fois un numéro de référence unique pour une soumission.",
                    array('importance' => 9)
                );
                return;
            }
        }

        // 
        // Genererer l'empreinte
        //

        $empreinte = $this->generer_empreinte($soumission_reference);

		//
		// Determiner les informations du soumissionnaire lors de la soumission
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

        $donnees_soumissionnaire = array(
            'agent_string' => $this->agent->agent_string() ?: NULL,
            'fureteur_id'  => $this->Admin_model->generer_fureteur_id()
        );

        $data = array(
            'groupe_id'                 => $this->groupe_id,
            'semestre_id'               => $post_data['semestre_id'],
            'cours_id'                  => $post_data['cours_id'],
            'evaluation_id'             => $post_data['evaluation_id'],
            'enseignant_id'             => $post_data['enseignant_id'],
            'etudiant_id'               => $post_data['etudiant_id'],
            'session_id'                => $post_data['session_id'],
            'unique_id'                 => $this->Admin_model->generer_unique_id(),
            'adresse_ip'                => $_SERVER['REMOTE_ADDR'],
            'evaluation_reference'      => $post_data['evaluation_reference'],
            'soumission_reference'      => $soumission_reference,
            'empreinte'                 => $empreinte,
            'soumission_data'           => json_encode($donnees_soumissionnaire),
            'soumission_debut_epoch'    => $post_data['soumission_debut_epoch'],
            'soumission_date'           => date_humanize($this->now_epoch, TRUE),
            'soumission_epoch'          => $this->now_epoch,
            'prenom_nom'                => trim($post_data['prenom_nom']),
            'numero_da'                 => trim($post_data['numero_da']),
            'courriel'                  => $post_data['courriel'],
            'cours_data_gz'             => gzcompress(json_encode($post_data['cours']), 4)      ?: json_encode($post_data['cours']),
            'evaluation_data_gz'        => gzcompress(json_encode($post_data['evaluation']), 4) ?: json_encode($post_data['evaluation']),
            'questions_data_gz'         => gzcompress(json_encode($post_data['questions']), 8)  ?: json_encode($post_data['questions']),
            'images_data_gz'            => gzcompress(json_encode($post_data['images']), 8)     ?: json_encode($post_data['images']),
            'documents_data_gz'         => gzcompress(json_encode($post_data['documents']), 8)  ?: json_encode($post_data['documents']),
            'extra_data'                => empty($post_data['extra']) ? NULL : json_encode($post_data['extra']),
            'points_obtenus'            => $post_data['points_obtenus'],
            'points_total'              => $post_data['points_total'],
            'points_evaluation'         => $post_data['points_evaluation'],
            'permettre_visualisation'   => $post_data['permettre_visualisation'],
            'corrections_terminees'     => $post_data['corrections_terminees'],
            'corrections_manuelles'     => $post_data['corrections_manuelles'],
            'version'                   => 2
        ); 

        $this->db->insert('soumissions', $data);

        $soumission_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        //
        // Assigner les documents des questions de type 10 a cette soumission.
        //

        if ( ! empty($post_data['questions_ids_docs']))
        {
            foreach($post_data['questions_ids_docs'] as $question_id)
            {
                $this->Document_model->assigner_documents_soumission(
                    array(
                        'question_id'          => $question_id,
                        'evaluation_id'        => $post_data['evaluation_id'],
                        'evaluation_reference' => $post_data['evaluation_reference'],
                        'soumission_id'        => $soumission_id,
                        'soumission_reference' => $soumission_reference,
                        'session_id'           => $post_data['session_id']
                    )
                );
            }
        }

        if ( ! $this->logged_in) 
        {
            // Les enseignants pourraient envoyer une soumission pour tester avec la previsualisation.
            // Ne pas les enregistrer comme des etudiants.

            //
            // Enregistrer, de facon encryptee, les informations sur l'identite de l'etudiant dans un cookie,
            // pour etre en mesure de l'identifier lorsqu'il se connecte au site.
            //

            $etudiant = array(
                'prenom_nom' => $data['prenom_nom'],
                'cours_data' => gzuncompress($data['cours_data_gz']),
                'numero_da'  => $data['numero_da']
            );

            set_cookie('adata', $this->encryption->encrypt(serialize($etudiant)), 60*60*24*999);
        }

        //
        // Effacer le(s) chargement(s) de cet etudiant
        //

        if ( ! $this->est_enseignant)
        {
            $c_data = array(
                'efface'       => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            );

            $this->db->where ('evaluation_reference', $post_data['evaluation_reference']);
            $this->db->where ('epoch >=', $post_data['ajout_epoch']);

            if ($this->est_etudiant)
            {
                $this->db->where    ('etudiant_id', $post_data['etudiant_id']);
                $this->db->or_where ('session_id', $post_data['session_id']);
            }
            else
            {
                $this->db->where ('session_id', $post_data['session_id']);
            }

            $this->db->update('evaluations_securite_chargements', $c_data);
        }

        $this->kcache->remove_category('corrections', $post_data['enseignant_id']);

        // return $soumission_reference;

        return array(
            'soumission_id'        => $soumission_id,
            'soumission_reference' => $soumission_reference
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Résultats cumules session
     *
     * Combien y a-t-il de resultats pour cette session?
     *
     * -------------------------------------------------------------------------------------------- */
    function resultats_cumules_session($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id' => $this->enseignant_id,
                // 'semestre_id'   => $this->enseignant['semestre_id'] // le semestre actif de l'enseignant
                // 'semestre_id'   => $this->semestre_id // le semestre actif de l'enseignant
                'semestre_id'   => $this->enseignant['semestre_id'] ?? $this->semestre_id
           ),
           $options
        );

        $this->db->from ('soumissions as s');

        $this->db->where('s.enseignant_id', $options['enseignant_id']);
        $this->db->where('s.semestre_id', $options['semestre_id']);
        $this->db->where('s.corrections_terminees', 1);
		$this->db->where('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->num_rows();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Résultats cumules total
     *
     * Combien y a-t-il de resultats au total pour cet enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function resultats_cumules_total($options = array())
    {
        $this->db->from ('soumissions as s');

        $this->db->where('s.enseignant_id', $options['enseignant_id']);
        $this->db->where('s.corrections_terminees', 1);
		$this->db->where('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
        
        return $query->num_rows();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Corrections en attente
     *
     * Est-ce qu'il y a des corrections en attente? 
     * OUI = Retourner le nombre.
     * NON = FALSE.
     *
     * -------------------------------------------------------------------------------------------- */
    function corrections_en_attente($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id' => NULL, // obligatoire
                'groupe_id'     => NULL,
                'semestre_id'   => NULL
           ),
           $options
        );

        if (empty($options['enseignant_id']))
        {
            return '999999';
        }

        $cache_key = __FUNCTION__ . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->from ('soumissions as s');

        $this->db->where('s.enseignant_id', $options['enseignant_id']);

        if ($options['groupe_id'] !== NULL)
        {
            $this->db->where ('s.groupe_id', $options['groupe_id']);
        }

        if ($options['semestre_id'])
        {
            $this->db->where ('s.semestre_id', $options['semestre_id']);
        }

        $this->db->where ('s.corrections_terminees', 0);
		$this->db->where ('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            $this->kcache->save($cache_key, -1, 'corrections', 30);
            return -1;
        }

        $corrections_en_attente = $query->num_rows();
        
        $this->kcache->save($cache_key, $corrections_en_attente, 'corrections', 30);

        return $corrections_en_attente;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Sous-domaine d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function sous_domaine_soumission($reference)
    {
        if ( ! ctype_alpha($reference))
        {
            return FALSE;
        }

        $this->db->from     ('soumissions as s, groupes as g');
        $this->db->select   ('g.sous_domaine');
        $this->db->where    ('s.groupe_id = g.groupe_id');
        $this->db->where    ('s.soumission_reference', $reference);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array()['sous_domaine'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Evaluations_privees_total
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluations_privees_total($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id' => $this->enseignant_id,
           ),
           $options
        );

        $this->db->from ('evaluations as e');

        $this->db->where('e.enseignant_id', $options['enseignant_id']);
        $this->db->where('e.public', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }
        
        return $query->num_rows();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumission($soumission_id, $options = array())
    {
    	$options = array_merge(
            array(
                'extraire_gz' => FALSE
           ),
           $options
        );

		$this->db->select ('s.*');
        $this->db->from   ('soumissions as s, etudiants as e');
        $this->db->where  ('s.soumission_id', $soumission_id);
		$this->db->where  ('s.efface', 0);

		$this->db->where  ('s.etudiant_id = e.etudiant_id');
		$this->db->where  ('e.efface', 0);

        if ($this->enseignant['privilege'] < 90)
        {
            $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $soumission = $query->row_array();

        if ($options['extraire_gz'])
        {
            if ( ! empty($soumission['cours_data_gz']))
                $soumission['cours_data'] = json_decode(gzuncompress($soumission['cours_data_gz']), TRUE);

            if ( ! empty($soumission['evaluation_data_gz']))
                $soumission['evaluation_data'] = json_decode(gzuncompress($soumission['evaluation_data_gz']), TRUE);

            if ( ! empty($soumission['questions_data_gz']))
                $soumission['questions_data'] = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);
        }

        return $soumission;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une soumission par reference
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumission_par_reference($soumission_reference, $options = array())
    {
    	$options = array_merge(
            array(
                'tous_les_enseignants' => FALSE
           ),
           $options
        );

        $this->db->from ('soumissions as s');
        $this->db->where('s.soumission_reference', $soumission_reference);
		$this->db->where('s.efface', 0);

        if ($this->enseignant['privilege'] < 90 && ! $options['tous_les_enseignants'])
        {
            $this->db->where('s.enseignant_id', $this->enseignant['enseignant_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les soumissions selectionnees
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumissions_selectionnees($options = array())
    {
    	$options = array_merge(
            array(
                'soumission_ids'        => array(),
                'corrections_terminees' => 1
           ),
           $options
        );

        if ( ! is_array($options['soumission_ids']) || empty($options['soumission_ids']))
        {
            return array();
        } 

        $this->db->from   ('soumissions as s');
        $this->db->where  ('s.enseignant_id', $this->enseignant_id);
        $this->db->where  ('s.corrections_terminees', 1);
        $this->db->where  ('s.efface', 0);
        $this->db->where_in ('s.soumission_id', $options['soumission_ids']);
        $this->db->order_by ('s.soumission_epoch', 'desc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'soumission_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire toutes les soumissions
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_toutes_soumissions()
    {
        $this->db->from  ('soumissions');
        $this->db->where ('efface', 0);

        $query = $this->db->get();

        return $query->result_array(); 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les soumissions
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumissions($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id'         => NULL,
                'semestre_id'           => $this->semestre_id,
                'evaluation_id'         => NULL,                // chercher seulement les soumissions de cette evaluation (stats)
                'corrections_terminees' => 1,                	// chercher seulement les soumissions sont les corrections sont terminees
                'epoch'                 => NULL,                // chercher a partir d'une date
                'ordre'                 => 'remise',            // remise (== remise_asc), remise_desc
                'select'                => NULL                 // 'resultats'
           ),
           $options
       );

        if (empty($options['enseignant_id']))
		{
            return array();
        }

        $from = 'soumissions as s, etudiants as e';

        $this->db->from  ($from);

        if (empty($options['select']))
        {
            $this->db->select('s.*');
        }
        elseif ($options['select'] == 'resultats')
        {
            /*
             * Ceci ne sert a rien car les resultats ont besoin de presque tous les champs, incluant
             * cours_data et evaluation_data. 
             *
             */
            /*
            $this->db->select(
                's.soumission_id, s.evaluation_id, s.enseignant_id, s.groupe_id, s.cours_id, s.semestre_id' .
                ', s.soumission_reference, s.soumission_debut_epoch, s.soumission_epoch, s.soumission_date' .
                ', s.prenom_nom, s.numero_da, s.points_obtenus, s.points_total, s.corrections_terminees, s.permettre_visualisation' .
                ', s.cours_data, s.evaluation_data'
            ); 
            */

            $this->db->select('s.*');
        }

        if ($options['ordre'] == 'remise' || $options['ordre'] == 'remise_asc')
        {
            $this->db->order_by('s.soumission_epoch', 'asc');
        }

        if ($options['ordre'] == 'remise_desc')
        {
            $this->db->order_by('s.soumission_epoch', 'desc');
        }

        if ( ! empty($options['epoch']))
		{
            $this->db->where('soumission_epoch >', $options['epoch']);
        }

        $this->db->where ('s.enseignant_id', $options['enseignant_id']);
		$this->db->where ('s.efface', 0);

		$this->db->where ('s.etudiant_id = e.etudiant_id');
		$this->db->where ('e.efface', 0);

        if ($options['semestre_id'] !== NULL)
        {
            $this->db->where ('s.semestre_id', $options['semestre_id']);
        }

        if ($options['corrections_terminees'] !== NULL)
        {
            $this->db->where('s.corrections_terminees', $options['corrections_terminees']);
        }

        if ($options['evaluation_id'] !== NULL)
        {
            $this->db->where('s.evaluation_id', $options['evaluation_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
		}

        return array_keys_swap($query->result_array(), 'soumission_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une soumission (par reference)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumission_reference($reference)
    {
        $this->db->from ('soumissions as s');

        $this->db->where('s.soumission_reference', $reference);
		$this->db->where('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_soumission($soumission_id)
    {
		$enseignant_id = $this->enseignant['enseignant_id'];

		//
		// Extraire la soumission pour verifier les permissions et le status
		//

        $this->db->from  ('soumissions as s');
		$this->db->where ('s.enseignant_id', $enseignant_id);
        $this->db->where ('s.soumission_id', $soumission_id);
        $this->db->where ('s.efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $soumission = $query->row_array();

        $data = array(
            'efface'       => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

		$this->db->where ('soumission_id', $soumission_id);
		$this->db->update('soumissions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        //
        // Effacer les soumissions partagees
        //

        $data = array(
            'efface'       => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

		$this->db->where ('soumission_id', $soumission_id);
		$this->db->update('soumissions_partagees', $data);

        //
        // Effacer les documents des etudiants associes a cette soumission
        //

        $this->Document_model->effacer_documents_soumission_terminee($soumission_id, array('unlink' => TRUE));

        //
        // Effacer les traces
        //

        if ( ! empty($soumission['etudiant_id']))
        {
            $this->db->where  ('soumission_reference', $soumission['soumission_reference']);
            $this->db->where  ('etudiant_id', $soumission['etudiant_id']);

            $this->db->delete ('etudiants_traces');

            /*
            $this->db->where ('efface', 0);
            $this->db->update('etudiants_traces', 
                array(
                    'efface' => 1
                )
            );
            */
        }
        else
        {
            $this->db->where  ('soumission_reference', $soumission['soumission_reference']);
            $this->db->where  ('session_id', $soumission['session_id']);
            $this->db->where  ('etudiant_id', NULL); 

            $this->db->delete ('etudiants_traces');
        }

        $this->kcache->remove_category('soumissions');

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Changer la visibilite d'une soumission corrigee
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_visibilite($soumission_ids, $operation, $sans_reponses = 0)
    {
		$enseignant_id = $this->enseignant['enseignant_id'];

		//
		// Verifier la permission et le status
		//

        $this->db->from    ('soumissions as s');
		$this->db->where   ('s.enseignant_id', $enseignant_id);
        $this->db->where_in('s.soumission_id', $soumission_ids);
		$this->db->where   ('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $soumissions    = array_keys_swap($query->result_array(), 'soumission_id');
		$soumission_ids = array_keys($soumissions);

		$data = array();

		foreach($soumission_ids as $soumission_id)
        {
            $permettre_visualisation = ($operation == 'visible' ? 1 : 0);

            if ($permettre_visualisation && $sans_reponses)
            {
                $permettre_visualisation = 2;
            }

			$data[] = array(
				'soumission_id'           => $soumission_id,
                'permettre_visualisation' => $permettre_visualisation
			);
		}

		if ( ! empty($data))
		{
			$this->db->update_batch('soumissions', $data, 'soumission_id');
		}

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rendre visible une soumission (evaluation corrigee)
     *
     * -------------------------------------------------------------------------------------------- */
    function rendre_visible($soumission_ids, $date = NULL, $heure = NULL)
    {
		//
		// Verifier la permission et le status
        //

        $this->db->from    ('soumissions as s');
        $this->db->select  ('s.soumission_id');
		$this->db->where   ('s.enseignant_id', $this->enseignant_id);
		$this->db->where   ('s.efface', 0);
        $this->db->where_in('s.soumission_id', $soumission_ids);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $s = $query->result_array();

        if (count($s) != count($soumission_ids))
        {
            return FALSE;
        }

        //
        // Expiration
        //

        $epoch = 0;

        if ( ! empty($date))
        {
            $format_date = $date;

            if ( ! empty($heure))
            {
                $format_date .= ' ' . $heure . ':00';
            }
            else
            {
                $format_date .= ' ' . '00:00:00';
            }

            $epoch = date_epochize_plus($format_date);
        }

        $data = array(
            'permettre_visualisation' => 1,
            'permettre_visualisation_expiration' => $epoch
        );

		if ( ! empty($data))
        {
            $this->db->where_in('soumission_id', $soumission_ids);
			$this->db->update  ('soumissions', $data);
		}

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rendre invisible une soumission (evaluation corrigee)
     *
     * -------------------------------------------------------------------------------------------- */
    function rendre_invisible($soumission_ids)
    {
		//
		// Verifier la permission et le status
		//

        $this->db->from    ('soumissions as s');
		$this->db->where   ('s.enseignant_id', $this->enseignant_id);
        $this->db->where_in('s.soumission_id', $soumission_ids);
		$this->db->where   ('s.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $s = $query->result_array();

        if (count($s) != count($soumission_ids))
        {
            return FALSE;
        }

        $data = array(
            'permettre_visualisation'            => 0,
            'permettre_visualisation_expiration' => 0
        );

		if ( ! empty($data))
        {
            $this->db->where_in('soumission_id', $soumission_ids);
			$this->db->update  ('soumissions', $data);
		}

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le titre d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_titre($evaluation_id, $post_data)
    {
        //
        // Verifier que l'evaluation peut etre modifiee par cet enseignant.
        //

        $this->db->from  ('evaluations as e, cours as c');
        $this->db->where ('e.evaluation_id', $evaluation_id);
        $this->db->where ('e.efface', 0);
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission de modifier cette evaluation.
            // Cet enseignant n'appartient pas au groupe qui est le proprietaire de cette question.

            return FALSE;
        }

        //
        // Modifier le titre de l'evaluation
        //
        
        $data = array(
            'evaluation_titre'  => htmlentities(mb_strimwidth(strip_tags($post_data['evaluation_titre']), 0, 153, '...'))
        );

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer l'ordre d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_ordre($evaluation_id, $ordre)
    {
        $data = array(
            'ordre' => $ordre
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('efface', 0);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter une variable
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_variable($evaluation_id, $post_data)
    {
        if (empty($evaluation = $this->extraire_evaluation($evaluation_id)))
        {
            // Cette evaluation n'existe pas.
            return FALSE;
        }

        //
        // Verifier que le maximum est egal ou plus grand que le minimum.
        //

        if ($post_data['variable_maximum'] < $post_data['variable_minimum'])
        {
            echo json_encode('Le maximum doit etre egal ou plus grand que le minimum.');
            return FALSE;
        }

        //
        // Verifier que la variable n'est pas deja utilisee.
        //

        $variables = $this->extraire_variables($evaluation_id);

        if ( ! empty($variables))
        {
            if (array_key_exists($post_data['variable_nom'], $variables))
            {
                echo json_encode('Cette variable existe.');
                return FALSE;
            }
        }

        //
        // Description
        //

        $variable_desc = NULL;

        if (array_key_exists('variable_desc', $post_data) && ! empty($post_data['variable_desc']))
        {
            $variable_desc = json_encode(trim($post_data['variable_desc']));
        }

        //
        // L'empreinte md5 sert a eviter le tamponnage par les etudiants en modifiant la valeur des variables dans la session.
        // Ceci est peut-etre pas necessaire si la session est encryptee.
        //

        $data = array(
            'evaluation_id' => $evaluation_id,
            'label'         => $post_data['variable_nom'],
            'minimum'       => $post_data['variable_minimum'],
            'maximum'       => $post_data['variable_maximum'],
            'decimales'     => $post_data['variable_decimals'],
            'ns'            => $post_data['variable_ns'] ?: 0,
            'cs'            => $post_data['variable_cs'] ?: 0,
            'variable_desc' => $variable_desc,
            'modification_epoch' => $this->now_epoch
        );

        $this->db->insert('variables', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier une variable
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_variable($variable_id, $post_data)
    {
        //
        // Verifier que la variable peut etre modifiee par l'enseignant.
        //
        $variable = $this->extraire_variable($variable_id);

        if (empty($variable))
        {
            return FALSE;
        }
            
        $data = array();

        // Encoder la description

        if (array_key_exists('variable_desc', $post_data))
        {
            $post_data['variable_desc'] = trim($post_data['variable_desc']);

            if (empty($post_data['variable_desc']))
            {
                $post_data['variable_desc'] = NULL;
            }
            else
            {
                $post_data['variable_desc'] = json_encode($post_data['variable_desc']);
            }
        }

        foreach($post_data as $field => $value)
        {
            if (in_array($field, array('variable_id', 'evaluation_id', 'variable_nom')))
                continue;

            if ($post_data[$field] !== $variable[$field])
            {
                $data[$field] = $post_data[$field];
            }
        } 

        if (empty($data))
        {
            // Aucun changement detecte
            return TRUE;
        }

        $data['modification_epoch'] = $this->now_epoch;

        $this->db->where('variable_id', $variable_id);
        $this->db->update('variables', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une variable
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_variable($variable_id, $evaluation_id)
    {
        //
        // Verifier permission
        //
        // @TODO

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('variable_id', $variable_id);
        $this->db->delete('variables');

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire variable
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_variable($variable_id)
    {
        $this->db->from  ('variables as v, evaluations as ev');
        $this->db->where ('v.variable_id', $variable_id);
        $this->db->where ('v.efface', 0);
        $this->db->where ('v.evaluation_id = ev.evaluation_id');
        $this->db->where ('ev.groupe_id', $this->enseignant['groupe_id']);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire variables
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_variables($evaluation_id)
    {
        $this->db->from  ('variables as v');
        $this->db->where ('v.efface', 0);
        $this->db->where ('v.evaluation_id', $evaluation_id);

        $this->db->order_by('v.label', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'label');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier la description
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_description($evaluation_id, $post_data)
    {
        //
        // Verifier que l'evaluation peut etre modifiee par cet enseignant.
        //

        $this->db->from  ('evaluations as e, cours as c');
        $this->db->where ('e.evaluation_id', $evaluation_id);
        $this->db->where ('e.efface', 0);
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission de modifier cette evaluation.
            // Cet enseignant n'appartient pas au groupe qui est le proprietaire de cette question.

            return FALSE;
        }

        //
        // Modifier la description
        //
        
        $data = array(
            'evaluation_desc' => _html_in($post_data['description'])
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer la description
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_description($evaluation_id)
    {
        //
        // Verifier permission
        //
        // @TODO

        $data = array(
            'evaluation_desc' => NULL
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier les instructions
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_instructions($evaluation_id, $post_data)
    {
        //
        // Verifier que l'evaluation peut etre modifiee par cet enseignant.
        //

        $this->db->from  ('evaluations as e, cours as c');
        $this->db->where ('e.evaluation_id', $evaluation_id);
        $this->db->where ('e.efface', 0);
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission de modifier cette evaluation.
            // Cet enseignant n'appartient pas au groupe qui est le proprietaire de cette question.

            return FALSE;
        }

        //
        // Modifier les instructions
        //

        $data = array(
            'instructions'  => _html_in($post_data['instructions'], array('json' => FALSE))
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer les instructions
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_instructions($evaluation_id)
    {
        //
        // Verifier permission
        //
        // @TODO

        $data = array(
            'instructions' => NULL
        );

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Les dernieres soumissions
     *
     * -------------------------------------------------------------------------------------------- */
    function dernieres_soumissions($enseignant_id = NULL, $options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id'   => $this->groupe_id,
                'limite'      => 200, // Il y a trop de soumissions pour pouvoir toutes les charger en memoire.
                'semestre_id' => NULL,
                'admin'       => FALSE
           ),
           $options
        );

        //
        // S'il n'y a pas de semestre actif, ne pas retourner les dernieres soumissions.
        //

        if (empty($options['semestre_id']) && ! $options['admin'])
        {
           return array(); 
        }

        $cache_key = __FUNCTION__ . $enseignant_id . md5(serialize($options)); 

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->from ('soumissions as s');

        /*
         * Je n'ai pas constate de difference de vitesse en utilisant le code suivant (2020-04-07).
         *
        $this->db->select ('s.soumission_id, s.soumission_date, s.soumission_epoch, 
                            s.groupe_id, s.semestre_id, s.cours_id, s.evaluation_id, s.enseignant_id, s.etudiant_id, s.evaluation_reference,
                            s.soumission_reference, s.empreinte, s.prenom_nom, s.numero_da,
                            s.soumission_debut_epoch, s.cours_data_gz,
.                           s.points_obtenus, s.points_total, s.points_evaluation, s.corrections_terminees,
                            s.permettre_visualisation, s.vues');
        */

        if ($options['groupe_id'] !== NULL)
        {
            $this->db->where ('s.groupe_id', $options['groupe_id']);
        }

        if ( ! empty($enseignant_id))
        {
            $this->db->where ('s.enseignant_id', $enseignant_id);
        }

        if ( ! empty($options['semestre_id']))
        {
            $this->db->where ('s.semestre_id', $options['semestre_id']);
        }

        $this->db->where    ('s.efface', 0);
        $this->db->order_by ('s.soumission_epoch', 'desc');

        if ( ! empty($options['limite']))
        {
            $this->db->limit ($options['limite']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array(); 
        }
        
        $dernieres_soumissions = $query->result_array();

        //
        // Extraire le nom des partenaires de laboratoire des soumissions
        //

        $etudiant_ids = array();

        foreach($dernieres_soumissions as $s)
        {
            if ( ! empty($s['lab_etudiant2_id']))
            {
                $etudiant_ids[] = $s['lab_etudiant2_id'];
            }

            if ( ! empty($s['lab_etudiant3_id']))
            {
                $etudiant_ids[] = $s['lab_etudiant3_id'];
            }
        }

        if ( ! empty($etudiant_ids))
        {
            $etudiants = $this->Etudiant_model->extraire_etudiants(array('etudiant_ids' => $etudiant_ids));

            foreach($dernieres_soumissions as $id => $s)
            {
                if ( ! empty($s['lab_etudiant2_id']))
                {
                    $e_id = $s['lab_etudiant2_id'];
                    $dernieres_soumissions[$id]['lab_etudiant2_nom'] = $etudiants[$e_id]['prenom'] . ' ' . $etudiants[$e_id]['nom'];
                }

                if ( ! empty($s['lab_etudiant3_id']))
                {
                    $e_id = $s['lab_etudiant3_id'];
                    $dernieres_soumissions[$id]['lab_etudiant3_nom'] = $etudiants[$e_id]['prenom'] . ' ' . $etudiants[$e_id]['nom'];
                }
            }
        }

        // $this->kcache->save($cache_key, $dernieres_soumissions, 'soumissions', 60);

        return $dernieres_soumissions;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Les statistiques des soumissions
     *
     * -------------------------------------------------------------------------------------------- */
    function soumissions_stats($options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id'   => NULL,
                'semestre_id' => NULL
           ),
           $options
        );

        $this->db->from   ('soumissions as s');
        $this->db->select ('soumission_id, soumission_epoch');

        if ( ! empty($options['groupe_id']))
        {
            $this->db->where ('s.groupe_id', $options['groupe_id']);
        }

        if ( ! empty($options['semestre_id']))
        {
            $this->db->where ('s.semestre_id', $options['semestre_id']);
        }

		$this->db->where    ('s.efface', 0);
        $this->db->order_by ('s.soumission_epoch', 'desc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array(); 
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Incrementer le nombre de vues d'une soumission corrigee
     *
     * -------------------------------------------------------------------------------------------- */
    function incrementer_vues_soumission($soumission_id)
    {
        $this->db->where('soumission_id', $soumission_id);
		$this->db->where('efface', 0);
        $this->db->set('vues', 'vues+1', FALSE);
        $this->db->update('soumissions');

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Log de la consultation d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function log_soumission_consultation($options = array())
    {
        //
        // Verifier les champs obligatoires
        //

        $champs_obligatoires = array('soumission_id', 'soumission_reference', 'etudiant_id', 'enseignant_id');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options))
            {
                log_alerte(
                    array(
                        'code'       => 'LOGSC',
                        'desc'       => "Un champ obligatoire est manquant lors du log de la soumission consultée.",
                        'extra'      => NULL,
                        'importance' => 2
                    )
                );

                return;
            }
        }

        //
        // Detecter l'identite de l'etudiant, si necessaire
        //

        $identite = NULL;

        if ( ! $this->est_etudiant)
        {
            $etudiant_data = get_cookie('adata');

            if ( ! empty($etudiant_data))
            {
                $etudiant_serialized = $this->encryption->decrypt($etudiant_data);
                $etudiant_unserialized = unserialize($etudiant_serialized);
                $cours_data = (array) json_decode($etudiant_unserialized['cours_data']);

                $identite = @$etudiant_unserialized['prenom_nom'] . ';' 
                    . @$etudiant_unserialized['numero_da'] . ';'
                    . @$cours_data['cours_code_court'] . ';'
                    . @$cours_data['semestre_code'];
            }
        }

        //
        // Enregistrer les informations
        //

        $data = array(
            'soumission_id'            => $options['soumission_id'],
            'soumission_reference'     => $options['soumission_reference'],
            'etudiant_id'              => $options['etudiant_id'],
            'consulte_par_etudiant_id' => $this->est_etudiant ? $this->etudiant_id : NULL,
            'enseignant_id'            => $options['enseignant_id'],
            'identite'                 => $this->est_etudiant ? NULL : $identite,
            'data'                     => json_encode(
                                               array(
                                                   'adresse_ip'    => $_SERVER['REMOTE_ADDR'],
                                                   'referencement' => $this->agent->referrer() // sert a detecter si le lien provient de Facebook ou ailleurs
                                               )
                                          ),
            'date'                     => date_humanize($this->now_epoch, TRUE),
            'epoch'                    => $this->now_epoch
        );

        $this->db->insert('soumissions_consultees', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Les dernieres corrections consultees
     *
     * -------------------------------------------------------------------------------------------- */
    function corrections_consultees($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id' => $this->enseignant_id,
                'groupe_id'     => $this->groupe_id,
                'limite'        => NULL,
                'semestre_id'   => NULL
           ),
           $options
        );

        $this->db->from   ('soumissions_consultees as sc, soumissions as s');
        $this->db->select ('sc.*, s.semestre_id');

        $this->db->where  ('sc.soumission_id = s.soumission_id');
        $this->db->where  ('s.groupe_id', $options['groupe_id']);

        //
        // Il y a seulement l'admin qui peut voir les consultations des soumissions de tous les enseignants,
        // autrement l'enseignant peut seulement voir les consultations de ses soumissions.
        //

        if ( ! (empty($options['enseignant_id']) && $this->enseignant['privilege'] > 89))
        {
            $this->db->where('sc.enseignant_id', $options['enseignant_id']);
        }
            
        if ( ! empty($options['semestre_id']))
        {
            $this->db->where('s.semestre_id', $options['semestre_id']);
        }

        $this->db->order_by('sc.epoch', 'desc');

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $consultations = $query->result_array();
        $consultations = array_keys_swap($consultations, 'id');

        //
        // Extraire les soumission_ids de ces consultations
        //

        $consultations_limitees = array(); // conserver seulement les consultations avant la limite atteinte
        $soumission_ids = array();

        $comptage = 1;

        foreach ($consultations as $c)
        {
            // Ne pas montrer deux fois la consultation de la meme soumission, 
            // car le nombre de vues permet de connaitre cette information.

            if (in_array($c['soumission_id'], $soumission_ids))
            {
                // unset($consultations[$c['id']]); // enlever les doublons
                continue;
            }

            $soumission_ids[] = $c['soumission_id'];
            $consultations_limitees[$c['id']] = $c;

            $comptage++;

            if ( ! empty($options['limite']) && $options['limite'] < $comptage)
            {
                break;
            }
        } 

        $consultations = $consultations_limitees;

        if (empty($soumission_ids))
        {
            return array();
        }

        //
        // Extraire les informations des soumissions consultees
        //

        $soumissions = array();

        $this->db->from     ('soumissions as s, evaluations as e, cours as c, semestres as sem, enseignants as en');
        $this->db->select   ('s.*, c.cours_code_court, sem.semestre_code, en.prenom, en.nom, e.groupe_id');

		$this->db->where    ('s.efface', 0);
        $this->db->where_in ('s.soumission_id', $soumission_ids);

        $this->db->where    ('s.semestre_id = sem.semestre_id');
        $this->db->where    ('s.evaluation_id = e.evaluation_id');
        $this->db->where    ('e.enseignant_id = en.enseignant_id');
        $this->db->where    ('e.cours_id = c.cours_id');
            
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $soumissions = $query->result_array();
        $soumissions = array_keys_swap($soumissions, 'soumission_id');

        $corrections = array(); // Le tableau a retourner.

        foreach($consultations as $c)
        {
            // La soumission qui correspond a cette consultation.

			// Ceci empeche l'erreur suivante :
			// Si un etudiant a consulte sa correction et que sa soumission a ete ensuite effacee par l'enseignant,
			// la consultation n'a pas ete effacee de la base de donnees ce qui cause une erreur
			// car la soumission n'a pu etre trouvee.
			if ( ! array_key_exists($c['soumission_id'], $soumissions))
				continue;

            $s = $soumissions[$c['soumission_id']];

			// Ne pas presenter les consultations des autres groupes.
			if ($s['groupe_id'] != $this->groupe_id)
				continue;			

            //
            // Extraire l'identite de la personne qui a fait cette consultation.
            //
            // Cette information n'est pas toujours disponible car elle provient
            // de la session lors de la consultation.
            //

            $identite = NULL;

            if ( ! empty($c['identite']))
            {
                if (strpos($c['identite'], ';') !== FALSE)
                {
                    if (preg_match('/(.*);(.*);(.*);(.*)/', $c['identite'], $matches))
                    {
                        $identite = $matches[1];
                    }
                }
            }

            //
            // Ajustements aux points totaux obtenus
            //

            $ajustements = ! empty($s['ajustements_data']) ? unserialize($s['ajustements_data']) : array();
            $points_obtenus = array_key_exists('total', $ajustements) ? $ajustements['total'] : $s['points_obtenus']; 

            $data = array(
                'prenom_nom'            => $s['prenom_nom'],
                'numero_da'             => $s['numero_da'],
                'etudiant_id'           => $s['etudiant_id'],
                'vues'                  => $s['vues'],
                'identite_prenom_nom'   => $identite, 
                'soumission_id'         => $s['soumission_id'],
                'soumission_reference'  => $s['soumission_reference'],
                'cours_code_court'      => $s['cours_code_court'],
                'semestre_code'         => $s['semestre_code'],
                'soumission_epoch'      => $s['soumission_epoch'],
                'enseignant'            => $s['prenom'] . ' ' . $s['nom'],
                'points_obtenus'        => $points_obtenus,
                'points_total'          => $s['points_total'],
                'points_evaluation'     => $s['points_evaluation'],
                'date'                  => $c['date'],
                'epoch'                 => $c['epoch'],
            );

            $corrections[] = $data;
        }

        return $corrections;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajuster les points obtenus d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function ajuster_corrections_soumission($soumission_id, $nouveau_points_obtenus, $soumission_points)
    {
        if ($nouveau_points_obtenus > $soumission_points)
            return FALSE;

        //
        // Extraire et decompresser la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);
        $soumission = decompresser_soumissions($soumission);

        //
        // Verifier les permissions
        //

        if ( ! $this->est_enseignant)
        {
            return FALSE;
        }

        if ($this->enseignant['privilege'] < 90)
        {
            if ($soumission['enseignant_id'] != $this->enseignant_id)
            {
                return FALSE;
            }
        }

        //
        // Extraire les ajustements existants de la soumission
        //

        $ajustements = array();

        if ( ! empty($soumission['ajustements_data']))
        {
            $ajustements = unserialize($soumission['ajustements_data']);
        }

        //
        // Preparer l'ecrire des nouveaux ajustements, en preservant les autres ajustements
        //

        $ajustements['total'] = $nouveau_points_obtenus;
        
        $data = array(
            'ajustements_data' => serialize($ajustements)
        );

        //
        // Ecrire des ajustements dans la base de donnees
        //

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajuster les corrections d'une question, ou d'un tableau, pour une soumission deja envoyee
     *
     * -------------------------------------------------------------------------------------------- */
    function ajuster_corrections($soumission_id, $options = array())
    {
        $options = array_merge(
            array(
                'question_id'             => NULL,      // le id de la question en ajustement
                'tableau_no'              => NULL,      // le numero du tableau en ajustement
                'nouveau_points_obtenus'  => 0,         // *OBLIGATOIRE* les nouveaux points obtenus de la question ou du tableau
                'points'                  => 0          // *OBLIGATOIRE* les points totaux de la question ou du tableau
            ),
            $options
        );

        extract($options);

        //
        // Extraire et decompresser la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);
        $soumission = decompresser_soumissions($soumission);

        //
        // Verifier les permissions
        //

        if (($soumission['enseignant_id'] != $this->enseignant_id) && $this->enseignant['privilege'] < 90)
        {
            return FALSE;
        }

        //
        // Evaluation
        //

        if ( ! $soumission['lab'])
        {
            //
            // Verifier que la question est corrigee et peut etre ajustee
            //

            if ( ! array_key_exists($question_id, $soumission['questions_data']) || ! $soumission['questions_data'][$question_id]['corrigee'])
            {
                return FALSE;
            }
        }

        //
        // Extraire les ajustements existants de la soumission
        //

        $ajustements = array();

        if ( ! empty($soumission['ajustements_data']))
        {
            $ajustements = unserialize($soumission['ajustements_data']);
        }

        // ------------------------------------------------------------
        //
        // Calculer les points obtenus de la soumission
        //
        // Il faut recalculer les points au complet, tableaux et questions, parce que
        // si l'enseignant ajoute un ajustement de soumission, et qu'il l'enleve, il faut etre
        // en mesure de determiner les points obtenus (et la difference ne peut plus etre suivie).
        // 
        // Il faut calculer les tableaux egalement car c'est la meme methode pour les
        // evaluations et les laboratoires, meme si celle-ci est utilisee seulement
        // pour les questions.
        //
        // ------------------------------------------------------------

        $r = calculer_points_soumission(
            $soumission_id,
            array(
                'soumission'              => $soumission,
                'question_id'             => $options['question_id'] ?? NULL,
                'tableau_no'              => $options['tableau_no'] ?? NULL,
                'nouveau_points_obtenus'  => $options['nouveau_points_obtenus'],
                'points'                  => $options['points']
            )
        );

        if ($r === FALSE)
            return FALSE;

        if ( ! array_key_exists('soumission_points_obtenus', $r))
            return FALSE;

        //
        // Preparer l'ecrire des nouveaux ajustements, en preservant les autres ajustements
        //

        $data = array(
            'points_obtenus' => $r['soumission_points_obtenus'],
        );

        //
        // Laboratoire
        //

        if ( ! empty($options['tableau_no']))
        {
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);

            $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] = $nouveau_points_obtenus;

            $data['lab_points_tableaux'] = json_encode($lab_points_tableaux);
        }

        //
        // Evaluation
        //

        elseif ( ! empty($options['question_id']))
        {
            $ajustements[$question_id] = array(
                'points_obtenus' => $nouveau_points_obtenus
            );
            
            $data['ajustements_data'] = serialize($ajustements);
        }

        //
        // Ecrire des ajustements dans la base de donnees
        //

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer l'ajustement des points d'une question, ou d'un tableau
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_ajustement($soumission_id, $options = array())
    {
    	$options = array_merge(
            array(
                'question_id' => NULL,
                'tableau_no'  => NULL
           ),
           $options
        );

        extract($options);

        //
        // Extraire et decompresser la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);
        $soumission = decompresser_soumissions($soumission);

        //
        // Extraire les ajustements existants de la soumission
        // Effacer l'ajustement
        //

        $data = array(); 

        if ( ! empty($question_id))
        {
            if (empty($soumission['ajustements_data']))
            {
                p(array('erreur' => 'les donnees sur les ajustements sont vides'));
                return FALSE;
            }

            $ajustements = unserialize($soumission['ajustements_data']);

            unset($ajustements[$question_id]);
            
            $soumission['ajustements_data'] = serialize($ajustements);
            $data['ajustements_data'] = serialize($ajustements);
        }

        //
        // Effacer l'ajustement
        // (important d'effacer avant de recalculer les points obtenus de la soumission)
        //
        
        if ( ! empty($tableau_no))
        {
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);

            if (array_key_exists('points_obtenus_ajustement', $lab_points_tableaux[$tableau_no]))
            {
                unset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']);
            }

            $soumission['lab_points_tableaux'] = json_encode($lab_points_tableaux);
            $data['lab_points_tableaux'] = json_encode($lab_points_tableaux);
        }

        // ------------------------------------------------------------
        //
        // Calculer les points obtenus de la soumission
        //
        // Il faut recalculer les points au complet, tableaux et questions, parce que
        // si l'enseignant ajoute un ajustement de soumission, et qu'il l'enleve, il faut etre
        // en mesure de determiner les points obtenus (et la difference ne peut plus etre suivie).
        // 
        // Il faut calculer les tableaux egalement car c'est la meme methode pour les
        // evaluations et les laboratoires, meme si celle-ci est utilisee seulement
        // pour les questions.
        //
        // ------------------------------------------------------------

        $r = calculer_points_soumission(
            $soumission_id,
            array(
                'soumission'              => $soumission,
                'question_id'             => $options['question_id'] ?? NULL,
                'tableau_no'              => $options['tableau_no'] ?? NULL
            )
        );

        if ($r === FALSE)
            return FALSE;

        if ( ! array_key_exists('soumission_points_obtenus', $r))
            return FALSE;

        //
        // Preparer l'ecrire des nouveaux ajustements, en preservant les autres ajustements
        //

        $data['points_obtenus'] = $r['soumission_points_obtenus'];

        //
        // Ecrire des ajustements dans la base de donnees
        //

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer certains ajustements d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_ajustement_soumission($soumission_id)
    {
        //
        // Extraire et decompresser la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);
        $soumission = decompresser_soumissions($soumission);

        //
        // Extraire les ajustements existants de la soumission
        //

        $ajustements = array();

        if (empty($soumission['ajustements_data']))
        {
            return FALSE;
        }

        $ajustements = unserialize($soumission['ajustements_data']);

        //
        // Effacer l'ajustement de la question id specifiee
        //

        unset($ajustements['total']);

        //
        // Preparer l'ecrire des nouveaux ajustements, en preservant les autres ajustements
        //

        $data = array(
            'ajustements_data' => serialize($ajustements)
        );

        //
        // Ecrire des ajustements dans la base de donnees
        //

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Creer les traces
     *
     * -------------------------------------------------------------------------------------------- */
    function creer_traces($evaluation_reference, $evaluation_id, $traces_arr = array())
    {
		//
		// Aucune trace pour les etudiants non inscrits
		//

		if ( ! $this->logged_in)
		{
			return FALSE;
		}

        //
        // Extraire l'evaluation
        //

        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
        {
            return FALSE;
        }

        $lab = $evaluation['lab'];

        //
        // Enseignant
        //

		if ($this->est_enseignant)
		{
			$data = array(
				'enseignant_id'     => $this->enseignant_id,
				'evaluation_id'     => $evaluation_id,
                'expiration_epoch'  => $this->now_epoch + $this->config->item('traces_expiration_enseignants'),
                'expiration_date'   => date_humanize($this->now_epoch + $this->config->item('traces_expiration_enseignants'), TRUE),
                'lab'               => $lab,
				'data'		        => serialize($traces_arr)
			);

            $this->db->insert('enseignants_traces', $data);
		}

        //
        // Etudiants
        //

		if ($this->est_etudiant)
		{
			$data = array(
				'etudiant_id' 			 => $this->etudiant_id,
				// 'session_id'             => session_id(),
				'evaluation_id'          => $evaluation_id,
                'evaluation_reference'   => $evaluation_reference,
				'semestre_id'            => $this->semestre_id,
				'soumission_debut_epoch' => $this->now_epoch,
                'soumission_debut_date'  => date_humanize($this->now_epoch, TRUE),
                'lab'                    => $lab,
				'data'		             => serialize($traces_arr)
			);

            $this->db->insert('etudiants_traces', $data);
		}

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les traces
     *
     * --------------------------------------------------------------------------------------------
     *
     * Extraire les traces d'un seul ou plusieurs etudiants simultanement.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_traces($evaluation_reference, $options = array()) 
    {
    	$options = array_merge(
            array(
                'etudiant_id'   => NULL,
                'enseignant_id' => NULL,
                'semestre_id'   => NULL
                // 'etudiants_inscrits_seulement' => TRUE // DESUET
           ),
           $options
        );

        // 
        // Etudiant inscrit seulement
        // 

        $this->db->from ('etudiants_traces');

        if ( ! empty($options['etudiant_id']))
        {
            $this->db->where ('etudiant_id', $options['etudiant_id']);
        }

        if ( ! empty($options['semestre_id']))
        {
            $this->db->where ('semestre_id', $options['semestre_id']);
        }

        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('evaluation_envoyee', 0);
        $this->db->where ('efface', 0);
        $this->db->where ('efface_par_etudiant', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->result_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lire les traces d'une evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * 2024-08-03 Les traces des reponses des enseignants en previsualisation sont conservees.
     *
     * -------------------------------------------------------------------------------------------- */
    function lire_traces($evaluation_reference, $evaluation_id, $session_id = NULL)
    {
		//
		// Les etudiants NON inscrits n'auront plus la possiblite de remplir des evaluations,
		// donc cette fonction est rendue desuette.
		//

		if ( ! $this->logged_in)	
		{
			return FALSE;
		}

		//
		// Enseignant
		//

		if ($this->est_enseignant)
		{
			//
			// Chercher les traces existantes (toujours d'une previsualisation)
			//

            $this->db->where ('enseignant_id', $this->enseignant_id);
			$this->db->where ('evaluation_id', $evaluation_id);
			// $this->db->where ('expiration_epoch >', date('U'));
			$this->db->where ('efface', 0);
			$this->db->limit (1);

			$query = $this->db->get('enseignants_traces');

			if ($query->num_rows() > 0)
			{
				$row = $query->row_array();
			}
			else
			{
				//
				// Aucune trace trouvee, creer les traces
				//

				$this->creer_traces($evaluation_reference, $evaluation_id);

				$row['data'] = serialize(array());
			}

			return $row['data'];
		}

        // 
        // Etudiant
        // 

        if ($this->est_etudiant)
        {
			//
			// Chercher les traces existantes
			//

            $this->db->where('etudiant_id', $this->etudiant_id);
			$this->db->where('evaluation_reference', $evaluation_reference);
			$this->db->where('evaluation_id', $evaluation_id);
			$this->db->where('efface', 0);
			$this->db->limit(1);

			$query = $this->db->get('etudiants_traces');

			if ($query->num_rows() > 0)
			{
				$row = $query->row_array();

				//
				// L'evaluation ne doit pas avoir ete envoyee.
				//
				// Ceci previent la situation ou un enseignant a force la terminaison de
				// l'evaluation de l'etudiant. L'etudiant n'est pas immediatement au courant
				// que son evaluation a ete terminee alors il ne faut pas creer de nouvelles traces
				// s'il continue son evaluation.
				//

				if ( ! empty($row['soumission_id']))
				{
					return FALSE;
				}
			}
			else
			{
				//
				// Aucune trace trouvee, creer les traces
				//

				$this->creer_traces($evaluation_reference, $evaluation_id);

				$row['data'] = serialize(array());
			}

			//
			// Si l'etudiant avait efface ses traces de redaction, il faut les retablir.
			// Il ne faut pas les creer de nouveau car cela permettrait a l'etudiant de choisir ses questions
			// en rafraichissant la page de multiples fois.
			//

			if (array_key_exists('efface_par_etudiant', $row) && $row['efface_par_etudiant'] == 1)
			{
				$data = array(
					'efface_par_etudiant' => 0
				);

				$this->db->where ('id', $row['id']);
				$this->db->update('etudiants_traces', $data);
			}

			return $row['data'];
        }
		
		return FALSE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lire les traces d'un etudiant, pour un enseignant (usager externe)
     *
     * -------------------------------------------------------------------------------------------- */
    function lire_traces_externe($evaluation_reference, $evaluation_id, $etudiant_id)
    {
        // 
        // Etudiant inscrit seulement
        // 

        $this->db->where('evaluation_reference', $evaluation_reference);
        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->where('etudiant_id', $etudiant_id);

        $this->db->where('evaluation_envoyee', 0);
        $this->db->where('efface', 0);
        $this->db->limit (1);

        $query = $this->db->get('etudiants_traces');

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array()['data'];
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Ecrire les traces d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Cette fonction n'ajoute pas les nouvelles traces aux traces actuelles, mais les ecrase.
     *
     * -------------------------------------------------------------------------------------------- */
    function ecrire_traces($evaluation_reference, $evaluation_id, $traces_arr, $options = array())
    {
    	$options = array_merge(
            array(
                // 'session_id' => session_id()
                'session_id' => NULL
           ),
           $options
        );

		//
		// Les etudiants NON inscrits n'ont plus de traces.
		//

		if ( ! $this->logged_in)
		{
			return FALSE;
		}

        //
        // Extraire l'evaluation
        //

        $evaluation = $this->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
        {
            return FALSE;
        }

        $lab = $evaluation['lab'];

		//
		// Enseignant
		//

		if ($this->est_enseignant)
		{
            $data = array(
                'lab'                     => $lab,
				'data'                    => serialize($traces_arr),
				'data_modification_epoch' => $this->now_epoch,
                'data_modification_date'  => date_humanize($this->now_epoch, TRUE),
                'expiration_epoch'        => $this->now_epoch + $this->config->item('traces_expiration_enseignants'),
                'expiration_date'         => date_humanize($this->now_epoch + $this->config->item('traces_expiration_enseignants'), TRUE)
			);

			$this->db->where ('enseignant_id', $this->enseignant_id);
			$this->db->where ('evaluation_id', $evaluation_id);
			$this->db->where ('efface', 0); 
			// $this->db->where ('expiration_epoch >', date('U'));
			$this->db->update('enseignants_traces', $data);

			if ($this->db->affected_rows())
			{
				return TRUE;
			}
		}

		//
		// Etudiant
		//

		if ($this->est_etudiant)
		{
			$data = array(
                'lab'                     => $lab,
				'data'                    => serialize($traces_arr),
				'data_modification_epoch' => $this->now_epoch,
				'data_modification_date'  => date_humanize($this->now_epoch, TRUE)
			);
			
			$this->db->where ('etudiant_id', $this->etudiant_id);
			$this->db->where ('evaluation_reference', $evaluation_reference);
			$this->db->where ('evaluation_id', $evaluation_id);
			$this->db->where ('efface', 0); // depuis 2021-09-28 8:08
			$this->db->update('etudiants_traces', $data);

			if ($this->db->affected_rows())
			{
				return TRUE;
			}
		}

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Evaluations avec des traces (en cours de redaction) pour les etudiants INSCRITS
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ceci est affiche sur le page d'accueil mais n'affichera pas les evaluations effaces par les
     * etudiants.
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluations_traces()
    {
        $evaluations_en_cours = array();

        //
        // Extraire les evaluations en cours de l'etudiant
        //

        $this->db->from  ('etudiants_traces as et, evaluations as e, cours as c, rel_enseignants_evaluations as ree');
        $this->db->select('et.*, e.evaluation_titre, e.lab, c.cours_nom, c.cours_nom_court, c.cours_code, c.cours_code_court');

        $this->db->where ('et.etudiant_id', $this->etudiant_id);
        $this->db->where ('et.semestre_id', $this->semestre_id);

        $this->db->where ('et.evaluation_envoyee', 0);
        $this->db->where ('et.efface', 0);
        $this->db->where ('et.efface_par_etudiant', 0);

        $this->db->where ('et.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->where ('et.evaluation_id = ree.evaluation_id');
        $this->db->where ('et.evaluation_reference = ree.evaluation_reference');

        $this->db->where ('ree.efface', 0);

        $this->db->order_by ('et.soumission_debut_epoch', 'asc');
        
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $evaluations_en_cours = array_merge($evaluations_en_cours, $query->result_array());
        }

        //
        // Extraire les laboratoires debutes par son partenaire
        //

        $this->db->from  ('etudiants_traces as et, evaluations as e, cours as c, rel_enseignants_evaluations as ree');
        $this->db->select('et.*, e.evaluation_titre, e.lab, c.cours_nom, c.cours_nom_court, c.cours_code, c.cours_code_court');

        $this->db->where ('et.lab', 1);

        $this->db->group_start();
            $this->db->where    ('et.lab_etudiant2_id', $this->etudiant_id);
            $this->db->or_where ('et.lab_etudiant3_id', $this->etudiant_id);
        $this->db->group_end();


        $this->db->where ('et.semestre_id', $this->semestre_id);

        $this->db->where ('et.evaluation_envoyee', 0);
        $this->db->where ('et.efface', 0);
        $this->db->where ('et.efface_par_etudiant', 0);

        $this->db->where ('et.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->where ('et.evaluation_id = ree.evaluation_id');
        $this->db->where ('et.evaluation_reference = ree.evaluation_reference');

        $this->db->where ('ree.efface', 0);

        $this->db->order_by ('et.soumission_debut_epoch', 'asc');
        
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $evaluations_en_cours = array_merge($evaluations_en_cours, $query->result_array());
        }

        return $evaluations_en_cours;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer les traces d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_traces($evaluation_reference, $evaluation_id, $options = array())
    {
        if ( ! is_cli() && ! @$this->logged_in)
		{
			return TRUE;
		}

    	$options = array_merge(
            array(
                'etudiant_id'           => @$this->etudiant_id ?? NULL,
                'soumission_id'         => NULL,
                'soumission_reference'  => NULL,
                'session_id'            => NULL,
                'evaluation_envoyee'    => TRUE,
                'evaluation_terminee'   => TRUE
           ),
           $options
        );

        //
        // Etudiant
        //

        if ($options['etudiant_id'])
        {
			$data = array(
				'soumission_id'        => $options['soumission_id'],
				'soumission_reference' => $options['soumission_reference'],
                'evaluation_envoyee'   => $options['evaluation_envoyee'], 
                'evaluation_terminee'  => $options['evaluation_terminee'],
                'session_id'           => $options['session_id'],
				'efface'               => 1
            );

			$this->db->where  ('etudiant_id', $options['etudiant_id']);
			$this->db->where  ('evaluation_reference', $evaluation_reference);
			$this->db->where  ('evaluation_id', $evaluation_id);
			$this->db->where  ('efface', 0); // Les traces ne doivent pas deja avoir ete effacee
			$this->db->update ('etudiants_traces', $data);
        }

		//
		// Enseignant
        //
        
        //
        // On n'efface pas les traces de l'enseignant.
        //

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer les traces d'une evaluation en redaction par l'etudiant
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_traces_redaction($evaluation_reference)
    {
        if ( ! $this->est_etudiant)
        {
            return FALSE;
        }

        $data = array(
            'efface_par_etudiant' => 1
        );

        $this->db->where ('etudiant_id', $this->etudiant_id);
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->update('etudiants_traces', $data);
        
        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire la reference de l'evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_evaluation_reference($evaluation_id)
    {
        $this->db->from  ('rel_enseignants_evaluations');
        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->where ('semestre_id', $this->semestre_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }
                                                                                                                                                                                                                                  
        $row = $query->row_array();

        return $row['evaluation_reference'];        
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ping des etudiants inscrits en train de faire leur evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * Il faut absoluement prendre la valeur de la session_id qui a ete enregistree dans l'evaluation
     * lorsque celle-ci a ete chargee, car la session_id reelle de l'etudiant sera probablement
     * differente du au parametre $config['sess_time_to_update'] = 300 (en secondes).
     *
     * La valeur de la session_id dans les arguments de la fonction est disponible pour les etudiants
     * inscrits et non inscrits, mais elle n'est pas utilisee pour les etudiants inscrits car il est
     * preferable de verifier leur identite par leur etudiant_id.
     *
     * -------------------------------------------------------------------------------------------- */
    function ping_etudiant($options = array())
    {
    	$options = array_merge(
            array(
                'evaluation_reference' => NULL, // obligatoire
                'evaluation_id'        => NULL, // obligatoire
                'session_id'           => NULL,
                'notificaction'        => FALSE
           ),
           $options
        );

        $champs_obligatoires = array('evaluation_reference', 'evaluation_id');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
            {
                return FALSE;
            }
        }

        extract($options);

        if ( ! $this->etudiant_id)
        {
            $this->Admin_model->debogage(
                array(
                    'evaluation_reference' => $evaluation_reference,
                    'evaluation_id' => $evaluation_id,
                    'code' => 'DSMEST002',
                    'msg' => "ping etudiant : etudiant_id = NULL",
                    'class' => __CLASS__,
                    'function' => __FUNCTION__
                )
            );
            return FALSE;
        }

        //
        // Tableau de retour
        //

        $r = array(
            'epoch'      => $this->now_epoch,
			'intervalle' => $this->config->item('ping_etudiant_evaluation_intervalle') // en secondes
        );

        //
        // Assignons la valeur de l'intervalle entre chaque ping (en secondes), et si elle est introuvable,
        // etablissons une valeur par defaut de 180s.
        //

        $ping_intervalle = $this->config->item('ping_etudiant_evaluation_intervalle') ?? 180; 

        //
        // Extraire l'activite de l'etudiant de ses traces
        //

        $this->db->from('etudiants_traces');

        $this->db->where    ('etudiant_id', $this->etudiant_id);
        $this->db->where    ('evaluation_reference', $evaluation_reference);
        $this->db->where    ('evaluation_id', $evaluation_id);
        $this->db->where    ('efface', 0);
        $this->db->order_by ('soumission_debut_epoch', 'desc');  // Ceci permet de regler un prob durant le developpement (tests) car il n'extrayait pas la derniere ligne.
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            // 
            // Les traces sont introuvables
            //
            // Il s'agit probalement d'un etudiant non inscrit dont ses traces ont ete effacees
            // lorsque l'evaluation a ete terminee par l'enseignant.
            //

            /*
            $this->Admin_model->debogage(
                array(
                    'evaluation_reference' => $evaluation_reference,
                    'code' => 'DEMA002',
                    'msg' => "ping_etudiant : les traces sont introuvables",
                    'class' => __CLASS__,
                    'function' => __FUNCTION__
                )
            );
            */

            //
            // Extraire l'evaluation
            //

            $evaluation = $this->Evaluation_model->extraire_evaluation($options['evaluation_id']);

            if ($evaluation['lab'])
            {
                return array('terminee_lab_partenaire' => TRUE);
            }

            return array('terminee_non_inscrit' => TRUE);
        }

		$row = $query->row_array();

        if ($row['efface'] && ! $row['evaluation_terminee'] && $row['evaluation_envoyee'])
        {
            //
            // L'evaluation a deja ete envoyee et enregistree, alors retourner le signal de la fin.
            //

            return array('terminee' => TRUE);
        }

		//
		// Ajout d'inforations utiles au tableau de retour
		//

		$r['soumission_debut_epoch'] = $row['soumission_debut_epoch'];

        //
        // Verifier la presence de notification pour l'etudiant
        //

        if ($this->est_etudiant)
        {
            $this->db->from     ('etudiants_evaluations_messages as em, etudiants_evaluations_notifications as en');
            $this->db->where    ('em.evaluation_reference', $evaluation_reference);
            $this->db->where    ('em.semestre_id', $this->semestre_id);
            $this->db->where    ('em.message_id = en.message_id');
            $this->db->where    ('en.etudiant_id', $this->etudiant_id);
            $this->db->where    ('en.extrait', 0);
            $this->db->order_by ('em.epoch', 'asc');
            $this->db->limit    (1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                $n = $query->row_array();

                //
                // Indique que la notification a ete faite (extraite).
                //
                
                $this->db->where ('notification_id', $n['notification_id']);
                $this->db->update('etudiants_evaluations_notifications', array('extrait' => 1));

                //
                // Ajouter dans le tableau de retour.
                //

                $r = array_merge($r, 
                    array(
                        'message'         => $n['message'],
                        'message_id'      => $n['message_id'],
                        'notification_id' => $n['notification_id']
                    )
                );
            }
        }

        //
        // Verifier si l'activite doit etre mise a jour, ou si il est trop tot pour le faire.
        //

        if (($row['activite_epoch'] + $ping_intervalle) > $this->now_epoch)
        {
            return $r;
        }        

        //
        // Mettre a jour
        //

        // Il faut faire une incrementation d'un intervalle fixe pour s'assurer que cette activite
        // represente seulement le temps devant l'ecran. Si on ajoute la difference entre le temps present
        // et l'activite_epoch, alors ca ne serait pas seulement le temps devant l'ecran qui est calcule.

        $data = array(
            'secondes_en_redaction' => $row['secondes_en_redaction'] + $ping_intervalle,
            'activite_epoch'        => $this->now_epoch,
            'activite_date'         => date_humanize($this->now_epoch, TRUE),
        );

        if ($this->est_etudiant && $this->etudiant_id != NULL)
        {
            $this->db->where ('etudiant_id', $this->etudiant_id);
        }
        else
        {
            $this->db->where ('session_id', $session_id ?? session_id());
        }
        
        $this->db->where  ('evaluation_reference', $evaluation_reference);
        $this->db->where  ('evaluation_id', $evaluation_id);
        $this->db->where  ('efface', 0);

        $this->db->update ('etudiants_traces', $data);

        return $r;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Remettre a zero les vues d'une soumission
     *
     * -------------------------------------------------------------------------------------------- */
    function remettre_zero_vues($soumission_id, $options = array())
    {
        //
        // Verifier la permission de cette soumission
        //

        $this->db->from     ('soumissions');
        $this->db->where    ('soumission_id', $soumission_id);
        $this->db->where    ('enseignant_id', $this->enseignant_id);
        $this->db->where    ('efface', 0);
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $data = array(
            'vues' => 0
        );

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Planifier une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function planifier_evaluation($options = array())
    {
    	$options = array_merge(
            array(
                'evaluation_id'        => NULL, // obligatoire
                'evaluation_reference' => NULL, // obligatoire
                'debut_date'           => NULL,
                'debut_epoch'          => 0,
                'fin_date'             => NULL,
                'fin_epoch'            => 0,
                'temps_limite'         => NULL
           ),
           $options
        );

        /* Commente 2021-09-04
        if (empty($options['debut_epoch']) && empty($options['fin_epoch']))
        {
            return FALSE;
        }
        */

        //
        // Extraire les donnees de l'evaluation en redaction
        //

        $this->db->from  ('rel_enseignants_evaluations');
        $this->db->where ('evaluation_id', $options['evaluation_id']);
        $this->db->where ('evaluation_reference', $options['evaluation_reference']); 
        $this->db->where ('semestre_id', $this->semestre_id);
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        //
        // Preparer la mise a jour
        //

        $data = array();

        if ( ! empty($options['debut_epoch']))
        {
            $data['debut_date']  = date_humanize($options['debut_epoch'], TRUE);
            $data['debut_epoch'] = $options['debut_epoch'];
        }

        if ( ! empty($options['fin_epoch']))
        {
            $data['fin_date']  = date_humanize($options['fin_epoch'], TRUE);
            $data['fin_epoch'] = $options['fin_epoch'];
        }

        $data['temps_limite'] = empty($options['temps_limite']) || $options['temps_limite'] < 0 ? NULL : $options['temps_limite'];

        if ($options['temps_limite'] == 0)
        {
            $options['temps_limite'] = NULL;
        }

        //
        // Mettre a jour
        //

        $this->db->where ('evaluation_id', $options['evaluation_id']);
        $this->db->where ('evaluation_reference', $options['evaluation_reference']); 
        $this->db->where ('semestre_id', $this->semestre_id);
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);

        $this->db->update('rel_enseignants_evaluations', $data);
        
        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer la planification d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_planification_evaluation($post_data)
    {
        $this->db->from  ('rel_enseignants_evaluations');
        $this->db->where ('evaluation_id', $post_data['evaluation_id']);
        $this->db->where ('evaluation_reference', $post_data['evaluation_reference']); 
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $data = array(
            'debut_date'   => NULL,
            'debut_epoch'  => NULL,
            'fin_date'     => NULL,
            'fin_epoch'    => NULL,
            'temps_limite' => NULL
        );
                                                                                                                                                                                                                                  
        $this->db->where ('evaluation_id', $post_data['evaluation_id']);
        $this->db->where ('evaluation_reference', $post_data['evaluation_reference']); 
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);

        $this->db->update('rel_enseignants_evaluations', $data);
        
        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * ADMIN : Extraire les evaluations selectionnees
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_toutes_evaluations_selectionnees($options = array())
    {
    	$options = array_merge(
            array(
                'groupe_id'     => NULL,
                'semestre_id'   => NULL,
                'enseignant_id' => NULL
           ),
           $options
        );
    
        $this->db->from  ('rel_enseignants_evaluations as rel, evaluations as ev, semestres as s, enseignants as en, cours as c, groupes as g');
        $this->db->select('rel.*, s.semestre_code, s.semestre_nom, g.sous_domaine, en.nom, en.prenom, en.genre, c.cours_code, c.cours_code_court' . 
                          ', ev.evaluation_titre, ev.formative, rel.inscription_requise');
        $this->db->where ('rel.evaluation_id = ev.evaluation_id');
        $this->db->where ('rel.groupe_id = g.groupe_id');
        $this->db->where ('rel.cours_id = c.cours_id');
        $this->db->where ('rel.enseignant_id = en.enseignant_id');
        $this->db->where ('rel.semestre_id = s.semestre_id');

        $this->db->where ('rel.efface', 0);
        $this->db->where ('ev.efface', 0);
        $this->db->where ('g.actif', 1);
        $this->db->where ('g.efface', 0);
        $this->db->where ('en.actif', 1);
        $this->db->where ('en.efface', 0);
        $this->db->where ('s.actif', 1);

        if ( ! empty($options['groupe_id']) && is_numeric($options['groupe_id']))
        {
            $this->db->where ('rel.groupe_id', $options['groupe_id']);
        }

        if ( ! empty($options['semestre_id']) && is_numeric($options['semestre_id']))
        {
            $this->db->where('rel.semestre_id', $options['semestre_id']);
        }

        if ( ! empty($options['enseignant_id']) && is_numeric($options['enseignant_id']))
        {
            $this->db->where('rel.enseignant_id', $options['enseignant_id']);
        }

        $this->db->order_by('rel.ajout_epoch', 'desc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'evaluation_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'activite d'un etudiant pendant son evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_activite_evaluation($etudiant_id, $debut_epoch, $fin_epoch)
    {
        $this->db->from  ('activite');
        $this->db->where ('etudiant_id', $etudiant_id);
        $this->db->where ('epoch >=', $debut_epoch);
        $this->db->where ('epoch <=', $fin_epoch);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter l'activite louche pendant une evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ceci est une amelioration de 'extraire_activite_evaluations', qui a ete conserver pour
     * retrocompatibilite.
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_activite_louche($options = array())
    {
        //
        // Le tableau de retour par default
        //

        $r_arr = array(
            'meme_ip'               => array(),
            'meme_ip_louche'        => FALSE,
            'aide_externe'          => array(),
            'aide_externe_louche'   => FALSE,
            'etudiants_debut_epoch' => array(),
            'etudiants_fin_epoch'   => array()
        );

        //
        // Verifier la presence des champs obligatoires
        //

        $champs_obligatoires = array('etudiant_ids', 'evaluation_references', 'debut_epoch', 'fin_epoch');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options))
            {
                return $r_arr;
            }
        }

        //
        // Creer les variables
        //

        extract($options);

        //
        // Extraire l'activite pertinente
        //

        $this->db->from     ('activite');
        $this->db->where_in ('etudiant_id', $etudiant_ids);
        $this->db->where    ('epoch >=', $debut_epoch);
        $this->db->where    ('epoch <=', $fin_epoch);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return $r_arr;
        }
        
        //
        // Detection de plusieurs etudiants sur une meme connexion internet.
        //

        $meme_ip        = array();
        $meme_ip_louche = FALSE;

        //
        // Detection de plusieurs connexions internet sur une meme evaluation/soumission.
        //

        $aide_externe        = array();
        $aide_externe_louche = FALSE;

        //
        // Parser l'activite
        //

        foreach($query->result_array() as $row)
        {
            if ( ! preg_match('/evaluation\/(.*)/', $row['uri'], $m))
                continue;

            $evaluation_reference = $m[1];

            if ( ! in_array($evaluation_reference, $evaluation_references))
                continue;

            if ($evaluation_reference == 'soumission')
                continue;

            $etudiant_id = $row['etudiant_id'];
            $adresse_ip  = $row['adresse_ip'];

            //
            // Certains etudiants ont accede de nouveau l'evaluation, meme apres l'avoir soumise, donc
            // il ne faut pas prendre en compte cette activite. Il faut verifier que l'activite ne s'est pas
            // produite avant ou apres la soumission, pour chaque etudiant individuellement.
            //

            if (
                $row['epoch'] < $etudiants_debut_epoch[$etudiant_id] ||
                $row['epoch'] > $etudiants_fin_epoch[$etudiant_id]
               )
            {
                continue;
            }

            //
            // meme ip
            //

            if ( ! array_key_exists($adresse_ip, $meme_ip))
            {
                $meme_ip[$adresse_ip] = array();
            }

            if ( ! in_array($etudiant_id, $meme_ip[$adresse_ip]))
            {
                $meme_ip[$adresse_ip][] = $etudiant_id;
            }

            if (count($meme_ip[$adresse_ip]) > 1)
            {
                $meme_ip_louche = TRUE;
            }

            //
            // aide externe
            //
    
            if ( ! array_key_exists($etudiant_id, $aide_externe))
            {
                $aide_externe[$etudiant_id] = array();
            }

            if ( ! in_array($adresse_ip, $aide_externe[$etudiant_id]))
            {
                $aide_externe[$etudiant_id][] = $adresse_ip;
            }

            if (count($aide_externe[$etudiant_id]) > 1)
            {
                $aide_externe_louche = TRUE;
            }
        }

        return array(
            'meme_ip'             => $meme_ip,
            'meme_ip_louche'      => $meme_ip_louche,
            'aide_externe'        => $aide_externe,
            'aide_externe_louche' => $aide_externe_louche
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier les parametres d'une evaluation en redaction
     *
     * -------------------------------------------------------------------------------------------- */
    function parametres_evaluation($post_data)
    {
        $data = array(
            'inscription_requise' => FALSE,
            'cacher'              => FALSE,
            'bloquer'             => FALSE
        );

        foreach($data as $k => $v)
        {
            if (array_key_exists($k, $post_data) && $post_data[$k] == 'on')
            {
                $data[$k] = TRUE;
            }
        }

        $this->db->where  ('enseignant_id', $this->enseignant_id);
        $this->db->where  ('semestre_id',   $this->enseignant['semestre_id']);
        // $this->db->where  ('evaluation_id', $post_data['evaluation_id']);
        $this->db->where  ('evaluation_reference', $post_data['evaluation_reference']);
        $this->db->where  ('efface', 0);

        $this->db->update ('rel_enseignants_evaluations', $data);
        
        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier les filtres d'une evaluation en redaction
     *
     * -------------------------------------------------------------------------------------------- */
    function filtrer_evaluation($post_data)
    {
        $data = array(
            'filtre_enseignant'              => 0,
            'filtre_cours'                   => 0,
            'filtre_groupe'                  => NULL,

            'filtre_enseignant_autorisation' => 0,
            'filtre_cours_autorisation'      => 0,
            'filtre_groupe_autorisation'     => NULL
        );

        if ( ! array_key_exists('filtre', $post_data))
        {
            return FALSE;
        }

        //
        // Filtres AVEC autorisation
        //

        elseif ($post_data['filtre'] == 'enseignant_autorisation')
        {
            $data['filtre_enseignant_autorisation'] = 1;
        }

        elseif ($post_data['filtre'] == 'cours_autorisation')
        {
            $data['filtre_cours_autorisation'] = 1;
        }

        elseif (preg_match('/groupe_autorisation_(.*)/', $post_data['filtre'], $matches))
        {
            if (empty($matches[1]))
            {
                return FALSE;
            }

            $data['filtre_groupe_autorisation'] = $matches[1];
        }

        //
        // Filtres SANS autorisation
        //

        elseif ($post_data['filtre'] == 'enseignant')
        {
            $data['filtre_enseignant'] = 1;
        }

        elseif ($post_data['filtre'] == 'cours')
        {
            $data['filtre_cours'] = 1;
        }

        elseif (preg_match('/groupe_(.*)/', $post_data['filtre'], $matches))
        {
            if (empty($matches[1]))
            {
                return FALSE;
            }

            $data['filtre_groupe'] = $matches[1];
        }
        
        //
        // Filtre introuvable
        //

        else
        {
            return FALSE;
        }

        $this->db->where  ('enseignant_id', $this->enseignant_id);
        $this->db->where  ('semestre_id', $this->enseignant['semestre_id']);
        $this->db->where  ('evaluation_id', $post_data['evaluation_id']);
        $this->db->where  ('efface', 0);
        $this->db->update ('rel_enseignants_evaluations', $data);
        
        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer les filtres d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_filtres_evaluation($post_data)
    {
        $data = array(
            'filtre_enseignant'              => 0,
            'filtre_enseignant_autorisation' => 0,
            'filtre_cours'                   => 0,
            'filtre_cours_autorisation'      => 0,
            'filtre_groupe'                  => NULL,
            'filtre_groupe_autorisation'     => NULL
        );

        $this->db->where('enseignant_id', $this->enseignant_id);
        $this->db->where('semestre_id',   $this->enseignant['semestre_id']);
        $this->db->where('evaluation_id', $post_data['evaluation_id']);
        $this->db->where('efface', 0);

        $this->db->update('rel_enseignants_evaluations', $data);
        
        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Sauvegarder un commentaire laisse a l'etudiant pendant la correction manuelle (pour une soumission)
     *
     * -------------------------------------------------------------------------------------------- */
    function correction_sauvegarder_commentaire_soumission($post_data)
    {
        $soumission_id = $post_data['soumission_id'];

        //
        // Extraire la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);

        if (empty($soumission))
        {
            // La soumission est introuvable.
            return FALSE;
        }

        //
        // Extraire les commentaires existants
        //

        $commentaires_data = array();

        if ( ! empty($soumission['commentaires_data_gz']))
        {
            $commentaires_data = unserialize(gzuncompress($soumission['commentaires_data_gz']));
        }

        //
        // Enregister le nouveau commentaire
        //

        $commentaires_data['total'] = _html_in($post_data['commentaire']);

        $commentaires_data_gz = gzcompress(serialize($commentaires_data));

        $data = array(
            'commentaires_data_gz' => $commentaires_data_gz
        );

        $this->db->where ('soumission_id', $post_data['soumission_id']);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Sauvegarder un commentaire laisse a l'etudiant pendant la correction manuelle (pour une question)
     *
     * -------------------------------------------------------------------------------------------- */
    function correction_sauvegarder_commentaire($soumission_id, $options)
    {
    	$options = array_merge(
            array(
                'question_id' => 0,
                'tableau_no'  => 0,
                'commentaire' => NULL
           ),
           $options
        );

        extract($options);

        if (empty($question_id) && empty($tableau_no))
            return FALSE;

        //
        // Extraire la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);

        if (empty($soumission))
        {
            // La soumission est introuvable.
            return FALSE;
        }

        //
        // Enregister le nouveau commentaire
        //

        if ( ! empty($tableau_no))
        {
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);
            $lab_points_tableaux[$tableau_no]['commentaires'] = _html_in($commentaire);

            $data = array(
                'lab_points_tableaux' => json_encode($lab_points_tableaux)
            );
        }

        elseif ( ! empty($question_id))
        {
            $commentaires_data = array();

            if ( ! empty($soumission['commentaires_data_gz']))
            {
                $commentaires_data = unserialize(gzuncompress($soumission['commentaires_data_gz']));
            }

            $commentaires_data[$question_id] = _html_in($commentaire);
            $commentaires_data_gz = gzcompress(serialize($commentaires_data));

            $data = array(
                'commentaires_data_gz' => $commentaires_data_gz
            );
        }

        if (empty($data))
            return FALSE;

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un commentaire laisse a l'etudiant pendant la correction manuelle (pour une soumission)
     *
     * -------------------------------------------------------------------------------------------- */
    function correction_effacer_commentaire_soumission($post_data)
    {
        $soumission_id = $post_data['soumission_id'];

        //
        // Extraire la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);

        if (empty($soumission))
        {
            return FALSE;
        }

        $commentaires_data = array();

        if (empty($soumission['commentaires_data_gz']))
        {
            return TRUE;
        }

        $commentaires_data = unserialize(gzuncompress($soumission['commentaires_data_gz']));

        if ( ! array_key_exists('total', $commentaires_data))
        {
            // Il n'existe pas de commentaire pour cette question.
            return TRUE;
        }

        //
        // Effacer et enregistrer le tableau des commentaires
        //

        unset($commentaires_data['total']);

        $commentaires_data_gz = gzcompress(serialize($commentaires_data));

        $data = array(
            'commentaires_data_gz' => $commentaires_data_gz
        );

        $this->db->where ('soumission_id', $post_data['soumission_id']);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un commentaire laisse a l'etudiant pendant la correction manuelle (pour une question)
     *
     * -------------------------------------------------------------------------------------------- */
    function correction_effacer_commentaire($soumission_id, $options = array())
    {
    	$options = array_merge(
            array(
                'question_id' => 0,
                'tableau_no'  => 0
           ),
           $options
        );

        extract($options);

        if (empty($question_id) && empty($tableau_no))
            return FALSE;

        //
        // Extraire la soumission
        //

        $soumission = $this->extraire_soumission($soumission_id);
        
        if (empty($soumission))
        {
            return FALSE;
        }

        //
        // Effacer le commentaire
        //

        if ( ! empty($tableau_no))
        {
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);

            if (empty($lab_points_tableaux[$tableau_no]['commentaires']))
                return TRUE;

            unset($lab_points_tableaux[$tableau_no]['commentaires']);

            $data = array(
                'lab_points_tableaux' => json_encode($lab_points_tableaux)
            );
        }

        elseif ( ! empty($question_id))
        {
            $commentaires_data = array();

            if (empty($soumission['commentaires_data_gz']))
                return TRUE;

            $commentaires_data = unserialize(gzuncompress($soumission['commentaires_data_gz']));

            if ( ! array_key_exists($question_id, $commentaires_data))
                return TRUE;

            unset($commentaires_data[$question_id]);

            $commentaires_data_gz = gzcompress(serialize($commentaires_data));

            $data = array(
                'commentaires_data_gz' => $commentaires_data_gz
            );
        }

        if (empty($data))
            return FALSE;

        $this->db->where ('soumission_id', $soumission_id);
        $this->db->update('soumissions', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction sert a extraire les ponderations selon les criteres pour determiner si les
     * donnees entrees sont fiables.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderations_par_soumissions($soumissions, $semestre_id = NULL)
    {
        //
        // Extraire la combinaisison evaluation_id et semestre_id des soumissions
        //

        $sem_ev = array(); // semestre - evaluation

        foreach($soumissions as $s)
        {
            if ( ! array_key_exists($s['semestre_id'], $sem_ev))
            {
                $sem_ev[$s['semestre_id']] = array();
            } 

            if ( ! in_array($s['evaluation_id'], $sem_ev))
            {
                $sem_ev[$s['semestre_id']][] = $s['evaluation_id'];
            }
        }

        //
        // Extraire les ponderations
        //

        $ponderations = array();

        foreach($sem_ev as $sem_id => $ev_ids)
        {
            $ponderations = array_merge($ponderations, $this->extraire_ponderations($ev_ids, $sem_id));
        }

        return $ponderations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction sert a extraire les ponderations selon les criteres pour determiner si les
     * donnees entrees sont fiables.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderations($evaluation_ids, $semestre_id)
    {
        $evaluation_ids = array_unique($evaluation_ids);

        $this->db->from     ('evaluations_ponderations as ep');
        $this->db->where_in ('ep.evaluation_id', $evaluation_ids);
        $this->db->where    ('ep.semestre_id', $semestre_id);
        $this->db->where    ('ep.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {   
            return array();
        }

        $ponderations  = array();           // officielles
        $ponderations_etudiants = array();  // officieuses

        foreach($query->result_array() as $r)
        {
            $label = 's' . $r['semestre_id'] . 'e' . $r['evaluation_id'];

            //
            // Une ponderation officielle
            //

            if ($r['enseignant_id'])
            {
                $ponderations[$label] = array(
                    'ponderation' => $r['ponderation'],
                    'enseignant'  => 1,
                    'etudiant'    => 0
                );

                unset($ponderations_etudiants[$label]);
            }

            //
            // Une ponderation officieuse
            //

            elseif ($r['etudiant_id'])
            {       
                if (array_key_exists($label, $ponderations))
                {
                    // Une ponderation officielle existe deja
                    continue;
                }

                if ( ! array_key_exists($label, $ponderations_etudiants))
                {
                    $ponderations_etudiants[$label] = array(
                        'ponderations' => array()
                    );
                }

                $ponderations_etudiants[$label]['ponderations'][] = $r['ponderation'];
            }
        }

        //
        // Determiner les ponderations officieuses
        //

        if ( ! empty($ponderations_etudiants))
        {
            foreach($ponderations_etudiants as $label => $pe)
            {
                if (array_key_exists($label, $ponderations))
                {
                    continue;
                }

                //
                // Determiner la ponderation avec le plus de valeur et la rendre officieuse
                //

                $valeurs = array_count_values($pe['ponderations']);

                asort($valeurs);

                $valeurs1 = $valeurs;
                $valeurs2 = array_flip($valeurs);

                $p_fois      = array_pop($valeurs1); 
                $p_populaire = array_pop($valeurs2); 

                // Le nombre de donnees concordantes avant de considerer cette ponderation comme officieuse. 
                //
                // Ceci devrait etre au moins $p_fois > 2 mais comme les etudiants n'entrent pas les ponderations,
                // en reduisant je crois que cela pourrait les motiver.

                if ($p_fois > 0)
                {
                    $ponderations[$label] = array(
                        'ponderation' => $p_populaire,
                        'enseignant'  => 0,
                        'etudiant'    => 1,
                        'nb_fois'     => $p_fois
                    );
                }
            }
        }

        return $ponderations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations de l'etudiant (par soumissions)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction sert a extraire les ponderations selon les criteres pour determiner si les
     * donnees entrees sont fiables.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_mes_ponderations_par_soumissions($soumissions, $semestre_id = NULL)
    {
        //
        // Extraire la combinaisison evaluation_id et semestre_id des soumissions
        //

        $sem_ev = array(); // semestre - evaluation

        foreach($soumissions as $s)
        {
            if ( ! array_key_exists($s['semestre_id'], $sem_ev))
            {
                $sem_ev[$s['semestre_id']] = array();
            } 

            if ( ! in_array($s['evaluation_id'], $sem_ev))
            {
                $sem_ev[$s['semestre_id']][] = $s['evaluation_id'];
            }
        }

        //
        // Extraire les ponderations
        //

        $mes_ponderations = array();

        foreach($sem_ev as $sem_id => $ev_ids)
        {
            $mes_ponderations = array_merge($mes_ponderations, $this->extraire_mes_ponderations($ev_ids, $sem_id));
        }

        return $mes_ponderations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations de l'etudiant (par evaluation_ids)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette fonction sert a extraire les ponderations selon les criteres pour determiner si les
     * donnees entrees sont fiables.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_mes_ponderations($evaluation_ids, $semestre_id)
    {

        $this->db->from     ('evaluations_ponderations as ep');
        $this->db->where_in ('ep.evaluation_id', $evaluation_ids);
        $this->db->where    ('ep.semestre_id', $semestre_id);
        $this->db->where    ('ep.etudiant_id', $this->etudiant_id);
        $this->db->where    ('ep.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {   
            return array();
        }

        $mes_ponderations = array();

        foreach($query->result_array() as $r)
        {
            $label = 's' . $r['semestre_id'] . 'e' . $r['evaluation_id'];

            $mes_ponderations[$label] = array(
                'ponderation' => $r['ponderation']
            );
        }

        return $mes_ponderations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une ponderation officielle (d'un enseignant)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Les ponderations officielles sont celles entrees par les enseignants.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderation_officielle($evaluation_id, $semestre_id)
    {
        $evaluation_ids = array($evaluation_id);

        $r = $this->extraire_ponderations_officielles($evaluation_ids, $semestre_id);

        if (empty($r))
        {
            return array();
        }

        return $r[0];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations officielles (d'un enseignant)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Les ponderations officielles sont celles entrees par les enseignants.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderations_officielles($evaluation_ids, $semestre_id)
    {
        $evaluation_ids = array_unique($evaluation_ids);

        $this->db->from     ('evaluations_ponderations');
        $this->db->where    ('enseignant_id !=', NULL);
        $this->db->where    ('etudiant_id', NULL);
        $this->db->where_in ('evaluation_id', $evaluation_ids);
        $this->db->where    ('semestre_id', $semestre_id);
        $this->db->where    ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une ponderation officieuseuse (d'un etudiant)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Les ponderations officieuses sont celles entrees par les etudiants.
     * Pour etre considerees, il doit y avoir au moins 3 ou plus ponderations identiques.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderation_officieuse($evaluation_id, $semestre_id)
    {
        $evaluation_ids = array($evaluation_id);

        $r = $this->extraire_ponderations_officieuses($evaluation_ids, $semestre_id);

        if (empty($r))
        {
            return array();
        }

        return $r[0];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les ponderations officieuseuses (d'un etudiant)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Les ponderations officieuses sont celles entrees par les etudiants.
     * Pour etre considerees, il doit y avoir au moins 3 ou plus ponderations identiques.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ponderations_officieuses($evaluation_ids, $semestre_id)
    {
        $evaluation_ids = array_unique($evaluation_ids);

        $this->db->from     ('evaluations_ponderations');
        $this->db->where    ('etudiant_id', $this->etudiant_id);
        $this->db->where_in ('evaluation_id', $evaluation_ids);
        $this->db->where    ('semestre_id', $semestre_id);
        $this->db->where    ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajuster la ponderation
     *
     * -------------------------------------------------------------------------------------------- */
    function ajuster_ponderation($post_data)
    {
        $n_ponderation = str_replace(',', '.', $post_data['ponderation']);

        //
        // Exclure les ponderations plus grande que 100%
        //

        if ($n_ponderation > 100 || $n_ponderation < 0)
        {
            return TRUE;
        }

        $mise_a_jour = FALSE;

        $data = array();

        //
        // Ajustement d'un enseignant
        //

        if ($this->est_enseignant)
        {
            $data['enseignant_id'] = $this->enseignant_id;

            //
            // Verifier que cet enseignant n'a pas deja ajuste la ponderation, et si c'est le cas, que la ponderation est differente.
            //

            $ponderation = $this->extraire_ponderation_officielle($post_data['evaluation_id'], $post_data['semestre_id']);

            if (array_key_exists('ponderation', $ponderation))
            {
                if ($ponderation['ponderation'] == $n_ponderation)
                {
                    return TRUE;
                }

                $mise_a_jour = TRUE;
            }
        }

        //
        // Ajustement d'une etudiant
        //

        elseif ($this->est_etudiant)
        {
            $data['etudiant_id'] = $this->etudiant_id;

            //
            // Verifier que cet etudiant n'a pas deja ajuste la ponderation, et si c'est le cas, que la ponderation est differente.
            //

            $ponderation = $this->extraire_ponderation_officieuse($post_data['evaluation_id'], $post_data['semestre_id']);

            if (array_key_exists('ponderation', $ponderation))
            {
                if ($ponderation['ponderation'] == $n_ponderation)
                {
                    return TRUE;
                }

                $mise_a_jour = TRUE;
            }
        }

        //
        // Doit etre un enseignant ou un etudiant.
        //

        else
        {
            return TRUE;
        }

        if ( ! $mise_a_jour)
        {
            //
            // Nouvelle ponderation
            //

            $data['evaluation_id'] = $post_data['evaluation_id'];
            $data['semestre_id']   = $post_data['semestre_id'];
            $data['ponderation']   = $n_ponderation;
            $data['date']          = date_humanize($this->now_epoch, TRUE);
            $data['epoch']         = $this->now_epoch;

            $this->db->insert('evaluations_ponderations', $data);
        }
        else
        {
            //
            // Mettre a jour la ponderation
            //

            $data['ponderation'] = $n_ponderation;
            $data['date']        = date_humanize($this->now_epoch, TRUE);
            $data['epoch']       = $this->now_epoch;

            $this->db->where ('ponderation_id', $ponderation['ponderation_id']);
            $this->db->update('evaluations_ponderations', $data);

        }

        $this->kcache->remove_category('resultats');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer la ponderation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_ponderation($evaluation_id, $semestre_id)
    {
        if ($this->est_enseignant)
        {
            if (empty($this->extraire_ponderation_officielle($evaluation_id, $semestre_id)))
            {
                // Cette ponderation est introuvable.
                return TRUE;
            }

            $this->db->where('enseignant_id', $this->enseignant_id);
        }

        elseif ($this->est_etudiant)
        {
            if (empty($this->extraire_ponderation_officieuse($evaluation_id, $semestre_id)))
            {
                // Cette ponderation est introuvable.
                return TRUE;
            }

            $this->db->where('etudiant_id', $this->etudiant_id);
        }

        //
        // Doit etre un enseignant ou un etudiant.
        //

        else
        {
            return TRUE;
        }

        $this->db->where  ('evaluation_id', $evaluation_id);
        $this->db->where  ('semestre_id', $semestre_id);
        $this->db->delete ('evaluations_ponderations');

        $this->kcache->remove_category('resultats');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Communiquer avec les etudiants en redaction
     *
     * --------------------------------------------------------------------------------------------
     *
     * Il faut considerer le semestre car un etudiant aurait pu faire le meme cours a deux 
     * semestres differents, dans le cas d'un echec la premiere fois par exemple.
     *
     * -------------------------------------------------------------------------------------------- */
    function communiquer_etudiants($evaluation_reference, $message, $etudiant_id = NULL)
    {
        //
        // Extraire l'activite de l'etudiant de ses traces
        //

        $this->db->from  ('etudiants_traces');
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('semestre_id', $this->semestre_id);
        $this->db->where ('evaluation_envoyee', 0);
        $this->db->where ('efface', 0);
        $this->db->where ('efface_par_etudiant', 0);

        if (empty($etudiant_id))
        {
            $this->db->where ('etudiant_id !=', NULL);  

        }
        else
        {
            $this->db->where ('etudiant_id', $etudiant_id);  
        }

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return 0;
        }

        $etudiants = $query->result_array();

        //
        // Ecrire le message
        //
            
        $data = array(
            'enseignant_id'         => $this->enseignant_id,
            'evaluation_reference'  => $evaluation_reference,
            'semestre_id'           => $this->semestre_id,
            'message'               => _html_in($message),
            'date'                  => date_humanize($this->now_epoch, TRUE),
            'epoch'                 => $this->now_epoch
        );

        $this->db->insert('etudiants_evaluations_messages', $data);

        $message_id = $this->db->insert_id();

        //
        // Ecrire les notifications
        //

        $data_batch = array();

        foreach($etudiants as $e)
        {
            $data_batch[] = array(
                'etudiant_id'           => $e['etudiant_id'],
                'enseignant_id'         => $this->enseignant_id,
                'semestre_id'           => $this->semestre_id,
                'evaluation_reference'  => $evaluation_reference,
                'message_id'            => $message_id
            );
        }

        if ( ! empty($data_batch))
        {
            $this->db->insert_batch('etudiants_evaluations_notifications', $data_batch);
        }

        return count($data_batch);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'activite de l'etudiant pendant son evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette function ne doit pas causer d'erreur lors de l'evaluation, sous aucun pretexte.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_activite_evaluation2($options = array())
    {
		if ( ! $this->config->item('evaluation_activite_log'))
			return TRUE;

    	$options = array_merge(
            array(
                'soumission_reference' => NULL
           ),
           $options
        );

        $champs_obligatoires = array('soumission_reference');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
                return array();
        }

        $this->db->from     ('activite_evaluation as ae');
		$this->db->where    ('ae.soumission_reference', $options['soumission_reference']);
		$this->db->order_by ('ae.epoch', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();

        return $query->result_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'activite de l'etudiant pendant son evaluation (en direct)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_activite_evaluation_direct($options = array())
    {
		if ( ! $this->config->item('evaluation_activite_log'))
			return TRUE;

    	$options = array_merge(
            array(
				'etudiant_id'		   => NULL,
                'evaluation_reference' => NULL
           ),
           $options
        );

        $champs_obligatoires = array('etudiant_id', 'evaluation_reference');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
                return array();
        }

        $this->db->from     ('activite_evaluation');
		$this->db->where	('etudiant_id', $options['etudiant_id']);
		$this->db->where    ('evaluation_reference', $options['evaluation_reference']);
		$this->db->order_by ('epoch', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return array();

        return $query->result_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Ecrire l'activite de l'etudiant pendant son evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette function ne doit pas causer d'erreur lors de l'evaluation, sous aucun pretexte.
     *
     * -------------------------------------------------------------------------------------------- */
    function ecrire_activite_evaluation($options = array())
    {
		if ( ! $this->config->item('evaluation_activite_log'))
			return TRUE;

		if ( ! is_cli() && ! ($this->logged_in && $this->est_etudiant))
			return TRUE;

    	$options = array_merge(
            array(
				'action'		   	   => NULL,
				'action_court'		   => NULL,
				'action_data'		   => NULL,
                'etudiant_id'          => @$this->etudiant_id ?? 0,
                'semestre_id'          => @$this->semestre_id ?? 0,
                'evaluation_id'        => NULL, 
                'evaluation_reference' => NULL,
                'question_id'          => NULL,
				'planificateur'		   => FALSE
           ),
           $options
        );

        $champs_obligatoires = array('action', 'evaluation_id', 'evaluation_reference');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
                return TRUE;
        }

        $data = array(
            'etudiant_id'           => $options['etudiant_id'],
			'semestre_id'			=> $options['semestre_id'],
            'evaluation_id'         => $options['evaluation_id'],
            'evaluation_reference'  => $options['evaluation_reference'],
            'question_id'           => $options['question_id'],
            'action'                => $options['action'],
			'action_court'			=> $options['action_court'],
			'action_data'			=> $options['action_data'],
            'date'                  => date_humanize($this->now_epoch, TRUE),
            'epoch'                 => $this->now_epoch
        );

		if ( ! $options['planificateur'])
		{
			$data['adresse_ip']  = $this->input->ip_address();
			$data['fureteur_id'] = $this->Admin_model->generer_fureteur_id();
			$data['fureteur']    = $this->agent->browser() . ' ' . $this->agent->version();
            $data['plateforme']  = $this->agent->platform();
		}

        $this->db->insert('activite_evaluation', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ecrire l'activite de l'etudiant de soumettre son evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function ecrire_activite_evaluation_soumission($options = array())
    {
		if ( ! $this->config->item('evaluation_activite_log'))
			return TRUE;

    	$options = array_merge(
            array(
				'semestre_id'		   => NULL,
				'etudiant_id'		   => NULL,
				'action'		   	   => NULL,
				'action_court'		   => NULL,
				'action_data'		   => NULL,
                'evaluation_id'        => NULL, 
                'evaluation_reference' => NULL,
				'soumission_id'		   => NULL,
				'soumission_reference' => NULL,
                'question_id'          => NULL,
                'planificateur'        => TRUE
           ),
           $options
        );

        $champs_obligatoires = array('semestre_id', 'etudiant_id', 'action', 'evaluation_id', 'evaluation_reference', 'soumission_id', 'soumission_reference');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
                return TRUE;
        }

        // Fixer le semestre id si vide ou absent

        if (empty($options['semestre_id']))
        {
            $options['semestre_id'] = $this->semestre_id ?? 0;
        }

		//
		// Les differents types de soumission :
		//
		// soumission_formulaire, soumission_cli, soumission_enseignant
		//

        $data = array(
			'semestre_id'			=> $options['semestre_id'],
            'etudiant_id'           => $options['etudiant_id'],
            'evaluation_id'         => $options['evaluation_id'],
            'evaluation_reference'  => $options['evaluation_reference'],
			'soumission_id'			=> $options['soumission_id'],
			'soumission_reference'  => $options['soumission_reference'],
            'question_id'           => $options['question_id'],
            'action'                => $options['action'],
			'action_court'			=> $options['action_court'],
			'action_data'			=> $options['action_data'],
            'date'                  => date_humanize($this->now_epoch, TRUE),
            'epoch'                 => $this->now_epoch
        );

		if ( ! $options['planificateur'])
		{
			$data['adresse_ip']  = $this->input->ip_address();
			$data['fureteur_id'] = $this->Admin_model->generer_fureteur_id();
			$data['fureteur']    = $this->agent->browser() . ' ' . $this->agent->version();
            $data['plateforme']  = $this->agent->platform();
		}

        $this->db->insert('activite_evaluation', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Finaliser l'activite de l'evaluation (de l'etudiant) apres la soumission
     *
     * --------------------------------------------------------------------------------------------
     *
     * Inscrire les informations sur la soumission dans l'activite de l'evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function finaliser_activite_evaluation($options = array())
    {
    	$options = array_merge(
            array(
				'semestre_id'		   => NULL,
                'soumission_id'        => NULL,
                'soumission_reference' => NULL,
                'etudiant_id'          => NULL,
                'evaluation_id'        => NULL
           ),
           $options
        );

        $champs_obligatoires = array('etudiant_id', 'evaluation_id', 'evaluation_reference', 'soumission_id', 'soumission_reference');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
                return TRUE;
        }

        // Fixer le semestre id si vide ou absent

        if (empty($options['semestre_id']))
        {
            $options['semestre_id'] = $this->semestre_id ?? 0;
        }

        // Trouver les entrees a modifier

        $this->db->select   ('id');
        $this->db->from     ('activite_evaluation');
        $this->db->where    ('etudiant_id',   		 $options['etudiant_id']);
        $this->db->where    ('evaluation_id', 		 $options['evaluation_id']);
		$this->db->where    ('evaluation_reference', $options['evaluation_reference']);
		$this->db->where    ('semestre_id',   		 $options['semestre_id']);
		$this->db->where	('soumission_id', 	 	 NULL);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return TRUE;
                                                                                                                                                                                                                                  
        $ids = array_column($query->result_array(), 'id');

        if ( ! empty($ids))
        {
            $this->db->where_in('id', $ids);
            $this->db->update('activite_evaluation', 
                array(
                    'soumission_id' 	   => $options['soumission_id'],
                    'soumission_reference' => $options['soumission_reference']
                )
            );
        } 

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Evaluation deja chargee
     *
     * -------------------------------------------------------------------------------------------- */
    function evaluation_deja_chargee($evaluation_id, $evaluation_reference)
	{
		$this->db->from  ('etudiants_traces');
		$this->db->where ('evaluation_id', $evaluation_id);
		$this->db->where ('evaluation_reference', $evaluation_reference);
		$this->db->where ('efface', 0);
		$this->db->limit	(1);
		
		$query = $this->db->get();
		
		if ( ! $query->num_rows() > 0)
			 return FALSE;
		
		return TRUE;		
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Exporter une evaluation au format JSON
     *
     * -------------------------------------------------------------------------------------------- */
    function exporter_json($evaluation_id)
    {
        $json_arr = array();

        //
        // Evaluation
        //

        $evaluation = $this->extraire_evaluation($evaluation_id);

        if ($evaluation['lab'])
        {
            return FALSE;
        }

        $json_arr['evaluation'] = array(
            'evaluation_id'     => $evaluation_id,
            // 'evaluation_titre'  => $evaluation['evaluation_titre'],
            'evaluation_titre'  => html_entity_decode($evaluation['evaluation_titre'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'questions_aleatoires' => $evaluation['questions_aleatoires'],
            'formative'         => $evaluation['formative'],
            'instructions'      => $evaluation['instructions'],
            'ajout_date'        => $evaluation['ajout_date'],
            'ajout_epoch'       => $evaluation['ajout_epoch']
        );

        //
        // Cours
        //

        $cours = $this->Cours_model->extraire_cours(array('evaluation_id' => $evaluation_id));

        $json_arr['cours'] = array(
            'cours_id'   => $cours['cours_id'],
            'cours_code' => $cours['cours_code'],
            'cours_nom'  => $cours['cours_nom']
        );

        //
        // Variables
        //

        $variables = $this->extraire_variables($evaluation_id);

        if ( ! empty($variables))
        {
            $json_arr['variables'] = array();

            foreach($variables as $v)
            {
                $json_arr['variables'][] = array(
                    'variable_id'       => $v['variable_id'],
                    'label'             => $v['label'],
                    'minimum'           => $v['minimum'],
                    'maximum'           => $v['maximum'],
                    'decimales'         => $v['decimales'],
                    'ns'                => $v['ns'],
                    'cs'                => $v['cs'],
                    // 'variable_desc'  => $v['variable_desc']
                    'variable_desc'     => html_entity_decode($v['variable_desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                );
            }
        }

        //
        // Blocs
        //

        $blocs = $this->Question_model->extraire_blocs($evaluation_id);

        if ( ! empty($blocs))
        {
            $json_arr['blocs'] = array();

            foreach($blocs as $b)
            {
                if ($b['efface']) continue;

                $json_arr['blocs'][] = array(
                    'bloc_id'           => $b['bloc_id'],
                    'bloc_label'        => $b['bloc_label'],
                    'bloc_desc'         => $b['bloc_desc'],
                    'bloc_desc'         => html_entity_decode($b['bloc_desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    'bloc_points'       => $b['bloc_points'],
                    'bloc_nb_questions' => $b['bloc_nb_questions'],
                    'bloc_actif'        => $b['bloc_actif'],
                );
            }
        }

        //
        // Grilles de correction
        //

        $grilles = $this->Question_model->extraire_grilles_correction_par_evaluation_id($evaluation_id);

        //
        // Questions
        //  - reponses
        //  - tolerances
        //  - similarites
        //  - grille de correction
        //

        $questions = $this->Question_model->lister_questions($evaluation_id);

        if ( ! empty($questions))
        {
            $question_ids = array_column($questions, 'question_id');

            $reponses_toutes = $this->Reponse_model->lister_reponses_toutes($question_ids);

            $json_arr['questions'] = array();

            foreach($questions as $q)
            {
                if ($q['efface']) continue;

                $reponses_arr = array();

                //
                // reponses
                //
        
                if (array_key_exists($q['question_id'], $reponses_toutes))
                {
                    foreach($reponses_toutes[$q['question_id']] as $r)
                    {
                        if ($r['efface']) continue;

                        $reponses_arr[] = array(
                            'reponse_id'        => $r['reponse_id'],
                            // 'reponse_texte'  => $r['reponse_texte'],
                            'reponse_texte'     => html_entity_decode($r['reponse_texte'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'reponse_correcte'  => $r['reponse_correcte'],
                            'equation'          => $r['equation'],
                            'cs'                => $r['cs'],
                            'notsci'            => $r['notsci'],
                            'actif'             => $r['actif'],
                        );
                    }
                } 

                //
                // tolerances
                //

                $tolerances = $this->Question_model->extraire_tolerances($q['question_id']);

                //
                // similarites
                //

                $similarites = array();

                if ($q['question_type'] == 7)
                {
                    $similarites = $this->Question_model->extraire_similarite($q['question_id']);
                }
    
                //
                // grille
                //

                $grille = array();

                if (array_key_exists($q['question_id'], $grilles))
                {
                    $g = $grilles[$q['question_id']];

                    if ($g['efface'])
                    {
                        $grille = array(
                            'grille_id'         => $g['grille_id'],
                            'grille_affichage'  => $g['grille_affichage'],
                            'pourcentage'       => $g['pourcentage'],
                            'actif'             => $g['actif']
                        );

                        $elements = array();

                        if ( ! empty($g['elements']))
                        {
                            foreach($g['elements'] as $e)
                            {
                                $elements[] = array(
                                    'element_id'        => $e['element_id'],
                                    'element_type'      => $e['element_type'],
                                    'element_type_desc' => $e['element_type'] == 1 ? 'Additif' : 'Déductif',
                                    // 'element_desc'   => $e['element_desc'],
                                    'element_desc'      => html_entity_decode($e['element_desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                                    'element_ordre'     => $e['element_ordre'],
                                    'element_pourcent'  => $e['element_pourcent'],
                                    'actif'             => $e['actif']
                                );
                            }
                        }

                        $grille['elements'] = $elements;
                    }
                }

                // assembler le json de la question


                $json_arr['questions'][] = array(
                    'question_id'           => $q['question_id'],
                    'ordre'                 => $q['ordre'],
                    'bloc_id'               => $q['bloc_id'],
                    // 'question_texte'     => $q['question_texte'],
                    'question_texte'        => html_entity_decode($q['question_texte'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    'question_type'         => $q['question_type'],
                    'question_type_desc'    => $this->config->item('questions_types')[$q['question_type']]['desc'],
                    'question_points'       => $q['question_points'],
                    'sondage'               => $q['sondage'],
                    'reponses_aleatoires'   => $q['reponses_aleatoires'],
                    'selecteur'             => $q['selecteur'],
                    'actif'                 => $q['actif'],
                    'reponses'              => $reponses_arr,
                    'tolerances'            => $tolerances,
                    'similarites'           => $similarites,
                    'grille'                => $grille
                );
            }
        }

        $json = json_encode($json_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES);

        return $json;
	}
}
