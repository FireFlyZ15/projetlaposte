<?php
$this->load->helper('graph');
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf-8" src="<?=base_url()?>assets/js/jquery-3.4.1.min.js"></script>
</head>
<body>
    <div class="row" id="resizableParent">
        <div class="progress"></div>
        <div class="col-md-2 bg" id="resizableLeft">
        </div>
        <div class="col-md-10" id="resizableRight">
                <div id="datacreate">
                    <div id="title">
                        <?php if($user->actual_table == "prixdeplacement"): ?>
                            <h2>Créer une nouvelle donnée pour la table prix déplacement</h2>
                        <?php elseif($user->actual_table == "prixverification"): ?>
                            <h2>Créer une nouvelle donnée pour la table prix vérification</h2>
                        <?php else: ?>
                            <h2>Créer une nouvelle donnée pour la table <?php echo $user->actual_table ?></h2>
                        <?php endif ?>
                    </div>
                    <div class="formulaire">
                        <?php 
                        foreach($columns as $column){
                            if($column->name == "id") continue;
                            if($column->name == "idModele"){
                                echo "<label>Id modele </label>";
                                echo ":<select id='Modele' class='ajoutData' data-field='".$column->name."'>";
                                foreach($idModele as $id){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$id->libelle."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "idBalance"){
                                echo "<label>Id balance </label>";
                                echo ":<select id='Balance' class='ajoutData' data-field='".$column->name."'>";
                                foreach($idBalance as $id){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$id->codeActif."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "idLot")
                            {
                                echo "<label>Numero du Lot</label>";
                                echo ":<select id='Lot' class='ajoutData' data-field='".$column->name."'>";
                                if($idLot == null){
                                    echo "<option>Il n'y a aucun lot d'ajouter</option>";
                                }
                                foreach($idLot as $id){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$id->numerolot."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "idEntite"){
                                echo "<label>Id entite</label>";
                                echo ":<select id='Entite' class='ajoutData' data-field='".$column->name."'>";
                                foreach($idEntite as $id){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$id->codeRegate."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "tranche"){
                                echo "<label>Tranche</label>";
                                echo ":<select id='tranche' class='ajoutData' data-field='".$column->name."'>";
                                foreach($tranches as $tranche){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$tranche->tranche."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "utilisation"){
                                echo "<label>Utilisation</label>";
                                echo ":<select id='utilisation' class='ajoutData' data-field='".$column->name."'>";
                                foreach($utilisations as $utilisation){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$utilisation->utilisation."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "statut"){
                                echo "<label>Statut</label>";
                                echo ":<select id='statut' class='ajoutData' data-field='".$column->name."'>";
                                foreach($statuts as $statut){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$statut->statut."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "type"){
                                echo "<label>Type</label>";
                                echo ":<select id='type' class='ajoutData' data-field='".$column->name."'>";
                                foreach($typeEntite as $type){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$type->type."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "idPrestataire"){
                                echo "<label>Prestataire</label>";
                                echo ":<select id='Prestataire' class='ajoutData' data-field='".$column->name."'>";
                                if($idPrestataire == null){
                                    echo "<option>Il n'y a aucun prestataire d'ajouté</option>";
                                }
                                foreach($idPrestataire as $id){
                                    echo "<option data-id'".$column->name."' data-field='".$column->name."' id='".$column->name."'>".$id->libelle."</option>";
                                }
                                echo "</select>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "dateVerification"){
                                echo "<label>Date de Vérification</label>";
                                echo ":<input type='date' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }
                            if($column->name == "numeroLot"){
                                echo "<label>Numéro du lot</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "codeRegate"){
                                echo "<label>Code regate</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "automates"){
                                echo "<label>Automates</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "departements"){
                                echo "<label>Départements</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "numeroSerie"){
                                echo "<label>Numéro de Série</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "codeSource"){
                                echo "<label>Code source</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "codePostal"){
                                echo "<label>Code postal</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "ville"){
                                echo "<label>Ville</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "adresse"){
                                echo "<label>Adresse</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "localisation"){
                                echo "<label>Localisation</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "codeActif"){
                                echo "<label>Code actif</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "codeArticle"){
                                echo "<label>Code article</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "libelle"){
                                echo "<label>Libellé</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "numeroDepartement"){
                                echo "<label>Numéro du département</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche0à30"){
                                echo "<label>Tranche de 0 à 30Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche31à200"){
                                echo "<label>Tranche de 31 à 200Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche201à600"){
                                echo "<label>Tranche de 201 à 600Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche601à1500"){
                                echo "<label>Tranche de 601 à 1500Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche1501à3000"){
                                echo "<label>Tranche de 1501 à 3000Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche3001à6000"){
                                echo "<label>Tranche de 3001 à 6000Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }elseif($column->name == "tranche6001à10000"){
                                echo "<label>Tranche de 6001 à 10 000Kg</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }
                            elseif($column->name == "statutVerification"){
                                echo "<label>Statut vérification</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }
                            elseif($column->name == "statutBalance"){
                                echo "<label>Statut balance</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                                echo "</br>";
                                continue;
                            }
                            echo "<label>".$column->name.":</label>".":<input type='text' class='ajoutData' data-id='".$column->name."' data-field='".$column->name."' id='".$column->name."'>";
                            echo "</br>";
                        }
                        ?>
                        <button type="submit" id="ajout">Ajouter un nouvel enregistrement</button>
                    </div>
                    <div id="ajoutExcel">
                        <h2>Ajout de nouvelles données avec fichier excel</h2>
                        <?php if($user->actual_table == "balance") echo "Attendez environ 2 minutes après avoir envoyer les données sur la base de données pour que toutes les données soit ajouter"; ?>
                        <p>Ordre d'insertion des données: lot->prestataire->entite->balance->prix->reporting</p>
                        1) Télécharger le fichier sur le serveur
                        <div style="padding: 10px;">
                        <?php echo form_open_multipart('upload_controller/do_upload');?>
                        <?php echo "<input type='file' name='userfile' size='20' />"; ?>
                        <?php echo "<input type='submit' name='submit' value='Chargement du fichier' style='width: 200px; padding:0;'/> ";?>
                        <?php echo "</form>"?>
                        </div>
                        2) Ajout des données dans la base de données<br/>
                        <button type="submit" id="excel">Mettre les données dans la base de données</button>
                        <?php if(isset($data)){ ?>
                        <p id="nameexcel" hidden><?php echo $data; ?></p>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </div>
</body>
<style>
.progress {
  background: red;
  display: block;
  height: 20px;
  text-align: center;
  transition: width .3s;
  width: 0;
}

.progress.hide {
  opacity: 0;
  transition: opacity 1.3s;
}
.formulaire {
  margin-bottom: 15px;
  padding: 10px;
}
 
label {
  width: 210px;
  display: inline-block;
  vertical-align: top;
  margin: 6px;
}
 
input{
  width: 249px;
}
 
select {
  width: 254px;
}
 
input[type=file] {
  width: 300px;
}
input[type=submit] {
  padding: 10px;
}
</style>
<script>
    var base_url = "<?php echo base_url();?>";
    $(document).ready(function(e) {
        //Add data
        var create = document.getElementById('ajout');
        create.onclick = function(){
            var data = document.getElementsByClassName('ajoutData');
            <?php if($user->actual_table == "balance"){ ?>
                var idModele = document.getElementById('Modele');
                idModele = idModele.options[idModele.selectedIndex].text;
                var idEntite = document.getElementById('Entite');
                idEntite = idEntite.options[idEntite.selectedIndex].text;
            <?php }else if($user->actual_table == "entite"){ ?>
                var idLot = document.getElementById('Lot');
                idLot = idLot.options[idLot.selectedIndex].text;
                var idPrestataire = document.getElementById('Prestataire');
                idPrestataire = idPrestataire.options[idPrestataire.selectedIndex].text;
            <?php }else if($user->actual_table == "prestataire"){ ?>
                var idLot = document.getElementById('Lot');
                idLot = idLot.options[idLot.selectedIndex].text;
            <?php }else if($user->actual_table == "prixdeplacemenet" || $user->actual_table == "prixverification"){ ?>
                var idPrestataire = document.getElementById('Prestataire');
                idPrestataire = idPrestataire.options[idPrestataire.selectedIndex].text;
            <?php }else if($user->actual_table == "verification"){ ?>
                var idBalance = document.getElementById("Balance");
                idBalance = idBalance.options[idBalance.selectedIndex].text;
            <?php } ?>
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
            var excel = document.getElementById('nameexcel').innerHTML;
            if(confirm("Etes-vous sur de vouloir ajouter ce fichier excel " +excel+ " ?")){
                var data = [];
                for (var i = 0; i < 100000; i++) {
                    var tmp = [];
                    for (var i = 0; i < 100000; i++) {
                        tmp[i] = 'hue';
                    }
                data[i] = tmp;
                };
                $.ajax({
                    xhr: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        console.log(percentComplete);
                        $('.progress').css({
                            width: percentComplete * 100 + '%'
                        });
                            if (percentComplete === 1) {
                                $('.progress').addClass('hide');
                            }
                        }
                    }, false);
                    xhr.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        console.log(percentComplete);
                            $('.progress').css({
                                width: percentComplete * 100 + '%'
                            });
                        }
                    }, false);
                    return xhr;
                    },
                   url: '<?= base_url() ?>index.php/crud/ajoutExcel',
                    dataSrc: "data",
                    type: 'post',
                    data: {excel: excel, data: data},
                    success: function(data){
                    }
                });
            }
        }
    });
</script>