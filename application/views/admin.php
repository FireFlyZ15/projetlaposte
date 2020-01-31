<div class="col-xs-6">
    <div class="list-group">
        <h2>Liste des utilisateurs</h2>
        <h3><span class="glyphicon glyphicon-king" aria-hidden="true"></span> Administrateurs</h3>
            <?php
                $nbuseradmin = 0;
                foreach ($listUser as $fuser) {
                    if($fuser->type=="admin"){
                        echo '<a class="list-group-item" href="'.site_url("user/user_management/".$fuser->id).'" >'.$fuser->email;
                        if($user->id!=$fuser->id) echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/remove/".$fuser->id).'\');return false;" title="Supprimer l\'utilisateur"><span class="glyphicon glyphicon-trash" aria-hidden="true" ></span></span>';
                        if($user->id!=$fuser->id) echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/lecteur").'\');return false;" title="Mettre en lecteur"><span class="glyphicon glyphicon-pawn" aria-hidden="true"></span></span>';
                        if($user->id!=$fuser->id) echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/createur").'\');return false;" title="Mettre en créateur"><span class="glyphicon glyphicon-knight" aria-hidden="true"></span></span>';
                        echo '</a>';
                        $nbuseradmin++;
                    }

                }
            if($nbuseradmin==0) echo "<div class=\"alert alert-warning\" role=\"alert\">Aucun administrateur</div>";

            ?>
        <h3><span class="glyphicon glyphicon-knight" aria-hidden="true"></span> Créateur</h3>
        <?php
        $nbuserlecteur = 0;
        foreach ($listUser as $fuser) {
            if($fuser->type=="createur"){
                echo '<a class="list-group-item" href="'.site_url("user/user_management/".$fuser->id).'">'.$fuser->email;
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/remove/".$fuser->id).'\');return false;" title="Supprimer l\'utilisateur"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span>';
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/lecteur").'\');return false;" title="Mettre en lecteur"><span class="glyphicon glyphicon-pawn" aria-hidden="true"></span></span>';
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/admin").'\');return false;" title="Mettre en admin"><span class="glyphicon glyphicon-king" aria-hidden="true"></span></span>';
                echo '</a>';
                $nbuserlecteur++;
            }
        }
        if($nbuserlecteur==0) echo "<div class=\"alert alert-warning\" role=\"alert\">Aucun créateur</div>";
        ?>
        <h3><span class="glyphicon glyphicon-pawn" aria-hidden="true"></span> Lecteurs</h3>
        <?php
        $nbuserlecteur = 0;
        foreach ($listUser as $fuser) {
            if($fuser->type=="lecteur"){
                echo '<a class="list-group-item" href="'.site_url("user/user_management/".$fuser->id).'">'.$fuser->email;
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/remove/".$fuser->id).'\');return false;" title="Supprimer l\'utilisateur"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span>';
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/createur").'\');return false;" title="Mettre en créateur"><span class="glyphicon glyphicon-knight" aria-hidden="true"></span></span>';
                echo '<span class="badge badge-default badge-pill" onclick="window.location.replace(\''.site_url("user/changetype/".$fuser->id."/admin").'\');return false;" title="Mettre en admin"><span class="glyphicon glyphicon-king" aria-hidden="true"></span></span>';
                echo '</a>';
                $nbuserlecteur++;
            }
        }
        if($nbuserlecteur==0) echo "<div class=\"alert alert-warning\" role=\"alert\">Aucun lecteur</div>";
        ?>
    </div>
    <h3>Création d'un utilisateur</h3>
    <?=generate_create_user_form('user/admin', $error)?>
</div>
<div class="col-xs-6">
    <h2>Configurations</h2>
    <div class="list-group">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Source de donnée par défaut pour les nouveaux utilisateurs
                    </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <?=form_open('user/changedefaultbdd')?>
                        <label for="sourceForm">Source de donnée</label>
                        <SELECT id="sourceForm" name="source" size="1" class="form-control"
                                onchange="generateDefaultChoiceForm('<?= $default_source ?>','<?= $default_database ?>','<?= $default_table ?>')">
                        </SELECT>


                        <label for="tableForm">Table de donnée</label>
                        <SELECT id="tableForm" name="table" size="1" class="form-control">
                        </SELECT>

                        <input type="submit" class="btn btn-primary form-control" value="Changer de base de donnée"/>
                        <?=form_close()?>
                        <h3>Description des données disponible <input type="button" class="btn btn-primary btn-xs" value="Modifier la description des données" onclick="switch_descriptiondatabase_mode()"/></h3>
                        <div id="descriptiondatabase_readmode" style="display : block;">
                            <p><?=$descriptiondatabase?></p>
                        </div>
                        <div id="descriptiondatabase_writemode" style="display : none;">
                            <?=form_open('user/changedescriptiondatabase')?>
                                <label for="description_database">Changer la description des données disponibles</label>
                                <textarea id="description_database" name="description_database" class="form-control textareaRight" rows="10"><?=$descriptiondatabase?></textarea>
                                <input type="submit" class="btn btn-primary form-control" value="Changer la description des bases de données"/>
                            <?=form_close()?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingThree">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Gestion des groupes d'utilisateurs
                        </a>
                    </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                    <div class="panel-body">
                        <?=form_open('user/addgroup')?>
                            <input type="text" id="group_name" name="group_name" class="form-control" placeholder="Nom du groupe" required/>
                            <input id="save_graph" type="submit" class="btn btn-primary form-control" value="Créer le groupe"/>
                        <?=form_close()?>
                        <div id="table-div" class="table-overflow">
                            <table class="table table-striped">
                                <tr><th>Nom du groupe</th><th>Nombre d'utilisateurs</th><th>Nombre de graphiques</th><th>Action</th></tr>
                                <?php foreach ($listGroup as $row): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url("user/group/" . $row->id) ?>"><?= ($row->name != "") ? $row->name : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?></a>
                                        </td>
                                        <td><?=(isset($countUsersInGroup[$row->id])) ? $countUsersInGroup[$row->id] : 0?></td>
                                        <td><?=(isset($countGraphByGroup[$row->id])) ? $countGraphByGroup[$row->id] : 0?></td>
                                        <td>
                                            <?php if(!isset($countGraphByGroup[$row->id]) && !isset($countUsersInGroup[$row->id])) : ?>
                                            <span class="badge badge-default badge-pill" onclick="window.location.replace('<?=site_url("user/removegroup/".$row->id)?>');return false;" title="Supprimer le groupe"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span></td>
                                            <?php endif;?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingFour">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour"
                           aria-expanded="false" aria-controls="collapseFour">
                            Gestion du cache
                        </a>
                    </h4>
                </div>
                <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                    <div class="panel-body">
                        <button class="btn btn-info btn-sm"
                                onclick="location.href = '<?= site_url("user/purge_cache/") ?>'">Suppression du cache
                            obsolete
                        </button>
                        <button class="btn btn-danger btn-sm"
                                onclick="location.href = '<?= site_url("user/purge_cache/true") ?>'">Suppression de tous
                            le cache
                        </button>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingBddManagement">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseBddManagement"
                           aria-expanded="false" aria-controls="collapseBddManagement">
                            Gestion des sources de données
                        </a>
                    </h4>
                </div>
                <div id="collapseBddManagement" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingBddManagement">

                    <div class="panel-body">
                        <h4>Ajout d'une source de donnée</h4>
                        <?= form_open('user/adddatasource') ?>
                        <label for="database_name">Nom de la source de donnée</label>
                        <input type="text" id="database_name" name="database_name" class="form-control"
                               placeholder="Nom de la source de donnée" onchange="resetBddManagementButton()" required/>
                        <label for="database_engine">Moteur de donnée</label>
                        <SELECT id="database_engine" name="database_engine" size="1" class="form-control">
                            <OPTION value="mysql">Mysql
                            <OPTION value="elasticsearch">Elasticsearch
                        </SELECT>
                        <label for="database_url">URL de la base de donnée</label>
                        <input type="text" id="database_url" name="database_url" class="form-control"
                               placeholder="URL de la base de donnée => mysqli://user:passwd@ip:port/db"
                               onkeyup="resetBddManagementButton()" on="resetBddManagementButton()" required/>
                        <input id="test_database" type="button" class="btn btn-info" value="Test la base de donnée"
                               onclick="databaseConnexionTest()"/>
                        <input id="save_database" type="submit" class="btn btn-primary form-control"
                               value="Créer la base de donnée"
                               title="Il faut tester la base de donnée pour la sauvegarder" disabled/>
                        <?= form_close() ?>
                        <h4>Liste des sources de donnée</h4>
                        <ul class="list-group">
                            <?php foreach ($listDatabase as $row): ?>
                                <li class="list-group-item">
                                    <strong><?= $row->name ?></strong> : <?= $row->engine ?>
                                    (<?= preg_replace("/\/\/.*@/", "//****:****@", $row->url) ?>)
                                    <?php if ($row->name != "local"): ?>
                                        <span class="badge badge-default badge-pill"
                                              onclick="window.location.replace('<?= site_url("user/removedatasource/" . $row->id) ?>')"
                                              title="Supprimer l'acces à la source de donnée"><span
                                                    class="glyphicon glyphicon-trash"
                                                    aria-hidden="true"></span></span></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingDataAuthorized">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseDataAuthorized"
                           aria-expanded="false" aria-controls="collapseDataAuthorized">
                            Gestion des données autorisées
                        </a>
                    </h4>
                </div>
                <div id="collapseDataAuthorized" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingDataAuthorized">
                    <div class="panel-body">
                        <button id="dataAuthorizedButtonModif" class="btn btn-default btn-info"
                                title="Rafraichir les informations" onclick="getlistAuthorizedData()"><span
                                    class="glyphicon glyphicon-refresh"></span></button>
                        <span id="lastDateUpdateDataAuthorized"></span>
                        <?php foreach ($listDatabase as $row): ?>
                            <?= form_open('user/modify_authorized/' . $row->id) ?>
                            <h4><?= $row->name ?> <em>(<?= $row->engine ?>
                                    : <?= preg_replace("/\/\/.*@/", "//****:****@", $row->url) ?>)</em>
                                <button id="dataAuthorizedButtonModif_<?= $row->id ?>"
                                        class="btn btn-default btn-warning"
                                        onclick="authorizedDataTableSwitch('<?= $row->id ?>','<?= $row->engine ?>');return false;"><span
                                            class="glyphicon glyphicon-cog"></span></button>
                                <button id="dataAuthorizedButtonSubmit_<?= $row->id ?>"
                                        class="btn btn-default btn-success" disabled><span
                                            class="glyphicon glyphicon-floppy-saved"></span></button>
                            </h4>
                            <div id="dataAuthorizedselectAddDiv_<?= $row->id ?>"
                                 class="dataAuthorizedselectAddDiv form-inline form-group"></div>
                            <div id="dataAuthorizedDiv_<?= $row->id ?>">
                                <div class="alert alert-info" role="alert">Aucune donnée autorisées</div>
                            </div>
                            <?= form_close() ?>
                        <?php endforeach; ?>
                        <div id="dataAutorized"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/jquery.min.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/graph.js"></script>
<script type='text/javascript' src="<?= base_url() ?>assets/js/admin.js"></script>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/bootstrap.min.js"></script>
<script>
    <?="var listAuthorizedData =JSON.parse('" . json_encode($listAuthorizedData) . "');\n"?>
    //Generation des données pour la rubrique Gestion des données autorisées
    getlistAuthorizedData();
    generateDefaultChoiceForm('<?=$default_source?>', '<?=$default_database?>', '<?=$default_table?>');
</script>