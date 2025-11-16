<div id="admin">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>Gestion de groupe</h3>

    <div class="space"></div>

    <? if ($groupe_id != 0) : ?>
        <h4 style="font-weight: 300"><?= $ecole['ecole_nom']; ?> <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> <?= $groupe['groupe_nom']; ?></h4>
    <? else : ?>
        <h4 style="font-weight: 300"><?= $groupe['groupe_nom']; ?></h4>
    <? endif; ?>

    <? if ( ! empty(@$flash_message)) : ?>

        <div class="alert alert-<?= $flash_message['alert']; ?> alert-dismissible fade show mt-4" role="alert" style="margin-bottom: 0px">
            <i class="fa fa-exclamation-circle"></i> <?= @$flash_message['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ENSEIGNANT(S) EN ATTENTE D'APPROBATION
     *
     * ======================================================================== */ ?>

    <? if ( ! empty($enseignants_a_approuver)) : ?>

        <div class="space"></div>

        <div class="alert alert-danger" role="alert">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
            Il y a 
            <?= count($enseignants_approbation) ? 'un enseignant' : 'des enseignants'; ?> 
            en attente d'approbation. 
        </div>

        <div class="hspace"></div>

    <? else : ?>

        <div class="tspace"></div>

    <? endif; ?>

    <? if ( ! empty($enseignants_approbation)) : 

        if ($enseignants_a_approuver)
        {
            $color = 'crimson';
        }
        else
        {
            $color = '#aaa';
        }
    ?>
        <div class="groupe-section" style="border-color: <?= $color; ?>">

            <div class="groupe-section-titre" style="background: <?= $color; ?>; border-color: <?= $color; ?>">

                <i class="fa fa-square" style="margin-right: 10px"></i> 
                ENSEIGNANT<?= count($enseignants_approbation) > 1 ? 'S' : ''; ?> EN ATTENTE D'APPROBATION POUR JOINDRE CE GROUPE

            </div>

            <div class="groupe-section-box">

                <table id="enseignants-approbation" class="table" style="margin-top: -10px; margin-bottom: 0; border-bottom: 1px solid #ddd">
                    <tr>
                        <th>Prénom et Nom</th>
                        <th>Courriel</th>
                        <th>Opérations</th>
                    </tr>

                    <? foreach($enseignants_approbation as $e) : ?>

                    <tr style="background: <?= $e['traitement'] ? '#eee' : 'inherit'; ?>">
                            <td style="padding-top: 15px"><?= $e['prenom'] . ' ' . $e['nom']; ?></td>
                            <td style="padding-top: 15px;"><?= $e['courriel']; ?></td>
                            <td>
                                <div class="approuver-enseignant btn btn-sm btn-outline-success"
                                     data-joindre_id="<?= $e['joindre_id']; ?>" 
                                     data-toggle="modal" 
                                     data-target="#approuver-enseignant">
                                    <i class="fa fa-check" style="margin-right: 5px"></i> 
                                    Approuver
                                </div>
                                <div class="desapprouver-enseignant btn btn-outline-danger btn-sm spinnable"
                                     data-joindre_id="<?= $e['joindre_id']; ?>">
                                    <i class="fa fa-times" style="margin-right: 5px"></i> Refuser
                                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
                                </div>
                            </td>
                        </tr>

                    <? endforeach; ?>

                </table>

            </div>

        </div> <!-- .groupe-section -->

        <div class="space"></div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  PARAMETRES
     *
     * ======================================================================== */ ?>

    <? if ($groupe_id != 0) : ?>

        <div class="groupe-section">

            <div id="groupe-parametres" class="groupe-section-titre">

                <i class="fa fa-square" style="margin-right: 10px"></i> 
                PARAMÈTRES

            </div>

            <div class="groupe-section-box">

                <div class="form-inline row" style="margin-top: 2px">
                    <div class="col-3">
                        Permettre les inscriptions
                    </div>
                    <div class="col-9">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label id="inscription-permise-oui" class="btn btn-outline-primary <?= $groupe['inscription_permise'] ? 'active' : ''; ?>" style="width: 60px">
                                <input type="radio" name="inscription_permise" value="oui" autocomplete="off" <?= $groupe['inscription_permise'] ? 'checked' : ''; ?>> oui
                            </label>
                            <label id="inscription-permise-non" class="btn btn-outline-primary <?= $groupe['inscription_permise'] ? '' : 'active'; ?>" style="width: 60px">
                                <input type="radio" name="inscription_permise" value="non" autocomplete="off" <?= $groupe['inscription_permise'] ? '' : 'checked' ?>> non
                            </label>
                        </div>
                </div>
                </div> <!--  .form-inline -->

                <div class="space"></div>

                <div class="form-inline row">
                    <div class="col-3">
                        Code d'inscription
                    </div>
                    <div class="col-9">
                        <input type="text" class="form-control" name="code_inscription" id="code-inscription" autocomplete="off" value="<?= $groupe['inscription_code']; ?>">
                        <div id="code-inscription-sauvegarder" class="btn btn btn-outline-success d-none">
                            <i class="fa fa-save" style="margin-right: 5px"></i> 
                            Sauvergarder
                            <i id="code-inscription-sauvegarder-spinner" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                        <button id="code-inscription-effacer" class="btn btn-outline-secondary spinnable <?= empty($groupe['inscription_code']) ? 'd-none' : ''; ?>">
                            <i class="fa fa-trash" style="color: crimson; margin-right: 5px"></i> 
                            Effacer le code
                            <i id="code-inscription-effacer-spinner" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </button>
                        <span style="font-size: 0.85em; margin-left: 10px; color: #777;"><i class="fa fa-exclamation-circle" style="color: #bbb;"></i> Doit avoir de 5  à 15 caractères alphanumériques.</span>
                    </div>

                </div>

            </div> <!-- .groupe-section-box -->

        </div> <!-- .groupe-section -->

        <div class="space"></div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  SEMESTRES
     *
     * ======================================================================== */ ?>

    <div class="groupe-section">

        <div class="groupe-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            SEMESTRES

        </div>

        <div class="groupe-section-box">

            <? if (empty($semestres)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson; margin-right: 5px"></i> Aucun semestre trouvé

            <? else : ?>

                <table id="semestres" class="table" style="margin-top: -10px; margin-bottom: 0; border-bottom: 1px solid #ddd">
                    <tr>
                        <th>Semestre</th>
                        <th>Code</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Opérations</th>
                    </tr>

                    <? foreach($semestres as $semestre_id => $s) : ?>

                        <tr>
                            <td style="padding-top: 15px"><?= $s['semestre_nom']; ?></td>
                            <td style="padding-top: 15px"><?= $s['semestre_code']; ?></td>
                            <td style="padding-top: 15px"><?= $s['semestre_debut_date']; ?></td>
                            <td style="padding-top: 15px"><?= $s['semestre_fin_date']; ?></td>
                            <td>
                                <div class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-editer-semestre">

                                    <div class="semestre-data d-none" 
                                         data-semestre_id="<?= $semestre_id; ?>"
                                         data-semestre_nom="<?= htmlentities($s['semestre_nom']); ?>"
                                         data-semestre_code="<?= htmlentities($s['semestre_code']); ?>"
                                         data-semestre_debut_date="<?= $s['semestre_debut_date']; ?>"
                                         data-semestre_fin_date="<?= $s['semestre_fin_date']; ?>">
                                    </div>

                                    <i class="fa fa-edit"></i> Modifier</i>
                                </div>
                            </td>
                        </tr>

                    <? endforeach; ?>

                </table>

            <? endif; ?>

            <div class="space"></div>

            <div class="btn btn-outline-primary" data-groupe_id="<?= $groupe_id; ?>" data-toggle="modal" data-target="#modal-ajouter-semestre">
                <i class="fa fa-plus-circle"></i> Ajouter un semestre
            </div>

        </div> <!-- .groupe-section-box -->

    </div> <!-- .groupe-section -->

    <div class="space"></div>

    <? 
    /* ========================================================================
     *
     *  COURS
     *
     * ======================================================================== */ ?>

    <div class="groupe-section">

        <div class="groupe-section-titre">

            <i class="fa fa-square" style="margin-right: 10px"></i> 
            COURS

        </div>

        <div class="groupe-section-box">

            <? if (empty($cours_raw)) : ?>

                <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun cours trouvé

            <? else : ?>

                <table id="enseignants" class="table" style="margin-top: -10px; margin-bottom: 0; border-bottom: 1px solid #ddd">
                    <tr>
                        <th>Cours</th>
                        <th>Nom usuel</th>
                        <th>Code</th>
                        <th style="width: 75px; text-align: center">Offert</th>
                        <th style="text-align: center">URL</th>
                        <th>Opérations</th>
                    </tr>

                    <? foreach($cours_raw as $c) : ?>

                        <tr>
                            <td style="padding-top: 15px"><?= $c['cours_nom']; ?></td>
                            <td style="padding-top: 15px"><?= $c['cours_nom_court']; ?></td>
                            <td style="padding-top: 15px"><?= $c['cours_code']; ?> (<?= $c['cours_code_court']; ?>)</td>
                            <td style="padding-top: 15px; text-align: center">
                                <? if (array_key_exists('desuet', $c) && $c['desuet']) : ?>

                                    <span style="color: crimson">✕</span>

                                <? else : ?>

                                    <svg viewBox="0 0 16 16" class="bi bi-check2" fill="limegreen" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                    </svg>

                                <? endif; ?>
                            </td>
                            <td style="padding-top: 15px; text-align: center">
                                <? if ( ! empty($c['cours_url'])) : ?>
                                    <a target="_blank" href="<?= $c['cours_url']; ?>">
                                        <i class="fa fa-link"></i>
                                    </a>
                                <? endif; ?>
                            </td>
                            <td>
                                <div class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-editer-cours">

                                    <div class="cours-data d-none" 
										data-cours_id="<?= $c['cours_id']; ?>"
                                        data-cours_nom="<?= htmlentities($c['cours_nom']); ?>"
                                        data-cours_nom_court="<?= htmlentities($c['cours_nom_court']); ?>"
                                        data-cours_code="<?= htmlentities($c['cours_code']); ?>"
										data-cours_code_court="<?= htmlentities($c['cours_code_court']); ?>"
                                        data-cours_url="<?= htmlentities($c['cours_url']); ?>"
                                        data-desuet="<?= $c['desuet']; ?>"
                                        data-toggle="modal" 
                                        data-target="#modal-editer-cours">
                                    </div>

                                    <i class="fa fa-edit"></i> Modifier</i>
                                </div>
                            </td>
                        </tr>

                    <? endforeach; ?>

                </table>

            <? endif; ?>

            <div class="space"></div>

            <div class="btn btn-outline-primary" data-groupe_id="<?= $groupe_id; ?>" data-toggle="modal" data-target="#modal-ajouter-cours">
                <i class="fa fa-plus-circle"></i> Ajouter un cours
            </div>

        </div>

    </div> <!-- .groupe-section -->

    <div class="space"></div>

    <? 
    /* ========================================================================
     *
     *  ENSEIGNANTS
     *
     * ======================================================================== */ ?>

    <? if ($groupe_id != 0) : ?>

        <div class="groupe-section">

            <div class="groupe-section-titre">

                <i class="fa fa-square" style="margin-right: 10px"></i> 
                ENSEIGNANTS

            </div>

            <div class="groupe-section-box">

                <? if (empty($enseignants)) : ?>

                    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun enseignant dans ce groupe        

                <? else : ?>

                    <table id="enseignants" class="table" style="margin-top: -10px; margin-bottom: 0; border-bottom: 1px solid #ddd">
                        <tr>
                            <th>Prénom et Nom</th>
                            <th style="">Courriel</th>
                            <th style="text-align: center">Niveau</th>
                            <th style="text-align: center">Actif</th>
                            <th>Opérations</th>
                        </tr>

                        <? foreach($enseignants as $e) : ?>

                            <tr>
                                <td style="padding-top: 15px">
                                    <?= $e['prenom'] . ' ' . $e['nom']; ?>
                                    <? if ($this->groupe['admin_enseignant_id'] == $e['enseignant_id']) : ?>
                                        <i class="fa fa-star" style="margin-left: 5px; color: gold"></i>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-secondary" style="cursor: pointer" data-toggle="tooltip" data-placement="top" title="<?= $e['courriel']; ?>">
                                        Courriel
                                    </span>
                                </td>
                                <td style="padding-top: 15px; text-align: center"><?= $e['niveau']; ?></td>
                                <td style="padding-top: 15px; text-align: center">
                                    <? if ($e['actif']) : ?>
                                        <i class="fa fa-check-circle"></i>
                                    <? else : ?>
                                        <i class="fa fa-times-circle"></i>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? if (
                                            $e['enseignant_id'] != $this->groupe['admin_enseignant_id'] && 
                                            ($enseignant['niveau'] >= $e['niveau'] || $enseignant['enseignant_id'] == $e['enseignant_id'])
                                          ) : ?>

                                        <div class="btn btn-outline-primary btn-sm modifier-enseignant" 
                                            data-toggle="modal" 
                                            data-target="#modal-editer-enseignant"
                                            data-enseignant_id="<?= $e['enseignant_id']; ?>"
                                            data-nom="<?= $e['nom']; ?>"
                                            data-prenom="<?= $e['prenom']; ?>"
                                            data-courriel="<?= $e['courriel']; ?>"
                                            data-niveau="<?= $e['niveau']; ?>"
                                            data-genre="<?= $e['genre']; ?>"
                                            >
                                            <i class="fa fa-edit" style="margin-right: 3px"></i> Modifier

                                        </div>

                                        <? if ($e['actif']) : ?>
                                            <div data-enseignant_id="<?= $e['enseignant_id']; ?>" class="desactiver-enseignant btn btn-outline-secondary btn-sm">
                                                <i class="fa fa-times" style="margin-right: 3px"></i> Désactiver 
                                                <i id="activer-desactiver-spinner-<?= $e['enseignant_id']; ?>" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
                                            </div>
                                        <? else : ?>
                                            <div data-enseignant_id="<?= $e['enseignant_id']; ?>" class="activer-enseignant btn btn-outline-danger btn-sm">
                                                <i class="fa fa-times-circle" style="margin-right: 3px"></i> Désactivé
                                                <i id="activer-desactiver-spinner-<?= $e['enseignant_id']; ?>" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
                                            </div>
                                        <? endif; ?>

                                    <? endif; ?>

                                    <? if (1 == 2 && $enseignant['niveau'] >= $this->config->item('admin_groupe', 'niveaux') && $e['niveau'] >= $this->config->item('admin_groupe', 'niveaux')) : ?>

                                        <div data-enseignant_id="<?= $e['enseignant_id']; ?>" data-groupe_id="<?= $groupe['groupe_id']; ?>"
                                                class="changer-responsable btn btn-sm btn-outline-primary <?= $groupe['admin_enseignant_id'] == $e['enseignant_id'] ? 'active' : ''; ?>">
                                            <i class="fa fa-group"></i> Responsable
                                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
                                        </div>

                                    <? endif; ?>

                                </td>
                            </tr>

                        <? endforeach; ?>

                    </table>

                    <div style="font-size: 0.9em; margin-top: 20px">
                        
                        <i class="fa fa-star" style="color: gold"></i> = créateur du groupe
                    </div>
                
                <? endif; ?>

            </div>

        </div> <!-- .groupe-section -->

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ANCIENNES DEMANDES
     *
     * ======================================================================== */ ?>

    <? if ($groupe_id != 0 && ! empty($anciennes_demandes)) : ?>

        <div class="space"></div>

        <div class="groupe-section">

            <div class="anciennes-demandes groupe-section-titre" style="cursor: pointer; background: #bbb; color: #5C6BC0; border-bottom: 0;">
    
                <div class="row">
                    <div class="col-10">
                        <i class="fa fa-square" style="margin-right: 10px"></i> 
                        ANCIENNES DEMANDES REFUSÉES (<?= $anciennes_demandes_count; ?>)
                    </div>
                    <div class="col-2" style="text-align: right">
                        <i class="fa fa-plus-square-o" style="margin-right: 10px"></i>
                    </div>
                </div>

            </div>

            <div class="anciennes-demandes groupe-section-titre" style="display: none; cursor: pointer; background: #bbb; color: #5C6BC0">

                <div class="row">
                    <div class="col-10">
                        <i class="fa fa-square" style="margin-right: 10px"></i> 
                        ANCIENNES DEMANDES REFUSÉES 
                    </div>
                    <div class="col-2" style="text-align: right">
                        <i class="fa fa-minus-square-o" style="margin-right: 10px"></i>
                    </div>
                </div>

            </div>

            <div class="anciennes-demandes groupe-section-box" style="display: none; background: #eee">

                <table id="enseignants" class="table" style="margin-top: -10px; margin-bottom: 0; border-bottom: 1px solid #ddd">
                    <tr>
                        <th>Prénom et Nom</th>
                        <th>Courriel</th>
                        <th style="">Date de refus</th>
                        <th>Opérations</th>
                    </tr>

                    <? 
                        foreach($demandes as $d) : 

                            if ($d['refusee'] != 1) continue;
                    ?>

                        <tr>
                            <td style="padding-top: 15px">
                                <?= $d['prenom'] . ' ' . $d['nom']; ?>
                             </td>
                            <td>
                                <span class="badge badge-pill badge-secondary" style="cursor: pointer" data-toggle="tooltip" data-placement="top" title="<?= $d['courriel']; ?>">
                                    Courriel
                                </span>
                            </td>
                            <td style="padding-top: 15px">
                                <?= date_humanize($d['traitement_epoch'], TRUE); ?>
                            </td>
                            <td>
                                <div class="approuver-enseignant btn btn-sm btn-outline-success"
                                     data-joindre_id="<?= $d['joindre_id']; ?>" 
                                     data-toggle="modal" 
                                     data-target="#approuver-enseignant">
                                    <i class="fa fa-check" style="margin-right: 5px"></i> 
                                    Approuver
                                </div>
                            </td>
                        </tr>

                    <? endforeach; ?>

                </table>

            </div>

        </div> <!-- .groupe-section -->

    <? endif; ?>


</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-admin -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER LE GROUPE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-groupe" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier ce groupe</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-groupe-form'), 
						array('groupe_id' => $groupe_id)
					); ?>
					
					<div class="space"></div>				

                        </div>

					</div>

					<div class="hspace"></div>				
					<div class="space"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-nom">Niveau :</label>
                        <input name="groupe_nom" class="form-control" style="width: 100%" placeholder="Le nom du groupe">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>


                    <div class="hspace"></div>

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-code">Courriel :</label>
						<input name="enseignant_code" class="form-control col-md-8">
                        <small class="form-text text-muted">
                            ex. 202-NYA-05
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-nom">URL du enseignant :</label>
						<input name="enseignant_url" class="form-control" id="modal-editer-groupe-url">
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
					<div id="modal-editer-groupe-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Modifier ce groupe</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN SEMESTRE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-semestre" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajouter un semestre</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-semestre-form'), 
                        array('groupe_id' => $groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-ajouter-semestre-nom">Le titre du semestre :</label>
						<input name="semestre_nom" class="form-control" id="modal-ajouter-semestre-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-semestre-code">Le code du semestre :</label>
						<input name="semestre_code" class="form-control col-md-2" id="modal-ajouter-semestre-code">
                        <small class="form-text text-muted">
                            ex. H2018
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-ajouter-semestre-debut-date">La date du <strong>début</strong> du semestre :</label>
                            <input name="semestre_debut_date" type="date" class="form-control" id="modal-ajouter-semestre-debut-date">
                            <small class="form-text text-muted">
                                ex. 2018-08-15
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-4 offset-md-1">
                            <label for="modal-ajouter-semestre-fin-date">La date de la <strong>fin</strong> du semestre :</label>
                            <input name="semestre_fin_date" type="date" class="form-control" id="modal-ajouter-semestre-fin-date">
                            <small class="form-text text-muted">
                                ex. 2018-12-31
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>
                    
                    <div class="erreurs erreur-chronologie d-none" style="color: crimson;">
                        <i class="fa fa-exclamation-circle"></i>
                        La date du début du semestre doit être antérieure à la date de fin.
                    </div>

                    <div class="erreurs erreur-recoupe d-none" style="color: crimson;">
                        <i class="fa fa-exclamation-circle"></i>
                        Il y a un autre semestre qui recoupe ces dates. Veuillez changer les dates.
                    </div>

                    <div class="erreurs erreur-meme_code d-none" style="color: crimson;">
                        <i class="fa fa-exclamation-circle"></i>
                        Ce code de semestre est déjà utilisé.
                    </div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
        		<div id="modal-ajouter-semestre-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Ajouter ce semestre</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER SEMESTRE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-semestre" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un semestre</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-semestre-form'), 
						array('semestre_id' => NULL, 'groupe_id' => $groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-editer-semestre-nom">Le titre du semestre :</label>
						<input name="semestre_nom" class="form-control" id="modal-editer-semestre-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-editer-semestre-code">Le code du semestre :</label>
						<input name="semestre_code" class="form-control col-md-2" id="modal-editer-semestre-code">
                        <small class="form-text text-muted">
                            ex. H2018
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

                    <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="modal-editer-semestre-debut-date">La date du <strong>début</strong> du semestre :</label>
                            <input type="date" name="semestre_debut_date" class="form-control" id="modal-editer-semestre-debut-date" placeholder="<?= date_humanize($this->now_epoch); ?>">
                            <small class="form-text text-muted">
                                ex. 2018-08-15
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-4 offset-md-1">
                            <label for="modal-editer-semestre-fin-date">La date de la <strong>fin</strong> du semestre :</label>
                            <input type="date" name="semestre_fin_date" class="form-control" id="modal-editer-semestre-fin-date" placehoder="<?= date_humanize($this->now_epoch); ?>">
                            <small class="form-text text-muted">
                                ex. 2018-12-31
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

					<div id="modal-effacer-semestre-confirmation" class="form-group" style="margin-top: 15px">
						<div class="form-check">
							<input type="checkbox" name="confirmation_effacer_semestre" class="form-check-input">
							<label class="form-check-label" for="exampleCheck1">
								Pour effacer ce semestre, confirmez en cochant.
								<br />(La seule raison valable pour effacer est s'il vient d'être ajouté par mégarde.)
							</label>
						</div>
				  	</div>

                    <div class="erreurs erreur-chronologie d-none">
                        <i class="fa fa-exclamation-circle"></i>
                        La date du début du semestre doit être antérieure à la date de fin.
                    </div>

                    <div class="erreurs erreur-recoupe d-none">
                        <i class="fa fa-exclamation-circle"></i>
                        Il y a un autre semestre qui recoupe ces dates. Veuillez changer les dates.
                    </div>

                    <div class="erreurs erreur-contient_soumissions d-none">
                        <i class="fa fa-exclamation-circle"></i>
                        Ce semestre contient des soumissions : effacement impossible.
                    </div>

                    <div class="erreurs erreur-aucun_changement d-none">
                        <i class="fa fa-exclamation-circle"></i>
                        Aucun changement détecté
                    </div>
				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-effacer-semestre-sauvegarde" class="btn btn-danger"><i class="fa fa-trash"></i> Effacer ce semestre</div>
                <div id="modal-editer-semestre-sauvegarde" class="btn btn-success"><i class="fa fa-save"></i> Sauvegarder les modifications</div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN COURS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-cours" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajouter un cours</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-cours-form'), 
						array('groupe_id' => $groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">Nom officiel du cours : </label>
						<input name="cours_nom" class="form-control" id="modal-ajouter-cours-nom">
                        <small class="form-text text-muted">
                            ex. Chimie générale : la matière
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">Nom du cours :</label>
						<input name="cours_nom_court" class="form-control" id="modal-ajouter-cours-nom-court">
                        <small class="form-text text-muted">
                            ex. Chimie générale
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="hspace"></div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-ajouter-cours-code">Code du cours :</label>
                            <input name="cours_code" class="form-control" id="modal-ajouter-cours-code">
                            <small class="form-text text-muted">
                                ex. 202-NYA-05
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-3 offset-md-1">
                            <label for="modal-ajouter-cours-code">Code abrégé du cours :</label>
                            <input name="cours_code_court" class="form-control" id="modal-ajouter-cours-code_court">
                            <small class="form-text text-muted">
                                ex. NYA
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">URL du cours :</label>
						<input name="cours_url" class="form-control" id="modal-ajouter-cours-url">
					</div>

                    <div class="erreurs erreur-cours_existant d-none">
                        <i class="fa fa-exclamation-circle"></i>
                        Ce code de cours est existant.
                    </div>
				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-ajouter-cours-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Ajouter ce cours</div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER (MODIFIER) UN COURS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-cours" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un cours</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-cours-form'), 
						array('cours_id' => NULL, 'groupe_id' => $groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-editer-cours-nom">Nom officiel du cours :</label>
						<input name="cours_nom" class="form-control" id="modal-editer-cours-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="form-group">
						<label for="modal-editer-cours-nom">Nom du cours :</label>
						<input name="cours_nom_court" class="form-control" id="modal-editer-cours-nom-court">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-editer-cours-code">Code du cours :</label>
                            <input name="cours_code" class="form-control" id="modal-editer-cours-code">
                            <small class="form-text text-muted">
                                ex. 202-NYA-05
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-3 offset-md-1">
                            <label for="modal-editer-cours-code-court">Code abrégé du cours :</label>
                            <input name="cours_code_court" class="form-control" id="modal-editer-cours-code-court">
                            <small class="form-text text-muted">
                                ex. NYA
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

					<div class="form-group">
						<label for="modal-editer-cours-nom">URL du cours :</label>
						<input name="cours_url" class="form-control" id="modal-editer-cours-url">
					</div>

					<div class="form-group" style="margin-top: 20px">
						<div class="form-check">
							<input type="checkbox" name="desuet" class="form-check-input">
							<label class="form-check-label">
								Ce cours n'est plus offert.
							</label>
						</div>
				  	</div>

					<div class="form-group" style="margin-top: 20px; margin-bottom: 5px">
						<div class="form-check">
							<input id="confirmation-effacer-cours" type="checkbox" name="confirmation_effacer_cours" class="form-check-input">
							<label class="form-check-label">
								Pour effacer ce cours, confirmez en cochant.
								<br />
								<span style="font-size: 0.9em">
								<i class="fa fa-exclamation-circle"></i>
								La seule raison valable pour effacer un cours est s'il vient d'être ajouté par mégarde.
							</label>
						</div>
				  	</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-effacer-cours-sauvegarde" style="margin-left: 5px" class="btn btn-danger d-none" data-dismiss="modal">
					<i class="fa fa-trash"></i> 
					Effacer ce cours
				</div>
                <div id="modal-editer-cours-sauvegarde" class="btn btn-success spinnable">
					<i class="fa fa-save"></i> 
					Sauvegarder les modifications
            		<i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
				</div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER UN ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-enseignant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un enseignant</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

                <? $disabled = ($this->enseignant['niveau'] < $this->config->item('niveaux')['sysop']) ? 'disabled' : ''; ?>

				<?= form_open(NULL, 
						array('id' => 'modal-editer-enseignant-form'), 
						array('enseignant_id' => NULL, 'groupe_id' => $groupe_id)
					); ?>
					
					<div class="space"></div>				

					<div class="form-inline">
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Nom : </div>
                            <input name="nom" class="form-control" style="width: 100%" placeholder="Nom" <?= $disabled; ?>>
                        </div>
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Prénom : </div>
                            <input name="prenom" class="form-control" style="width: 100%" placeholder="Prénom" <?= $disabled; ?>>
                        </div>
					</div>

					<div class="space"></div>				

					<div class="form-group">
						<div class="col-md-3">
							<label>Genre : </label>
                            <select name="genre" class="custom-select" <?= $disabled; ?>>
								<option value="M" selected>M</option>
								<option value="F">F</option>
							</select>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Niveau :</label>
                            <select name="niveau" class="custom-select">
                                <? foreach($this->config->item('niveaux') as $k => $v) : ?>
                                    <? if ($v > $enseignant['niveau']) continue; ?>
                                    <? if ($v == 0) continue; ?>
                                    <option value="<?= $v; ?>"><?= $v; ?>: <?= $this->config->item($v, 'niveaux_desc'); ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>

					<div class="form-group col-md-12">
						<label for="modal-editer-enseignant-code">Courriel :</label>
                        <input name="courriel" class="form-control col-md-8" <?= ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) ? '' : 'disabled'; ?>>
					</div>

                    <? if ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) : ?>

						<div class="form-inline" style="margin-top: 30px">
							<div class="col-md-6">
								<div style="margin-bottom: 10px">Mot-de-passe :</div>
								<input name="password" type="password" class="form-control" style="width: 100%" placeholder="Mot-de-passe">
							</div>
							<div class="col-md-6">
								<div style="margin-bottom: 10px">&nbsp;</div>
								<input name="password2" type="password" class="form-control" style="width: 100%" placeholder="Confirmation">
							</div>
						</div>

						<div class="tspace"></div>				

					<? endif; ?>

				</form>
				
      		</div>
      
            <div class="modal-footer">
				<div id="modal-editer-enseignant-sauvegarde" class="btn btn-success"><i class="fa fa-plus-circle"></i> Sauvegarder les changements</div>
				<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

        </div>
    </div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER UN ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-enseignant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier un enseignant</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-enseignant-form'), 
						array('enseignant_id' => NULL, 'groupe_id' => $groupe_id)
					); ?>
					
					<div class="space"></div>				

					<div class="form-inline">
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Nom : </div>
                            <input name="nom" class="form-control" style="width: 100%" placeholder="Nom" disabled>
                        </div>
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Prénom : </div>
                            <input name="prenom" class="form-control" style="width: 100%" placeholder="Prénom" disabled>
                        </div>
					</div>

					<div class="space"></div>				

					<div class="form-group">
						<div class="col-md-3">
							<label>Genre : </label>
							<select name="genre" class="custom-select">
								<option value="M" selected>M</option>
								<option value="F">F</option>
							</select>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Niveau :</label>
                            <select name="niveau" class="custom-select">
                                <? foreach($this->config->item('niveaux') as $k => $v) : ?>
                                    <? if ($v > $enseignant['niveau']) continue; ?>
                                    <? if ($v == 0) continue; ?>
                                    <option value="<?= $v; ?>"><?= $v; ?>: <?= $this->config->item($v, 'niveaux_desc'); ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>

					<div class="form-group col-md-12">
						<label for="modal-modifier-enseignant-code">Courriel :</label>
                        <input name="courriel" class="form-control col-md-8" <?= ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) ? '' : 'disabled'; ?>>
					</div>

                    <? if ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) : ?>

						<div class="form-inline" style="margin-top: 30px">
							<div class="col-md-6">
								<div style="margin-bottom: 10px">Mot-de-passe :</div>
								<input name="password" type="password" class="form-control" style="width: 100%" placeholder="Mot-de-passe">
							</div>
							<div class="col-md-6">
								<div style="margin-bottom: 10px">&nbsp;</div>
								<input name="password2" type="password" class="form-control" style="width: 100%" placeholder="Confirmation">
							</div>
						</div>

						<div class="tspace"></div>				

					<? endif; ?>

				</form>
				
      		</div>
      
            <div class="modal-footer">
				<div id="modal-modifier-enseignant-sauvegarde" class="btn btn-success"><i class="fa fa-plus-circle"></i> Sauvegarder les changements</div>
				<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

        </div>
    </div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: APPROUVER ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div class="modal fade" id="approuver-enseignant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approbation d'un nouvel enseignant</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

			<br />
			<center>
				<span style="color: crimson">Attention !</span><br /><br />
				Cette opération est irrévocable.
			</center>
			<br />

      </div>
      <div class="modal-footer">
        <div class="btn btn-secondary" data-dismiss="modal">Annuler</div>
        <div id="approuver-enseignant-sauvegarder" data-joindre_id="" class="btn btn-success spinnable">
            <i class="fa fa-check" style="margin-right: 5px"></i> 
			Approuver
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
		</div>
      </div>
    </div>
  </div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: DESAPPROUVER ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div class="modal fade" id="desapprouver-enseignant" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Refuser un nouvel enseignant</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

			<br />
			<center>
				<span style="color: crimson">Attention !</span><br /><br />
				Cette opération est irrévocable.
			</center>
			<br />

      </div>
      <div class="modal-footer">
        <div class="btn btn-secondary" data-dismiss="modal">Annuler</div>
        <div id="desapprouver-enseignant-sauvegarder" data-joindre_id="" class="btn btn-danger spinnable">
            Refuser
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
		</div>
      </div>
    </div>
  </div>
</div>
