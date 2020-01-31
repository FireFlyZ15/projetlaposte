<?php
$this->load->helper('graph');
$DateFormatFrList = json_decode(DATE_FORMAT_FR_LIST);
$DateFormatFrListTimeTamps = json_decode(DATE_FORMAT_FR_LIST_TIMETAMPS);
$DateFormatList = json_decode(DATE_FORMAT_LIST);
?>
<head>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf-8" src="<?=base_url()?>assets/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?=base_url()?>assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
</head>
<body>
    <div class="row" id="resizableParent">
        <div class="col-md-2 bg" id="resizableLeft">
            <h4><?=EXPORT_CONSTRUCTION_TITLE?>
                <a type="button" class="btn btn-link" target="_blank" href="<?php echo base_url('assets/pdf/Hadoop definition des données.pdf');?>" title="Définition des données">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                </a>
            </h4>
            <a class="btn btn-primary btn-info"
               onclick="showTable('<?= $config['source'] ?>','<?= $config['database'] ?>','<?= $config['table'] ?>');">Voir
                les données</a><br>
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
            <div id="data">
                Données de la Table<br/>
                <a class="editor_create btn btn-success create" id="create"><span class="glyphicon glyphicon-plus"></span>Créer une nouvelle données</a>
                <?php ?>
                <table id="datatable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <?php
                                $array = array_keys(get_object_vars($test[0]));
                                $width = (1/count($array))*100;
                                foreach($array as $key){
                                    echo "<th>".$key."</th>";
                                }
                            ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $keys = array_keys(get_object_vars($test[0]));
                            $i = 0;
                            foreach($test as $data){
                                echo "<tr id='".$data->id."'>";
                                $id = "";
                                foreach($keys as $key){
                                    if($key == "statut"){
                                        echo"<td><span class='edit' >".$data->$key."</span><select class='selectedit' id='statut' data-field='".$key."' data-id='".$data->id."'>";
                                        foreach($statuts as $statut){ 
                                            if($data->statut == $statut->statut){
                                                echo "<option data-id='".$data->id."' data-field'".$key."' id='".$data->id."' selected>".$statut->statut."</option>";
                                            }else{
                                                echo "<option data-id='".$data->id."' data-field'".$key."' id='".$data->id."' >".$statut->statut."</option>";
                                            }
                                        }
                                        echo "</select></td>";
                                    }else{
                                        echo "<td><span class='edit' >".$data->$key."</span><input type='text' class='txtedit' data-id='".$data->id."' data-field='".$key."' id='".$data->id."' value='".$data->$key."'></td>";
                                    }
                                }
                                echo "<td><a class='btn btn-danger delete' data-id='".$data->id."' id='idDelete'><span class='glyphicon glyphicon-trash'></a></td>";
                                echo "</tr>";
                            }
                        ?>    
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php
                                $array = array_keys(get_object_vars($test[0]));
                                $width = (1/count($array))*100;
                                foreach($array as $key){
                                    echo "<th width = ".$width.">".$key."</th>";
                                }
                            ?>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?=generate_modal("error_modal","Erreur")?>
            <h4>Filtres utilisés</h4>
            <?=generate_array_list_html($config)?>
        </div>
    </div>
</body>
<script>
    var editor;
    var base_url = "<?php echo base_url();?>";
    $(document).ready(function(e) {
        $('#datatable tfoot th').each(function() {
           var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search '+ title + '" />');
        });
        
        var table = $('#datatable').DataTable({
            scrollX: true,
            "aLengthMenu": [
                [10, 25, 50, 75, -1],
                [10, 25, 50, 75, "All"]
            ]
        });
        
        table.columns().every(function(){
           var that = this;
            
            $('input', this.footer()).on('keyup change clear', function(){
               if (that.search() !== this.value) {
                   that.search(this.value).draw();
               } 
            });
        });
        
        //Hide input Element
        $('.edit').click(function() {
            
            $('.txtedit').hide();
            //Show next input element
            $(this).next('.txtedit').show().focus();
            
            //Hide clicked element
            $(this).hide();
        });
        
        $('.selectedit').focusout(function() {
            var edit_id = $(this).data('id');
            var fieldname = $(this).data('field');
            var value = $(this).val();
            
            //Send AJAX request
            if(confirm("Etes-vous sur de vouloir modifier ce champ :"+fieldname+ "avec la valeur : "+value+ " ?")){
            $.ajax({
               url: '<?= base_url() ?>index.php/crud/update',
                dataSrc: "data",
                type: 'post',
                data: {field: fieldname, value: value, id: edit_id},
                success: function(response){
                    console.log(response);
                },
                fail: function(response){
                    console.log(response);
                }
            });
            }
        });
        
        //Focus out from a textbox
        $('.txtedit').focusout(function() {
           var edit_id = $(this).data('id');
            var fieldname = $(this).data('field');
            var value = $(this).val();
            
            //Hide Input element
            $(this).hide();
            
            //Update viewing value and display it
            $(this).prev('.edit').show();
            $(this).prev('.edit').text(value);
            
            //Send AJAX request
            if(confirm("Etes-vous sur de vouloir modifier ce champ :"+fieldname+ "avec la valeur : "+value+ " ?")){
            $.ajax({
               url: '<?= base_url() ?>index.php/crud/update',
                dataSrc: "data",
                type: 'post',
                data: {field: fieldname, value: value, id: edit_id},
                success: function(response){
                    console.log(response);
                },
                fail: function(response){
                    console.log(response);
                }
            });
            }
        });
        var create = document.getElementById("create");
        
        //Delete data
        var Bdelete = document.getElementById("idDelete");
        Bdelete.onclick = function() {
            var parentDom = document.getElementById($(this).data('id'));
        
            var Listdata = parentDom.getElementsByClassName("edit");
            console.log(Listdata);
            var data;
            var i;
            for(i = 0; i < Listdata.length; i++){
                data = data + Listdata[i].textContent + ",";
            }
            if(confirm("Etes-vous sur de vouloir supprimer cette données :"+data+" ?")){
                var edit_id = $(this).data('id'); 
                $.ajax({
                    url: '<?= base_url() ?>index.php/crud/delete',
                    dataSrc: "data",
                    type: 'post',
                    data: {id: edit_id},
                    success: function(response){
                        console.log(response);
                    },
                    fail: function(response){
                        console.log(response);
                    }
                });
            }
        }
    });
</script>