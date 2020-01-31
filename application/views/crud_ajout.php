<?php
$this->load->helper('graph');
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<body>
    <div class="row" id="resizableParent">
        <div class="col-md-2 bg" id="resizableLeft">
            <h4><?=EXPORT_CONSTRUCTION_TITLE?>
                <a type="button" class="btn btn-link" target="_blank" href="<?php echo base_url('assets/pdf/Hadoop definition des données.pdf');?>" title="Définition des données">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                </a>
            </h4>
        </div>
        <div class="col-md-10" id="resizableRight">
            <div class="row">
                <div class="col-md-8">
                    <h3>
                        Ajout sur la BDD
                        <a type="button" class="btn btn-link" href="<?php echo site_url('user/myaccount'); ?>"
                           title="Changer de source de données">
                            Changer de source de données
                        </a>
                    </h3>
                </div>
                <div class="col-md-4 text-right">
                    <p> Dernière mise à jour des données : <?=$update_time_table?></p>
                </div>
            </div>
            <ul>
                <button>Ajout</button>
                <button>Modification</button>
                <button>Suppression</button>
            </ul>
            <div id="modif">
            </div>
            <div id="ajout">
                <?=form_open('crud/add') ?>
                <h1>Insertion des données</h1>
                <?php
                    if (isset($message)){ ?>
                    <CENTER><h3>Data inserted successfully</h3></CENTER><br>
                <?php 
                    } ?>
                <?php   
                    $nbElement = 0;
                    $array = array_keys(get_object_vars($test[0]));
                    foreach($array as $key){
                        ?>
                        <?php echo form_label($key.':'); ?> 
                        <?php echo form_error($key); ?><br/>
                        <?php 
                        echo form_input(array('id' => $key, 'name' => $key)); ?><br/> <?php
                    }
                    ?>
                    <?php echo form_submit(array('id' => 'submit', 'value' => 'Submit')); ?>
                    <?php echo form_close(); ?><br/>
            </div>
            <div id="delete">
            </div>
            <?=generate_modal("error_modal","Erreur")?>
            <h4>Filtres utilisés</h4>
            <?=generate_array_list_html($config)?>
        </div>
    </div>
</body>