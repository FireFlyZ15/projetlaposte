<?php
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<body>
<div class="row" id="resizableParent">
    <div class="col-md-2 bg" id="resizableLeft">
        <h4><?= GRAPH_CONSTRUCTION_TITLE ?>
            <a type="button" class="btn btn-link" target="_blank"
               href="<?php echo base_url('assets/pdf/Dictionnaire de données Vidéocodage 360.pdf'); ?>"
               title="Définition des données">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </a>
        </h4>
        <input type="text" id="url" class="form-control" value="<?php if ($url != ""): echo $url; endif; ?>"/>
        <button id="submit" type="submit" class="btn btn-primary form-control" onclick="setUrlFrame();return false;">
            Actualiser
        </button>
        </form>
        <?= generate_save_graph_form($name, $description, $listGroup, $group, $config, $image_name, $public, $live, "frame", $id) ?>
    </div>
    <div class="col-md-10" id="resizableRight">
        <div class="row">
            <div class="col-md-8">
                <h3><?php echo $titre; ?></h3>
            </div>
            <div class="col-md-4 text-right">
                <p> Dernière mise à jour des données : <?= $update_time_table ?></p>
            </div>
        </div>
        <div id="chargement" class="alert alert-info">
            <div id="chargement-message">Les frames ne marchent pas avec tous les sites (exemple google.fr).</div>
        </div>
        <iframe id="iframe" src="" height="750" width="100%" onerror="alert('error');"></iframe>


        <?= generate_modal("error_modal", "Erreur") ?>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
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
<script type='text/javascript' src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/jquery-ui.min.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/Chart.bundle.min.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/graph.js"></script>
<script>
    resizableOn();
    if (document.getElementById('url').value != "") {
        setUrlFrame();
    }

    function setUrlFrame() {
        var url = document.getElementById('url').value;
        var regex = "^(([a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*)|\\.[a-z]{2,5}((([0-9]{1,3})\\.){3}([0-9]{1,3})))(:[0-9]{1,5})?(\\/.*)?$";
        var regex_with_http = "^(http:\\/\\/www\\.|https:\\/\\/www\\.|http:\\/\\/|https:\\/\\/)(([a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*)|\\.[a-z]{2,5}((([0-9]{1,3})\\.){3}([0-9]{1,3})))(:[0-9]{1,5})?(\\/.*)?$";
        if (url.match(regex)) {
            url = "http://" + url;
            document.getElementById('url').value = "http://" + document.getElementById('url').value;
        }
        var url_element = document.getElementById('url');
        if (url.match(regex_with_http)) {
            document.getElementById('iframe').src = url;
            configGraph = {
                url: url
            };
            url_element.style.borderColor = "";
            document.getElementById('save_graph').disabled = false;
        } else {
            url_element.style.borderColor = "red";
            url_element.onchange = function () {
                url_element.style.borderColor = "";
            };
            alert("L'url n'est pas valide !");
        }

    }
</script>