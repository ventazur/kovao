<? 
/* ====================================================================
 *
 * BARRE DE STATUS (ECOLE / GROUPE / ENSEIGNANT)
 *
 * ==================================================================== */ ?>

<? 
/* ====================================================================
 *
 * BARRE DE STATUS NON PRESENTE
 *
 * ==================================================================== */ ?>

<? if ($this->uri->segment(1) == 'erreur') : ?>

    <div></div>

<? elseif ($this->sous_domaine == 'www' && @$current_controller == 'evaluation' && empty($current_method)) : ?>

    <div></div>

<? elseif ($this->sous_domaine == 'www' && ! $this->logged_in && @$current_controller != 'evaluation') : ?>

    <div></div>

<? elseif ($this->sous_domaine == 'www' && $this->logged_in && $this->est_etudiant) : ?>

    <div></div>

<? 
/* ====================================================================
 *
 * BARRE DE STATUS PRESENTE
 *
 * ==================================================================== */ ?>

<? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) != 'soumission') : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

        <div id="navbar-2" class="container-fluid">

            <div class="text-nowrap text-truncate">

                <? if ( ! empty($ecole) && ! empty($this->groupe_id)) : ?>

                    <?= $ecole['ecole_nom']; ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                <? endif; ?>

                <? if ( ! empty($groupe)) : ?> 
                
                    <? if ($this->groupe_id != 0) : ?>
                        <?= $groupe['groupe_nom']; ?>
                    <? endif; ?>

                <? endif; ?>

                <? if ( ! empty($enseignant)) : ?>

                    <? if ( $this->groupe_id != 0) : ?>
                        <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                    <? else : ?>
                        <i class="fa fa-square-o" style="margin-right: 5px"></i>
                    <? endif; ?>
                    <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></a>

                <? else : ?>

                    <? // C'est un etudiant. ?>
                
                    <? if ( ! empty($evaluation_details)) : ?>

                        <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                        <?= $evaluation_details['enseignant_prenom'] . ' ' . $evaluation_details['enseignant_nom']; ?></a>

                    <? endif; ?>

                <? endif; ?>

            </div>

        </div> <!-- #navbar-2 -->
    </nav>

<? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) == 'soumission') : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

        <div id="navbar-2" class="container-fluid">

            <div class="text-nowrap text-truncate">

                <? if ( ! empty($ecole) && ! empty($this->groupe_id)) : ?>

                    <?= $ecole['ecole_nom']; ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                <? endif; ?>

                <? if ( ! empty($groupe)) : ?> 
                
                    <? if ($this->groupe_id != 0) : ?>
                        <?= $groupe['groupe_nom']; ?>
                    <? endif; ?>

                <? endif; ?>

                <? if ( $this->groupe_id != 0) : ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                <? else : ?>
                    <i class="fa fa-square-o" style="margin-right: 5px"></i>
                <? endif; ?>
                <?= @$enseignant['prenom'] . ' ' . @$enseignant['nom']; ?></a>

            </div>

        </div> <!-- #navbar-2 -->
    </nav>

<? else : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

        <div id="navbar-2" class="container-fluid">

            <div class="text-nowrap text-truncate">

                <? if ( ! empty($ecole) && $this->groupe_id != 0) : ?>

                    <?= $ecole['ecole_nom']; ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                <? endif; ?>

                <? if ( ! empty($groupe) && $this->groupe_id != 0) : ?> 

                    <?= $groupe['groupe_nom']; ?>

                <? endif; ?>

                <? if ( ! empty($enseignant) && $this->appartenance_groupe) : ?>

                    <? // C'est un enseignant. ?>

                    <? if ( $this->groupe_id != 0) : ?>
                        <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                    <? else : ?>
                        <i class="fa fa-square-o" style="margin-right: 5px"></i>
                    <? endif; ?>
                    <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></a>

                    <? if ( ! empty($enseignant['semestre_id'])) : ?>

                        <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                        <?= @$this->semestres[$enseignant['semestre_id']]['semestre_code']; ?>

                    <? endif; ?>

                <? endif; ?>

            </div>

        </div> <!-- #navbar-2 -->
    </nav>

<? endif; ?>
