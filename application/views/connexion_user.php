<?php
$this->load->helper('form');
$logo_laposte2020_properties = array(
    'src'   => 'assets/img/La Poste 2020.png',
    'class' => 'post_images',
    'width' => '75%',
    'class' => 'center'
);
$data_form = array(
    'class'         => 'form-inline'
);
$data_form_button = array(
    'name'          => 'connexion',
    'id'            => 'connexion',
    'value'         => 'true',
    'type'          => 'submit',
    'content'       => 'Connexion',
    'class'         => 'form-control'
);
$data_form_email = array(
    'name'          => 'email_connexion',
    'id'            => 'email_connexion',
    'type'          => 'email',
    'class'         => 'form-control',
    'placeholder'   => 'Email',
    'required' => 'required'
);
$data_form_password = array(
    'name'          => 'password_connexion',
    'id'            => 'password_connexion',
    'type'          => 'password',
    'class'         => 'form-control',
    'placeholder'   => 'Mot de passe',
    'required' => 'required',
    'rules'   => 'trim|required|callback_oldpassword_check'
);
?>
<div class="col-xs-6">
    <?php echo img($logo_laposte2020_properties);?>
    <!--<p>360° Videocodage est une application Web d'analyse de donnée issue du cluster Butia. Le site propose de créer
        vos graphiques.</p>-->
</div>
<div class="col-xs-6">
    <h1>Connexion</h1>
    <?php
    echo form_open('user/connexion/?lasturl='.urlencode($lasturl));
    if($error) echo $error."<br>";
    echo form_label('Email', 'email_connexion');
    echo form_input($data_form_email);
    echo form_label('Mot de passe', 'password_connexion');
    echo form_password($data_form_password);
    echo form_button($data_form_button)."<br>";
    echo form_close();

    ?>
    <div class="row">
        <div class="col-xs-6 center">
            <a href="<?php echo site_url("/User/register");?>">Nouveau compte</a>
        </div>
        <div class="col-xs-6 center">
            <a href="<?php echo site_url("/User/passwordReset");?>" class="not-active">Mot de passe oublié!</a>
        </div>
    </div>
</div>

