<div class="row">
    <div class="col-md-2 bg">
        <form class="navbar-form" role="search" action="" method="get">
            <h4 for="search hundredpercentwitdh">Tri des graphiques</h4>
            <div class="input-group form-group">
                <select class="form-control selectConfig75" name="order_name">
                    <option value="id" <?= ($order_name == "id") ? "selected" : "" ?>>Création</option>
                    <option value="date_update" <?= ($order_name == "date_update") ? "selected" : "" ?>>Mise à jour
                    </option>
                    <option value="date_view" <?= ($order_name == "date_view") ? "selected" : "" ?>>Visualisation
                    </option>
                </select>
                <select class="form-control selectConfig25" name="order_type">
                    <option value="ASC" <?= ($order_type == "ASC") ? "selected" : "" ?>>&and;</option>
                    <option value="DESC" <?= ($order_type == "DESC") ? "selected" : "" ?>>&or;</option>
                </select>
            </div>

            <div class="form-group checkboxwidth">
                <h4 for="search hundredpercentwitdh">Recherche</h4>
                <div class="input-group" id="searchbar">
                    <input type="text" class="form-control" placeholder="Recherche" id="search" name="search" value="<?=$search?>">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default" aria-label="Left Align" onclick="document.getElementById('search').value='';return false;">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>

            </div>
            <div class="form-group hundredpercentwitdh">
                <h4>Type de graphique</h4>
                <div class="checkbox hundredpercentwitdh overflow500">
                    <?php foreach ($difftype as $key=>$type) : ?>
                        <input type="checkbox" id="type<?=$key?>" name="type[]" class="form-control" value="<?=$type->value?>" <?=(in_array($type->value, $typeGet)) ? 'checked' : ''?>>
                        <label for="type<?=$key?>"><?=$type->value?></label><br/>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group hundredpercentwitdh">
                <h4 class="wordwrap_label" >Nom de la source de donnée</h4>
                <div class="checkbox hundredpercentwitdh overflow500">
                    <?php
                    $array_source = [];
                    $array_database = [];
                    ?>
                    <?php foreach ($difftable as $table) : ?>
                        <?php if ($table->table != ""): ?>
                            <?php if (!in_array($table->source, $array_source)): ?>
                                <h4><?= $table->source_name ?></h4>
                                <?php
                                $array_source[] = $table->source;
                                $array_database = [];
                                ?>
                            <?php endif; ?>
                            <?php if (!in_array($table->database, $array_database)): ?>
                                <h4>
                                    <small><?= $table->database ?></small>
                                </h4>
                                <?php $array_database[] = $table->database; ?>
                            <?php endif; ?>
                            <input id="database_<?= $table->source . "." . $table->database . "." . $table->table ?>"
                                   type="checkbox" name="table[]" id="database"
                                   class="form-control"
                                   value="<?= $table->source . "." . $table->database . "." . $table->table ?>" <?= (in_array($table->source . "." . $table->database . "." . $table->table, $tableGet)) ? 'checked' : '' ?>>
                            <label class="wordwrap_label"
                                   for="database_<?= $table->source . "." . $table->database . "." . $table->table ?>"><?= $table->table ?></label>
                            <br/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group hundredpercentwitdh">
                <h4>Créateur</h4>
                <div class="checkbox hundredpercentwitdh overflow500">
                    <?php foreach ($diffuser as $key=>$userForm) : ?>
                        <input id="userForm<?=$key?>" type="checkbox" name="userForm[]" id="userForm" class="form-control" value="<?=$userForm->value?>" <?=(in_array($userForm->value, $userGet)) ? 'checked' : ''?>>
                        <label class="wordwrap_label" for="userForm<?=$key?>"><?=$userForm->email?></label><br/>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group hundredpercentwitdh">
                <h4>Groupes</h4>
                <div class="checkbox hundredpercentwitdh overflow500">
                    <?php foreach ($diffgroup as $key=>$groupForm) : ?>
                        <?php if(in_array($groupForm->value,$getGroup_In_User)) :?>
                            <input type="checkbox" name="groupForm[]" id="groupForm<?=$key?>" class="form-control" value="<?=$groupForm->value?>" <?=(in_array($groupForm->value, $groupGet)) ? 'checked' : ''?>>
                            <label class="wordwrap_label" for="groupForm<?=$key?>"><?=$groupForm->name?></label><br/>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-default btn-lg btn-block "><span class="glyphicon glyphicon-search"></span></button>
        </form>
    </div>
    <div class="col-md-10">
        <h3>Graphiques</h3>
        <div class="row">

        <?php if(count($listGraph)==0) :?>
            <div class="alert alert-warning" role="alert">Aucun graphique disponible</div>
        <?php endif; ?>
        <?php foreach ($listGraph as $graph) :?>
                <div class="col-md-3">
                    <a style="color: inherit;display: block; " class="itemGraph" href="<?=site_url('graph/view'.$graph->type.'/'.$graph->id)?>">
                    <div class="thumbnail">
                        <img src="<?=base_url('uploads/'.$graph->image_name)?>" alt="" class="thumbnail_picture"/>
                        <div class="caption">
                            <div style="height: 75px">
                                <h4 title="<?=$graph->name?>">
                                    <?php if (strlen($graph->name) > 90) :?>
                                        <?=wordwrap(substr($graph->name, 0, 87),30) . '...'?>
                                    <?php else:?>
                                        <?=wordwrap($graph->name,30)?>
                                    <?php endif;?>
                                </h4>
                            </div>
                            <p><b>Type</b> : <?=$graph->type?></p>
                            <p><b>Créateur</b> : <?=$graph->email?></p>
                            <p><b>Source de donnée</b> : <br/>
                                <?php if ($graph->source_name != "") : ?>
                                    <?= $graph->source_name ?>
                                <?php else: ?>
                                    <?= "<br/>" ?>
                                <?php endif; ?>
                            </p>
                            <p><b>Base de donnée</b> : <br/>
                                <?php if ($graph->database != "") : ?>
                                    <?= $graph->database ?>
                                <?php else: ?>
                                    <?= "<br/>" ?>
                                <?php endif; ?>
                            </p>
                            <p><b>Table</b> : <br/>
                                <?php if ($graph->table != "") : ?>
                                    <?= $graph->table ?>
                                <?php else: ?>
                                    <?= "<br/>" ?>
                                <?php endif; ?>
                            </p>
                            <p><b>Date de création</b> : <?=$graph->date_creation?></p>
                            <p><b>Groupe</b> : <br/><?=($graph->group!="")?$graph->group:"<br/>"?></p>
                            <p>
                                <span title="<?=($graph->public==1) ? 'Visible par tous les utilisateurs.' : 'Visible seulement par le créateur.'?>" class="glyphicon glyphicon-eye-<?=($graph->public==1) ? 'open' : 'close'?>" aria-hidden="true"/>
                                <span title="<?=($graph->live==1) ? 'Affichage en temps réel du graphique.' : 'Affichage d\'une sauvegarde du graphique.'?>" class="glyphicon glyphicon-<?=($graph->live==1) ? 'refresh' : 'floppy-disk'?>" aria-hidden="true"/>
                            </p>

                            <p>
                                <a href="<?=site_url('graph/'.$graph->type.'_generator/'.$graph->id)?>" title="Modifier le graphique" class="btn btn-default glyphicon glyphicon-cog" role="button" <?php if($user->type!="admin" && $user->id!=$graph->user){?>disabled="disabled" onclick="return false;"<?php }?>></a>
                                <a href="<?=site_url('graph/duplicate/'.$graph->id)?>" title="Dupliquer le graphique" class="btn btn-default glyphicon glyphicon-duplicate" role="button" <?php if(($user->type!="admin" && $user->type!="createur") && $user->id!=$graph->user){?>disabled="disabled" onclick="return false;"<?php }?>></a>
                                <a href="<?=site_url('graph/delete/'.$graph->id)?>" title="Supprimer le graphique"class="btn btn-danger btn-sm glyphicon glyphicon-trash" role="button" <?php if($user->type!="admin" && $user->id!=$graph->user){?>disabled="disabled" onclick="return false;"<?php }?>></a>

                            </p>

                        </div>
                    </div>
                    </a>
                </div>
        <?php endforeach; ?>
        </div>
        <?php $url=preg_replace('/&nbPage=.*/',"",$_SERVER['REQUEST_URI']);
        if(preg_match('/(graph|Graph)\/?$/',$url)){
            $url.="?search=";
        }
        ?>
        <nav aria-label="..." class="center">
            <ul class="pagination">
                <li ><a href="<?=$url."&nbPage="?><?=($nbPage-1<1)?$nbPage:($nbPage-1)?>" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                <?php for($i = 1;$i<=$nbPageMax;$i++) :?>
                        <li <?=($i==$nbPage)?'class="active"':''?>><a href="<?=$url."&nbPage=".$i?>"><?=$i?></a></li>
                <?php endfor; ?>

                <li ><a href="<?=$url."&nbPage="?><?=($nbPage+1>$nbPageMax)?$nbPage:($nbPage+1)?>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
            </ul>
        </nav>
    </div>
</div>