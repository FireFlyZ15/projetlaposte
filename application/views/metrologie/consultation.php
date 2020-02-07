<?php
$this->load->helper('graph');
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<body>
    <div class="row" id="resizableParent">
        <div id="resizableLeft" class="col-md-2 bg">
        <h4><?=TABLE_CONSTRUCTION_TITLE?>
            <a type="button" class="btn btn-link" target="_blank" href="<?php echo base_url('assets/pdf/Dictionnaire de données Vidéocodage 360.pdf');?>" title="Définition des données">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </a>
        </h4>

        <form action="">
            <?php if ($config['engine'] == "mysql" && $config['expertmode']): ?>
                <a class="btn btn-info btn-sm" href="<?php echo site_url('graph/table1D_generator/');?>">Mode normal</a><br/><br/>
                <input type="hidden" name="expertmode" value="true"/>
                <a data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Voir la base de donnée
                </a><br/>
                <div class="collapse" id="collapseExample">
                    <div id="database" class="well">
                    </div>
                </div>
                <label for="request">Requete SQL</label><br/>
                <textarea id="request" name="request" class="form-control textareaRight" cols="40" rows="5" placeholder="Votre requete SQL"><?=$config['request']?></textarea><br/>
            <?php else: ?>
                <?php if ($config['engine'] == "mysql"): ?>
                    <a class="btn btn-info btn-sm" href="<?php echo site_url('graph/table1D_generator/?expertmode=true');?>">Mode expert</a><br/><br/>
                <?php endif; ?>
                <a class="btn btn-primary btn-info"
                   onclick="showSample('<?= $config['source'] ?>','<?= $config['database'] ?>','<?= $config['table'] ?>');">Voir
                    les données</a><br>

                <label for="wording"><?=FIELD_LABEL?></label><br/>
                <?php foreach ($resultColumn as $rowColumn): ?>
                    <?php if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"): ?>
                        <input type="checkbox" id="<?=$rowColumn->name?>ID" name="champs[]" value="<?=$rowColumn->name?>">
                        <label class="wordwrap_label" for="<?=$rowColumn->name?>ID"><?=$rowColumn->name?></label><br/>
                    <?php elseif ($rowColumn->type == "date"): ?>
                        <?php $i=0;?>
                        <?php foreach ($DateFormatFrList as $DateFormat): ?>
                            <?php if (in_array($rowColumn->name."::".$DateFormatList[$i], $config['champs'])): ?>
                                <input type="checkbox" id="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID" name="champs[]" value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" checked>
                                <label class="wordwrap_label" for="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID"><?= $rowColumn->name." - $DateFormat"?></label><br/>
                            <?php else: ?>
                                <input type="checkbox" id="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID" name="champs[]" value="<?=$rowColumn->name."::".$DateFormatList[$i]?>">
                                <label class="wordwrap_label" for="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID"><?= $rowColumn->name." - $DateFormat"?></label><br/>
                            <?php endif; ?>
                            <?php $i++;?>
                        <?php endforeach; ?>
                    <?php elseif ($rowColumn->type == "timestamp"||$rowColumn->type == "datetime"): ?>
                        <?php $i=0;?>
                        <?php foreach ($DateFormatFrListTimeTamps as $DateFormat): ?>
                            <?php if (in_array($rowColumn->name."::".$DateFormatList[$i], $config['champs'])): ?>
                                <input type="checkbox" id="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID" name="champs[]" value="<?=$rowColumn->name."::".$DateFormatList[$i]?>" checked>
                                <label class="wordwrap_label" for="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID"><?= $rowColumn->name." - $DateFormat"?></label><br/>
                            <?php else: ?>
                                <input type="checkbox" id="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID" name="champs[]" value="<?=$rowColumn->name."::".$DateFormatList[$i]?>">
                                <label class="wordwrap_label" for="<?=$rowColumn->name."::".$DateFormatList[$i]?>ID"><?= $rowColumn->name." - $DateFormat"?></label><br/>
                            <?php endif; ?>
                            <?php $i++;?>
                        <?php endforeach; ?>
                    <?php elseif (in_array($rowColumn->name, $config['champs'])): ?>
                        <input type="checkbox" id="<?=$rowColumn->name?>ID" name="champs[]" value="<?=$rowColumn->name?>" checked>
                        <label class="wordwrap_label" for="<?=$rowColumn->name?>ID"><?=$rowColumn->name?></label><br/>
                    <?php else: ?>
                        <input type="checkbox" id="<?=$rowColumn->name?>ID" name="champs[]" value="<?=$rowColumn->name?>">
                        <label class="wordwrap_label" for="<?=$rowColumn->name?>ID"><?=$rowColumn->name?></label><br/>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?=generate_form_calcul($config, $resultColumn)?>
                <div id="messageErreur"></div>
                <h4>Filtrage du diagramme</h4>
                <?= generate_form_min_max($config["minimum"], $config["maximum"], $config["engine"]) ?><br/>
                <?=generate_form_filters2($resultColumn)?>
                <h4 title="Ajoute la possibilité d'avoir des choix plus fin">Filtres rapides dynamiques</h4>
                <?=generate_form_add_speed_filters($resultColumn,$DateFormatFrList,$DateFormatFrListTimeTamps,$config,$DateFormatList)?>
            <?php endif; ?>
            <button id="submit" type="submit" class="btn btn-primary form-control">Actualiser</button>
        </form>
            <?=generate_save_graph_form($name,$description, $listGroup, $group,$config,$image_name,$public,$live,"table1D",$id)?>
    </div>
        <div id="resizableRight" class="col-md-10">
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
                    <?php if($config['champs']!=[] || $config['request']!=""):?>
                        <!--<h4><a href="<?=site_url() . "/graph/export/".$id."?" . http_build_query($config, '', '&')?>">Export CSV</a></h4>-->
                        <div id="downloadurl" hidden></div>
                    <?php endif;?>
                </div>
            </div>
            <div id="chargement" class="alert alert-info">
                <?php if ($config['champs'] != [] || ($config['engine'] == "mysql" && $config['expertmode'] && $config['request'])): ?>
                    <img id="loader-img" src="<?= base_url() ?>assets/img/ajax-loader.gif"/>
                    <div id="chargement-message" ><?=SEARCH_RESULT?></div>
                <?php else: ?>
                    <div id="chargement-message">Pour générer un tableau, choisissez les champs et les filtres puis faites Actualiser.</div>
                <?php endif;?>
            </div>
            <div id="table-div" class="table-overflow">
                <table class="table table-striped table-bordered" id="table"></table>
            </div>
            <nav aria-label="..." class="center">
            <ul id="pagination" class="pagination">
            </ul>
            </nav>
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
                <div class="col-md-4">
                    <h4>Information tableau</h4>
                    <p>Nombre de lignes total (affiché) : <span id="nb_line">?</span> (<span id="nb_line_show">?</span>)
                    </p>
                    <p>Nombre de colonnes total (affiché) : <span id="nb_column">?</span></p>
                </div>
            </div>



        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
</body>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery-ui.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/Chart.bundle.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/graph.js"></script>
<script type = 'text/javascript' src = "<?=base_url();?>assets/js/table.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/formcheck.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/html2canvas.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>

<script>
    resizableOn();
    var jq=jQuery.noConflict();
    jq("#speed-filters-form").chosen({max_selected_options: 10, no_results_text: "Aucun résultat pour", width: "100%"});
    <?php $config["request"]=str_replace("\"","\\\"",$config["request"]);?>
    <?="var configJSON ='".json_encode($config)."';"?>
    var configGraph = JSON.parse(configJSON);
    <?php if($config['engine'] == "mysql" && $config['expertmode']):?>
    var jq=jQuery.noConflict();
    var urlDatabase='<?=site_url()."/api/getBDDInfo"?>';
    jq.post(urlDatabase, {'id': '<?=$config["source"]?>', 'autorized': true}, function (data) {
        showDatabase(JSON.parse(data), '<?=$config["source"]?>');
    });
    <?php endif;?>
    <?php if($config['champs']!=[] && !$config['expertmode']):?>
    addFilterAuto(configJSON, configGraph['engine']);

         var url='<?=site_url()."/api/getDataRAW?".http_build_query($config, '', '&')?>';

        jq.get(url, function(data) {
            if(data!="[]"){
                dataVar = data;
                datalist = JSON.parse(data);
                createSpeedFilters(datalist,configGraph);
                tableCreate1D(datalist,"","",0);
                if(datalist.length<5000){
                    getTableImg();
                }
                createSpeedFilterRangeTable1D(datalist);
                document.getElementById('save_graph').disabled = false;
                createDownloadCSVButton(datalist, "", "<?=$config['table'] . " " . $update_time_table?>");

            }else{
                document.getElementById('chargement').className = "alert alert-warning";
                document.getElementById('chargement').innerHTML = 'Aucune donnée disponible pour votre recherche';
            }

         }).fail(function(data) {
             show_error_ajax(data);
             document.getElementById('chargement').className = "alert alert-danger";
             document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
         });
    <?php elseif($config['engine'] == "mysql" && $config['expertmode'] && $config['request']):?>
    <?php $request_array = array('expertmode' => true, 'request' => $config['request'], 'idfilesave' => $config['idfilesave'], 'source' => $source->id);?>
        var url='<?=site_url()."/api/getData?expertmode=true&request=".http_build_query($request_array,'','&')?>';

    jq.get(url, function(data) {
            if(data=="[]"){
                document.getElementById('chargement').className = "alert alert-warning";
                document.getElementById('chargement').innerHTML = 'Aucune donnée disponible pour votre recherche';
                return;
            }else if(data=="query prohibited"){
                document.getElementById('chargement').className = "alert alert-warning";
                document.getElementById('chargement').innerHTML = 'Votre requete est erronée ou interdite (drop, delete, insert, update, create, alter, truncate, merge).';
                return;
            }
            datalist = JSON.parse(data);
            document.getElementById('chargement-message').innerHTML = "Génération du tableau";
            tableCreate1D(datalist,"","",0);
        createSpeedFiltersFormAdd(datalist);

        if (datalist.length < 5000) {
                getTableImg();
            }
        createDownloadCSVButton(datalist, "", "<?=$config['table'] . " " . $update_time_table?>");
            document.getElementById('save_graph').disabled = false;
        }).fail(function(data) {
            show_error_ajax(data);
            document.getElementById('chargement').className = "alert alert-danger";
            document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
        });
    <?php endif;?>
    var champs = <?=arrayPHPToJs($resultColumn);?>;
</script>