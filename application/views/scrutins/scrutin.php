<div id="scrutin">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

	<? 
	   //
       // initialiser les champs invisibles
       //

        if ($previsualisation)
        {
            $scrutin_lance_id   = 0;
            $scrutin_reference  = 'previsualisation';
            $scrutin['termine'] = 0;
            $scrutin['prenom']  = $this->enseignant['prenom'];
            $scrutin['nom']     = $this->enseignant['nom'];
            $form_url           = base_url() . 'scrutins';
        }
        else
        {
            $form_url = base_url() . 'scrutin/' . $scrutin_reference . '/voter';
        }

        $hidden = array(
            'scrutin_reference' => $scrutin_reference,
            'scrutin_id'        => $scrutin_id,
            'anonyme'           => $scrutin['anonyme'],
            'scrutin_lance_id'  => $scrutin_lance_id,
            'enseignant_id'     => $this->enseignant_id,
            'session_id'        => session_id(),
            'previsualisation'  => $previsualisation ?: FALSE
   	   ); 
	?>

    <?= form_open($form_url, array('id' => 'scrutin-soumission-form'), $hidden); ?>

        <h4>Scrutin</h4>

        <div class="space"></div>

        <? if ( ! $previsualisation && ! $scrutin['termine'] && $scrutin['echeance_epoch'] > $this->now_epoch) : ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Vous avez jusqu'au <strong><?= date_french_full($scrutin['echeance_epoch']); ?></strong> pour voter.
            </div>

        <? else : ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Vous avez jusqu'au <strong><?= ! empty($scrutin['echeance_epoch']) ? date_french_full($scrutin['echeance_epoch']) : date_french_full($this->now_epoch + 90*60*60*24); ?></strong> pour voter.
            </div>

        <? endif; ?>

        <? if (1 == 2) : ?>
            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin a été créé par <?= $scrutin['prenom'] . ' ' . $scrutin['nom']; ?>.
            </div>
        <? endif; ?>

        <div class="tspace"></div>

        <div id="scrutin-question" style="<?= ! empty($documents) ? 'border-radius: 3px 3px 0 0; border-bottom: 1px solid #9FA8DA;' : ''; ?>">
            <?= $scrutin['scrutin_texte']; ?>
        </div>

        <? if ( ! empty($documents)) : 

                $plusieurs = count($documents) > 1 ? 's' : '';
        ?>
            <? 
            $i=0;

            $mime_view = array(
                'application/pdf', 'image/jpg', 'image/jpeg', 'image/png'
            );

            foreach($documents as $d) : 

                $i++;
                $doc_url = base_url() . $this->config->item('documents_path') . $d['doc_filename'];
            ?>
                <div class="scrutin-document" style="<?= $i == 1 ? 'border-top: 0' : ''; ?>">

                    <div class="row">
                        <div class="col-9 text-truncate">
                            <div style="padding-top: 4px">

                                <i class="fa fa-lg <?= determiner_file_icon($d['doc_mime_type']); ?>" style="margin-right: 10px;"></i>

                                <a <?= in_array($d['doc_mime_type'], $mime_view) ? 'href="' . $doc_url . '"' : ''; ?>>
                                    <? if (empty($d['doc_caption'])) : ?>
                                        Document <?= $scrutin_id . '.' . $i; ?>
                                    <? else : ?>
                                        <?= $d['doc_caption']; ?>
                                    <? endif; ?>
                                </a>
                            </div>
                        </div> <!-- .col-8 -->
                        <div class="col-3" style="text-align: right">
                            <a href="<?= $doc_url; ?>" class="btn btn-sm btn-outline-dark" download="Document<?= $scrutin_id . '_' . $i . '.' . determiner_extension($d['doc_filename']); ?>">
                                <i class="fa fa-download"></i> 
                            </a>
                        </div>

                    </div>

                </div>

            <? endforeach; ?>

        <? endif; ?>

        <div class="tspace"></div>

        <div id="scrutin-choix">

            <i class="fa fa-square" style="color: #3949AB; margin-right: 10px"></i> 
            <span style="color: #3F51B5">
                Faites votre choix :
            </span>

            <div class="hspace"></div>
        
            <? if (empty($choix)) : ?>

                <div class="hspace"></div>

                <span style="font-size: 0.9em">
                    <i class="fa fa-exclamation-circle"></i> Il n'y a aucun choix de défini.
                </span>

            <? else : ?>

                <? foreach($choix as $c) : 

                    if ($previsualisation)
                    {
                        $scrutin_lance_choix_id = random_string('numeric', 4);
                    }
                    else
                    {
                        $scrutin_lance_choix_id = $c['scrutin_lance_choix_id'];
                    }
                ?>

                    <div class="scrutin-choix" data-choix_id="<?= $scrutin_lance_choix_id; ?>">

                        <div class="form-check">
                            <input name="scrutin_lance_choix_id" id="choix_<?= $scrutin_lance_choix_id; ?>" class="form-check-input" type="radio" value="<?= $scrutin_lance_choix_id; ?>" required>
                            <label class="form-check-label" style="margin-left: 7px" for="choix_<?= $scrutin_lance_choix_id; ?>">
                                <?= $c['choix_texte']; ?>
                            </label>
                        </div>

                    </div> <!-- .scrutin-choix -->

                <? endforeach; ?>

            <? endif; ?>

        </div> <!-- #scrutin-choix -->

        <div class="tspace"></div>

        <? if ($scrutin['anonyme']) : ?>
            <div style="font-size: 0.9em">
                <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                Ce scrutin est <strong>anonyme</strong>.
            </div>
        <? else : ?>
            <div style="font-size: 0.9em">
                <i class="fa fa-info-circle" style="color: darkorange; margin-right: 5px"></i>
                Ce scrutin n'est pas anonyme. Votre nom sera associé publiquement à votre choix.
            </div>
        <? endif; ?>

        <div class="tspace"></div>

        <? if ($previsualisation) : ?>

            <a class="btn btn-primary" href="<?= base_url() . 'scrutins/editeur/' . $scrutin_id; ?>">
                <i class="fa fa-undo" style="margin-right: 7px"></i>
                Retour à l'éditeur
            </a>

        <? else : ?>

            <button type="submit" class="btn btn-primary">
                Je vote
                <i class="fa fa-check-circle" style="margin-left: 7px"></i> 
            </button>

        <? endif; ?>

    </form>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils -->
