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
            <h4><?=EXPORT_CONSTRUCTION_TITLE?>
                <a type="button" class="btn btn-link" target="_blank" href="<?php echo base_url('assets/pdf/Hadoop definition des données.pdf');?>" title="Définition des données">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                </a>
            </h4>
        </div>
        <div class="col-md-10" id="resizableRight">
                <div id="datacreate">
                    <div id="title">
                        <h2>Créer une nouvelle données</h2>
                    </div>
                    <div>
                        <?php 
                        $keys = array_keys(get_object_vars($test[0]));
                        foreach($keys as $key){
                            if($key == "id") continue;
                            if($key == "idModele"){
                                echo "idModele :";
                                echo "<select id='Modele' class='ajoutData' data-field='".$key."'>";
                                foreach($idModele as $id){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$id->libelle."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($key == "idEntite"){
                                echo "idEntite :";
                                echo "<select id='Entite' class='ajoutData' data-field='".$key."'>";
                                foreach($idEntite as $id){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$id->codeRegate."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($key == "tranche"){
                                echo "tranche :";
                                echo "<select id='tranche' class='ajoutData' data-field='".$key."'>";
                                foreach($tranches as $tranche){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$tranche->tranche."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($key == "utilisation"){
                                echo "utilisation :";
                                echo "<select id='utilisation' class='ajoutData' data-field='".$key."'>";
                                foreach($utilisations as $utilisation){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$utilisation->utilisation."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($key == "statut"){
                                echo "statut :";
                                echo "<select id='statut' class='ajoutData' data-field='".$key."'>";
                                foreach($statuts as $statut){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$statut->statut."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($key == "type"){
                                echo "Type :";
                                echo "<select id='type' class='ajoutData' data-field='".$key."'>";
                                foreach($typeEntite as $type){
                                    echo "<option data-id'".$key."' data-field='".$key."' id='".$key."'>".$type->type."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            echo $key.": <input type='text' class='ajoutData' data-id='".$key."' data-field='".$key."' id='".$key."'>";
                            echo "</br>";
                        }
                        ?>
                        <button type="submit" id="ajout">Ajouter le nouvelle enregistrement</button>
                    </div>
                    <div id="ajoutExcel">
                        <h2>Ajout de nouvelle données avec fichier excel</h2>
                        <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
                        Transfère le fichier <input type="file" id="monfichier" name="monfichier"/>
                        <select id="table">
                        <?php 
                            foreach($table as $tab){
                                ?>
                                 <option><?php echo $tab ?></option>   
                                <?php
                            }
                        ?>
                        </select>
                        <button type="submit" id="excel">Envoyer fichier Excel</button>
                    </div>
            </div>
        </div>
    </div>
</body>
<script>
    var base_url = "<?php echo base_url();?>";
    $(document).ready(function(e) {
        //Add data
        var create = document.getElementById('ajout');
        create.onclick = function(){
            var data = document.getElementsByClassName('ajoutData');
            var idModele = document.getElementById('Modele');
            idModele = idModele.options[idModele.selectedIndex].text;
            var idEntite = document.getElementById('Entite');
            idEntite = idEntite.options[idEntite.selectedIndex].text;
            var i;
            var fieldname = "";
            var fielddata = "";
            for(i = 0; i < data.length; i++){
                fielddata = fielddata + data[i].value + ",";
                fieldname = fieldname + $(data[i]).data('field') + ",";
            }
            if(confirm("Etes-vous sur de vouloir ajouter cette données :"+fieldname+", "+fielddata+" ?")){
                $.ajax({
                    url: '<?= base_url() ?>index.php/crud/create',
                    dataSrc: "data",
                    type: 'post',
                    data: {field: fieldname, value: fielddata},
                    success: function(response){
                        console.log(response);
                    },
                    fail: function(response){
                        console.log(response);
                    }
                });
            }
        }
        var createExcel = document.getElementById('excel');
        createExcel.onclick = function(){
            var excel = document.getElementById('monfichier');
            var table = document.getElementById('table');
            table = table.options[table.selectedIndex].text;
            if(confirm("Etes-vous sur de vouloir ajouter ce fichier excel " +excel+ " ?")){
                $.ajax({
                   url: '<?= base_url() ?>index.php/crud/ajoutExcel',
                    dataSrc: "data",
                    type: "post",
                    data: {excel: excel, table: table},
                    success: function(response){
                        console.log(response);
                    },
                    fail: function(response){
                        console.log(response);
                    }
                });
            }
        }
    }
</script>