<?php
$data_form_button = array(
    'name'          => 'registration',
    'id'            => 'registration',
    'value'         => 'true',
    'type'          => 'submit',
    'content'       => 'Connexion',
    'class'         => 'form-control',
    'onclick'      => 'return checkPassword(\'password_registration\',\'password_registration_verification\')'
);
$data_form_password = array(
    'name'          => 'password_registration',
    'id'            => 'password_registration',
    'type'          => 'password',
    'class'         => 'form-control',
    'placeholder'   => 'Mot de passe',
    'required' => 'required',
    'rules'   => 'trim|required|callback_oldpassword_check'
);
$data_form_password_verification = array(
    'name'          => 'password_registration_verification',
    'id'            => 'password_registration_verification',
    'type'          => 'password',
    'class'         => 'form-control',
    'placeholder'   => 'Mot de passe',
    'required' => 'required',
    'onchange'      => 'checkPassword(\'password_registration\',\'password_registration_verification\');'
);
?>
<div class="col-xs-6">
    <div class="list-group">
        <h2>Changement de Mot de passe</h2>
        <?=form_open('user/myaccount')?>
        <?php if($success) echo '<div class="alert alert-success" role="alert">'.$success.'</div>';?>
        <?php if($error) echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';?>
        <?=form_label('Mot de passe', 'password_registration')?>
        <?=form_password($data_form_password)?>
        <?=form_label('Confirmation du mot de passe', 'password_registration_verification')?>
        <?=form_password($data_form_password_verification)?>
        <?=form_error('password_registration_verification', '<div class="error">', '</div>')?>
        <?=form_button($data_form_button)."<br>"?>
        <?=form_close()?>
    </div>
</div>
<?php if($user->type=="admin"||$user->type=="createur"): ?>
<div class="col-xs-6">
    <div class="list-group">
        <h2>Source de donnée utilisée : </h2>

        <?php if (array_key_exists($user->actual_source, $listAuthorizedData)): ?>
            <div class="alert alert-info">
                Actuel
                : <?= $listAuthorizedData[$user->actual_source]->source_name . " : " . $user->actual_database . "." . $user->actual_table ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                La source de donnée n'existe plus.
                <?php  ?>
                Actuel : <?= $user->actual_source . " : " . $user->actual_database . "." . $user->actual_table ?>
            </div>
        <?php endif; ?>



        <?=form_open('user/changebdd')?>
        <label for="sourceForm">Source de donnée</label>
        <SELECT id="sourceForm" name="source" size="1" class="form-control" onchange="generateTableFormV2()">
        </SELECT>


        <label for="tableForm">Table de donnée</label>
        <SELECT id="tableForm" name="table" size="1" class="form-control">
        </SELECT>

        <input type="submit" class="btn btn-primary form-control" value="Changer de base de donnée"/>
        <?=form_close()?>
        <h3>Description des données disponible</h3>
        <p><?=$descriptiondatabase?></p>
    </div>
    <?php if($user->actual_database == "projetlaposte"){
        echo "la base de données est projet la poste";
        echo $user->actual_table;
    }else if($user->actual_database == "hadoopviewer"){
        echo "la base de données est hadoopviewer";
    } 
    ?>
</div>
<?php endif;?>
<script type='text/javascript' src="<?= base_url() ?>assets/js/formcheck.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/user.js"></script>
<script>
    <?="var listAuthorizedData =JSON.parse('" . json_encode($listAuthorizedData) . "');\n"?>
    <?="var user =JSON.parse('" . json_encode($user) . "');\n"?>
    generateTableFormV2();
</script>