    <body>
    <?php if ($ux != "mini"): ?>
        <div class="row">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <p> Date de création : <?= $graph->date_creation ?><br/>
                    Derniere mise à jour des données : <?= ($graph->live) ? $update_time_table : $graph->date_update ?>
                </p>
                <div id="downloadurl" hidden></div>
            </div>
        </div>
        <h1>
            <?php echo $titre; ?>
            <span id="icon_mode" aria-hidden="true"></span>
        </h1>
    <?php endif; ?>
    <?php if (isset($graph->error_msg)): ?>
        <div id="error_msg" class="alert alert-danger">
            <?= $graph->error_msg ?>
        </div>
    <?php endif; ?>
      <div id="chargement" class="alert alert-info">
          <img id="loader-img" src="<?= base_url() ?>assets/img/ajax-loader.gif"/>
          <div id="chargement-message" > Recherche des résultats</div>
      </div>
      <div class="mybox" class="contenuG">
          <div id="canvas-div" class="canvas-overflow">
              <canvas id="canvas" width="1400px" height="800px" onmousemove="canvasMouseEvent(event)" onmouseout="endCanvasMouseEvent(this)" onclick="canvasClickEvent(event)"></canvas>
          </div>

      </div>
    <?php if ($ux != "mini"): ?>
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
        <div class="col-md-4">
            <h4>Description</h4>
            <?=$graph->description?>
        </div>
        <?php endif; ?>
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

    </body>
     <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/jquery.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/Chart.bundle.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/graph.js"></script>
    <script type = 'text/javascript' src = "<?=base_url();?>assets/js/table.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/squarify.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
    <script type='text/javascript' src="<?= base_url() ?>assets/js/jquery-ui.min.js"></script>
<script>
    <?php if($graph->live):?>
    if (document.getElementById("icon_mode")) {
        document.getElementById("icon_mode").className = "glyphicon glyphicon-refresh";
        document.getElementById("icon_mode").title = "Mode live";
    }
    <?="var configJSON ='".json_encode($config)."';"?>
    var configGraph = JSON.parse(configJSON);
        var url='<?=site_url()."/api/getData?".http_build_query($config, '', '&')?>';
        $.get(url, function(data) {

            var json = JSON.parse(data);
            createDownloadCSVButton(json, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $update_time_table?>");
            nbOcc = json.length;
                appliColor(json,configJSON,true);
                if(configGraph.speedfilters !== undefined && configGraph.speedfilters != []){
                    createSpeedFiltersTreemap(json,configGraph);
                    createSpeedFilterRangeTreemap(json);
                }
        }).fail(function(data) {
            show_error_ajax(data);
            document.getElementById('chargement').className = "alert alert-danger";
            document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
        });
    <?php else:?>
    if (document.getElementById("icon_mode")) {
        document.getElementById("icon_mode").className = "glyphicon glyphicon-floppy-disk";
        document.getElementById("icon_mode").title = "Mode cache";
    }
        document.getElementById('chargement-message').innerHTML = "Génération du graphique.";
        <?="var configJSON ='".json_encode($config)."';"?>
        var configGraph = JSON.parse(configJSON);
    <?php if(isset($config->idfilesave)): ?>
    url_script = '<?=base_url() . GRAPH_FOLDER_URL . $config->idfilesave . ".json"?>';
    $.get(url_script, function (dataAjax) {
        json = dataAjax;
        appliColor(json, configJSON, true);
        if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
            createSpeedFiltersTreemap(json, configGraph);
            createSpeedFilterRangeTreemap(json);
        }
        createDownloadCSVButton(json, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
    });

    <?php else :?>
    var json = JSON.parse('<?=$graph->script?>');
    appliColor(json, configJSON, true);
    if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
        createSpeedFiltersTreemap(json, configGraph);
        createSpeedFilterRangeTreemap(json);
    }
    createDownloadCSVButton(json, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
    <?php endif;?>



    <?php endif;?>


</script>
</html>
