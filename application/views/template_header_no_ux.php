<?php $this->load->helper('html');
$this->load->helper('form');
$logo_properties = array(
    'src' => 'assets/img/logo.png',
    'class' => 'post_images',
    'width' => '75',
    'height' => '56',
);
$data_form = array(
    'class' => 'form-inline'
);
$connexion_button = array(
    'name' => 'connexion',
    'id' => 'connexion',
    'value' => 'true',
    'type' => 'button',
    'content' => 'Connexion',
    'class' => 'btn btn-info btn-sm',
    'onclick' => 'location.href = \'' . site_url('user/connexion') . '\''
);
$register_button = array(
    'name' => 'register',
    'id' => 'register',
    'value' => 'true',
    'type' => 'button',
    'content' => 'Créer un compte',
    'class' => 'btn btn-warning btn-sm',
    'onclick' => 'location.href = \'' . site_url('user/register') . '\''
);
$logout_button = array(
    'name' => 'logout',
    'id' => 'logout',
    'value' => 'true',
    'type' => 'button',
    'content' => 'Deconnexion',
    'class' => 'btn btn-danger btn-sm',
    'onclick' => 'location.href = \'' . site_url('user/logout') . '\''
);
$myaccount_button = array(
    'name' => 'myaccount',
    'id' => 'myaccount',
    'value' => 'true',
    'type' => 'button',
    'content' => 'Mon compte',
    'class' => 'btn btn-info btn-sm',
    'onclick' => 'location.href = \'' . site_url('user/myaccount') . '\''
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title><?= $titre ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/templete_header.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/loading.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/chosen.min.css'); ?>" type="text/css"/>
</head>
