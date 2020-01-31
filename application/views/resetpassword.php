<?php
$data_form_email = array(
    'name'          => 'email_reset',
    'id'            => 'email_reset',
    'type'          => 'email',
    'class'         => 'form-control',
    'placeholder'   => 'Email',
    'required' => 'required'
);
$data_form_button = array(
    'name'          => 'connexion',
    'id'            => 'connexion',
    'value'         => 'true',
    'type'          => 'submit',
    'content'       => 'Réinitialiser le mot de passe',
    'class'         => 'form-control'
);
?>
<div class="col-xs-6">
    <h1>Réinitialisation du mot de passe</h1>
    <?php
    echo form_open('user/passwordsend');
    echo form_label('Email', 'email_connexion');
    echo form_input($data_form_email);
    echo form_button($data_form_button)."<br>";
    echo form_close();

    ?>
    <div class="row">
        <div class="center">
            <a href="<?php echo site_url("");?>">Retour</a>
        </div>

    </div>
</div>