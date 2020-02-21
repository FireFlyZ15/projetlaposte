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
        <div class="col-md-10" id="resizableRight" style="min-width: 100%;">
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
                            if($user->actual_table == "verification"){
                                ?>
                            <th>Mise a jour</th>
                            <th>Controle</th>
                            <?php } ?>
                            <th>Suppresions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $keys = array_keys(get_object_vars($test[0]));
                            $i = 0;
                            if($user->actual_table == "verification"){
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
                                        echo "<td><span class='edit' name='".$key."'>".$data->$key."</span><input type='text' class='txtedit' data-id='".$data->id."' data-field='".$key."' id='".$data->id."' value='".$data->$key."'></td>";
                                    }
                                    if($key == "idBalance"){
                                        echo "<p id='' hidden>".$data->$key."</p>";
                                    }
                                }
                                echo "<td><a class='btn btn-success update' data-id='".$data->id."' id='idUpdate'><span class='glyphicon glyphicon-pencil'></a></td>";
                                echo "<td><a class='btn btn-warning control' data-id='".$data->id."' id='idUpdate'><span class='glyphicon glyphicon-pencil'></a></td>";
                                echo "<td><a class='btn btn-danger delete' data-id='".$data->id."' id='idDelete'><span class='glyphicon glyphicon-trash'></a></td>";
                                echo "</tr>";
                            }
                            }else{
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
                                    if($key == "idBalance"){
                                        echo "<p id='' hidden>".$data->$key."</p>";
                                    }
                                }
                                echo "<td><a class='btn btn-danger delete' data-id='".$data->id."' id='idDelete'><span class='glyphicon glyphicon-trash'></a></td>";
                                echo "</tr>";
                            }
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
    var base_url = "<?php echo base_url();?>";
    $(document).ready(function(e) {
        $('#datatable thead th').each(function() {
           var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search '+ title + '" />');
        });
        
        var table = $('#datatable').DataTable({
            scrollX: true,
            deferLoading: 100,
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
        $('#datatable tbody').on('click', '.edit', function() {
            
            $('.txtedit').hide();
            //Show next input element
            $(this).next('.txtedit').show().focus();
            
            //Hide clicked element
            $(this).hide();
        });
        
        $('#datatable tbody').on('focusout', '.selectedit', function() {
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
        $('#datatable tbody').on('focusout','.txtedit', function() {
           var edit_id = $(this).data('id');
            var fieldname = $(this).data('field');
            var value = $(this).val();
            var val = $(this);
            var oldvalue = val[0].attributes[5].value;
            
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
            }else{
                $(this).prev('.edit').text(oldvalue);
            }
        });
        
        //Delete data
        $('#datatable tbody').on("click",'.delete', function() {
        var parentDom = document.getElementById($(this).data('id'));
        
        var Listdata = parentDom.getElementsByClassName("edit");
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
                    document.getElementById('datatable').deleteRow(parentDom.sectionRowIndex+1);
                    console.log(response);
                },
                fail: function(response){
                    console.log(response);
                }
            });
        }
        });
        
        //Control data
        $('#datatable tbody').on("click",'.control', function() {
            <?php if(isset($control)){foreach($control as $row){ ?>
                var codeActif = '<?=$row->codeActif?>';
                var codeRegate = '<?=$row->codeRegate?>';
                if(codeActif == $(this).closest('tr').find('td:eq(6)').text()){
                    if(codeRegate == $(this).closest('tr').find('td:eq(5)').text()){
                        console.log(codeRegate);
                        $(this).closest('tr').css('background-color',' green');
                    }else{
                        $(this).closest('tr').css('background-color',' orange');
                    }
                }
            <?php }} ?>
        });
        
        //Update data with button
        $('#datatable tbody').on("click",'.update', function() {
        var parentDom = document.getElementById($(this).data('id'));
        
        var Listdata = parentDom.getElementsByClassName("edit");
            var codeActif = "";
            var date = "";
            var codeRegate = "";
            var statutBalance = "";
            $(Listdata).each(function() {
                if($(this)[0].nextElementSibling.attributes[3].textContent == "codeActif"){
                    codeActif = $(this)[0].textContent;
                } else if($(this)[0].nextElementSibling.attributes[3].textContent == "dateVerification"){
                    date = $(this)[0].textContent;          
                }else if($(this)[0].nextElementSibling.attributes[3].textContent == "codeRegate"){
                    codeRegate = $(this)[0].textContent;
                }else if($(this)[0].nextElementSibling.attributes[3].textContent == "statutBalance"){
                    statutBalance = $(this)[0].textContent;
                }
            });

        if(confirm("Etes-vous sur de vouloir modifier les données de la balance :"+codeActif+" avec ces données : "+date+","+codeRegate+","+statutBalance+" ?")){
            $.ajax({
                url: '<?= base_url() ?>index.php/crud/update',
                dataSrc: "data",
                type: 'post',
                data: {codeActif: codeActif, date: date, codeRegate: codeRegate, statutBalance: statutBalance},
                success: function(response){
                    console.log(response);
                },
                fail: function(response){
                    console.log(response);
                }
            });
        }
        });
    });
</script>