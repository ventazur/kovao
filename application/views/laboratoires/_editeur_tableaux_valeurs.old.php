<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX - SECTION
 *
 * -------------------------------------------------------------------- */ ?>

<div id="editeur-tableaux" class="mt-4 mb-4">

    <a class="anchor" name="tableaux-valeurs"></a>

    <div id="editeur-tableaux-titre" class="editeur-section-titre">
        <i class="fa fa-table" style="color: #fff; margin-right: 5px"></i> 
        Tableaux : valeurs
    </div>

    <? if (permis('admin_lab')) : ?>

        <div id="editeur-tableaux-contenu" class="editeur-section-contenu">
            
            <div class="btn btn-outline-primary" id="ajouter-tableau-champ" data-toggle="modal" data-target="#modal-tableau-ajouter-champ">
                <i class="bi bi-plus-circle mr-1"></i>
                Ajouter une valeur à un champ
            </div>

        </div>

    <? endif; ?>

</div> <!-- #editeur-tableaux -->

<? 
    $lv = $lab_valeurs;

    $data = array(
        'lv' => $lv,
        'lp' => $lab_prefix
    );
?>

<? if ( ! empty($lv)) : ?>

<? 
/* --------------------------------------------------------------------
 *
 * TABLEAUX
 *
 * -------------------------------------------------------------------- */ ?>

<?
    $tableau_max = 1;
    $tableaux_points = array();

	//
    // Organiser les champs des tableaux
	//

	$v_par_t = array();

    foreach($lv as $c => $c_arr)
    {
        // Il devrait toujours y avoir une clef 'tableau'.
        if ( ! array_key_exists('tableau', $c_arr))
            continue;
        
        $tableau_no = $c_arr['tableau'];

        //
        // Organiser les champs par numero de tableau
        //

        if ( ! array_key_exists($c_arr['tableau'], $v_par_t))
        {
            $v_par_t[$tableau_no] = array();
        }

        $v_par_t[$tableau_no][$c] = $c_arr;

        if ($c_arr['tableau'] > $tableau_max)
        {
            $tableau_max = $tableau_no;
        }
    }

	//
	// Ordonner selon le numero du tableau
	//

	ksort($v_par_t);

	//
	// Order les champs alphabetiquement
	//

	foreach($v_par_t as &$s_arr)
	{
		ksort($s_arr);
	}

?>

<?  foreach(range(1, $tableau_max) as $tableau) : ?>

    <? if ( ! array_key_exists($tableau, $v_par_t)) continue; ?>

    <?  
        $tableau_points = 0;

        // Determiner les points associes au tableau

        if ( ! empty($lab_points_tableaux) && array_key_exists($tableau, $lab_points_tableaux) && is_array($lab_points_tableaux[$tableau]))
        {
            foreach($lab_points_tableaux[$tableau] as $c)
            {
                $tableau_points += $c['points'];
            }
        }

    ?>

	<?
	/* ----------------------------------------------------------------
     * 
     * TABLEAU
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="editeur-tableau mt-4">

        <div class="editeur-tableau-titre editeur-section-titre">

            <div class="row">

                <div class="col-sm-6">

                    Tableau <?= $tableau; ?>

                </div> <!-- /.col -->

                <div class="col-sm-6">

                    <div style="margin-top: -4px; margin-bottom: -4px; text-align: right; font-weight: 700">
                        <div class="btn btn-warning btn-sm">
                            <?= format_nombre($tableau_points); ?> point<?= $tableau_points > 1 ? 's' : ''; ?>
                        </div>
                    </div>

                </div> <!-- /.col -->

            </div> <!-- .row -->

        </div>    

        <div class="editeur-tableau-contenu">

			<? foreach($v_par_t[$tableau] as $c => $c_arr) : ?>

            <?
            /* ------------------------------------------------------------
             *
             * Description du champ
             *
             * ------------------------------------------------------------ */
            $data['champ'] = $c;

            $this->load->view('laboratoires/_editeur_tableaux_champ', $data);
            ?>

			<? endforeach; ?>

        </div> <!-- .editeur-tableau-contenu -->

    </div> <!-- .editeur-tableau -->

<? endforeach; ?>

<? endif; // if ! empty($lv) ?>

<?
/* --------------------------------------------------------------------
 *
 * MODALS
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* -------------------------------------------------------------------------
 *
 * (modal) AJOUTER UNE VALEUR A UN CHAMP
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-tableau-ajouter-champ" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle" style="margin-right: 5px"></i> 
                    Ajouter une valeur à un champ
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div> <!-- .modal-header -->

            <div class="modal-body">

                <? $champ = substr(bin2hex(random_bytes(5)), 0, 7); ?>

                <div id="tableau-ajouter-champ-data" class="d-none"
                    data-lab_prefix="<?= $lab_prefix; ?>"
                    data-champ_tmp="<?= $champ; ?>"
                ></div>

                <?= 
                form_open(NULL, array('id' => 'modal-tableau-ajouter-champ-form'),
                    array(        
                        'evaluation_id'  => $evaluation_id,
                        'champ_tmp'      => $champ
                    )
                );
                ?>

                <div class="alert alert-danger d-none" role="alert" style="margin: 15px">
                    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                    <span class="alert-msg"></span>
                </div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Nom du champ
                 *
                 * -------------------------------------------------------------------- */ ?>

				<?
                    $liste_champs = array();

                    $types = array('standard', 'comparaison');

					if ( ! empty($lab_points) && is_array($lab_points))
					{
						foreach($lab_points as $c => $c_arr)
						{
							if (array_key_exists($c, $lab_valeurs))
								continue;

							if (in_array($c_arr['type'], $types))
								$liste_champs[] = $c;
						}

						sort($liste_champs);
					}
				?>

				<div class="form-group col-md-12">
					<label for="selectField">Nom du champ</label>
					<select class="form-control col-md-4" name="nom_champ" id="<?= $lab_prefix . '-' . $champ . '-champ'; ?>">
						<? foreach($liste_champs as $c) : ?>
							<option value="<?= $c; ?>"><?= $c; ?></option>
						<? endforeach; ?>
					</select>
				</div>
	
				<? /*
                <div class="form-group col-md-12">
                    <label for="<?= $lab_prefix . '-' . $champ . '-champ'; ?>">Nom du champ (sans espace, ni tiret)</label>
                    <input name="<?= $champ . '-champ'; ?>" class="form-control col-md-3" id="<?= $lab_prefix . '-' . $champ . '-champ'; ?>" type="text" value="champagne">
                </div>
				*/ ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Valeur / Notation scientifique / Unites
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-4" style="margin-bottom: -10px">

					<div class="form-group col-md-4">
                        <label for="<?= $lab_prefix . '-' . $champ . '-valeur'; ?>">Valeur</label>
                        <input name="valeur" type="text" class="form-control" 
                            id="<?= $lab_prefix . '-' . $champ . '-valeur'; ?>" value="1,0">
                    </div> <!-- form-group -->

					<div class="form-group col-md-4">
						<label for="<?= $lab_prefix . '-' . $champ . '-nsci'; ?>">Notation scientifique</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">&times;10<span style="font-weight: 400; color: crimson"> <sup>n</sup></span>, &nbsp;<span style="color: crimson">n</span>&nbsp;=</div>
						   </div>
							<input name="nsci" class="form-control" id="<?= $lab_prefix . '-' . $champ . '-nsci'; ?>" type="number" value="0">
						</div>
						<div class="mt-1 ml-1" style="font-size: 0.8em; color: #777;">
							Si n = 0, aucune notation scientifique.
						</div>
					</div>

					<div class="form-group col-md-4">
						<label for="<?= $lab_prefix . '-' . $champ . '-unites'; ?>">Unités</label>
						<input name="unites'; ?>" class="form-control" id="<?= $lab_prefix . '-' . $champ . '-unites'; ?>" type="text" value="mL">
					</div>
					
                </div> <!-- .form-row -->

      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
                <div class="col">
                    <? /*
                    <div id="modal-modifier-champ-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce champ
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                        </div>
                    */ ?>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-tableau-ajouter-champ-sauvegarde" class="btn btn-success spinnable">
                        <i class="bi bi-plus-circle" style="margin-right: 3px"></i> Ajouter ce champ
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>

            </div> <!-- .modal-footer -->

    	</div> <!-- .modal-content -->
  	</div> <!-- .modal-dialog -->
</div> <!-- .modal -->

<?
/* -------------------------------------------------------------------------
 *
 * (modal) MODIFIER TABLEAU CHAMP
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-tableau-modifier-champ" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="bi bi-pencil-square" style="margin-right: 5px"></i> Modifier le champ d'un tableau</span></var></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div> <!-- .modal-header -->

      		<div class="modal-body">



                <? // le contenu est genere dynamiquement ?>



      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
			
				<? if (permis('admin_lab')) : ?>	

					<div class="col">
						<div id="modal-tableau-effacer-champ" class="btn btn-outline-danger spinnable">
							<i class="fa fa-trash"></i> Effacer ce champ
							<i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
						</div>
					</div>

				<? endif; ?>

                <div class="col" style="text-align: right">
                    <div id="modal-tableau-modifier-champ-sauvegarde" class="btn btn-success spinnable">
                        <i class="bi bi-floppy" style="margin-right: 3px"></i> Sauvegarder
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>

            </div> <!-- .modal-footer -->

    	</div> <!-- .modal-content -->
  	</div> <!-- .modal-dialog -->
</div> <!-- .modal -->


