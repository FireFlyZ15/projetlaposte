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
            <a class="btn btn-primary btn-info"
               onclick="showSample('<?= $config['source'] ?>','<?= $config['database'] ?>','<?= $config['table'] ?>');">Voir
                les données</a><br>
            <form action="">
                <div class="form-inline form-group">
                    <label for="wording"><?=FIELD_LABEL?></label><br/>
                    <?php foreach ($resultColumn as $rowColumn): ?>
                        <?php if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"): ?>
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
                </div>
                <?=generate_form_calcul($config, $resultColumn)?>
                <div id="messageErreur"></div>
                <h4>Filtrage du diagramme</h4>
                <?= generate_form_min_max($config["minimum"], $config["maximum"], $config["engine"]) ?><br/>
                <?=generate_form_filters($resultColumn)?>
                <button id="submit" type="submit" class="btn btn-primary form-control">Actualiser</button>
            </form>
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
                </div>
            </div>
            <div id="chargement" class="alert alert-info">
                <?php if($config['champs']!=[]):?>
                    <img src="<?= base_url() ?>assets/img/ajax-loader.gif"/>
                    <div id="chargement-message" ><?=SEARCH_RESULT?></div>
                <?php else: ?>
                    <div id="chargement-message">Pour générer un CSV, choisissez les champs et les filtres puis faites Actualiser.</div>
                <?php endif;?>
            </div>
            <?php var_dump($test); ?>
            <div id="result">
                <div id="downloadurl" hidden>
                    <button class="btn btn-primary copycsv" onclick="copycsv()">Copy CSV</button>
                </div>
                <div id="showcsv" class="showcsv"></div>
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
            <h4>Filtres utilisés</h4>
            <?=generate_array_list_html($config)?>
        </div>
    </div>
</body>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery-ui.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/Chart.bundle.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/graph.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/html2canvas.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
<script type='text/javascript' src="<?= base_url(); ?>assets/js/table.js"></script>
<script>
    resizableOn();
    <?="var configJSON ='".json_encode($config)."';"?>
    var configGraph = JSON.parse(configJSON);
    <?php if($config['champs']!=[]):?>

    addFilterAuto(configJSON, configGraph['engine']);

        var url='<?=site_url()."/api/getDataRAW?".http_build_query($config, '', '&')?>';
        var jq=jQuery.noConflict();
        jq.get(url, function(data) {
            document.getElementById('chargement-message').innerHTML = "Génération du CSV";
            //tableCreate(data,configJSON);
            //getTableImg();
            console.log(configGraph);
            var test = JSON.parse(data);
            console.log(data);
            test.forEach(element => console.log(element.codeActif));
            var csv = convertJSONtoCSV(data);
            document.getElementById('showcsv').innerHTML = csv;
            document.getElementById('chargement').innerHTML = "";
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv));
            element.setAttribute('download', "data.csv");
            element.innerHTML="Téléchargement du CSV";
            element.className="btn btn-primary";
            var element2 = document.createElement('a');
            element2.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv));
            element2.setAttribute('download', "data.xls");
            element2.innerHTML="Téléchargement de l'EXCEL";
            element2.className="btn btn-primary";
            //element.click();
            document.getElementById('downloadurl').appendChild(element);
            document.getElementById('downloadurl').hidden = false;
            document.getElementById('downloadurl').appendChild(element2);
            document.getElementById('downloadurl').hidden = false;
            document.getElementById('chargement').className = "";
            document.getElementById('chargement').innerHTML = '';
        }).fail(function(data) {
            show_error_ajax(data);
            document.getElementById('chargement').className = "alert alert-danger";
            document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
        });
    <?php endif;?>
    var champs = <?=arrayPHPToJs($resultColumn);?>;
</script>