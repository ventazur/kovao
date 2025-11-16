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
 * MAIL MODEL
 *
 * ============================================================================ */

use \Mailjet\Resources;

class Mail_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();

		// $this->options = $this->config->item('courriel_options');
		
        $this->options = array(
            'from'       => 'KOVAO',
            'from-email' => 'nepasrepondre@' . $this->domaine
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel
	 *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel($courriel, $sujet, $contenu, $contenu_data = array())
    {
		// Test
        // return $this->envoyer_courriel_mailjet($courriel, $sujet, $contenu, $contenu_data);

		$method = 'envoyer_courriel_';
        
        //
        // Determiner si une specificite est presente
        //
        // Une specificite est un domaine qui necessite un fournisseur specifique
        // parce qu'il est bloque ou encore les courriels ne se rendent pas a destination/
        //

        $specificites = $this->config->item('courriels_specificites');
        $domaine      = strstr($courriel, '@');

        if (array_key_exists($domaine, $specificites))
        {
            $method .= $specificites[$domaine];

            return $this->$method($courriel, $sujet, $contenu, $contenu_data);
        }
            
		//
		// Extraire les fournisseurs
		//
		
		$fournisseurs 		= $this->config->item('courriels_fournisseurs');
		$fournisseurs_ordre = $this->config->item('courriels_fournisseurs_ordre');

		//
		// Etablir un tableau de correspondence fournisseur_nom <=> fournisseur_id
		//

		$fournisseurs_ids   = array();

		foreach($fournisseurs as $f_nom => $f)
		{
			$fournisseurs_ids[$f['id']] = $f_nom;
		}

		//
		// Determiner le fournisseur par default
		// (Il correspond au dernier de la liste de l'ordre des fournisseurs.)
		//

        $fournisseur_default = $fournisseurs_ordre[count($fournisseurs_ordre) - 1];

		//
		// Extraire les courriels envoyes durant les derniers 24h
		//

		$this->db->from  ('courriels_envoyes');
		$this->db->where ('epoch >', $this->now_epoch - 60*60*24);
		$this->db->where ('status_code', 202);
	
		$query = $this->db->get();

		if ( ! $query->num_rows() > 0)
		{
			// Utiliser le premier fournisseur dans l'ordre
			$method .= $fournisseurs_ordre[0];

        	return $this->$method($courriel, $sujet, $contenu, $contenu_data);
		}

		$courriels_envoyes = $query->result_array();

		//
		// Determiner quel fournisseur utiliser
		//

		foreach($fournisseurs_ordre as $fo)
		{
			$fournisseur = $fo;

			// Nous sommes rendus a la fin de la liste, utiliser le
			// fournisseur par default.
			if ($fo == $fournisseur_default)
				break;	

			$fournisseur_id = $fournisseurs[$fo]['id'];
			$max   		    = $fournisseurs[$fo]['max'];
			$count          = 0;
			$suivant        = FALSE;

			foreach($courriels_envoyes as $c)
			{
				$count++;

				if ($count > $max)
				{
					$suivant = TRUE;	
					break;
				}								
			}
	
			if ($suivant === FALSE)
				break;
		}

		$method .= $fournisseur;

        return $this->$method($courriel, $sujet, $contenu, $contenu_data);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : SENDGRID
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_sendgrid($courriel, $sujet, $contenu, $contenu_data = array())
    {
		//
		// Logs
		//

		$log_data = array(
			'epoch' 		 => $this->now_epoch,
			'date'			 => date_humanize($this->now_epoch, TRUE),
			'fournisseur_id' => 1,
			'courriel'		 => $courriel
		);

		//
		// Envoie
		//

		$email = new \SendGrid\Mail\Mail();
		 
		$email->setFrom     ($this->options['from-email'], $this->options['from']);
		$email->setSubject  ($sujet);
		$email->addTo       ($courriel);
		// $email->addContent  ("text/plain", "and easy to do anywhere, even with PHP");
		$email->addContent  ("text/html", $this->load->view($contenu, $contenu_data, TRUE));
		 
		$sendgrid = new \SendGrid($this->config->item('api_key', 'sendgrid'));
		 
		try {
			$response = $sendgrid->send($email);

			/*
				p($response->statusCode());
				p($response->headers());
				p($response->body());
			*/
		}
	 	catch (Exception $e)
		{
			echo 'Caught exception: '. $e->getMessage() . "\n";
	 	}

		//
		// Ecriture des logs
		//

		$log_data['status_code'] = $response->statusCode();

		$this->db->insert('courriels_envoyes', $log_data);

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : MAILGUN
	 *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_mailgun($courriel, $sujet, $contenu, $contenu_data = array())
    {
		//
		// Logs
		//

		$log_data = array(
			'epoch' 		 => $this->now_epoch,
			'date'			 => date_humanize($this->now_epoch, TRUE),
			'fournisseur_id' => 2,
			'courriel'		 => $courriel
		);

		//
		// Envoie
		//

		$data = array(
			'from'						=> $this->options['from'] . '<' . $this->options['from-email'] . '>',
			'to'						=> '<' . $courriel . '>',
			'subject'					=> $sujet,
			'html'						=> $this->load->view($contenu, $contenu_data, TRUE)
			// 'text'					=> $text,
			// 'o:tracking'				=> 'yes',
			// 'o:tracking-clicks'    	=> 'yes',
			// 'o:tracking-opens'  		=> 'yes',
			// 'o:tag'=>				=> $tag,
			// 'h:Reply-To'				=> $this->options['from-email']
        );

		$session = curl_init($this->config->item('mailgun')['api_url'] . '/messages');
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_USERPWD, 'api:' . $this->config->item('mailgun')['api_key']);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $data);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($session);
		curl_close($session);
		$results = json_decode($response, true);

		if (preg_match('/Queued/', $results['message']))
		{
			$log_data['status_code'] = 202;
		}
		else
		{
			$log_data['status_code'] = 999;
		}

		//
		// Ecriture des logs
		//

		$this->db->insert('courriels_envoyes', $log_data);

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : MAILJET
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_mailjet($courriel, $sujet, $contenu, $contenu_data = array())
    {
		//
		// Logs
		//

		$log_data = array(
			'epoch' 		 => $this->now_epoch,
			'date'			 => date_humanize($this->now_epoch, TRUE),
			'fournisseur_id' => 3,
			'courriel'		 => $courriel
		);

		//
		// Envoie
		//

		$mj = new \Mailjet\Client($this->config->item('api_key', 'mailjet'), $this->config->item('api_secret', 'mailjet'), TRUE, ['version' => 'v3.1']);

		$body = array(
			'Messages' => array(
				array(
					'From' => array(
						'Email' => $this->options['from-email'],
						'Name'  => $this->options['from']
					),
					'To' => array(
						array(
							'Email' => $courriel,
							'Name'  => NULL
						)
					),
					'Subject' 	=> $sujet,
					'HTMLPart' 	=> $this->load->view($contenu, $contenu_data, TRUE)
				)
			)
		);

		$response = $mj->post(Resources::$Email, ['body' => $body]);

		if ($response->success())
		{
			$log_data['status_code'] = 202;
		}
		else
		{
			$log_data['status_code'] = 400;
			// var_dump($response->getData());
		}

		//
		// Ecriture des logs
		//

		$this->db->insert('courriels_envoyes', $log_data);

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : AMAZON
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_amazon($courriel, $sujet, $contenu, $contenu_data = array())
    {
		// @TODO
	}

}
