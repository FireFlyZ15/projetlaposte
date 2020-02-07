<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('generateMonth')){
    /**
     * Transforme le numéro de mois en nom français du mois
     * @param $nb Numéro de mois
     * @return mixed Nom du mois
     */
    function generateMonth($nb){
            $month = array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre");
            return $month[$nb-1];
    }
}
if ( ! function_exists('arrayPHPToJs')){
    /**
     * Traduction d'une table php en table js
     * @param $array Table php
     * @return string  code js pour créer la table
     */
    function arrayPHPToJs($array){
        $result = "[";
        foreach($array as $row){
            if(is_array($row)){
                $result .= arrayPHPToJs($row).",";
            }else if(is_object($row)){
                $result .= listObjectPHPToJs($row).",";
            }else{
                $result .= "\"".$row."\",";
            }
        }
        if(substr($result,-1)==","){
            $result = substr($result, 0, -1);
        }

        $result .= "]";
        return $result;

    }
}
if ( ! function_exists('arrayPHPToJs2')){
    /**
     * Traduction d'une table php en table js
     * @param $array Table php
     * @return string  code js pour créer la table
     */
    function arrayPHPToJs2($array){
        $result = "[";

        foreach($array as $row){

            if(is_array($row)){
                $result .= arrayPHPToJs2($row).",";
            }else{
                $arrayLibelle = explode(' - ', $row);
                if(count($arrayLibelle)>1){
                    $result .= '["'.implode('","',$arrayLibelle).'"],';
                }else{
                    $result .= "\"".$row."\",";
                }

            }
        }
        if(substr($result,-1)==","){
            $result = substr($result, 0, -1);
        }

        $result .= "]";
        return $result;

    }
}
if ( ! function_exists('listObjectPHPToJs')){
    /**
     * Traduction d'une table php en table js
     * @param $array Table php
     * @return string  code js pour créer la table
     */
    function listObjectPHPToJs($array){
        $result = "{";
        foreach($array as $key =>$row){
            if(is_array($row)){
                $result .= arrayPHPToJs($row).",";
            }else if(is_object($row)){
                $result .= listObjectPHPToJs((array) $row).",";
            }else{
                $result .= $key.":\"".$row."\",";
            }
        }
        if(substr($result,-1)==","){
            $result = substr($result, 0, -1);
        }

        $result .= "}";
        return $result;

    }
}

if ( ! function_exists('arrayPHPToJsWithDate')){
    /**
     * Traduction d'une table php en table js
     * @param $array Table php
     * @return string  code js pour créer la table
     */
    function arrayPHPToJsWithDate($array){
        $result = "[";
        foreach($array as $row){
            if(is_array($row)){
                $result .= arrayPHPToJs($row).",";
            }else if(is_object($row)){
                $result .= arrayPHPToJs((array) $row).",";
            }else{
                //$date = preg_split('/[- :]/',$row);
                $result .= "new Date('$row'),";
            }
        }
        if(substr($result,-1)==","){
            $result = substr($result, 0, -1);
        }

        $result .= "]";
        return $result;

    }
}
if ( ! function_exists('genereType')){
        function genereType($array, $name, $color,$positionGraph,$stacked){
          if($positionGraph=="line" and $stacked=="aucun"){
            return "{label: \"".$name."\",borderColor: \"".$color."\",backgroundColor: \"".$color."\",fill: false, data: ".arrayPHPToJs($array)."}";
          }else{
            return "{label: \"".$name."\",borderColor: \"".$color."\",backgroundColor: \"".$color."\" , data: ".arrayPHPToJs($array)."}";
          }

        }
}
if ( ! function_exists('genereType_pie')){
        function genereType_pie($array, $nbValue, $arrayColor){
          $value = "{data: ".arrayPHPToJs($array).",backgroundColor:[";
          for($i=0;$i<$nbValue;$i++){
              $value .= "\"".$arrayColor[$i]."\",";
          }
          $value = substr($value, 0, -1)."]}";
          return $value;
        }
}
if ( ! function_exists('rand_color')){
    /**
     * Génération d'une coleur aléatoire au format hexa
     * @return string Code hexa de la couleur
     */
    function rand_color() {
            return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
if ( ! function_exists('translate_word_fr')){
    /**
     * Traduction d'un mot en un autre
     * @param $word Mot initial
     * @return string Mot traduit
     */
    function translate_word_fr($word) {
        $translate = [];
        $translate["group"] = "groupement";
        $translate["wording"] = "libellé";
        $translate["stacked"] = "empilé";
        $translate["mode"] = "mode du graphique";
        $translate["doughnut"] = "anneau";
        $translate["pie"] = "secteur";
        $result = $word;

        if(is_string($word) && array_key_exists($word, $translate)){
            $result =  $translate[$word];
        }

        return $result;
    }
}

if ( ! function_exists('generate_array_list_html')){
    /**
     * Génération d'une liste en HTML du contenu d'un objet/table
     * @param $array objet/table à parser
     * @return string Liste en HTML du contenu de l'objet
     */
    function generate_array_list_html($array) {
        $ul = "<ul>";
        foreach ($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $ul .= "<li>".str_replace("_"," ",ucfirst(translate_word_fr($key)));
                $ul .= generate_array_list_html($value);
                $ul .= "</li>";
            }else{
                $ul .= "<li>".str_replace("_"," ",ucfirst(translate_word_fr($key)))." : ".str_replace("_"," ",ucfirst(translate_word_fr($value)))."</li>";
            }
        }
        $ul .= "</ul>";
        return $ul;
    }
}

if ( ! function_exists('generate_form_filters')){
    /**
     * Génération des champs permettant l'ajout ou la suppression de filtres de type where
     * @param $array objet/table à parser
     * @return string Liste en HTML du contenu de l'objet
     */
    function generate_form_filters($resultColumn) {
        $form = "<div class=\"form-inline form-group\"><label for=\"filters\">".FILTER_CHOICE_LABEL."</label><br/><SELECT id=\"filters\" name=\"filters\" class=\"form-control selectFilter\">";
         foreach ($resultColumn as $rowColumn){
             if ($rowColumn->type == "timestamp" || $rowColumn->type == "datetime" || $rowColumn->type == "date") {
                 $form .= "<OPTION value='$rowColumn->name::DATE'> $rowColumn->name";
             } else if ($rowColumn->type != "smallint" && $rowColumn->type != "int" && $rowColumn->type != "bigint" && $rowColumn->type != "double") {
                 $form .= "<OPTION value='$rowColumn->name'> $rowColumn->name";
             }
         }
        $form .= "</SELECT><button type=\"button\" onclick='addFilter(configGraph.source, configGraph.database, configGraph.table)' class=\"btn btn-default\"><span id=\"addFiltersButton\" class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"/></button></div><div id=\"filtersBox\" class=\"filtersBox resizeV\"></div><div id=\"filtersBoxselected\"><ul id=\"data\"></ul></div>";

        return $form;
    }
    
    function generate_form_filters2($resultColumn) {
        $form = "<div class=\"form-inline form-group\"><label for=\"filters\">".FILTER_CHOICE_LABEL."</label><br/><SELECT id=\"filters\" name=\"filters\" class=\"form-control selectFilter\">";
         foreach ($resultColumn as $rowColumn){
             if ($rowColumn->type == "timestamp" || $rowColumn->type == "datetime" || $rowColumn->type == "date") {
                 $form .= "<OPTION value='$rowColumn->name::DATE'> $rowColumn->name";
             } else if ($rowColumn->type == "smallint" && $rowColumn->type == "int" && $rowColumn->type == "bigint" && $rowColumn->type == "double") {
                 $form .= "<OPTION value='$rowColumn->name'> $rowColumn->name";
             }else{
                 $form .= "<OPTION value='$rowColumn->name'> $rowColumn->name";
             }
         }
        $form .= "</SELECT><button type=\"button\" onclick='addFilter(configGraph.source, configGraph.database, configGraph.table)' class=\"btn btn-default\"><span id=\"addFiltersButton\" class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"/></button></div><div id=\"filtersBox\" class=\"filtersBox resizeV\"></div><div id=\"filtersBoxselected\"><ul id=\"data\"></ul></div>";

        return $form;
    }
}

if ( ! function_exists('generate_form_add_speed_filters')){
    /**
     * Génération des champs permettant l'ajout ou la suppression de filtres de type where
     * @param $array objet/table à parser
     * @return string Liste en HTML du contenu de l'objet
     */
    function generate_form_add_speed_filters($resultColumn,$DateFormatFrList,$DateFormatFrListTimeTamps,$config,$DateFormatList) {
        $hidden = "";
        $name = "name=\"speedfilters[]\"";
        if($config['typecalcul']=="AVG"){
            $hidden = "hidden=\"\"";
            $name = "";
        }
        $form = "<div id=\"speed-filters-div\" ".$hidden."><label for=\"filters\">".FILTER_CHOICE_LABEL."</label><br/><select id=\"speed-filters-form\" ".$name." data-placeholder=\"Ajouter des filtres rapides\" multiple class=\"chosen-select\">";
        foreach ($resultColumn as $rowColumn){
            if ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double"){

            }else if($rowColumn->type == "date"){
                $i=0;
                foreach ($DateFormatFrList as $DateFormat){
                    if(in_array($rowColumn->name."::".$DateFormatList[$i],$config["speedfilters"])){
                        $form .= "<OPTION value=\"$rowColumn->name::$DateFormatList[$i]\" selected>". $rowColumn->name." - $DateFormat";
                    }else{
                        $form .= "<OPTION value=\"$rowColumn->name::$DateFormatList[$i]\">". $rowColumn->name." - $DateFormat";
                    }
                    $i++;
                }
            }else if($rowColumn->type == "timestamp"){
                $i=0;
                foreach ($DateFormatFrListTimeTamps as $DateFormat){
                    if(in_array($rowColumn->name."::".$DateFormatList[$i],$config["speedfilters"])){
                        $form .= "<OPTION value=\"$rowColumn->name::$DateFormatList[$i]\" selected>". $rowColumn->name." - $DateFormat";
                    }else{
                        $form .= "<OPTION value=\"$rowColumn->name::$DateFormatList[$i]\">". $rowColumn->name." - $DateFormat";
                    }
                    $i++;
                }
            }else{
                if(in_array($rowColumn->name,$config["speedfilters"])){
                    $form .= "<OPTION selected> $rowColumn->name";
                }else{
                    $form .= "<OPTION> $rowColumn->name";
                }

            }
        }
        $form.="</SELECT></div>";
        return $form;
    }
}


if ( ! function_exists('generate_form_min_max')) {
    /**
     * Génération des champs permettant l'ajout ou la suppression de filtres de type where
     * @param $array objet/table à parser
     * @return string Liste en HTML du contenu de l'objet
     */
    function generate_form_min_max($min, $max, $engine = "mysql")
    {
        $disabled = "";
        if ($engine == "elasticsearch") {
            $disabled = " title=\"La fonctionnalité n'est pas en charge avec ElasticSearch\" disabled";
        }
        return "<label for=\"minimum\">" . MIN_LABEL . "</label><br/><input type=\"number\" name=\"minimum\" class=\"form-control\" min=\"0\" max=\"9223372036854775807\" value=\"$min\" $disabled>" .
            "<label for=\"maximum\">" . MAX_LABEL . "</label><br/><input type=\"number\" name=\"maximum\" class=\"form-control\" min=\"0\" max=\"9223372036854775807\" value=\"$max\" $disabled>";
    }

}

if ( ! function_exists('generate_form_calcul')) {
    /**
     * Génération des champs permettant le choix de type de calcul
     * @param $array objet/table à parser
     * @return string Liste en HTML du contenu de l'objet
     */
    function generate_form_calcul($config, $resultColumn)
    {
        $result = '<div class="form-inline form-group">';
        $result .= '<label for="typecalcul">Calcul</label><br>';
        $result .= '<SELECT id="typecalcul" name="typecalcul" class="form-control selectConfig25" onchange="generateCalculForm(champs)">';
        $result .= '<OPTION ' . (($config['typecalcul'] == "COUNT") ? "selected" : "") . '>COUNT';
        $result .= '<OPTION ' . (($config['typecalcul'] == "SUM") ? "selected" : "") . '>SUM';
        $result .= '<OPTION ' . (($config['typecalcul'] == "AVG") ? "selected" : "") . '>AVG';
        $result .= '</SELECT>';
        $result .= '<SELECT id="typecalculchamp" name="typecalculchamp" class="form-control selectConfig75">';
        if ($config['typecalcul'] == "COUNT") {
            $result .= '<OPTION>';
        }
        foreach ($resultColumn as $rowColumn) {
            if ($config['typecalcul'] == "COUNT" && ($rowColumn->type != "smallint" && $rowColumn->type != "int" && $rowColumn->type != "bigint" && $rowColumn->type != "double")) {
                $result .= '<OPTION ' . (($config['typecalculchamp'] == $rowColumn->name) ? "selected" : "") . '>' . $rowColumn->name;
            }else if (($config['typecalcul'] == "SUM" || $config['typecalcul'] == "AVG") && ($rowColumn->type == "smallint" || $rowColumn->type == "int" || $rowColumn->type == "bigint" || $rowColumn->type == "double")) {
                $result .= '<OPTION ' . (($config['typecalculchamp'] == $rowColumn->name) ? "selected" : "") . '>' . $rowColumn->name;
            }

        }

        $result .= '</SELECT>';
        $result .= '</div>';
        return $result;
    }
}

if ( ! function_exists('generate_form_select')) {
    function generate_form_select($list, $name, $nameForUser, $seleted, $class, $valueVar, $valueUserVar)
    {
        $result = "<label for=\"$name\">$nameForUser</label><select id=\"$name\" name=\"$name\" class=\"$class\">";
        foreach ($list as $row) {
            if($seleted==$row->$valueVar){
                $result .= "<option value='".$row->$valueVar."' selected>".$row->$valueUserVar;
            }else{
                $result .= "<option value='".$row->$valueVar."'>".$row->$valueUserVar;
            }

        }
        $result .="</select>";
        return $result;
    }

}

if ( ! function_exists('generate_modal')) {
    function generate_modal($id, $title)
    {
        echo '<div class="modal fade" id="'.$id.'" tabindex="-1" role="dialog" aria-labelledby="'.$id.'_label">';
            echo '<div class="modal-dialog modal-lg" role="document" style="width: 90%;">';
                echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        echo '<h4 class="modal-title" id="'.$id.'_label">'.$title.'</h4>';
                    echo '</div>';
                    echo '<div id="error_modal_body" class="modal-body">';
                    echo '</div>';
                    echo '<div class="modal-footer">';
                     echo '<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

}
/**
 * Genere le formulaire de sauvegarde de graphique
 */
if ( ! function_exists('generate_save_graph_form')) {
    function generate_save_graph_form($name,$description, $listGroup, $group,$config,$image_name,$public,$live,$typeGraph,$id="")
    {
        echo '<form action="'.site_url().'/graph/saveGraph" method="post">';
        echo '<h4>'.COLOR_CHANGE_TITLE.'</h4>';
        echo '<div id="colormanager"></div>';
        echo '<h4>'.SAVE_TITLE.'</h4>';
        echo '<label for="name">'.NAME_GRAPH_LABEL.'</label>';
        echo '<input type="text" id="name" class="form-control" name="name" value="'.$name.'" required />';
        echo '<label for="description">'.DESCRIPTION_GRAPH_LABEL.'</label>';
        echo '<textarea id="description" name="description" class="form-control textareaRight" cols="40" rows="5">'.$description.'</textarea>';
        echo generate_form_select($listGroup, "groupUser", GROUP_USER_GRAPH_LABEL, $group, "form-control textareaRight", "id", "name");
        echo '<input id="id" name="id" type="hidden" value="'.$id.'" />';
        //echo '<input id="script" name="script" type="hidden" value=""/>';
        echo '<input id="type" name="type" type="hidden" value="'.$typeGraph.'"/>';
        echo '<input id="config" name="config" type="hidden" value=\'\'/>';
        echo '<input id="image" name="image" type="hidden" value=""/>';
        echo '<input id="image_name" name="image_name" type="hidden" value="'.$image_name.'"/>';
        if($public){
            $public_html = "checked";
        }else{
            $public_html = "";
        }
        echo '<div class="checkbox">';
            echo '<label>';
                echo '<input type="checkbox" id="public" name="public" '.$public_html.'>';
                echo PUBLIC_MODE_LABEL;
                echo '</label>';
            echo '</div>';
        if($live){
            $live_html = "checked";
        }else{
            $live_html = "";
        }
        echo '<div class="checkbox">';
            echo '<label>';
                echo '<input type="checkbox" id="live" name="live" '.$live_html.'>';
                echo LIVE_MODE_LABEL;
                echo '</label>';
            echo '</div>';
        echo '<input id="save_graph" type="submit" class="btn btn-primary form-control" onclick="return saveGraph();return false;" value="Enregistrer" disabled/>';
        echo '</form>';

    }

}

