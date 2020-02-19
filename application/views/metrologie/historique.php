<?php
$this->load->helper('graph');
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<head>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf-8" src="<?=base_url()?>assets/js/jquery-3.4.1.min.js"></script>
</head>
<body>
    <div class="row" id="resizableParent">
        <div class="col-md-2 bg" id="resizableLeft">
        </div>
        <div class="col-md-10" id="resizableRight">
                <div id="datacreate">
                    <div id="title">
                        <h2>Historique d'ajout de données avec fichier excel</h2>
                    </div>
                    <div>
                        <?php //var_dump($historique); ?>
                        <?php 
                            foreach($historique as $hist)
                            {
                                echo "Fichier ajouter : ".$hist->fichier;
                                echo ", Le : ".$hist->dateAjout;
                                echo "<br/>";
                            }
                        ?>
                    </div>
            </div>
        </div>
    </div>
</body>
<script>
</script>