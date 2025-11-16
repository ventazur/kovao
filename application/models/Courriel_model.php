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
 * COURRIEL MODEL
 * 
 * ============================================================================ */

use \Mailjet\Resources;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class Courriel_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();

        //
        // Options globales
        //

        $this->options = array(
            'origine_nom'      => 'KOVAO',
            'origine_courriel' => 'nepasrepondre@kovao.com'
        );

        $this->marge = 5; // marge de manoeuvre pour les limites
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel
	 *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel($options = array())
    {
        //
        // Ne pas envoyer de courriel en developpement/
        //

        if (@$this->is_DEV && ! $this->config->item('courriels_dev'))
        {
            return TRUE;
        }

        //
        // Validation des champs obligatoires
        //

        $champs_obligatoires = array(
            'destination_courriel', 'sujet', 'contenu', 'contenu_data', 'raison'
        );

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
            {
                log_alerte(
                    array(
                        'code'       => 'CRL199', 
                        'desc'       => "Courriel : Un champ obligatoire est absent.", 
                        'importance' => 2
                    )
                );

                return FALSE;
            }
        }

        //
        // Initialisation des champs optionnels
        //

        if (array_key_exists('raison_data', $options) && ! empty($options['raison_data']))
        {
            $options['raison_data'] = json_encode($options['raison_data']);
        }

		//
		// Extraire les fournisseurs
		//

		$this->db->from     ('courriels_fournisseurs');
		$this->db->select   ('fournisseur_id, fournisseur_nom, priorite, limite');
		$this->db->where    ('actif', 1);
		$this->db->order_by ('priorite', 'asc');
		
		$query = $this->db->get();
		
		if ( ! $query->num_rows() > 0)
		{
			log_alerte(
				array(
					'code'       => 'CRL210', 
					'desc'       => "Courriel : Aucun fournisseur trouvé", 
					'importance' => 2
				)
			);

			return FALSE;
		}
																																																								  
		$fournisseurs = $query->result_array();
        $fournisseurs = array_keys_swap($fournisseurs, 'fournisseur_id');

		//
		// Initialisation les comptes des fournisseurs
		//

        array_walk($fournisseurs, function(&$f) {
			$f['limite_h'] = 0;
			$f['limite_j'] = 0;
			$f['limite_m'] = 0;

            $f['compte_h'] = 0;
			$f['compte_j'] = 0;
			$f['compte_m'] = 0;
        });

		//
		// Etablir le fournisseur par default comme etant le dernier fournisseur de la liste peu importe sa limite
		//

		$fournisseur_default    = end($fournisseurs)['fournisseur_nom'];
		$fournisseur_id_default = end($fournisseurs)['fournisseur_id']; 

		//
		// Extraire les limites des fournisseurs
		//

		$fournisseurs_limites = array();

		$this->db->from ('courriels_fournisseurs_limites');
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$fournisseurs_limites = $query->result_array();
		}

		//
		// Combiner les limites aux fournisseurs
		//

		if ( ! empty($fournisseurs_limites))
		{
			foreach($fournisseurs_limites as $fl)
			{
				$fournisseurs[$fl['fournisseur_id']]['limite_' . $fl['periode']] = $fl['max'];
			}
		}

        //
		// Determiner le fournisseur
		//

		if (array_key_exists('fournisseur', $options) && ! empty($options['fournisseur']))
        {
			//
			// Un fournisseur particulier a ete impose (pour les TESTS)
			//

			$fournisseur 	= $options['fournisseur'];
			$fournisseur_id = NULL;

			foreach($fournisseurs as $f)
			{
				if ($f['fournisseur_nom'] == $options['fournisseur'])
                {		
					$fournisseur_id = $f['fournisseur_id'];
					break;
				}
			}

			if (empty($fournisseur_id))
            {
				die("Ce fournisseur n'existe pas.");
			}
		}
		else
		{
			//
			// Extraire les courriels envoyes lors du dernier mois + 1 journee
			//

			$this->db->from  ('courriels_envoyes');
			$this->db->where ('epoch >', $this->now_epoch - 60*60*24*33);

			$courriels_envoyes = array();

			$query = $this->db->get();
			
			if ($query->num_rows() > 0)
			{
				$courriels_envoyes = $query->result_array();
			}

			//
			// Proceder au compte des courriels
			//

			$heure = date('YmdH', $this->now_epoch);
			$jour  = date('Ymd', $this->now_epoch);
			$mois  = date('Ym', $this->now_epoch);

			foreach($courriels_envoyes as $ce)
			{
				// Determiner les moments de ce courriel envoyee
				$ce_heure = date('YmdH', $ce['epoch']);
				$ce_jour  = date('Ymd', $ce['epoch']);
				$ce_mois  = date('Ym', $ce['epoch']);

				$ce_fournisseur_id = $ce['fournisseur_id'];

				//
				// Verifier que le fournisseur de ce courriel est dans la liste des fournisseurs actifs
				//

				if ( ! array_key_exists($ce_fournisseur_id, $fournisseurs))
					continue;

				//
				// Ne pas compter les courriels pour les fournisseurs n'ayant aucune limite
				//

				if ( ! $fournisseurs[$ce_fournisseur_id]['limite'])
					continue;

				//
				// Proceder au comptage
				//

				//
				// ... de cette heure
				//

				if ($ce_heure == $heure)
				{
					$fournisseurs[$ce_fournisseur_id]['compte_h'] += 1;
				}

				//
				// ... de ce jour
				//

				if ($ce_jour == $jour)
				{
					$fournisseurs[$ce_fournisseur_id]['compte_j'] += 1;
				}

				//
				// ... de ce mois
				//

				if ($ce_mois == $mois)
				{
					$fournisseurs[$ce_fournisseur_id]['compte_m'] += 1;
				}
			}

			//
			// Determiner le fournisseur a utiliser
			//

			$fournisseur    = $fournisseur_default;
			$fournisseur_id = $fournisseur_id_default;

			foreach($fournisseurs as $f)
			{
				if ( ! $f['limite'])
				{
					$fournisseur    = $f['fournisseur_nom'];
					$fournisseur_id = $f['fournisseur_id'];

					break;
				}
				
				//
				// Verifier si une des limites est atteinte
				//

				$limite_atteinte = FALSE;

				foreach(array('h', 'j', 'm') as $periode)
				{
					if ($f['limite_' . $periode])
					{
						if (($f['compte_' . $periode] + $this->marge) >= $f['limite_' . $periode]) // ajout de la marge de manoeuvre
						{		
							$limite_atteinte = TRUE;
							break;
						}
					}
				}

				if ($limite_atteinte)
					continue;

				//
				// Utiliser le premier fournisseur, selon la priorite, n'ayant pas a limite atteinte.
				//

				$fournisseur    = $f['fournisseur_nom'];
				$fournisseur_id = $f['fournisseur_id'];

				break;
			}
		}

        $methode = 'envoyer_courriel_' . $fournisseur;

		//
		// Preparation des logs
		//

		$this->log_data = array(
			'epoch' 		 => $this->now_epoch,
            'date'			 => date_humanize($this->now_epoch, TRUE),
			'fournisseur_id' => $fournisseur_id,
            'courriel'		 => $options['destination_courriel'],
            'raison'         => $options['raison'],
            'raison_data'    => $options['raison_data'] ?? NULL
        );

        $this->$methode($options);

		$this->db->insert('courriels_envoyes', $this->log_data);

        //
        // S'il n'a pas ete possible d'envoyer le courriel, changer
        // pour le fournisseur par default.
        //

        if ($this->log_data['status_code'] != 202)
        {
            // Envoyer le meme courriel via le fournisseur par default.

            $this->log_data['fournisseur_id'] = $fournisseur_id_default;
            $this->log_data['erreur_msg']     = NULL;

            $methode = 'envoyer_courriel_' . $fournisseur_default;

            $this->$methode($options);

            $this->db->insert('courriels_envoyes', $this->log_data);
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : SENDGRID
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_sendgrid($options)
    {
        $email = new \SendGrid\Mail\Mail();

		$email->setFrom     ($this->options['origine_courriel'], $this->options['origine_nom']);
		$email->setSubject  ($options['sujet']);
        $email->addTo       ($options['destination_courriel']);
		$email->addContent  ("text/html", $this->load->view($options['contenu'], $options['contenu_data'], TRUE));

		$sendgrid = new \SendGrid($this->config->item('api_key', 'sendgrid'));
		 
		try {
			$response = $sendgrid->send($email);
            
			/*
				p($response->statusCode());
				p($response->headers());
				p($response->body());
             */

            $this->log_data['status_code'] = $response->statusCode();

            if ($response->statusCode() != 202)
            {
                $erreur = json_decode($response->body())->errors[0]->message;

                $this->log_data['status_code'] = 999;
                $this->log_data['erreur_msg'] = $erreur;

                /*
                log_alerte(
                    array(
                        'code'       => 'CRL385', 
                        'desc'       => "Courriel : SendGrid n'a pu envoyer le courriel.",
                        'extra'      => $erreur,
                        'importance' => 2
                    )
                );
                */
            }

		}
	 	catch (Exception $e)
        {
            log_alerte(
                array(
                    'code'       => 'CRL199', 
                    'desc'       => 'Courriel : (Exception) ' . $e->getMessage(),
                    'importance' => 2
                )
            );

	 	}

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : MAILGUN
	 *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_mailgun($options)
    {
		$data = array(
			'from'						=> $this->options['origine_nom'] . '<' . $this->options['origine_courriel'] . '>',
			'to'						=> '<' . $options['destination_courriel'] . '>',
			'subject'					=> $options['sujet'],
			'html'						=> $this->load->view($options['contenu'], $options['contenu_data'], TRUE)
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
            $this->log_data['status_code'] = 202;

            /*
            Array
            (
                [id] => <20201010131317.1.AA83607AAC486DFA@kovao.com>
                [message] => Queued. Thank you.
            )
            */
		}
		else
		{
            $this->log_data['status_code'] = 999;
		}

        return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : MAILJET
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_mailjet($options)
    {
		$mj = new \Mailjet\Client($this->config->item('api_key', 'mailjet'), $this->config->item('api_secret', 'mailjet'), TRUE, ['version' => 'v3.1']);

		$body = array(
			'Messages' => array(
				array(
					'From' => array(
						'Email' => $this->options['origine_courriel'],
						'Name'  => $this->options['origine_nom']
					),
					'To' => array(
						array(
							'Email' => $options['destination_courriel'],
							'Name'  => NULL
						)
					),
					'Subject' 	=> $options['sujet'],
					'HTMLPart' 	=> $this->load->view($options['contenu'], $options['contenu_data'], TRUE)
				)
			)
		);

		$response = $mj->post(Resources::$Email, ['body' => $body]);

		if ($response->success())
		{
			$this->log_data['status_code'] = 202;
		}
		else
		{
			$this->log_data['status_code'] = 999;
			// var_dump($response->getData());
		}

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : AMAZON
     *
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_amazon($options)
    {
		$SesClient = new SesClient([
			// 'profile' => 'default',
			'version' => '2010-12-01',
			'region'  => $this->config->item('region', 'amazon'),
			'credentials' => [
        		'key'    => $this->config->item('api_key', 'amazon'),
        		'secret' => $this->config->item('api_secret', 'amazon'),
			]			
        ]);

		// This address must be verified with Amazon SES.
		$sender_email = $this->options['origine_nom'] . '<' . $this->options['origine_courriel'] . '>';

		$recipient_emails = [$options['destination_courriel']];

		// Specify a configuration set. If you do not want to use a configuration
		// set, comment the following variable, and the
		// 'ConfigurationSetName' => $configuration_set argument below.
		// $configuration_set = 'ConfigSet';

        $subject = $options['sujet'];

		// $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;

        $html_body = $this->load->view($options['contenu'], $options['contenu_data'], TRUE);

		$char_set = 'UTF-8';

		try 
		{
			$result = $SesClient->sendEmail([
				'Destination' => [
					'ToAddresses' => $recipient_emails,
				],
				'ReplyToAddresses' => [$sender_email],
				'Source' => $sender_email,
				'Message' => [
				  'Body' => [
					  'Html' => [
						  'Charset' => $char_set,
						  'Data' => $html_body,
                      ],
                      /*
					  'Text' => [
						  'Charset' => $char_set,
						  'Data' => $plaintext_body,
                      ],
                      */
				  ],
				  'Subject' => [
					  'Charset' => $char_set,
					  'Data' => $subject,
				  ],
				],
				// If you aren't using a configuration set, comment or delete the
				// following line
				// 'ConfigurationSetName' => $configuration_set,
            ]);

            // $messageId = $result['MessageId'];
            
			$this->log_data['status_code'] = 202;
		} 
		catch (AwsException $e) 
        {
            /*
			echo $e->getMessage();
			echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
            echo "\n";
            */
            
            $this->log_data['status_code'] = 999;
            $this->log_data['erreur_msg'] = $e->getAwsErrorMessage();
		}

        return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Envoyer un courriel : SENDINBLUE
   	 *  
     * -------------------------------------------------------------------------------------------- */
    function envoyer_courriel_sendinblue($options)
    {
		$data = array(
			'sender' => array(
                'name'  => $this->options['origine_nom'],
				'email' => $this->options['origine_courriel']
			),
			'to' => array(
				array(
					'email' => $options['destination_courriel']
				),
			),
			'subject'	  => $options['sujet'],
			'htmlContent' => $this->load->view($options['contenu'], $options['contenu_data'], TRUE)
        );

		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => $this->config->item('api_url', 'sendinblue'),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => [
			'accept: '  	 . 'application/json',
			'api-key: ' 	 . $this->config->item('api_key', 'sendinblue'),
			'content-type: ' . 'application/json'
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

        curl_close($curl);

        // Response ;
        // string
        // {"messageId":"<202010101534.48323908480@smtp-relay.mailin.fr>"}

		if ( ! $err) 
		{
			$this->log_data['status_code'] = 202;
		} 
		else 
		{
            $this->log_data['status_code'] = 999;
            $this->log_data['erreur_msg'] = $err;
		}

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Statistiques des fournisseurs
   	 *  
     * -------------------------------------------------------------------------------------------- */
    function statistiques_fournisseurs()
    {
		//
		// Extraire les fournisseurs
		//

		$this->db->from     ('courriels_fournisseurs');
		$this->db->select   ('fournisseur_id, fournisseur_nom');
		$this->db->order_by ('priorite', 'asc');
		
		$query = $this->db->get();
		
		if ( ! $query->num_rows() > 0)
        {
            echo 'Aucun fournisseur trouve';
			return FALSE;
		}
																																																								  
		$fournisseurs = $query->result_array();
        $fournisseurs = array_keys_swap($fournisseurs, 'fournisseur_id');

        array_walk($fournisseurs, function(&$f) {
            $f['suc'] = array(
                'h' => 0,
                'j' => [],
                'm' => []
            );
            $f['err'] = array(
                'h' => 0,
                'j' => [],
                'm' => []
            );
        });

        //
        // Extraire les courriels envoyes lors du dernier mois + 1 journee
        //

        $query = $this->db->get('courriels_envoyes');

        $courriels_envoyes = array();
        
        if ($query->num_rows() > 0)
        {
            $courriels_envoyes = $query->result_array();
        }

        //
        // Proceder au compte des courriels
        //

        foreach($courriels_envoyes as $ce)
        {
            $f_id = $ce['fournisseur_id'];

            if ( ! array_key_exists($f_id, $fournisseurs))
                continue;

            if ($ce['status_code'] == 202)
            {
                $status = 'suc';
            }
            else
            {
                $status = 'err';
            }

            $h  = date('YmdH', $this->now_epoch);
            $ch = date('YmdH', $ce['epoch']); 

            $j  = date('Ymd', $this->now_epoch);
            $cj = date('Ymd', $ce['epoch']);

			$m  = date('Ym', $this->now_epoch);
            $cm = date('Ym', $ce['epoch']);

            if ($ch == $h)
            {
                $fournisseurs[$f_id][$status]['h']++;
            }

            if ( ! array_key_exists($cj, $fournisseurs[$f_id][$status]['j']))
            {
                $fournisseurs[$f_id][$status]['j'][$cj] = 0;
            }

            if ( ! array_key_exists($cm, $fournisseurs[$f_id][$status]['m']))
            {
                $fournisseurs[$f_id][$status]['m'][$cm] = 0;
            }

            $fournisseurs[$f_id][$status]['j'][$cj]++;
            $fournisseurs[$f_id][$status]['m'][$cm]++;
        }

        return $fournisseurs;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Statistiques pour mailjet
     *  
     * --------------------------------------------------------------------------------------------
     *
     * Documentation : https://dev.mailjet.com/email/guides/statistics/#key-performance-statistics
     *
     * -------------------------------------------------------------------------------------------- */
    function statistiques_mailjet()
    {
		$mj = new \Mailjet\Client($this->config->item('api_key', 'mailjet'), $this->config->item('api_secret', 'mailjet'), TRUE, ['version' => 'v3']);

		$filters = [
		  'CounterSource' => 'ApiKey',
		  'CounterTiming' => 'Message',
		  'CounterResolution' => 'Lifetime'
		];

		$response = $mj->get(Resources::$Statcounters, ['filters' => $filters]);

		if ($response->success())
		{
			return($response->getData());
		}

		return 'Aucune statistique disponible';
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Statistiques pour amazon
     *  
     * --------------------------------------------------------------------------------------------
     *
     * Documentation : https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getsendstatistics
     *
     * -------------------------------------------------------------------------------------------- */
    function statistiques_amazon()
    {
		$SesClient = new SesClient([
			// 'profile' => 'default',
			'version' => '2010-12-01',
			'region'  => $this->config->item('region', 'amazon'),
			'credentials' => [
        		'key'    => $this->config->item('api_key', 'amazon'),
        		'secret' => $this->config->item('api_secret', 'amazon'),
			]			
		]);

		try 
		{
			return $SesClient->getSendStatistics([]);
		} 
		catch (AwsException $e) 
        {
            /*
			echo $e->getMessage();
			echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
            echo "\n";
            */
            
            $this->log_data['status_code'] = 999;
            $this->log_data['erreur_msg'] = $e->getAwsErrorMessage();
		}

        return TRUE;
    }
}
