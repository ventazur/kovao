<?
/* ------------------------------------------------------------------------
 *
 * IDENTIFICATION POUR LES LABORATOIRES
 *
 * ------------------------------------------------------------------------ */ ?>

<? $lab_partenaires_confirmes = $traces['lab_partenaires_confirmes'] ?? FALSE; ?>

<? $lab_individuel = array_key_exists('individuel', $lab_parametres) && $lab_parametres['individuel'] ? TRUE : FALSE; ?>

<div id="evaluation-identification-lab">

    <div id="identification-titre">
        <? if ($lab_individuel) : ?>

            Identification du laboratoire

        <? else : ?>

            Identification des partenaires de laboratoire

        <? endif; ?>
    </div>

    <div id="identification-contenu">

        <?
        /* ----------------------------------------------------------------
         *
         * Numero de place
         *
         * ---------------------------------------------------------------- */ ?>

        <div class="form-row">

            <div class="col-md-2">

                <label for="lab-place"># de place</label>
                <input name="lab_place" type="number" min="1" max="16"class="form-control" id="lab-place" 
                    value="<?= $traces['lab_place'] ?? NULL; ?>" required <?= ($lab_partenaires_confirmes && ! $this->est_enseignant) ? 'readonly' : ''; ?>>
                <div class="invalid-feedback d-none">
                    Ce champ est obligatoire.
                </div>

            </div>

        </div>

        <?
        /* ----------------------------------------------------------------
         *
         * Nom des partenaires
         *
         * ---------------------------------------------------------------- */ ?>

        <div class="form-row">

            <? 
                $partenaire1 = '';

                if ($this->est_etudiant)
                {
                    $partenaire1 = $this->etudiant['prenom'] . ' ' . $this->etudiant['nom'];
                }
                elseif  (is_array($traces) && array_key_exists('nom', $traces) && ! empty($traces['nom']))
                {
                   $partenaire1 =  $traces['nom'];
                }
                elseif ($previsualisation)
                {
                    $partenaire1 = $this->enseignant['prenom'] . ' ' . $this->enseignant['nom'];
                }
                else
                {
                    $partenaire1 = 'ERREUR';
                }
            ?>

            <?
            /* ------------------------------------------------------------
             *
             * Partenaire 1 (principal)
             *
             * ------------------------------------------------------------ */ ?>

            <div class="col-md-4 mt-3">
                <label for="evaluation-nom">Partenaire 1</label>
                <input name="prenom_nom" type="text" class="form-control lab-identification" id="evaluation-nom"
                    placeholder="Entrez votre prénom et nom" 
                    style="background: #E3F2FD;"
                    value="<?= $partenaire1; ?>" required readonly>
                <div class="invalid-feedback d-none">
                    Ce champ est obligatoire.
                </div>
            </div>

            <input name="lab_partenaire1_matricule" type="hidden" value="<?= $this->est_etudiant && ! empty($this->etudiant['numero_da']) ? $this->etudiant['numero_da'] : NULL; ?>">
            <input name="lab_cours_groupe" type="hidden" value="<?= $lab_cours_groupe ?? NULL; ?>">
            <input name="lab_individuel" type="hidden" value="<?= $lab_individuel ? 1 : 0; ?>">

            <? if ($lab_individuel) : ?>

                <div class="col"></div>

            <? else : ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaire 2 (facultatif)
                 *
                 * ------------------------------------------------------------ */ ?>

                <div class="col-md-4 mt-3">

                    <? if ($lab_partenaires_confirmes) : ?>

                        <? if ($this->est_enseignant) : ?>

                            <label for="lab-partenaire2">Partenaire 2</label>

                            <input name="lab_partenaire2_nom" type="text" class="form-control lab-identification" id="lab-partenaire2" value="<?= $traces['lab_partenaire2_nom'] ?? NULL; ?>"
                                placeholder="Entrez le prénom et nom">

                        <? elseif (array_key_exists('lab_partenaire2_eleve_id', $traces) && ! empty($traces['lab_partenaire2_eleve_id'])) : ?>

                            <?
                                $eleve_id 		 = $traces['lab_partenaire2_eleve_id'];
                                $eleve_nom 		 = $lab_eleves[$eleve_id]['eleve_prenom'] . ' ' . $lab_eleves[$eleve_id]['eleve_nom'];
                                $eleve_matricule = $lab_eleves[$eleve_id]['numero_da'];
                            ?>

                            <label for="lab-partenaire2">Partenaire 2</label>

                            <input class="form-control lab-identification"
                                style="background: #E3F2FD;"
                                value="<?= $eleve_nom; ?>" readonly>
                
                        <? endif; ?>

                    <? else : ?>

                        <label for="lab-partenaire2">Partenaire 2</label>

                        <? if ( ! empty($lab_eleves) && is_array($lab_eleves)) : ?>

                            <?
                                $eleve_id = NULL;

                                if (array_key_exists('lab_partenaire2_eleve_id', $traces) && ! empty($traces['lab_partenaire2_eleve_id']))
                                {
                                    $eleve_id 		 = $traces['lab_partenaire2_eleve_id'];
                                    $eleve_nom 		 = $lab_eleves[$eleve_id]['eleve_prenom'] . ' ' . $lab_eleves[$eleve_id]['eleve_nom'];
                                    $eleve_matricule = $lab_eleves[$eleve_id]['numero_da'];
                                }
                            ?>

                                <select name="lab_partenaire2" class="form-control lab-identification lab-partenaire-select" <?= $en_direct ? 'disabled' : ($lab_inviduel ? 'required' : ''); ?>>
                                <option value="" data-matricule=""></option>

                                <option value="0" 
                                        data-matricule=""
                                        <?= $eleve_id === '0' ? 'selected' : ''; ?>>
                                    --- pas de partenaire ---
                                </option>

                                <? $nom_selectionne = NULL; ?>

                                <? foreach($lab_eleves as $e_id => $e) : ?>
                                    <option value="<?= $e['eleve_id']; ?>" 
                                            data-matricule="<?= $e['numero_da']; ?>" 
                                            data-nom="<?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>"
                                            <?= $e['eleve_id'] == $eleve_id ? 'selected' : ''; ?>>
                                        <?= $e['eleve_nom'] . ', ' . $e['eleve_prenom']; ?>
                                    </option>

                                    <?  /* Ceci sert a enregistrer le nom si jamais cet eleve n'est pas trouve dans la base de donnees. */ ?>

                                    <? if ($e['eleve_id'] == $eleve_id) : ?>
                                        <? $nom_selectionne = $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>
                                    <? endif; ?>

                                <? endforeach; ?>

                            </select>

                        <? else : ?>

                            <input name="lab_partenaire2_nom" type="text" class="form-control lab-identification" id="lab-partenaire2" value="<?= $traces['lab_partenaire2_nom'] ?? NULL; ?>"
                                placeholder="Entrez le prénom et nom">

                        <? endif; ?>

                    <? endif; ?>

                    <div class="invalid-feedback d-none">
                        Ce champ est obligatoire.
                    </div>

                    <input id="lab_partenaire2_eleve_id" type="hidden" name="lab_partenaire2_eleve_id" value="<?= $traces['lab_partenaire2_eleve_id'] ?? NULL; ?>">
                    <input id="lab_partenaire2_etudiant_id" type="hidden" name="lab_partenaire2_etudiant_id" value="<?= $traces['lab_etudiant2_id'] ?? NULL; ?>">
                    <input id="lab_partenaire2_matricule" type="hidden" name="lab_partenaire2_matricule" value="<?= $eleve_matricule ?? NULL; ?>">

                    <? if ( ! $this->est_enseignant) : ?>
                        <input id="lab_partenaire2_nom" type="hidden" name="lab_partenaire2_nom" value="<?= $traces['lab_partenaire2_nom'] ?? NULL; ?>">
                    <? endif; ?>

                </div>

            <? endif; // lab_individuel ?> 

            <? if ( ! $lab_individuel) : ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaire 3 (facultatif)
                 *
                 * ------------------------------------------------------------ */ ?>

                <div class="col-md-4 mt-3">

                    <? if ($lab_partenaires_confirmes) : ?>

                        <? if ($this->est_enseignant) : ?>

                            <label for="lab-partenaire3">Partenaire 3</label>

                            <input name="lab_partenaire3_nom" type="text" class="form-control lab-identification" id="lab-partenaire3" value="<?= $traces['lab_partenaire3_nom'] ?? NULL; ?>"
                                placeholder="Entrez le prénom et nom">

                        <? elseif (array_key_exists('lab_partenaire3_eleve_id', $traces) && ! empty($traces['lab_partenaire3_eleve_id'])) : ?>

                            <?
                                $eleve_id 		 = $traces['lab_partenaire3_eleve_id'];
                                $eleve_nom 		 = $lab_eleves[$eleve_id]['eleve_prenom'] . ' ' . $lab_eleves[$eleve_id]['eleve_nom'];
                                $eleve_matricule = $lab_eleves[$eleve_id]['numero_da'];
                            ?>
                        
                            <label for="lab-partenaire3">Partenaire 3</label>

                            <input class="form-control lab-identification"
                                style="background: #E3F2FD;"
                                value="<?= $eleve_nom; ?>" readonly>
                
                        <? endif; ?>

                    <? else : ?>

                        <label for="lab-partenaire3">Partenaire 3</label>

                        <? if ( ! empty($lab_eleves) && is_array($lab_eleves)) : ?>

                            <?
                                $eleve_id = 0;

                                if (array_key_exists('lab_partenaire3_eleve_id', $traces) && ! empty($traces['lab_partenaire3_eleve_id']))
                                {
                                    $eleve_id 		 = $traces['lab_partenaire3_eleve_id'];
                                    $eleve_nom 		 = $lab_eleves[$eleve_id]['eleve_prenom'] . ' ' . $lab_eleves[$eleve_id]['eleve_nom'];
                                    $eleve_matricule = $lab_eleves[$eleve_id]['numero_da'];
                                }
                            ?>

                            <select name="lab_partenaire3" class="form-control lab-identification lab-partenaire-select" <?= $en_direct ? 'disabled' : ''; ?>>
                                <option value="" data-matricule=""></option>

                                <? $nom_selectionne = NULL; ?>

                                <? foreach($lab_eleves as $e) : ?>
                                    <option value="<?= $e['eleve_id']; ?>" 
                                            data-matricule="<?= $e['numero_da']; ?>" 
                                            data-nom="<?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>"
                                            <?= $e['eleve_id'] == $eleve_id ? 'selected' : ''; ?>>
                                        <?= $e['eleve_nom'] . ', ' . $e['eleve_prenom']; ?>
                                    </option>

                                    <?  /* Ceci sert a enregistrer le nom si jamais cet eleve n'est pas trouve dans la base de donnees. */ ?>

                                    <? if ($e['eleve_id'] == $eleve_id) : ?>
                                        <? $nom_selectionne = $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>
                                    <? endif; ?>

                                <? endforeach; ?>
                            </select>

                        <? else : ?>

                            <input name="lab_partenaire3_nom" type="text" class="form-control" id="lab-partenaire3_nom" value="<?= $traces['lab_partenaire3_nom'] ?? NULL; ?>"
                                placeholder="Entrez le prénom et nom">
                
                        <? endif; ?>

                    <? endif; ?>

                    <input id="lab_partenaire3_eleve_id" type="hidden" name="lab_partenaire3_eleve_id" value="<?= $traces['lab_partenaire3_eleve_id'] ?? NULL; ?>">
                    <input id="lab_partenaire3_etudiant_id" type="hidden" name="lab_partenaire3_etudiant_id" value="<?= $traces['lab_etudiant3_id'] ?? NULL; ?>">
                    <input id="lab_partenaire3_matricule" type="hidden" name="lab_partenaire3_matricule" value="<?= $eleve_matricule ?? NULL; ?>">

                    <? if ( ! $this->est_enseignant) : ?>
                        <input id="lab_partenaire3_nom" type="hidden" name="lab_partenaire3_nom" value="<?= $traces['lab_partenaire3_nom'] ?? NULL; ?>">
                    <? endif; ?>
                </div>

        <? endif; // individuel ?>

        </div>  <!-- .form-row -->

        <?
        /* ----------------------------------------------------------------
         *
         * Confirmation du choix des partenaires de laboratoire
         *
         * ---------------------------------------------------------------- */ ?>

        <? if ( ! $lab_partenaires_confirmes) : ?>

            <? if ($lab_individuel) : ?>

                <div id="lab-partenaires-confirmation" class="mt-5 text-right">
                
                    <div class="btn btn-outline-primary" data-toggle="modal" data-target="#lab-partenaires-confirmer">
                        <i class="bi bi-check-circle-fill mr-2"></i>

                        Confirmer votre place de laboratoire

                    </div>

                </div>

            <? else : ?>

                <div id="lab-partenaires-confirmation" class="mt-4 text-right">
                
                    <div class="btn btn-outline-primary" data-toggle="modal" data-target="#lab-partenaires-confirmer">
                        <i class="bi bi-check-circle-fill mr-2"></i>

                        Confirmer votre place et vos partenaires de laboratoire

                    </div>

                </div>

            <? endif; ?>

        <? endif; ?>

    </div> <!-- #identification-contenu -->

</div> <!-- #evaluation-identification-lab -->

<?
/* --------------------------------------------------------------------
 *
 * Modal : Confirmer partenaires de laboratoire
 *
 * -------------------------------------------------------------------- */ ?>

<div class="modal fade" id="lab-partenaires-confirmer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center pt-4 pb-4">

                <? if ($lab_individuel) : ?>

                    Veuillez confirmer votre place de laboratoire.<br /><br />

                <? else : ?>

                    Veuillez confirmer votre place et vos partenaires de laboratoire.<br /><br />

                <? endif; ?>

                <div style="color: crimson">
                    <i class="bi bi-exclamation-circle mr-1"></i>
                    Cette action est irrévocable.
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                <button id="lab-partenaires-confirmer-action" type="button" class="btn btn-success">
                    <i class="bi bi-check-circle mr-1"></i>
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

