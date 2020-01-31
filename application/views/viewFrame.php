<body>
<div class="row">
    <div class="col-md-8">
    </div>
    <div class="col-md-4 text-right">
        <p> Date de création : <?= $graph->date_creation ?><br/>
            Derniere mise à jour des données : <?= ($graph->live) ? $update_time_table : $graph->date_update ?></p>
        <div id="downloadurl" hidden></div>
    </div>
</div>
<h1><?php echo $titre; ?></h1>

<div class="mybox" class="contenuG">
    <div id="canvas-div" class="canvas-overflow">
        <iframe id="iframe" src="" height="600" width="100%"></iframe>
    </div>

</div>
<?= generate_modal("error_modal", "Erreur") ?>
<div class="row">
    <div class="col-md-4">
        <h4>Filtres utilisés</h4>
        <?= generate_array_list_html($config) ?>
    </div>
    <div class="col-md-4">
        <h4>Description</h4>
        <?= $graph->description ?>
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
</body>
<script type='text/javascript' src="<?php echo base_url();
?>assets/js/jquery.min.js"></script>
<script type='text/javascript' src="<?php echo base_url();
?>assets/js/Chart.bundle.min.js"></script>
<script type='text/javascript' src="<?php echo base_url();
?>assets/js/graph.js"></script>
<script type='text/javascript' src="<?= base_url(); ?>assets/js/table.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/squarify.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/chosen.jquery.min.js"></script>
<script>
    <?="var configJSON ='" . json_encode($config) . "';"?>
    var configGraph = JSON.parse(configJSON);
    document.getElementById('iframe').src = configGraph.url;

</script>
</html>
