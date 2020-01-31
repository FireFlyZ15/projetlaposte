<?php
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<body>
    <div class="row" id="resizableParent">
        <div class="col-md-2 bg" id="resizableLeft">
            <form action="">
                <h4><?=GRAPH_CONSTRUCTION_TITLE?>
                    <a type="button" class="btn btn-link" target="_blank" href="<?php echo base_url('assets/pdf/Dictionnaire de données Vidéocodage 360.pdf');?>" title="Définition des données">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>
                </h4>
                <a class="btn btn-primary btn-info"
                   onclick="showSample('<?= $config['source'] ?>','<?= $config['database'] ?>','<?= $config['table'] ?>');">Voir
                    les données</a><br>

                <label for="mode"><?=GRAPH_FORM_LABEL?></label><br/>
                    <SELECT id="mode" name="mode" size="1" onchange='verif()' class="form-control selectConfig">
                        <?php if ($config['mode'] == "bar"): ?>
                            <OPTION selected>bar
                            <OPTION>horizontalBar
                            <OPTION>line
                        <?php elseif ($config['mode'] == "horizontalBar"): ?>
                            <OPTION>bar
                            <OPTION selected>horizontalBar
                            <OPTION>line
                        <?php else: ?>
                            <OPTION>bar
                            <OPTION>horizontalBar
                            <OPTION selected>line
                        <?php endif; ?>
                    </SELECT>
                <div class="form-inline form-group">
                    <label for="stacked"><?=STACKED_LABEL?></label><br/>
                    <SELECT id="stacked" name="stacked" size="1" class="form-control selectConfig">
                        <?php if ($config['stacked'] == "aucun"): ?>
                            <OPTION selected>aucun
                            <OPTION>valeur
                            <OPTION>100%
                        <?php elseif ($config['stacked'] == "valeur"): ?>
                            <OPTION>aucun
                            <OPTION selected>valeur
                            <OPTION>100%
                        <?php else: ?>
                            <OPTION>aucun
                            <OPTION>valeur
                            <OPTION selected>100%
                        <?php endif; ?>
                    </SELECT>
                </div>
                <h4><?=DATA_TITLE?></h4>
                <label for="wording"><?=VALUE_LABEL?></label><br>
                <div class="form-inline form-group">
                    <?php if(is_array($config['wording'])): ?>
                        <?php foreach ($config['wording'] as $key => $wording_item): ?>
                            <SELECT id="wording<?=$key?>" name="wording[]" size="1" onchange='verif()' class="form-control selectFilter wording">
                                <?php foreach ($resultColumn as $rowColumn): ?>
                                <?php if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"): ?>
                                <?php elseif ($rowColumn->type == "timestamp"||$rowColumn->type == "datetime"): ?>
                                <?php $i=0;?>
                                <?php foreach ($DateFormatFrListTimeTamps as $DateFormat): ?>
                                <?php if ($wording_item == $rowColumn->name."::".$DateFormatList[$i]): ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" selected><?= $rowColumn->name." - $DateFormat"?>
                                    <?php else: ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>"><?=$rowColumn->name." - $DateFormat"?>
                                    <?php endif; ?>
                                    <?php $i++;?>
                                    <?php endforeach; ?>
                                    <?php elseif ($rowColumn->type == "date"): ?>
                                    <?php $i=0;?>
                                    <?php foreach ($DateFormatFrList as $DateFormat): ?>
                                    <?php if ($wording_item == $rowColumn->name."::".$DateFormatList[$i]): ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" selected><?= $rowColumn->name." - $DateFormat"?>
                                    <?php else: ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>"><?=$rowColumn->name." - $DateFormat"?>
                                    <?php endif; ?>
                                    <?php $i++;?>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <?php if ($wording_item == $rowColumn->name): ?>
                                <OPTION selected><?= $rowColumn->name ?>
                                    <?php else: ?>
                                <OPTION><?=$rowColumn->name ?>
                                    <?php endif; ?>
                                    <?php endif; ?>


                                    <?php endforeach; ?>
                            </SELECT>
                            <?php if($key==0) :?>
                                <button id="addWordingFormButton" type="button" onclick='addCopieSelect("wording")'
                                        class="btn btn-default" disabled><span class="glyphicon glyphicon-plus"
                                                                               aria-hidden="true"/></button>
                            <?php else:?>
                                <button class="btn btn-default"
                                        onclick="removeCopieSelect('wording',<?= $key ?>);return false;"
                                        id="wording<?= $key ?>_button"><span class="glyphicon glyphicon-minus"></span>
                                </button>
                            <?php endif;?>
                        <?php endforeach; ?>
                    <?php else :?>
                        <SELECT id="wording0" name="wording" size="1" onchange='verif()' class="form-control selectFilter wording">
                            <?php foreach ($resultColumn as $rowColumn): ?>
                                <?php if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"): ?>
                                <?php elseif ($rowColumn->type == "timestamp"||$rowColumn->type == "datetime"): ?>
                                <?php $i=0;?>
                                <?php foreach ($DateFormatFrListTimeTamps as $DateFormat): ?>
                                <?php if ($config['wording'] == $rowColumn->name."::".$DateFormatList[$i]): ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" selected><?= $rowColumn->name." - $DateFormat"?>
                                    <?php else: ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>"><?=$rowColumn->name." - $DateFormat"?>
                                    <?php endif; ?>
                                    <?php $i++;?>
                                    <?php endforeach; ?>
                                    <?php elseif ($rowColumn->type == "date"): ?>
                                    <?php $i=0;?>
                                    <?php foreach ($DateFormatFrList as $DateFormat): ?>
                                    <?php if ($config['wording'] == $rowColumn->name."::".$DateFormatList[$i]): ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" selected><?= $rowColumn->name." - $DateFormat"?>
                                    <?php else: ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>"><?=$rowColumn->name." - $DateFormat"?>
                                    <?php endif; ?>
                                    <?php $i++;?>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <?php if ($config['wording'] == $rowColumn->name): ?>
                                <OPTION selected><?= $rowColumn->name ?>
                                    <?php else: ?>
                                <OPTION><?=$rowColumn->name ?>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                        </SELECT>
                        <button id="addWordingFormButton" type="button" onclick='addCopieSelect("wording")'
                                class="btn btn-default"><span id="addFiltersButton" class="glyphicon glyphicon-plus"
                                                              aria-hidden="true"/></button>
                    <?php endif;?>

                </div>
                <label for="group"><?=VALUE_SEPARATE_LABEL?></label><br>
                <SELECT id="group" name="group" size="1" onchange='verif()' class="form-control selectConfig">
                    <OPTION>
                    <?php foreach ($resultColumn as $rowColumn): ?>
                        <?php if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"): ?>
                        <?php elseif ($rowColumn->type == "timestamp"||$rowColumn->type == "date"||$rowColumn->type == "datetime"): ?>
                        <?php $i=0;?>
                        <?php foreach ($DateFormatFrList as $DateFormat): ?>
                            <?php if ($config['group'] == $rowColumn->name."::".$DateFormatList[$i]): ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" selected><?= $rowColumn->name." - $DateFormat"?>
                            <?php else: ?>
                                <OPTION value="<?=$rowColumn->name."::".$DateFormatList[$i]?>"><?=$rowColumn->name." - $DateFormat"?>
                            <?php endif; ?>
                        <?php $i++;?>
                        <?php endforeach; ?>
                    <?php elseif ($config['group'] == $rowColumn->name): ?>
                            <OPTION selected><?=$rowColumn->name ?>
                        <?php else: ?>
                            <OPTION><?=$rowColumn->name ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </SELECT>
                    <?=generate_form_calcul($config, $resultColumn)?>
                <div id="messageErreur"></div>
                <h4>Filtrage du diagramme</h4>
                <?= generate_form_min_max($config["minimum"], $config["maximum"], $source->engine) ?><br/>
                <?=generate_form_filters($resultColumn)?>
                <h4 title="Ajoute la possibilité d'avoir des choix plus fin">Filtres rapides dynamiques</h4>
                <?=generate_form_add_speed_filters($resultColumn,$DateFormatFrList,$DateFormatFrListTimeTamps,$config,$DateFormatList)?>
                <button id="submit" type="submit" class="btn btn-primary form-control">Actualiser</button>
            </form>
            <?=generate_save_graph_form($name,$description, $listGroup, $group,$config,$image_name,$public,$live,"histogram",$id)?>
        </div>
        <div class="col-md-10" id="resizableRight">
            <div class="row">
                <div class="col-md-8">
                    <h3>
                        <?php echo $titre; ?>
                        <a type="button" class="btn btn-link" href="<?php echo site_url('user/myaccount'); ?>"
                           title="Changer de source de données">
                            Changer de source de données
                        </a>
                    </h3>
                </div>
                <div class="col-md-4 text-right">
                    <p> Dernière mise à jour des données : <?=$update_time_table?></p>
                    <?php if($config['wording']!=""):?>
                        <!--<h4><a href="<?=site_url() . "/graph/export/".$id."?" . http_build_query($config, '', '&')?>">Export CSV</a></h4>-->
                        <div id="downloadurl" hidden></div>
                    <?php endif;?>
                </div>
            </div>
            <div id="chargement" class="alert alert-info">
                <?php if($config['wording']!=""):?>
                    <img id="loader-img" src="<?= base_url() ?>assets/img/ajax-loader.gif"/>
                    <div id="chargement-message" ><?=SEARCH_RESULT?></div>
                <?php else: ?>
                    <div id="chargement-message">Pour générer un graphique, choisissez les champs et les filtres puis faites Actualiser.</div>
                <?php endif;?>
            </div>
            <div id="canvasDiv" style="height: auto;width:auto;">
                <canvas id="canvas" height="750" width="100%"></canvas>
            </div>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            <div id="table-div" class="table-overflow">
                                <table class="table table-striped table-bordered" id="modalTableSample"></table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
            <?=generate_modal("error_modal","Erreur")?>
            <div class="row">
                <div class="col-md-4">
                    <h4>Filtres rapide</h4>
                    <div id="speed-filters"></div>
                    <div id="canvasDiv" hidden></div>
                </div>
                <div class="col-md-4">
                    <h4>Filtres utilisés</h4>
                    <?=generate_array_list_html($config)?>
                </div>
            </div>
        </div>
    </div>
</body>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery-ui.min.js"></script>
<script type='text/javascript' src="<?=base_url()?>assets/js/Chart.bundle.min.js"></script>
<script type='text/javascript' src="<?=base_url()?>assets/js/graph.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/color.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
<script type='text/javascript' src="<?= base_url(); ?>assets/js/table.js"></script>
<script>
    resizableOn();
    <?="var configJSON ='".json_encode($config)."';\n"?>
    configGraph = JSON.parse(configJSON);
    var jq=jQuery.noConflict();
    jq("#speed-filters-form").chosen({max_selected_options: 10, no_results_text: "Aucun résultat pour", width: "100%"});

    <?php if($config['wording']!=""):?>
    var url = '<?=site_url() . "/api/getData/" . $id . "?" . http_build_query($config, '', '&')?>';
    jq.get(url, function (dataRAW) {
        data = JSON.parse(dataRAW);
        if (dataRAW != "" && dataRAW.indexOf("ERROR 300") != -1) {
            var nbOcc = dataRAW.split(':')[1].replace(/\s/g, '');
            document.getElementById('chargement').className = "alert alert-warning";
            document.getElementById('chargement').innerHTML = 'Il y a trop d’occurrences pour afficher le résultat (' + nbOcc + ') ! Le diagramme est capable d’afficher que 300 résultats.';
        } else if (dataRAW != "") {
            generateHistoGraph(data, configGraph);
            getHistoColorGraph(configGraph);
            createSpeedFiltersHisto(data, configGraph);
            createSpeedFilterRangeHisto(data, configGraph);
            <?php
            $wording = (is_array($config['wording'])) ? implode(";", $config['wording']) : $config['wording']
            ?>
            createDownloadCSVButtonForChartJs("histogram", "<?=$wording . ";" . $config['group'] . ";" . $config['typecalcul'] . "(" . $config['typecalculchamp'] . ")"?>", "<?=$config['table'] . " " . $update_time_table?>");
            document.getElementById('save_graph').disabled = false;
        } else {
            document.getElementById('chargement').className = "alert alert-warning";
            document.getElementById('chargement').innerHTML = 'Aucune donnée disponible pour votre recherche';
        }
    }).fail(function (data) {
        show_error_ajax(data);
        document.getElementById('chargement').className = "alert alert-danger";
        document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
    });
    addFilterAuto(configJSON);
    <?php endif;?>
    var champs = <?=arrayPHPToJs($resultColumn);?>;
</script>
