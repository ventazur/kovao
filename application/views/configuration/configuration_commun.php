<div id="configuration">

<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

	<div id="semestre-data" class="d-none" data-semestre_id="<?= $this->enseignant['semestre_id']; ?>"></div>

    <h3>Configuration 
        <? if ($this->sous_domaine === 'www') : ?>
            de votre groupe <span style="color: dodgerblue;">personnel</span>
        <? else : ?>
            <span style="color: dodgerblue;"><?= $this->groupe['sous_domaine']; ?></span>
        <? endif; ?>
    </h3>

    <div class="space"></div>

    <i class="fa fa-lightbulb-o fa-lg" style="color: dodgerblue; margin-right: 5px"></i> Cette page permet de rendre disponible vos évaluations à vos étudiants.

    <div class="tspace"></div>

    <? 
    /* ================================================================
     *
     *  SEMESTRES
     *
     * ================================================================ */ ?>

    <div id="selection-semestres" class="box-section">

        <div class="box-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            SEMESTRES

        </div>

        <div class="box-section-win">

            <? if (empty($semestres)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun semestre trouvé

                <div class="hspace"></div>

                <? if ($this->groupe_id != 0) : ?>
                    Vous devez demander à l'administrateur de votre groupe de créer un semestre.
                <? else : ?>
                    Vous devez ajouter un semestre à votre groupe personnel. 
                    <i class="fa fa-long-arrow-right"></i>
                    <a href="<?= base_url() . 'groupe/gerer'; ?>">Gérer</a>
                <? endif; ?>

            <? else : ?>

                <div class="semestres-liste" style="margin-bottom: -5px">

                <span style="font-weight: 300">Sélectionnez votre semestre actif :</span>

                <div class="space"></div>

                <? foreach($semestres as $s) : ?>

                    <div class="btn choisir-semestre mb-2 <?= $this->enseignant['semestre_id'] == $s['semestre_id'] ? 'btn-dark' : 'btn-outline-primary'; ?>" data-semestre_id="<?= $s['semestre_id']; ?>">
                        <?= $s['semestre_nom']; ?>
                        <i class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>

                <? endforeach; ?>

                </div>
    
                <? if ( ! empty($semestre_status) && $semestre_status != 'actuel') : ?>

                    <div style="font-weight: 300">

                        <div class="space"></div>
                        
                        <? if ($semestre_status == 'passe') : ?>

                            <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 5px"></i>
                            Ce semestre est terminé.

                        <? else : ?>

                            <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 5px"></i>
                            Ce semestre entrera en vigueur le <?= date_french_full($semestres[$semestre_selectionne]['semestre_debut_epoch']); ?>.

                        <? endif; ?>

                    </div>

                <? endif; ?>

            <? endif ; ?>

        </div> <!-- .box-section-win -->

    </div> <!-- .box-section -->

    <div class="tspace"></div>

    <? 
    /* ================================================================
     *
     *  COURS
     *
     * ================================================================ */ ?>

    <div id="selection-cours" class="box-section">

        <div class="box-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            COURS

        </div>

        <div class="box-section-win">

            <? if (empty($cours_raw)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun cours trouvé

                <div class="hspace"></div>

                <? if ($this->groupe_id != 0) : ?>
                    Vous devez demander à l'administrateur de votre groupe de créer un cours.
                <? else : ?>
                    Vous devez ajouter un cours à votre groupe personnel. 
                    <i class="fa fa-long-arrow-right"></i>
                    <a href="<?= base_url() . 'groupe/gerer'; ?>">Gérer</a>
                <? endif; ?>

            <? else : ?>

                <? if (empty($this->enseignant['semestre_id'])) : ?>

                    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Vous devez sélectionner un semestre avant de pouvoir choisir vos cours.

                <? else : ?>

                    <span style="font-weight: 300">Sélectionnez vos cours pour le semestre actif :</span>

                    <div class="space"></div>

                    <? foreach($cours_raw as $cours_id => $c) : ?>

                        <div class="btn choisir-cours mb-2  <?= array_key_exists($c['cours_id'], $cours_selectionnes) ? 'btn-dark' : 'btn-outline-primary'; ?>" data-cours_id="<?= $cours_id; ?>">
                            <?= $c['cours_nom_court'] . ' (' . $c['cours_code_court'] . ')'; ?> 
                            <i class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>

                    <? endforeach; ?>
                
                <? endif; ?>

            <? endif; ?>

        </div> <!-- .box-section-win -->

    </div> <!-- .box-section -->

    <div class="tspace"></div>

    <? 
    /* ================================================================
     *
     *  EVALUATIONS
     *
     * ================================================================ */ ?>

    <div id="selection-evaluations" class="box-section">

        <div class="box-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            ÉVALUATIONS

        </div> <!-- .box-section-titre -->

        <div class="box-section-win">

            <? if (empty($evaluations_par_cours)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune évaluation trouvée pour les cours sélectionnés

            <? else : ?>

                <span style="font-weight: 300">Sélectionnez vos évaluations à mettre en ligne pour chacun des cours suivants :</span>

                <? foreach($evaluations_par_cours as $cours_id => $epc) : ?>

                <div class="box-sous-section">

                    <div class="box-sous-section-titre">

                        <i class="fa fa-square-o" style="margin-right: 7px;"></i>
                        <?= $cours_raw[$cours_id]['cours_nom'] . ' (' . $cours_raw[$cours_id]['cours_code'] . ')'; ?>

                    </div> <!-- .box-sous-section-titre -->

                    <div class="box-sous-section-win">

                        <? if (empty($epc)) : ?>

                            <div class="btn btn-block btn-outline-danger disabled" style="text-align: left">
                                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune évaluation trouvée pour ce cours
                            </div>
    
                        <? else : ?>

                            <div class="row no-gutters">

                            <? foreach($epc as $evaluation_id => $e) : ?>

                                <div class="btn btn-block choisir-evaluation <?= array_key_exists($evaluation_id, $evaluations_selectionnees) ? 'btn-dark' : 'btn-outline-primary'; ?>"
                                     style="text-align: left"
                                     data-evaluation_id="<?= $evaluation_id; ?>" 
                                     data-evaluation_selectionnee="<?= array_key_exists($evaluation_id, $evaluations_selectionnees) ? '1' : '0'; ?>"
                                     data-cours_id="<?= $e['cours_id']; ?>">
                                    <div class="row">
                                        <div class="col-md-9">
                                            (<?= $cours_raw[$cours_id]['cours_code_court']; ?>)
                                            <span style="margin-left: 10px"><?= $e['evaluation_titre']; ?></span>
                                            <i class="fa fa-circle-o-notch fa-spin d-none evaluation-spinner" style="margin-left: 5px"></i>
                                        </div>
                                        <div class="col-md-3" style="text-align: right">
                                            <? if ( ! $e['public']) : ?>
                                                <span class="badge badge-primary">Mon évaluation</span>
                                            <? else : ?>
                                                <span class="badge badge-secondary">Département</span> 
                                            <? endif; ?>
                                        </div>
                                    </div>

                                    <span id="erreur<?= $evaluation_id; ?>" class="d-none erreur-evaluation" 
                                        style="padding: 3px 7px 3px 7px; border-radius: 3px; background: pink; color: crimson; font-size: 0.85em;">
                                        <i class="fa fa-exclamation-circle"></i>
                                        Cette évaluation comporte des erreurs. Veuillez la vérifier en la prévisualisant à partir de l'éditeur.
                                    </span>
                                </div>


                            <?  endforeach; // $epc ?>

                            </div>

                        <? endif; // $epc ?>

                    </div> <!-- .box-sous-section-win -->

                </div> <!-- .box-sous-section -->

                <? endforeach; ?>

                <? endif; // $evaluations_par_cours ?>

        </div> <!-- .box-section-win -->

    </div> <!-- .box-section -->

    <div class="tspace"></div>

    <? 
    /* ================================================================
     *
     *  ETUDIANTS
     *
     * ================================================================ */ ?>

    <div id="selection-etudiants" class="box-section">

        <div class="box-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            ÉTUDIANTS

        </div> <!-- .box-section-titre" -->
    
        <div class="box-section-win">

            <? if (empty($cours_raw)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun cours trouvé

            <? elseif (empty($eleves)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune liste d'étudiants trouvée pour le semestre sélectionné

            <? else : ?>

				<span style="font-weight: 300">Les listes de vos étudiants :</span>

                <? foreach($eleves as $c_id => $groupes) : ?>

                    <? 
                        // Les comptes autorises pour ce cours

                        if (array_key_exists($c_id, $comptes_autorises)) :

                            $comptes_a = $comptes_autorises[$c_id];

                        else :

                            $comptes_a = array();

                        endif; 
                    ?>
                
                <div class="n-listes-etudiants">

                    <? foreach($groupes as $groupe => $groupe_data) : ?>

                        <div class="n-liste-etudiants-titre">

                            <div class="row">

                                <div class="col">
                                    <?= $cours_raw[$c_id]['cours_nom']; ?>
                                    <span style="font-weight: 300">(<?= $cours_raw[$c_id]['cours_code']; ?>)</span> : groupe <?= $groupe; ?>
                                </div>
                                <div class="col" style="text-align: right">
                                    <?= count($groupe_data); ?> étudiant<?= count($groupe_data) > 1 ? 's' :''; ?>
                                </div>

                            </div> <!-- .row -->

                        </div>

                        <div class="n-liste-etudiants">

                            <table style="width: 100%; margin: 0;">
                                <thead>
                                    <tr>
                                        <th style="width: 150px;">
                                            Nom
                                            <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                                        </th>
                                        <th style="width: 150px">
                                            Prénom
                                            <span class="tri-button" data-clef="clef_tri_prenom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                                        </th>
                                        <th style="center; width: 120px">
                                            <?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?>
                                            <span class="tri-button" data-clef="clef_tri_nda"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
                                        </th>
                                        <th>
                                            Autoriser les comptes en cliquant sur le nom (Étudiant ID)
                                        </th>
                                        <th style="width: 100px"></th>
                                    </tr>
                                </thead>	
                                <tbody>
                                    <? foreach($groupe_data as $e) : ?>

                                        <tr
                                            data-clef_tri_nom="<?= (strtolower(strip_accents($e['eleve_nom']))); ?>"
                                            data-clef_tri_prenom="<?= (strtolower(strip_accents($e['eleve_prenom']))); ?>"
                                            data-clef_tri_nda="<?= $e['numero_da']; ?>">
                                            <td><?= $e['eleve_nom']; ?></td>
                                            <td><?= $e['eleve_prenom']; ?></td>
                                            <td class="mono"><?= $e['numero_da']; ?></td>
                                            <td>
                                                <? if ($e['temps_supp'] > 0) : ?>

                                                    <span style="color: #EF6C00; font-weight: 600; margin-right: 3px;">
                                                        (+<?= str_replace('.', ',', $e['temps_supp']); ?>% temps)
                                                    </span>
                                                    
                                                <? endif; ?>

                                                <? if (array_key_exists($e['numero_da'], $comptes_da)) : ?>

                                                    <? foreach($comptes_da[$e['numero_da']] as $etudiant_id) : ?>

                                                        <? // Determiner si ce compte est aurotise 

                                                            $autorise = FALSE;

                                                            if (array_key_exists($etudiant_id, $comptes_a))
                                                            {
                                                                if ($comptes_a[$etudiant_id]['cours_id'] == $c_id && 
                                                                    $comptes_a[$etudiant_id]['cours_groupe'] == $groupe)
                                                                {
                                                                    $autorise = TRUE;
                                                                }
                                                            }
                                                        ?>

                                                        <span class="compte-trouve spinnable <?= $autorise ? 'actif' : ''; ?>"
                                                            data-etudiant_id="<?= $etudiant_id; ?>"
                                                            data-numero_da="<?= $e['numero_da']; ?>"
                                                            data-cours_id="<?= $c_id; ?>"
                                                            data-semestre_id="<?= $this->enseignant['semestre_id']; ?>"
                                                            data-cours_groupe="<?= $groupe; ?>">

                                                            <i class="fa fa-user" style="margin-right: 3px; color: #7986CB;"></i>
                                                            <?= ucfirst($comptes[$etudiant_id]['prenom']) . ' ' . ucfirst($comptes[$etudiant_id]['nom']); ?>

                                                            <span style="margin-left: 3px">(<?= $etudiant_id; ?>)</span>
                                                            <span style="color: #888"><?= strstr($comptes[$etudiant_id]['courriel'], '@'); ?></span>

                                                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>

                                                        </span>

                                                    <? endforeach; ?>

                                                <? else : ?>

                                                    <span class="aucun-compte-trouve">
                                                        <i class="fa fa-exclamation-circle" style="color: #C5CAE9"></i>
                                                        Cet étudiant n'a pas de compte, ou n'a pas entré son 
                                                        <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?> 
                                                        dans son profil.
                                                    </span>

                                                <? endif; ?>
                                            </td>
                                            <td style="text-align: right">

                                                <!-- Modifier les mesures particulieres d'un etudiant -->

                                                <span style="margin-right: 10px; cursor: pointer"
                                                    data-toggle="modal" 
                                                    data-target="#modal-modifier-etudiant"
                                                    data-cours_id="<?= $c_id; ?>"
                                                    data-groupe="<?= $groupe; ?>"
                                                    data-eleve_id="<?= $e['eleve_id']; ?>"
                                                    data-eleve_prenom_nom="<?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>"
                                                    data-numero_da="<?= $e['numero_da']; ?>"
                                                    data-temps_supp="<?= str_replace('.', ',', $e['temps_supp']); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="dodgerblue" class="bi bi-sm bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                    </svg>
                                                </span>

                                                <!-- Effacer un etudiant d'une liste -->

                                                <span style="cursor: pointer"
                                                    data-toggle="modal" 
                                                    data-target="#modal-effacer-etudiant"
                                                    data-cours_id="<?= $c_id; ?>"
                                                    data-groupe="<?= $groupe; ?>"
                                                    data-eleve_id="<?= $e['eleve_id']; ?>"
                                                    data-eleve_prenom_nom="<?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>"
                                                    data-numero_da="<?= $e['numero_da']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi bi-sm bi-trash-fill" viewBox="0 0 16 16">
                                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                                    </svg> 
                                                </span>
                                            </td>
                                        </tr>
                                    <? endforeach; ?>

                                    <tr>
                                        <td colspan="5">
                                            <div class="btn btn-sm btn-outline-danger"
                                                data-toggle="modal" 
                                                data-target="#modal-effacer-liste"
                                                data-cours_id="<?= $c_id; ?>"
                                                data-groupe="<?= $groupe; ?>">
                                                <i class="fa fa-trash"></i> 
                                                Effacer cette liste
                                            </div>
                                            <div class="btn btn-sm btn-outline-primary"
                                                data-toggle="modal" 
                                                data-target="#modal-ajouter-etudiant"
                                                data-cours_id="<?= $c_id; ?>"
                                                data-cours_nom="<?= $cours_raw[$c_id]['cours_nom']; ?>"
                                                data-groupe="<?= $groupe; ?>">
                                                <i class="fa fa-plus-circle"></i> 
                                                Ajouter un étudiant à ce groupe
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>


                        </div>

                    <? endforeach; ?>
                </div>

                <? endforeach; ?>

            <? endif; ?>

            <? if ( ! empty($cours_raw) && ! empty($this->enseignant['semestre_id'])) : ?>

                <div class="ajout-liste-etudiants">

                    <div class="space"></div>

                    <div class="btn btn-outline-primary"
                        data-toggle="modal" 
                        data-target="#modal-ajouter-liste-eleves">
                        <i class="fa fa-upload" style="margin-right: 3px"></i> Ajouter une liste d'étudiants
                    </div>

                    <div class="ajout-liste-etudiants d-none">

                        <input type="file" name="listfile" id="ajout-liste-etudiants" class="ajout-liste-etudiants-input custom-file-input">
                        <label class="btn btn-primary" for="ajout-liste-etudiants" style="margin-top: -20px; cursor: pointer">

                            <i class="fa fa-plus-circle"></i> Ajouter une liste d'étudiants
                            <i class="fa fa-spin fa-circle-o-notch image-upload-spinner d-none" style="margin-left: 10px; margin-right: 10px; "></i>

                        </label>

                    </div>

                </div> <? // ajout liste d'etudiants ?>

            <? endif; ?>

        </div> <!-- .box-section-win -->

    </div> <!-- .box-section -->

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #configuration -->

<?
/* -------------------------------------------------------------------------
 *
 * MODALS
 *
 * ------------------------------------------------------------------------- */ ?>	

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE LISTE D'ELEVES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-liste-eleves" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-users" style="margin-right: 3px"></i> Ajouter une liste d'étudiants</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-liste-eleves-form'), 
                        array(
                            'enseignant_id' => $this->enseignant['enseignant_id'],
                            'groupe_id'     => $this->groupe_id,
                            'semestre_id'   => $semestre_selectionne
                        )
					); ?>

					<div class="form-group mt-2" style="padding: 0 10px 0 10px;">

					    Quel cours ?

                        <div class="input-group mt-2 mb-3">

					  	    <select name="cours_id" class="custom-select" id="modal-ajouter-liste-eleves-cours-id">
                                <?  $i=0; 
                                    foreach($cours_raw as $cours_id => $c) : 
                                        $i++;
                                ?>
                                    <option value="<?= $cours_id; ?>" <?= $i == 1 ? 'selected' : ''; ?>>
                                        <?= $c['cours_nom_court'] . ' (' . $c['cours_code_court'] . ')'; ?>
                                    </option>
                                <? endforeach; ?>
							</select>

						</div>

                        Quel est le numéro de ce groupe ?

                        <div class="row no-gutters mt-2">
                            <div class="input-group col-md-2">
                                <input type="number" class="form-control" name="numero_groupe" value="1" id="modal-ajouter-liste-eleves-numero-groupe">
                            </div>
                        </div>
                        <div class="mt-2 mb-3" style="font-size: 0.8em">
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i>
                            Le numéro doit être plus grand que 0.
                        </div>            
        

                        Quel est le format du fichier ?

                        <div class="row no-gutters">
                            <div class="input-group mt-2 mb-3 col-md-4">
                                <select name="plateforme" class="custom-select" id="modal-ajouter-liste-eleves-plateforme">
                                    <option value="colnet" <?= $this->ecole['plateforme'] == 'colnet' ? 'selected' : ''; ?>>Colnet</option>
                                    <option value="omnivox" <?= $this->ecole['plateforme'] == 'omnivox' ? 'selected' : ''; ?>>Omnivox</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 15px">
                            <div class="files-input-button">
                                <label class="btn btn-outline-primary btn-file">
                                    <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
                                    Choisir le fichier
                                    <input type="file" name="listfile" id="modal-ajouter-liste-eleves-fichier" class="files-input d-none">
                                </label>
                                <label class="btn" style="font-size: 0.9em" id="liste-eleves-nom-fichier"></label>
                            </div>

                            <div class="space"></div>     

                            <div style="font-size: 0.9em">
                                <i class="fa fa-exclamation-circle" style="color: orange"></i> 
                                Le fichier doit avoir été généré par votre plateforme au format CSV (séparateur ";").
                            </div>
                        </div>


                    </div> <!-- .form-group -->
                    
				</form>
				
      		</div> <!-- .modal-body -->
      
			<div class="modal-footer">
                <div id="modal-ajouter-liste-eleves-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-upload"></i> Envoyer la liste
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UN ETUDIANT D'UNE LISTE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-etudiant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="liste">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title" style="font-weight: 300">
                    Modifier les mesures particulières d'un étudiant
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-etudiant-form'), 
						array('eleve_id' => NULL, 'semestre_id' => NULL, 'cours_id' => NULL, 'groupe' => NULL, 'numero_da' => NULL)
					); ?>

                    <div class="form-row col-8" style="padding: 25px;">

                        <div class="col mt-2">
                            <label for="modal-temps-supp">
                                Temps supplémentaire :
                            </label>
                        </div>

                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="modal-temps-supp">+</label>
                                </div>
                                <input type="text" class="form-control" name="temps_supp" id="modal-temps-supp" style="">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">%</span>
                                </div>
                            </div>
                        </div>

                    </div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-modifier-etudiant-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Sauvegarder
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>


<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN ETUDIANT A UN GROUPE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-etudiant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="liste">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un étudiant</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-etudiant-form'), 
						array('semestre_id' => NULL, 'cours_id' => NULL, 'groupe' => NULL)
					); ?>

                    <div class="ml-2 mt-2 mb-3 mr-2" style="background: #f8f9fa; border: 1px solid #ddd; padding: 8px; font-family: Lato; font-weight: 300; font-size: 0.9em">
                        <table>
                            <tr>
                                <td style="width: 70px">Cours :</td>
                                <td><span id="modal-ajouter-etudiant-cours" style="font-weight: 600"></span></td>
                            </tr>
                            <tr>
                                <td>Groupe :</td>
                                <td><span id="modal-ajouter-etudiant-groupe" style="font-weight: 600"></span></td>
                            <tr>
                        </table>
                    </div>

                    <div class="form-row">
                        <div class="col-md-5 ml-2 mb-2">
                            <label for="modal-ajouter-etudiant-nom">Nom :</label>
                            <input name="nom" type="text" class="form-control" id="modal-ajouter-etudiant-nom" placeholder="Nom de famille" required>
                        </div>
                    </div>
                
                    <div class="form-row">
                        <div class="col-md-5 ml-2 mt-2 mb-2">
                            <label for="modal-ajouter-etudiant-prenom">Prénom :</label>
                            <input name="prenom" type="text" class="form-control" id="modal-ajouter-etudiant-prenom" placeholder="Prénom" required>
                        </div>
                    </div>

                    <? if ($this->groupe_id != 0) : ?>

                        <div class="form-row">
                            <div class="col-md-12 ml-2 mt-2 mb-2">
                                <label><?= $this->ecole['numero_da_nom'] ?: 'Matricule'; ?> :</label>
                                <input name="numero_da" type="text" class="form-control col-md-3" id="modal-ajouter-etudiant-numero-da" 
                                       placeholder="<?= ucfirst($this->ecole['numero_da_nom'] ?: 'Matricule'); ?>" required>
                            </div>
                        </div>

                    <? endif; ?>
				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajouter-etudiant-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UN ETUDIANT D'UNE LISTE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-etudiant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="liste">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title" style="font-weight: 300">
                    Enlever un étudiant d'un groupe
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-etudiant-form'), 
						array('eleve_id' => NULL, 'semestre_id' => NULL, 'cours_id' => NULL, 'groupe' => NULL, 'numero_da' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

						Êtes-vous certain de vouloir enlever <span id="eleve-prenom-nom" style="font-weight: 600"></span> de ce groupe ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-etudiant-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Enlever
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UNE LISTE D'ELEVES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-liste" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="liste">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer une liste</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-liste-form'), 
						array('semestre_id' => NULL, 'cours_id' => NULL, 'groupe' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

						Êtes-vous certain de vouloir effacer cette liste d'étudiants ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-liste-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

