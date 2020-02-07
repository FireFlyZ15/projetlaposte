<?php $this->load->helper('html');
$this->load->helper('form');
$logo_properties = array(
    'src'   => 'assets/img/logo.png',
    'class' => 'post_images',
    'width' => '75',
    'height'=> '56',
);
$logo_metrologie = array(
    'src'   => 'assets/img/picto_BEAmetrologie_avec contour_reparation.png',
    'class' => 'post_images',
    'width' => '75',
    'height'=> '56',
);
$data_form = array(
    'class'         => 'form-inline'
);
$connexion_button = array(
    'name'          => 'connexion',
    'id'            => 'connexion',
    'value'         => 'true',
    'type'          => 'button',
    'content'       => 'Connexion',
    'class'         => 'btn btn-info btn-sm',
    'onclick'       => 'location.href = \''.site_url('user/connexion').'\''
);
$register_button = array(
    'name'          => 'register',
    'id'            => 'register',
    'value'         => 'true',
    'type'          => 'button',
    'content'       => 'Créer un compte',
    'class'         => 'btn btn-warning btn-sm',
    'onclick'       => 'location.href = \''.site_url('user/register').'\''
);
$logout_button = array(
    'name'          => 'logout',
    'id'            => 'logout',
    'value'         => 'true',
    'type'          => 'button',
    'content'       => 'Deconnexion',
    'class'         => 'btn btn-danger btn-sm',
    'onclick'       => 'location.href = \''.site_url('user/logout').'\''
);
$myaccount_button = array(
    'name'          => 'myaccount',
    'id'            => 'myaccount',
    'value'         => 'true',
    'type'          => 'button',
    'content'       => 'Mon compte',
    'class'         => 'btn btn-info btn-sm',
    'onclick'       => 'location.href = \''.site_url('user/myaccount').'\''
);
?>
<!DOCTYPE html>
<html lang = "fr">
    <head>
        <meta charset="UTF-8" />
        <title><?=$titre?></title>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css');?>" type="text/css"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/templete_header.css');?>" type="text/css"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/loading.css');?>" type="text/css"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css');?>" type="text/css"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/chosen.min.css');?>" type="text/css"/>


    </head>
    <header>
        <div class="row">
            <?php echo $user->actual_source; ?>
            <?php if ($user->actual_source != "ds_5e281d009493e6.10541606"){?>
            <div class="col-md-8 left white"><h1><a href="<?php echo base_url(); ?>"
                                                    class="titre"><?php echo img($logo_properties); ?><?= PROJECT_NAME_360 ?></a>
                </h1><?= VERSION . " " . ENVIRONEMENT ?>
            </div>
            <?php }else{ ?>
            <div class="col-md-8 left white"><h1><a href="<?php echo base_url(); ?>"
                                                    class="titre"><?php echo img($logo_metrologie); ?><?= PROJECT_NAME_Metro ?></a>
                </h1><?= VERSION . " " . ENVIRONEMENT ?>
            </div>
            <?php } ?>
            <div class="col-md-4 right padding-loginfo">
                <?php if(!$this->session->has_userdata('logged_in')):?>
                    <?=form_button($connexion_button).form_button($register_button)?>
                    <a type="button" class="btn btn-link white" href="<?php echo site_url('user/about');?>" title="Contact">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>
                <?php else:?>
                    <div class="white"><?php echo $this->session->email." ".form_button($myaccount_button)." ".form_button($logout_button);?>
                        <a type="button" class="btn btn-link" href="<?php echo site_url('user/about');?>" title="Contact">
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        </a>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?php if (isset($errormsg) && $errormsg != ""): ?>
            <div class="alert alert-warning"><?= $errormsg ?></div>
        <?php endif; ?>
        <?php if($this->session->has_userdata('logged_in')){?>
        <?php if ($user->actual_source != "ds_5e281d009493e6.10541606"){?>
        <ul class="nav nav-pills nav-justified white">
                <li role="presentation" <?php if($type=="list"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/');?>">Listes des graphes</a></li>
            <li role="presentation" <?php if($type=="consult"){echo 'class="active"';}?>><a href="<?php echo site_url('consultation/');?>">Consultation des données</a></li>
                <?php if($user != null && ($user->type=="admin" || $user->type=="createur")) :?>
                    <li role="presentation" <?php if($type=="histogram"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/histogram_generator/');?>">Générateur Histogramme</a></li>
                    <li role="presentation" <?php if($type=="pie"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/pie_generator/');?>">Générateur Diagramme circulaire</a></li>
                    <li role="presentation" <?php if($type=="table1D"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/table1D_generator/');?>">Générateur Tableau 1D</a></li>
                    <li role="presentation" <?php if($type=="table2D"||$type=="table"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/table2D_generator/');?>">Générateur Tableau 2D</a></li>
                    <li role="presentation" <?php if($type=="treemap"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/treemap_generator/');?>">Générateur TreeMap</a></li>
                    <li role="presentation" <?php if ($type == "frame") {
                        echo 'class="active"';
                    } ?>><a href="<?php echo site_url('graph/frame_generator/'); ?>">Générateur Frame</a></li>
                    <li role="presentation" <?php if($type=="exportcsv_excel"){echo 'class="active"';}?>><a href="<?php echo site_url('graph/exportcsv_excel/');?>">ExportCSV/EXCEL</a></li>
                <?php endif; ?>
                <?php if($user != null && ($user->type=="admin")) :?>
                    <li role="presentation" <?php if($type=="crud"){echo 'class="active"';}?>><a href="<?php echo site_url('crud/');?>">Ajout/Modif/Suppr</a></li>
                    <li role="presentation" <?php if($type=="admin"){echo 'class="active"';}?>><a href="<?php echo site_url('user/admin/');?>">Administration</a></li>
                <?php endif; ?>
        </ul>
            <?php }else{ ?>
        <ul class="nav nav-pills nav-justified white">
                <li role="presentation" <?php if($type=="list"){echo 'class="active"';}?>><a href="<?php echo site_url('crud/ajout');?>">Ajout d'une nouvelle données</a></li>
                <li role="presentation" <?php if($type=="list"){echo 'class="active"';}?>><a href="<?php echo site_url('crud/modifsuppr');?>">Modifications/Suppression d'une données</a></li>
                <li role="presentation" <?php if($type=="list"){echo 'class="active"';}?>><a href="<?php echo site_url('crud/consultation');?>">Consultations des données</a></li>
                <?php if($user != null && ($user->type=="admin" || $user->type=="createur")) :?>
                <?php endif; ?>
        </ul>
        <?php }} ?>
        <div class="login"></div>
    </header>
