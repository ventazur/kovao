<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This file is part of Kovao - http://kovao.com/
//
// Kovao is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, version 3 of the License.
//
// Kovao is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Kovao.  If not, see <http://www.gnu.org/licenses/>.

/* ============================================================================
 *
 * LABORATOIRES
 *
 * ----------------------------------------------------------------------------
 *
 * Ceci est un projet prototype pour permettre aux etudiants de remplir
 * un rapport de laboratoire pendant la realisation de l'experience au labo.
 *
 * ============================================================================ */

class Laboratoires extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        //
        // Autorisations minimales
        //

        if ( ! $this->logged_in)
        {
            redirect();
            exit;
        }

        //
        // Parametres
        //

        $this->traces = TRUE;

        //
        // Constantes
        //

        $this->lab = array(
            'cours_ids' => array(
                1 => ['sigle' => 'sn1'],
                3 => ['sigle' => 'sn2']
            )
        );

        $this->load->model('Lab_model');
    }

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
	public function _remap($methode, $args = array())
    {
        $this->data['current_method'] = $methode;

        $methodes_enseignants = array(
            'index', 'copier', 'creer', 'editeur', 'groupe', 'previsualisation',
            'modal_modifier_champ', 'modal_modifier_champ_sauvegarde',
            'modifier_ordre_laboratoire', 'modifier_titre_laboratoire', 'effacer_laboratoire',
            'modifier_description', 'effacer_description', 'modifier_instructions', 'effacer_instructions',
            'verifier_json', 'sauvegarder_json',
            'ajouter_question', 'modifier_question', 'activer_desactiver_question', 'dupliquer_question', 'effacer_question',
            'changer_reponses_aleatoires', 'changer_ordre_question'
        );

        $methodes_etudiants = array(
            'rapport'
        );

        $methodes_ajax = array(
            'modal_modifier_champ', 'modal_modifier_champ_sauvegarde',
            'changer_ordre_laboratoire', 'modifier_titre_laboratoire', 'effacer_laboratoire',
            'modifier_description', 'effacer_description', 'modifier_instructions', 'effacer_instructions',
            'verifier_json', 'sauvegarder_json',
            'ajouter_question', 'modifier_question', 'activer_desactiver_question', 'dupliquer_question', 'effacer_question',
            'changer_reponses_aleatoires', 'changer_ordre_question'
        );

        //
        // ajax
        //

        if (in_array($methode, $methodes_ajax))
        {
            if ( ! $this->input->is_ajax_request()) 
            {
                exit('No direct script access allowed');
            }
        }

        //
        // Enseignants
        //

        if ($this->enseignant && in_array($methode, $methodes_enseignants))
        {
            switch($methode)
            {
                //
                // index
                //

                case 'index' :
                    $this->index();
                    return;
                    break;

                //
                // copier
                //

                case 'copier' :

                    if (empty($args[0]) || ! is_numeric($args[0]) || $args[0] < 0)
                    {
                        redirect(base_url());
                        exit;
                    }

                    $this->copier($args[0]);
                    return;
                    break;

                //
                // creer
                //

                case 'creer' :
                    $this->creer();
                    return;
                    break;

                //
                // editeur
                //

                case 'editeur' :

                    if (empty($args[0]) || ! is_numeric($args[0]) || $args[0] < 0)
                    {
                        redirect(base_url());
                        exit;
                    }

                    $groupe = FALSE;

                    if ( ! empty($args[1]) && $args[1] == 'groupe')
                        $groupe = TRUE;

                    $this->editeur($args[0], $groupe);
                    return;
                    break;

                //
                // laboratoires du groupe
                //

                case 'groupe' :
                    $this->index();
                    return;
                    break;

                //
                // previsualisation
                //

                case 'previsualisation' :

                    echo 'coucou'; die;

                    if (empty($args[0]) || ! is_numeric($args[0]) || $args[0] < 0)
                    {
                        redirect(base_url());
                        exit;
                    }

                    $groupe = FALSE;

                    if ( ! empty($args[1]) && $args[1] == 'groupe')
                        $groupe = TRUE;

                    $this->previsualisation($args[0], $args[1]);
                    return;
                    break;

                default:
                    $this->$methode();
                    return;
                    break;

            } // switch
        } // if

        //
        // Etudiants
        //

        else
        {
            if ($this->etudiant && in_array($methode, $methodes_etudiants))
            {
                switch($methode)
                {
                    case 'rapport' : 
                        if (empty($args[0]) || ! ctype_alpha($args[0]) || strlen($args[0]) != 6)
                        {
                            redirect(base_url());
                            exit;
                        }

                        echo 'montrer rapport';

                        break;
                }

            } // if
        } // else

        redirect(base_url());
        exit;
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        $this->_liste();
    }

    /* ------------------------------------------------------------------------
     *
     * Creer un nouveau laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function creer()
    {
        //
        // Extraire tous les cours du groupe
        //

        $cours = $this->Cours_model->lister_cours(array('groupe_id' => $this->groupe_id));

        if (empty($cours))
        {
            $this->_affichage('creer_aucun_cours');
            return;
        }

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Definition des messages d'erreur
        //

		$errors = array();
		$errors = array(
			'evaluation_titre' => null
		);

        $this->form_validation->set_rules('laboratoire_titre', 'Titre', 'required|min_length[4]|max_length[250]');
        $this->form_validation->set_rules('laboratoire_cours_id', 'Cours_ID', 'required|numeric');
        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
		// Validation du formulaire (form)
		// 

        $this->data['errors'] = $errors;

       	if ($this->form_validation->run() == FALSE)
        {
			//
			// Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
			//

			if ($this->form_validation->error('laboratoire_titre') !== '')
			{
				$this->data['errors']['laboratoire_titre'] = 'is-invalid'; // pour bootstrap
			}
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			// Verification de l'autorisation a se connecter.
			//

			$post_data = $this->input->post();

            $laboratoire_id = $this->Lab_model->creer_laboratoire($post_data);

            if ($laboratoire_id != FALSE)
            {
                redirect(base_url() . 'laboratoires/editeur/' . $laboratoire_id . '/groupe');
                exit;
            }

            redirect(base_url() . 'laboratoires');
            exit;
        }

        $this->data = array_merge(
            array(
                'cours_raw' => $cours
            ), $this->data
        );

        $this->_affichage('creer');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Copier un laboratoire dans mes laboratoires
     *
     * ------------------------------------------------------------------------ */
    public function copier($lab_id)
    {
        $lab     = $this->Lab_model->extraire_lab($lab_id, array('public' => TRUE));
        $labs_en = $this->Lab_model->extraire_labs();

        //
        // Verifier que ce labo n'existe pas deja dans les laboratoires de l'enseignant
        //

        if ( ! empty($labs_en))
        {
            foreach($labs_en as $le)
            {
                if ($le['lab_id_source'] == $lab_id && $le['lab_version'] == $lab['lab_version'])
                {
                    redirect(base_url() . 'laboratoires');
                    exit;
                }
            }
        }

        $n_lab_id = $this->Lab_model->copier_laboratoire($lab_id);

        //
        // Flash message
        //
        // @TODO
        //

        redirect(base_url() . 'laboratoires');
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Liste des laboratoires disponibles
     *
     * ------------------------------------------------------------------------ */
    public function _liste()
    {
        $this->laboratoires_groupe = $this->data['laboratoires_groupe'] = $this->uri->segment(2) == 'groupe' ? TRUE : FALSE;

		//
		// Verifier que les conditions de previsualisation soient remplies.
        //

        $arguments = $this->uri->segment_array();

        $cours_raw = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $this->groupe_id   
            )
        );

        $view = NULL;


        //
        // Extraire les laboratoires
        //

        $labs    = $this->Lab_model->extraire_labs(array('public' => $this->laboratoires_groupe));
        $labs_en = $this->Lab_model->extraire_labs();

        //
        // Cours avec au moins un laboratoire
        // (array de cours_id)
        //

        $cours_laboratoires_existent = array();

        foreach($labs as $l)
        {
            if ( ! in_array($l['cours_id'], $cours_laboratoires_existent))
                $cours_laboratoires_existent[] = $l['cours_id'];
        }

        //
        // Prepare l'affichage
        //

        $this->data = array_merge(
            array(
                'cours_raw' => $cours_raw,
                'labs'      => $labs,
                'labs_en'   => $labs_en ?? array(),
                'cours_laboratoires_existent' => $cours_laboratoires_existent
            ), $this->data
        );

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Editeur
     *
     * ------------------------------------------------------------------------ */
    public function editeur($lab_id, $groupe = FALSE)
    {
        $this->laboratoire_groupe = $this->data['laboratoire_groupe'] = $groupe;

        //
        // Extraire le labo
        //

        $lab = $this->Lab_model->extraire_lab($lab_id, array('public' => $this->laboratoire_groupe));

        //
        // Est-ce que le labo existe ?
        //

        if (empty($lab))
        {
            redirect(base_url() . 'laboratoires');
            exit;
        }

        //
        // Extraire les valeurs du laboratoire
        //

        $labv = json_decode($lab['lab_valeurs'], TRUE);

        //
        // Extraire le cours
        //

        $cours = $this->Cours_model->extraire_cours(array('cours_id' => $lab['cours_id']));

        //
        // Permissions (laboratoire)
        //
        // (Ceci est inutile pour les laboratoires mais je le hack pour m'eviter de changer le code de la vue.)
        //

        $permissions = array(
            'lire', 'ajouter_question', 'importer_question', 'modifier', 'changer_reponsable', 'effacer'
        );

        //
        // Permissions questions
        //
        // (Ceci est inutile pour les laboratoires mais je le hack pour m'eviter de changer le code de la vue.)
        //

        $permissions_questions = array(); // a definir plus bas

        //
        // Questions
        //

        $questions = $this->Question_model->lister_questions($lab_id, array('laboratoire' => TRUE));

        $question_ids = array_keys($questions);

        $questions_types_permises = array(1, 2, 4, 5, 6, 7, 8, 10, 11, 12);

        //
        // Reponses
        //

        $reponses_toutes = $this->Reponse_model->lister_reponses_toutes($question_ids);

        //
        // Itererer a travers les questions
        //

        $pointage = 0;
        $nb_questions_reel = 0;

        foreach($questions as $question_id => $q)
        {
            //
            // Si le texte de la question est en JSON, il faut le convertir.
            //

            $questions[$question_id]['question_texte'] = json_decode($q['question_texte']) ?: $q['question_texte'];

            // 
            // Verifier la permission
            //

            // Ces renseignements sont necessaires pour les permissions des questions.
            $q['enseignant_id'] = $lab['enseignant_id'];
            $q['public']        = $lab['public'];

            $permissions_questions[$question_id] = array(
                'modifier', 'ajouter_reponse', 'effacer'
            );

            //
            // Reponses
            //

            $reponses[$question_id] = array();
            
            if (array_key_exists($question_id, $reponses_toutes) && ! empty($reponses_toutes[$question_id]))
            {
                $reponses[$question_id] = $reponses_toutes[$question_id];
            }

            if ($q['actif'])
            {
                // Ne pas compter les points des questions sans reponse.
				//
                // (Je ne suis plus certain que cela soit pertinent suite a une
                // verification d'integrite plus approfondie des evaluations lors
                // de la previsualisation. (2019/02/01))
                //
                // if (empty($reponses[$question_id]) && $q['question_type'] != 2) 
                //    continue;

                $pointage = $pointage + $q['question_points'];
                $nb_questions_reel++;
            }

        } // foreach $questions

        //
        // Variables manquantes (pour l'instant)
        //

        $this->data['pointage'] = 10;
        $this->data['laboratoire_a_remplir'] = FALSE;

        $this->data = array_merge(
            array(
                'cours'         => $cours,
                'lab_id'        => $lab_id,
                'lab'           => $lab,
                'labv'          => $labv,
                'questions'     => $questions,
                'question_ids'  => $question_ids,
                'reponses'      => $reponses,
                'permissions'   => $permissions,
                'permissions_questions' => $permissions_questions,
                'questions_types_permises' => $questions_types_permises
            ), $this->data
        );

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Montrer le rapport aux enseignants (previsualisation)
     *
     * ------------------------------------------------------------------------ */
    public function previsualisation($lab_id, $groupe)
    {
        // 
        // Semestre
        //

        $this->data['semestre'] = $this->semestre;

        //
        // Affichage de la barre d'information
        //

        $this->data['lab_details'] = array(
            'enseignant_nom'    => $this->enseignant['nom'],
            'enseignant_prenom' => $this->enseignant['prenom'],
            'enseignant_genre'  => $this->enseignant['genre']
        );

        $this->data['previsualisation'] = TRUE;

        $this->_montrer_rapport($lab_id, $groupe);
    }

    /* ------------------------------------------------------------------------
     *
     * Montrer le rapport aux etudiants (par ref)
     *
     * ------------------------------------------------------------------------ */
    public function rapport($ref)
    {
        // 
        // Semestre
        //

        // *** Extraire de rel_enseignants_laboratoires
        $this->data['semestre'] = $this->semestre;

        //
        // Affichage de la barre d'information
        //

        // Extraire ces informations de rel_enseignants_lab
        $this->data['lab_details'] = array(
            'enseignant_nom'    => NULL,
            'enseignant_prenom' => NULL,
            'enseignant_genre'  => NULL
        );

        $this->_montrer_rapport($lab_id);
    }

    /* ------------------------------------------------------------------------
     *
     * Montrer le rapport
     *
     * ------------------------------------------------------------------------ */
    public function _montrer_rapport($lab_id, $groupe)
    {
        // L'autorisation minimale est requise (__construct).

        //
        // Extraire le laboratoire
        //

        $lab = $this->Lab_model->extraire_lab($lab_id, array('public' => $groupe));

        if (empty($lab) || ! is_array($lab) || $lab == FALSE)
        {
            redirect(base_url());
            return;
        }
        
		//
        // Verifier si ce laboratoire appartient a ce groupe,
        // sinon rediriger vers le groupe approprie
		//

        $groupe = $this->Groupe_model->extraire_groupe(array('groupe_id' => $lab['groupe_id']));

        if ($groupe['groupe_id'] != $this->groupe_id)
        {
            // Cette evaluation ne fait pas partie de ce groupe.
            // Rediriger vers le bon groupe.

            if ($this->previsualisation)
            {
                redirect('https://' . $groupe['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com'));
                exit;
            }
            else
            {
                redirect('https://' . $groupe['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/laboratoire/' . $lab_reference);
                exit;
            }
        }

		//
		// Ecole
        //

        $ecole = $this->Ecole_model->extraire_ecole(array('ecole_id' => $this->ecole_id));

        //
        // Enseignant
        //

        $enseignant = $this->Enseignant_model->extraire_enseignant($lab['enseignant_id']);

		//
		// Cours
		//

		$cours = $this->Cours_model->extraire_cours(array('cours_id' => $lab['cours_id']));

        $sigle = $this->lab['cours_ids'][$lab['cours_id']]['sigle'];
        $view = $lab['lab_view'];

        $this->data['lab']                  = $lab;
        $this->data['labv']                 = $labv;
        $this->data['lab_reference']        = '000000';
        $this->data['ecole']                = $ecole;
        $this->data['enseignant']           = $enseignant;
        $this->data['cours']                = $cours;
        $this->data['traces']               = array();
        $this->data['documents']            = $documents ?? NULL;
        $this->data['documents_mime_types'] = json_encode($this->config->item('documents_mime_types'));

		//
        // Champs invisibles
		//

		$this->data['hidden'] = array(
			'etudiant_id'               => $this->est_etudiant ? $this->etudiant_id : NULL,
			'lab_id'                    => $lab['lab_id'],
			'enseignant_id'             => $enseignant['enseignant_id'],
			'lab_reference'             => 'lab001',
			'groupe_id'                 => $this->groupe_id,
			'semestre_id'               => $this->semestre['semestre_id'] ?? NULL,
			'confirmation1_q'           => "J'ai bien vérifié toutes mes réponses.",
			'confirmation1'             => NULL,
			'confirmation2_q'           => "Je suis bien informé que seul le premier envoi sera pris en compte.",
			'confirmation2'             => NULL,
			// 'session_id'              => $session_id,
			// 'soumission_debut_epoch'  => $soumission_debut_epoch,
			'previsualisation'          => (@$previsualisation ? 1 : 0), // permet aux enseignants de tester
			'temps_ecoule'              => NULL
 		);

        $this->_affichage($sigle . '/' . $view);
    }

    /* ------------------------------------------------------------------------
     *
     * Methodes AJAX
     *
     * ------------------------------------------------------------------------ */

    function _verifier_post_data($post_data = array(), $variables = array())
    {
        $r = array(
            'succes' => FALSE,
            'erreur' => FALSE,
            'erreur_msg' => '' // le message d'erreur
        );

        if (empty($variables))
        {
            $r['succes'] = TRUE;

            return $r;
        }

        foreach($variables as $v)
        {
            if ( ! array_key_exists($v, $post_data))
            {
                $r['erreur'] = TRUE;
                $r['erreur_msg'] = "le champ [" . $v . "] est manquant";

                return $r;
            }

            //
            // 'lab_id'
            //

            if ($v == 'lab_id')
            {
                if (empty($post_data['lab_id']))
                {
                    $r['erreur'] = TRUE;
                    $r['erreur_msg'] = "lab_id est vide";

                    return $r;
                }

                if ( ! is_numeric($post_data['lab_id']))
                {
                    $r['erreur'] = TRUE;
                    $r['erreur_msg'] = "lab_id n'est pas un nombre";

                    return $r;
                }

                if ($post_data['lab_id'] < 1)
                {
                    $r['erreur'] = TRUE;
                    $r['erreur_msg'] = "lab_id est zero ou negatif";

                    return $r;
                }
            } // if lab_id

        } // foreach

        $r['succes'] = TRUE;

        return $r;
    }

    /* ------------------------------------------------------------------------
     *
     * Modal - Modifier Champ
     *
     * ------------------------------------------------------------------------
     *
     * Cette fonction genere le corps du modal pour modifier un champ,
     * mais ne modifie par le champ lui-meme.
     * Ce modal est genere dynamiquement.
     *
     * ------------------------------------------------------------------------ */
    function modal_modifier_champ()
    {
        $post_data = $this->input->post();
        
        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('lab_id', $post_data) || empty($post_data['lab_id']) || ! is_numeric($post_data['lab_id']))
        {
            echo json_encode(FALSE);
            return;
        }
        
        if ( ! array_key_exists('champ', $post_data) || empty($post_data['champ']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('laboratoire_groupe', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Extraire le laboratoire
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $post_data['laboratoire_groupe']));

        $lab_valeurs = json_decode($lab['lab_valeurs'], TRUE);

        if ( ! array_key_exists($post_data['champ'], $lab_valeurs))
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'lab_id' => $post_data['lab_id'],
            'champ'  => $post_data['champ'],
            'l'      => $lab,
            'lv'     => $lab_valeurs,
            'laboratoire_groupe' => $post_data['laboratoire_groupe']
        );

        //
        // Retourner la page html
        //

        echo json_encode(
            array(
                'html' => $this->load->view('laboratoires/_editeur_modal_champ_body', $data, TRUE)
            )
        );
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modal - Modifier Champ - Sauvegarde
     *
     * ------------------------------------------------------------------------
     *
     * "champ": {
     *  "tableau": (int),
     *  "desc":    (string),
     *  "valeur":  (string),
     *  "notsci":  (string),
     *  "unites":  (string)
     *  "a_incertitude": (bool) 0 ou 1
     *
     * "champ_d": {
     *  "valeur":  (string),
     *  "est_incertitude": (bool) 0 ou 1
     *
     * ------------------------------------------------------------------------ */
    function modal_modifier_champ_sauvegarde()
    {
        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        $champs_obligatoires         = array('lab_id', 'champ');
        $champs_obligatoires_champ   = array('desc', 'valeur', 'notsci', 'unites');
        $champs_obligatoires_champ_d = array('valeur');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data) || empty($post_data[$c]))
            {
                echo json_encode(FALSE);
                return;
            }
        }

        if ( ! array_key_exists('lab_id', $post_data) || empty($post_data['lab_id']) || ! is_numeric($post_data['lab_id']))
        {
            echo json_encode(FALSE);
            return;
        }
        
        if ( ! array_key_exists('champ', $post_data) || empty($post_data['champ']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('laboratoire_groupe', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Extraire le laboratoire
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $post_data['laboratoire_groupe']));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        $lab_valeurs = json_decode($lab['lab_valeurs'], TRUE);

        //
        // Reconstruire le json avec les changements
        //

        $changement_trouve = FALSE;

        foreach($post_data as $k => $v)
        {
            if (stripos($k, '-') === FALSE)
                continue;
    
            $exp = explode('-', $k);

            $c  = $exp[0]; // champ
            $sc = $exp[1]; // sous-champ

            if (array_key_exists($c, $lab_valeurs))
            {
                if ($lab_valeurs[$c][$sc] != $v)    
                {
                    $changement_detecte = TRUE;

                    $lab_valeurs[$c][$sc] = $post_data[$k];
                    continue;
                }
            }
        }

        if ($changement_detecte)
        {
            $nom_db = $post_data['laboratoire_groupe'] ? $this->lab_t : $this->labe_t;

            $this->db->where('lab_id', $post_data['lab_id']);
            $this->db->update($nom_db, array('lab_valeurs' => json_encode($lab_valeurs)));
        }

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier json
     *
     * ------------------------------------------------------------------------ */
    public function verifier_json()
    {
        $post_data = $this->input->post();

        if ( ! array_key_exists('contenu_json', $post_data) || empty($post_data['contenu_json']))
        {
            echo json_encode(FALSE);
            return;
        }

        if (json_decode($post_data['contenu_json']) == NULL)
        {
            echo json_encode(FALSE);
            return;
        }

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Sauvegarder json
     *
     * ------------------------------------------------------------------------ */
    public function sauvegarder_json()
    {
        $post_data = $this->input->post();

        //
        // Verifier les champs
        //

        if ( ! array_key_exists('lab_id', $post_data) || empty($post_data['lab_id']) || ! is_numeric($post_data['lab_id']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('contenu_json', $post_data) || empty($post_data['contenu_json']))
        {
            echo json_encode(FALSE);
            return;
        }

        if (json_decode($post_data['contenu_json']) == NULL)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Extraire le lab a changer
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => TRUE));

        if ($lab['lab_valeurs'] == $post_data['contenu_json'])
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', array('lab_valeurs' => $post_data['contenu_json']));
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier le titre d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function modifier_titre_laboratoire()
    {
        $post_data = $this->input->post();

        //
        // Verifier les champs
        //

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'lab_titre', 'laboratoire_groupe')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        //
        // Verifier les champs specifiques
        //

        if (strlen($post_data['lab_titre']) < 4 || strlen($post_data['lab_titre']) > 250)
        {
            // echo 'erreur lab_titre';
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('laboratoire_groupe', $post_data))
        {
            // echo 'erreur laboratoire_groupe';
            echo json_encode(FALSE);
            return;
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier s'il y a un changement
        //

        if ($lab['lab_titre'] == $post_data['lab_titre'])
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'lab_titre' => $post_data['lab_titre']
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier la description d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function modifier_description()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe', 'description')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        if (empty($post_data['description']))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier s'il y a un changement
        //

        if ($lab['lab_desc'] == $post_data['description'])
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'lab_desc' => $post_data['description']
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer la description d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function effacer_description()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'lab_desc' => NULL
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier les instructions d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function modifier_instructions()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe', 'instructions')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        if (empty($post_data['instructions']))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier s'il y a un changement
        //

        if ($lab['instructions'] == $post_data['instructions'])
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'instructions' => $post_data['instructions']
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer les instructions d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function effacer_instructions()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'instructions' => NULL
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier ordre d'un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function modifier_ordre_laboratoire()
    {
        $post_data = $this->input->post();

        //
        // Verifier les champs
        //

        if ( ! array_key_exists('lab_id', $post_data) || empty($post_data['lab_id']) || ! is_numeric($post_data['lab_id']))
        {
            // echo 'erreur lab_id';
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('ordre', $post_data) || empty($post_data['ordre']))
        {
            // echo 'erreur ordre';
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('laboratoire_groupe', $post_data))
        {
            // echo 'erreur laboratoire_groupe';
            echo json_encode(FALSE);
            return;
        }

        //
        // Determiner si ce laboratoire appartient au groupe
        //

        $groupe = FALSE;

        if ($post_data['laboratoire_groupe'])
        {
            $groupe = TRUE;
        }

        //
        // Extraire le lab
        //

        $lab = $this->Lab_model->extraire_lab($post_data['lab_id'], array('public' => $groupe));

        //
        // Verifier les permissions
        //

        if ($lab['enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier s'il y a un changement
        //

        if ($lab['ordre'] == $post_data['ordre'])
        {
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'ordre' => $post_data['ordre']
        );
    
        $this->db->where ('lab_id', $post_data['lab_id']);
        $this->db->update('laboratoires', $data);
        
        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function effacer_laboratoire()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        //
		// Effacer un laboratoire
		//

		if ($this->Lab_model->effacer_laboratoire($post_data['lab_id'], array('public' => $post_data['laboratoire_groupe'])) != TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Ajouter une question
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('lab_id', 'laboratoire_groupe')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'question_texte' :
					$validation_rules = 'required';
					break;
	
				case 'question_type' :
					$validation_rules = 'required|numeric';
					break;

				case 'question_points' :
					$validation_rules = 'required|decimal';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effectuer l'ajout de la question
		//

		if ($this->Question_model->ajouter_question($post_data['lab_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier une question
     *
     * ------------------------------------------------------------------------ */
    public function modifier_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'question_texte' :
					$validation_rules = 'required';
					break;
	
				case 'question_type' :
					$validation_rules = 'required|numeric';
					break;

				case 'question_points' :
					$validation_rules = 'required|decimal';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

        //
        // Question
        //

        $question = $this->Question_model->extraire_question($post_data['question_id'], NULL, array('laboratoire' => TRUE));

        //
        // Verifier la permission
        //

        if ($question['ajout_par_enseignant_id'] != $this->enseignant_id)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Effectuer les modifications a la question demandee
		//

		if ($this->Question_model->modifier_question($post_data['question_id'], $post_data, array('laboratoire' => TRUE)) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
        return;

    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Activer/Deactiver une question
     *
     * ------------------------------------------------------------------------ */
    public function activer_desactiver_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Question_model->activer_desactiver_question($post_data['question_id'], array('laboratoire' => TRUE)) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Dupliquer une question
     *
     * ------------------------------------------------------------------------ */
    public function dupliquer_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Question_model->dupliquer_question($post_data['question_id'], array('laboratoire' => TRUE)) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une question
     *
     * ------------------------------------------------------------------------ */
    public function effacer_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		//
		// Effacement
		//

		if ($this->Question_model->effacer_question_et_reponses($post_data['question_id'], array('laboratoire' => TRUE)) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer l'ordre servant a la classification des questions.
     *
     * ------------------------------------------------------------------------ */
    public function changer_ordre_question()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

        if ($post_data['ordre'] != '0')
        {
            if ( ! is_float($post_data['ordre']) && ! is_numeric($post_data['ordre']))
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Question_model->changer_ordre($post_data['question_id'], $post_data['ordre'], array('laboratoire' => TRUE)) !== TRUE)
        {
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }


    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer reponses aleatoires (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function changer_reponses_aleatoires()
    {
        $post_data = $this->input->post();

        if ($r = $this->_verifier_post_data($post_data, array('question_id')))
        {
            if ( ! $r['succes'])
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Question_model->changer_reponses_aleatoires($post_data['question_id'], $post_data['checked'], array('laboratoire' => TRUE)) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        if (empty($page))
        {
            $this->load->view('laboratoires/liste', $this->data);
        }
        else
        {
            $this->load->view('laboratoires/' . $page, $this->data);
        }

        $this->load->view('commons/footer', $this->data);
    }
}
