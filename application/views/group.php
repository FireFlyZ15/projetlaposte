<h2>Gestion du groupe <?=$group->name?></h2>
<div class="col-xs-3">
<h3>Membre du groupe</h3>
    <input type="checkbox" id="selectAll" onclick="selectAll(this.checked,'users[]')">
    <label for="selectAll">Sélectionner tout</label><br/>
    <?=form_open('user/changeuseringroup/'.$group->id)?>
    <div class="table-overflow">
        <?php foreach ($listUser as $user) :?>
            <?php if (in_array($user->id,$getUsers_In_Group)): ?>
                <input type="checkbox" id="user_<?=$user->id?>" name="users[]" class="user_in_group" value="<?=$user->id?>" checked>
                <label for="user_<?=$user->id?>"><?=$user->email?></label><br/>
            <?php else: ?>
                <input type="checkbox" id="user_<?=$user->id?>" name="users[]" class="user_in_group" value="<?=$user->id?>">
                <label for="user_<?=$user->id?>"><?=$user->email?></label><br/>
            <?php endif; ?>
        <?php endforeach;?>
    </div>
    <input type="submit" class="btn btn-primary" value="Mettre le groupe à jour"/>
    <a type="button" class="btn btn-primary" href="<?=site_url('user/admin/')?>">Retour</a>
    <?=form_close()?>
</div>
<div class="col-xs-8">
    <h3>Graphiques du groupe</h3>
    <?php foreach ($listGraph as $graph) :?>
    <div class="row">
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
                        <p>
                            <span title="<?=($graph->public==1) ? 'Visible par tous les utilisateurs.' : 'Visible seulement par le créateur.'?>" class="glyphicon glyphicon-eye-<?=($graph->public==1) ? 'open' : 'close'?>" aria-hidden="true"/>
                            <span title="<?=($graph->live==1) ? 'Affichage en temps réel du graphique.' : 'Affichage d\'une sauvegarde du graphique.'?>" class="glyphicon glyphicon-<?=($graph->live==1) ? 'refresh' : 'floppy-disk'?>" aria-hidden="true"/>
                        </p>
                    </div>
                </div>
            </a>
        </div>

    <?php endforeach; ?>
    </div>
    <?php $url=preg_replace('/&nbPage=.*/',"",$_SERVER['REQUEST_URI']);
    if(preg_match('/group\/\d*$/',$url)){
        $url.="?";
    }
    ?>
    <nav aria-label="..." class="center">
        <ul class="pagination">
            <li ><a href="<?=$url."&nbPage="?><?=($nbPage-1<1)?$nbPage:($nbPage-1)?>" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
            <?php for($i = 1;$i<=$nbPageMax;$i++) :?>
                <li <?=($i==$nbPage)?'class="active"':''?>><a href="<?=$url."&nbPage=".$i?>"><?=$i?> <span class="sr-only">(current)</span></a></li>
            <?php endfor; ?>

            <li ><a href="<?=$url."&nbPage="?><?=($nbPage+1>$nbPageMax)?$nbPage:($nbPage+1)?>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
        </ul>
    </nav>
</div>
<script type = 'text/javascript' src = "<?=base_url()?>assets/js/user.js"></script>