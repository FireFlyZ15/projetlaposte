    <body>
    <?php if ($ux != "mini"): ?>
    <div class="row">
        <div class="col-md-8">
            <h1><?php echo $titre; ?>
                <span id="icon_mode" aria-hidden="true"></span>
            </h1>
        </div>
        <div class="col-md-4 text-right">
            <p> Date de création : <?= $graph->date_creation ?><br/>
                Derniere mise à jour des données : <?= ($graph->live) ? $update_time_table : $graph->date_update ?></p>
            <p></p>
            <div id="downloadurl" hidden></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <?php if($graph->type=="pie"): ?>
            <div style="width: 1000px;height:600px; text-align: center;margin: auto;">
        <?php else: ?>
                <div style="width: 100%;height:900px;">
        <?php endif; ?>

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
                    <div id="canvasDiv" <?= ($graph->type == "histogram") ? 'style="overflow-y:scroll;"' : "" ?>>
                </div>


                    <?php if ($ux != "mini"): ?>
        </div>
            </div>
        </div>
        <?=generate_modal("error_modal","Erreur")?>
        <div class="row">
            <div class="col-md-4">
                <h4>Filtres rapide</h4>
                <div id="speed-filters"></div>
            </div>
            <div class="col-md-4">
                <h4>Filtres utilisés</h4>
                <?=generate_array_list_html($config)?>
            </div>
            <div class="col-md-4">
                <h4>Description</h4>
                <?=$graph->description?>
            </div>
        </div>
        <?php endif; ?>
    </body>
     <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/jquery.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/Chart.bundle.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/graph.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/color.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
    <script type='text/javascript' src="<?= base_url() ?>assets/js/jquery-ui.min.js"></script>
    <script>
        <?="var configJSON ='".json_encode($config)."';\n"?>
        configGraph = JSON.parse(configJSON);
      <?php if($graph->live):?>
        if (document.getElementById("icon_mode")) {
            document.getElementById("icon_mode").className = "glyphicon glyphicon-refresh";
            document.getElementById("icon_mode").title = "Mode live";
        }

        <?php if($graph->type=="histogram"):?>
        var url = '<?=site_url() . "/api/getData/".$id."?" . http_build_query($config, '', '&')?>';
        <?php elseif($graph->type=="pie"):?>
        <?="var url='" . site_url() . "/api/getData/" . $id . "?" . http_build_query($config, '', '&') . "';"?>
        <?php endif;?>
          $.get(url, function (dataRAW) {
              data_ajax = dataRAW;
              if(dataRAW != "" && dataRAW.indexOf("ERROR 300")!=-1){
                  var nbOcc = dataRAW.split(':')[1].replace(/\s/g, '');
                  document.getElementById('chargement').className = "alert alert-warning";
                  document.getElementById('chargement').innerHTML = 'Il y a trop d’occurrences pour afficher le résultat ('+nbOcc+') ! Le diagramme est capable d’afficher que 300 résultats.';
              }else if(dataRAW!=""){
                  data = JSON.parse(dataRAW);
                  <?php if($graph->type=="histogram"):?>
                  <?php
                  $wording = (is_array($config->wording)) ? implode(";",$config->wording) : $config->wording;
                  ?>
                  generateHistoGraph(data,configGraph);
                  createSpeedFiltersHisto(data,configGraph);
                  createSpeedFilterRangeHisto(data, configGraph);
                  createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$wording.";".$config->group.";".$config->typecalcul."(".$config->typecalculchamp.")"?>","<?=$graph->name." ". $update_time_table?>");
                  <?php elseif($graph->type=="pie"):?>
                    generatePieGraph(data,configGraph);
                    getPieColorGraph();
                    createSpeedFiltersPie(data,configGraph);
                  createSpeedFilterRangePie(data, configGraph);
                    createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$config->wording.";".$config->typecalcul."(".$config->typecalculchamp.")"?>","<?=$graph->name." ". $update_time_table?>");
                  <?php endif;?>
              }else{
                  document.getElementById('chargement').className = "alert alert-warning";
                  document.getElementById('chargement').innerHTML = 'Aucune donnée disponible pour votre recherche';
              }
          }).fail(function(data) {
              console.log(data);
              if (data.responseText.includes("doesn't exist")) {
                  cacheMode('La table ' + configGraph.source_name + ":" + configGraph.database + "." + configGraph.table + ' n\'existe plus dans la base de donnée. Un cache du graphique va être affiché');

              } else {
                  show_error_ajax(data);
                  document.getElementById('chargement').className = "alert alert-danger";
                  document.getElementById('chargement').innerHTML = 'Il y a eu une erreur dans le calcul des données, faites une autre requete.';
              }

          });
      <?php else:?>
        cacheMode("");
        <?php endif;?>
        /**
         * Affiche le cache du graphique
         * @param error_msg
         */
        function cacheMode(error_msg) {
            if (document.getElementById("icon_mode")) {
                document.getElementById("icon_mode").className = "glyphicon glyphicon-floppy-disk";
                document.getElementById("icon_mode").title = "Mode cache";
            }

            var data = "";
            <?php if($graph->type == "histogram"):?>
            <?php
            $wording = (is_array($config->wording)) ? implode(";", $config->wording) : $config->wording;
            ?>

            <?php if(isset($config->idfilesave)): ?>
            url_script = '<?=base_url() . GRAPH_FOLDER_URL . $config->idfilesave . ".json"?>';

            $.get(url_script, function (dataAjax) {
                data = dataAjax;
                generateHistoGraph(data, configGraph);
                createSpeedFiltersHisto(data, configGraph);
                createSpeedFilterRangeHisto(data, configGraph);
                createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
                document.getElementById('loader-img').style.display = 'none';
                if (error_msg == "") {
                    document.getElementById('chargement-message').innerHTML = "";
                    document.getElementById('chargement').className = "";
                    document.getElementById('chargement').style.display = 'none';
                } else {
                    document.getElementById('chargement').className = "alert alert-danger";
                    document.getElementById('chargement-message').innerHTML = error_msg;
                    document.getElementById('chargement').style.display = '';
                }
            });
            <?php else :?>
            <?="var dataRAW='" . $graph->script . "';";?>
            data = JSON.parse(dataRAW);
            generateHistoGraph(data, configGraph);
            createSpeedFiltersHisto(data, configGraph);
            createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
            <?php endif;?>

            <?php elseif($graph->type == "pie"):?>
            <?php if(isset($config->idfilesave)): ?>
            url_script = '<?=base_url() . GRAPH_FOLDER_URL . $config->idfilesave . ".json"?>';

            $.get(url_script, function (dataAjax) {
                data = dataAjax;
                generatePieGraph(data, configGraph);
                if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
                    createSpeedFiltersPie(data, configGraph);
                }
                console.log(data);
                createSpeedFilterRangePie(data, configGraph);
                createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$config->wording . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
                document.getElementById('loader-img').style.display = 'none';
                if (error_msg == "") {
                    document.getElementById('chargement-message').innerHTML = "";
                    document.getElementById('chargement').className = "";
                    document.getElementById('chargement').style.display = 'none';

                } else {
                    document.getElementById('chargement').className = "alert alert-warning";
                    document.getElementById('chargement-message').innerHTML = error_msg;
                    document.getElementById('chargement').style.display = '';
                }
            });
            <?php else :?>
            //Ratrapage
            <?="var dataRAW='" . $graph->script . "';";?>
            data = JSON.parse(dataRAW);
            generatePieGraph(data, configGraph);
            if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
                createSpeedFiltersPie(data, configGraph);
            }
            createDownloadCSVButtonForChartJs("<?=$graph->type?>", "<?=$config->wording . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
            <?php endif;?>

            <?php endif;?>
        }
   </script>

</html>