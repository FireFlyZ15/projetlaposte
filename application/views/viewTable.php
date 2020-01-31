    <body>
    <?php if ($ux != "mini"): ?>
      <div class="row">
          <div class="col-md-8">
              <h1>
                  <?php echo $titre; ?>
                  <span id="icon_mode" aria-hidden="true"></span>
              </h1>
          </div>
          <div class="col-md-4 text-right">
              <p> Date de création : <?= $graph->date_creation ?><br/>
                  Derniere mise à jour des données : <?= ($graph->live) ? $update_time_table : $graph->date_update ?>
              </p>
              <div id="downloadurl" hidden></div>
          </div>
      </div>

      <div>
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
          <div id="table-div" class="table-overflow">
              <table class="table table-striped table-bordered" id="table"></table>
          </div>
          <nav aria-label="..." class="center">
              <ul id="pagination" class="pagination">
              </ul>
          </nav>
          <?php if ($ux != "mini"): ?>
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
          <div class="col-md-4">
            <h4>Description</h4>
            <?=$graph->description?>
              <h4>Information tableau</h4>
              <p>Nombre de lignes total (affiché) : <span id="nb_line">?</span> (<span id="nb_line_show">?</span>)</p>
              <p>Nombre de colonnes total (affiché) : <span
                          id="nb_column">?</span> <?php if ($graph->type == "table2D"): ?>(<span
                          id="nb_column_show">?</span>)<?php endif; ?></p>
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
    <?php endif; ?>
    </body>
     <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/jquery.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/Chart.bundle.min.js"></script>
      <script type = 'text/javascript' src = "<?php echo base_url(); 
         ?>assets/js/graph.js"></script>
        <script type = 'text/javascript' src = "<?php echo base_url();
    ?>assets/js/table.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
    <script type = 'text/javascript' src = "<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
    <script type='text/javascript' src="<?= base_url() ?>assets/js/jquery-ui.min.js"></script>
    <script>
        <?php if($graph->live):?>
        if (document.getElementById("icon_mode")) {
            document.getElementById("icon_mode").className = "glyphicon glyphicon-refresh";
            document.getElementById("icon_mode").title = "Mode live";
        }
        var configJSON = String.raw`<?=json_encode($config, JSON_HEX_QUOT)?>`.replace(/\\u0022/g, '\\"');
            var configGraph = JSON.parse(configJSON);
            <?php if($graph->type=="table1D"):?>
                <?php if($config->expertmode && $config->request):?>
        <?php $request_array = array('expertmode' => true, 'request' => $config->request, 'source' => $config->source);?>
                    var url='<?=site_url()."/api/getData?".http_build_query($request_array,'','&')?>';
                <?php else:?>
                    var url='<?=site_url()."/api/getDataRaw?".http_build_query($config, '', '&')?>';
                <?php endif;?>

            <?php else:?>
                var url='<?=site_url()."/api/getData?".http_build_query($config, '', '&')?>';
            <?php endif;?>
            var jq=jQuery.noConflict();
            jq.get(url, function(data) {
                datalist = JSON.parse(data);
                document.getElementById('chargement-message').innerHTML = "Génération du tableau";
                <?php if($graph->type=="table1D"):?>
                    tableCreate1D(datalist,"","",0);
                if (configGraph.table == "expertmode") {
                    createSpeedFiltersFormAdd(datalist);
                } else if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
                        createSpeedFilters(datalist,configGraph);
                }
                if (configGraph.table != "expertmode") {
                    createSpeedFilterRangeTable1D(datalist);
                }

                createDownloadCSVButton(datalist, "", "<?=$graph->name . " " . $update_time_table?>");
                <?php else:?>
                    tableCreate(datalist,configJSON);
                    if(configGraph.speedfilters !== undefined && configGraph.speedfilters != []){
                        createSpeedFiltersTable2D(datalist,configGraph);
                    }
                createSpeedFilterRangeTable2D(datalist, configGraph);
                createDownloadCSVButton(datalist, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $update_time_table?>");
                <?php endif;?>


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
        //Bidoulle pour que les requetes sql mis dans le json ne corrompe pas le json.
        // String.raw permet de lire une chaine de caractère en caractère brut
        //JSON_HEX_QUOT transforme un " en \u0022
        var configJSON = String.raw`<?=json_encode($config, JSON_HEX_QUOT)?>`.replace(/\\u0022/g, '\\"');
        var configGraph = JSON.parse(configJSON);
            document.getElementById('chargement-message').innerHTML = "Génération du graphique.";
            <?php if($graph->type=="table1D"):?>
        <?php if(isset($config->idfilesave)): ?>
        url_script = '<?=base_url() . GRAPH_FOLDER_URL . $config->idfilesave . ".json"?>';
        $.get(url_script, function (dataAjax) {
            datalist = dataAjax;
            tableCreate1D(datalist, "", "", 0);
            if (configGraph.table == "expertmode") {
                createSpeedFiltersFormAdd(datalist);
            } else if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
                createSpeedFilters(datalist, configGraph);
            }
            if (configGraph.table != "expertmode") {
                createSpeedFilterRangeTable1D(datalist);
            }
            createDownloadCSVButton(datalist, "", "<?=$graph->name . " " . $graph->date_creation?>");
        });
        <?php else :?>
        <?="var dataRAW='" . $graph->script . "';";?>
        datalist = JSON.parse(dataRAW);
        tableCreate1D(datalist, "", "", 0);
        if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
            createSpeedFilters(datalist, configGraph);

        }

        createDownloadCSVButton(datalist, "", "<?=$graph->name . " " . $graph->date_creation?>");

        <?php endif;?>

            <?php else:?>
        <?php if(isset($config->idfilesave)): ?>
        url_script = '<?=base_url() . GRAPH_FOLDER_URL . $config->idfilesave . ".json"?>';

        $.get(url_script, function (dataAjax) {
            datalist = dataAjax;
            tableCreate(datalist, configGraph);
            if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
                createSpeedFiltersTable2D(datalist, configGraph);
            }
            createSpeedFilterRangeTable2D(datalist, configGraph);
            createDownloadCSVButton(datalist, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
        });
        <?php else :?>
        <?="var dataRAW='" . $graph->script . "';";?>
        datalist = JSON.parse(dataRAW);
        tableCreate(datalist, configGraph);
        if (configGraph.speedfilters !== undefined && configGraph.speedfilters != []) {
            createSpeedFiltersTable2D(datalist, configGraph);
        }
        createSpeedFilterRangeTable2D(datalist, configGraph);
        createDownloadCSVButton(datalist, "<?=$config->wording . ";" . $config->group . ";" . $config->typecalcul . "(" . $config->typecalculchamp . ")"?>", "<?=$graph->name . " " . $graph->date_creation?>");
        <?php endif;?>
            <?php endif;?>
        <?php endif;?>

   </script>

</html>