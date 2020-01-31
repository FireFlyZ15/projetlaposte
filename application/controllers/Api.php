<?php

/**
 * Class Api
 * Controleur pour la gestion des appels REST
 */
class Api extends CI_Controller

{

    public function accueil()
    {
        echo '';
    }

    /**
     * Affiche les informations sur la source de donnée désiré
     * @deprecated
     */
    public function getBDDInfo(){
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("id") != null && $this->input->post("id") != "local") {
            $id = $this->input->post("id");
            $this->load->helper('database');
            $url = mysql_get_url_by_id($this, $id);
            //print_r($url);
            if ($url == "") {
                $this->load->helper('error');
                echo error_json(ERROR_CODE_BAD_GET_POST_VALUE, ERROR_MSG_BAD_GET_POST_VALUE, "L'identifiant fourni n'existe pas (id=" . $id . ")!");
                return;
            }
        } else {
            $url = "local";
        }
        $this->load->model('DataV2_model');
        $db = $this->DataV2_model->loadDatabase($url);
        $databaseinfoRAW = $this->DataV2_model->getDatabaseInfo($db);
        if ($databaseinfoRAW == null) {
            die("{}");
        }
        $databaseinfo = [];
        $filter_mode = false;
        //echo $this->input->post("id");
        if ($this->input->post("autorized")) {
            $this->load->model('Database_model');
            $autorized = $this->Database_model->getListAuthorizedData();
            //print_r($autorized);
            $filter_mode = true;
        }
        foreach ($databaseinfoRAW as $table){
            if (!$filter_mode || isset($autorized[$this->input->post("id")][$table->TABLE_SCHEMA][$table->TABLE_NAME])) {
                $datas = $this->DataV2_model->getColumnsNameExpert($db, $table->TABLE_SCHEMA, $table->TABLE_NAME);
                $databaseinfo[$table->TABLE_SCHEMA][$table->TABLE_NAME] = $datas;
            }

        }
        print json_encode($databaseinfo);

    }

    /**
     * Affiche les tables hebergé sur la source de donnée désiré
     */
    public function getSimpleBDDInfo()
    {
        header('Content-Type: application/json');
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("id") != null && $this->input->post("id") != "local") {
            $id = $this->input->post("id");
            $this->load->helper('database');
            $url = mysql_get_url_by_id($this, $id);
            //print_r($url);
            if ($url == "") {
                $this->load->helper('error');
                echo error_json(ERROR_CODE_BAD_GET_POST_VALUE, ERROR_MSG_BAD_GET_POST_VALUE, "L'identifiant fourni n'existe pas (id=" . $id . ")!");
                return;
            }
        } else {
            $url = "local";
        }
        $this->load->model('DataV2_model');
        $db = $this->DataV2_model->loadDatabase($url);
        $databaseinfoRAW = $this->DataV2_model->getDatabaseInfo($db);
        print json_encode($databaseinfoRAW);

    }

    /**
     * Affiche les tables hebergé sur la source de donnée désiré
     */
    public function getExpertBDDInfo()
    {

        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            header('Content-Type: application/json');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("id") != null && $this->input->post("id") != "local") {
            $id = $this->input->post("id");
            $this->load->helper('database');
            $url = mysql_get_url_by_id($this, $id);
            //print_r($url);
            if ($url == "") {
                $this->load->helper('error');
                header('Content-Type: application/json');
                echo error_json(ERROR_CODE_BAD_GET_POST_VALUE, ERROR_MSG_BAD_GET_POST_VALUE, "L'identifiant fourni n'existe pas (id=" . $id . ")!");
                return;
            }
        } else {
            $url = "local";
        }

        $this->load->model('DataV2_model');
        $db = $this->DataV2_model->loadDatabase($url);
        $databaseinfoRAW = $this->DataV2_model->getDatabaseInfo($db);
        $databaseinfo = [];
        //print json_encode($databaseinfoRAW);
        foreach ($databaseinfoRAW as $table) {
            //Ajout d'informations sur les colonnes contenu dans la table
            //print_r($table);
            $table->tables = $this->DataV2_model->getColumnsNameExpert($db, $table->TABLE_SCHEMA, $table->TABLE_NAME);


        }
        header('Content-Type: application/json');
        print json_encode($databaseinfoRAW);
    }
    /**
     * Affiche les bases disponible sur elasticsearch
     */
    public function getElasticInfo()
    {
        $this->load->helper('error');
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("id") != null) {
            $id = $this->input->post("id");
            $this->load->helper('database');
            $url = mysql_get_url_by_id($this, $id);
            //print_r($url);
            if ($url == "") {
                $this->load->helper('error');
                header('Content-Type: application/json');
                echo error_json(ERROR_CODE_BAD_GET_POST_VALUE, ERROR_MSG_BAD_GET_POST_VALUE, "L'identifiant fourni n'existe pas (id=" . $id . ")!");
                return;
            }
        } else {
            echo error_json(ERROR_CODE_BAD_GET_POST_VALUE, ERROR_MSG_BAD_GET_POST_VALUE, "L'identifiant fourni n'existe pas (id=" . $this->input->post("id") . ")!");
            return;
        }
        $this->load->helper('elasticsearch');
        header('Content-Type: application/json');
        print json_encode(elastic_map_list($url . "_mapping"));
    }
    public function getSampleData(){
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        session_write_close();
        $this->load->model('DataV2_model');
        if (!$this->input->get("source") || !$this->input->get("database") || !$this->input->get("table")) {
            die("[get]");
        }
        $this->load->model('Database_model');
        $source = $this->Database_model->getBdd($this->input->get("source"));
        if ($source == null) {
            die("[source]");
        }
        $checkAutorised = $this->Database_model->getAuthorizedData($this->input->get("source"), $this->input->get("database"), $this->input->get("table"));
        if ($checkAutorised == null) {
            die("[not autorisedtable]");
        }
        if ($source->engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            $config = [];
            $config["filtres"] = [];
            $config["filtres2"] = [];
            print elastic_sample($source->url, $this->input->get("table"), $config, 100, false);
        } else {
            $db = $this->DataV2_model->loadDatabase($source->url);
            print json_encode($this->DataV2_model->getSampleData($db, $this->input->get("database"), $this->input->get("table")));
        }


    }

    public function getSampleDataWhere(){
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        session_write_close();

        if ($this->input->get("source") != null && $this->input->get("database") != null && $this->input->get("table") != null) {
            $config['source'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("source"));
            $config['database'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("database"));
            $config['table'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("table"));
            $this->load->model('Database_model');
            $source = $this->Database_model->getBdd($config['source']);
            if ($source == null) {
                echo "[]";
                return;
            }
            $engine = $source->engine;
        } else {
            //Pas de base de donnée indiqué donc pas de requete
            echo "[]";
            return;
        }

        $config["filtres"] = [];
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        $config["operators"] = [];
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if(is_array($this->input->get("filtres2"))){
            $config["filtres2"] = $this->input->get("filtres2");
        }
        if($this->input->get("nbocc")!=null){
            $config["nbocc"] = $this->input->get("nbocc");
        } else {
            $config["nbocc"] = 500;
        }

        if ($engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            print elastic_sample($source->url, $config['table'], $config, $config["nbocc"], false);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($source->url);
            $config['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $config['database'], $config['table']);
            $sample = $this->DataV2_model->getAllData($db, $config, true);
            print json_encode($sample);
        }

    }
    /**
     * Generation d'un Json contenant les valeurs differentes pour une column
     **/
    public function getDiffValueColumn()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        //Augmente la mémoire allouée possible pendant l'exécution de la requête
        ini_set('memory_limit', '4G');
        $this->load->helper('datacache');
        $data_view = array();
        $data["email"]=$this->session->email;
        session_write_close();
        $sql_column = "";
        if($this->input->get("column")!=null){
            $sql_column = $this->input->get("column");
        }else if($this->input->post("column")){
            $sql_column = $this->input->post("column");
        }else{
            die("ErreurColumn");
        }
        $sourceID = "";
        if ($this->input->get("source") != null) {
            $sourceID = $this->input->get("source");
        } else if ($this->input->post("source")) {
            $sourceID = $this->input->post("source");
        }else{
            die("ErreurSource");
        }
        $database = "";
        if($this->input->get("database")!=null){
            $database = $this->input->get("database");
        }else if($this->input->post("database")){
            $database = $this->input->post("database");
        }else{
            die("ErreurDatabase");
        }
        $table = "";
        if ($this->input->get("table") != null) {
            $table = $this->input->get("table");
        } else if ($this->input->post("table")) {
            $table = $this->input->post("table");
        } else {
            die("ErreurTable");
        }

        $this->load->model('Database_model');
        $source = $this->Database_model->getBdd($sourceID);

        if ($source == null) {
            die("Erreur");
        }


        if ($source->engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            $json = elastic_diff_value($source->url, $table, $sql_column, false);
        } else {
            $sql_column = explode('::', $sql_column)[0];
            $this->load->model('Cache_model');
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($source->url);
            if ($db == null) {
                die("Erreur");
            }
            $timestamp_database = $this->DataV2_model->getUpdateTime($db, $database, $table);
            //Cherche si un cache existe
            $nameFile = generate_id_cache($sourceID . "." . $database . "." . $table . "." . $sql_column);
            $cache = $this->Cache_model->getDataCache($nameFile);
            if (check_validity_data_cache($cache, $timestamp_database, $nameFile)) {
                //Recuperation du fichier cache
                $json = get_cache($nameFile);
            } else {
                //Lance la requete en base
                $resultColumn = $this->DataV2_model->getDiffValueColumn($db, $database, $table, $sql_column);
                $array = array();
                foreach ($resultColumn as $rowColumn) {
                    $array[] = $rowColumn;
                }
                $resultColumn = null;
                $json = json_encode($array);
                $array = null;
                $updateMode = false;
                if ($cache != null) {
                    $updateMode = true;
                }
                save_in_cache($this, $nameFile, $sourceID, $database, $table, $json, $timestamp_database, $updateMode);
            }
        }

        print $json;
    }
    /**
     * Generation d'un Json
     */
    public function getData(){
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        //Augmente la mémoire allouée possible pendant l'exécution de la requête
        ini_set('memory_limit', '4G');
        $data_view = array();
        $data["email"]=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->helper('datacache');
        $this->load->helper('file');

        if($this->input->get("expertmode")!=null){
            //Flitres les requetes de modification
            $request_regex = "/^((?!drop | delete |insert |update |create |alter |truncate |merge ).)*;$/i";
            if($this->input->get("request") !=null && preg_match($request_regex, $this->input->get("request"))){
                $data_view['request'] = $this->input->get("request");
                //Enlève les '\' ajoutés pour que les scripts js fonctionnent
                $data_view['request']=str_replace("\\","",$data_view['request']);
            }else{
                $data_view['request'] = "";
                echo "query prohibited";
                return;
            }
            if ($this->input->get("idfilesave") != null) {
                $data_view['idfilesave'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("idfilesave"));
            } else {
                $data_view['idfilesave'] = "";
            }
            if ($this->input->get("source") != null) {
                $data_view['source'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("source"));
                $this->load->model('Database_model');
                $source = $this->Database_model->getBdd($data_view['source']);
                if ($source == null) {
                    echo "[]";
                    return;
                }
                $engine = $source->engine;
            } else {
                //Pas de base de donnée indiqué donc pas de requete
                echo "[]";
                return;
            }
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($source->url);
            if ($db == null) {
                die("Erreur");
            }
            $data_view['resultGraph'] = $this->DataV2_model->getDataExpert($db, $data_view['request']);
            $json = json_encode($data_view['resultGraph']);
            if ($data_view['idfilesave'] != "") {
                purge_old_graph_tmp();
                save_graph($data_view['idfilesave'], $json);
            }
            print $json;
        }else{
            if($this->input->get("wording")!=null && $this->input->get("wording") != "nb"){
                $data_view['wording'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"));
            }else{
                $data_view['wording'] = "mois";
            }

            if($this->input->get("group")!=null && $this->input->get("group") != "nb"){
                $data_view['group'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"));
            }else{
                $data_view['group'] = "";
            }
            if($this->input->get("maximum")!=null && preg_match('/^\d+$/',$this->input->get("maximum"))){
                $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
            }else{
                $config['maximum'] = "";
            }

            if($this->input->get("minimum")!=null && preg_match('/^\d+$/',$this->input->get("minimum"))){
                $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
            }else{
                $config['minimum'] = "";
            }
            if($this->input->get("typecalcul")!=null){
                $config['typecalcul'] = strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul")));
            }else{
                $config['typecalcul'] = "sum";
            }

            if($this->input->get("typecalculchamp")!=null){
                $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
            } else if (($config['typecalcul'] == "count" || $config['typecalcul'] == "COUNT") && $this->input->get("typecalculchamp") == "") {
                $config['typecalculchamp'] = "";
            }else{
                $config['typecalculchamp'] = "nb_enveloppe";
            }

            if($this->input->get("positionGraph")!=null){
                $data_view['positionGraph'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("positionGraph"));
            }else{
                $data_view['positionGraph'] = "bar";
            }
            if($this->input->get("stacked")!=null){
                $data_view['stacked'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("stacked"));
            }else{
                $data_view['stacked'] = "aucun";
            }

            if ($this->input->get("idfilesave") != null) {
                $data_view['idfilesave'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("idfilesave"));
            } else {
                $data_view['idfilesave'] = "";
            }
            //Recuperation des données sur la BDD
            $config["filtres"] = [];
            if(is_array($this->input->get("filtres"))){
                $config["filtres"] = $this->input->get("filtres");
            }
            $config["speedfilters"] = [];
            if(is_array($this->input->get("speedfilters"))){
                $config["speedfilters"] = $this->input->get("speedfilters");
            }
            $config["operators"] = [];
            if(is_array($this->input->get("operators"))){
                $config["operators"] = $this->input->get("operators");
            }
            if ($this->input->get("source") != null && $this->input->get("database") != null && $this->input->get("table") != null) {
                $data_view['source'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("source"));
                $data_view['database'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("database"));
                $data_view['table'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("table"));
                $this->load->model('Database_model');
                $source = $this->Database_model->getBdd($data_view['source']);
                if ($source == null) {
                    echo "[]";
                    return;
                }
                $engine = $source->engine;
            } else {
                //Pas de base de donnée indiqué donc pas de requete
                echo "[]";
                return;
            }

            if ($engine == "elasticsearch") {
                $this->load->helper('elasticsearch');
                $config['champs'] = [];
                $config['champs'][] = $data_view['wording'];
                if ($data_view['group'] != "") {
                    $config['champs'][] = $data_view['group'];
                }
                $json = elastic_getdataraw($source->url, $data_view['table'], $config, false);
                $arrayTMP = json_decode($json);
                $nb_name = $config['typecalcul'] . "(" . $config['typecalculchamp'] . ")";
                foreach ($arrayTMP as $key => $value) {
                    $arrayTMP[$key]->libellesGraph = $value->$data_view['wording'];
                    if (!in_array($value->$data_view['wording'], $config["speedfilters"])) {
                        unset($value->$data_view['wording']);
                    }
                    if ($data_view['group'] != "") {
                        $arrayTMP[$key]->typesGraph = $value->$data_view['group'];
                        if (!in_array($value->$data_view['group'], $config["speedfilters"])) {
                            unset($value->$data_view['group']);
                        }
                    } else {
                        $arrayTMP[$key]->typesGraph = "couleur";
                    }

                    $arrayTMP[$key]->nb = $value->$nb_name;
                    unset($value->$nb_name);
                }
                $json = json_encode($arrayTMP);
            } else {
                $this->load->model('Cache_model');
                $this->load->model('DataV2_model');
                $db = $this->DataV2_model->loadDatabase($source->url);
                if ($db == null) {
                    die("Erreur");
                }
                $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['database'], $data_view['table']);
                $config['sql_libelle'] = $data_view['wording'];
                $config['sql_type'] = $data_view['group'];
                $config['database'] = $data_view['database'];
                $config['table'] = $data_view['table'];
                $config['resultColumn'] = $data_view['resultColumn'];

                if (is_array($this->input->get("wording[]"))) {
                    $libelleSTR = implode('', $config["sql_libelle"]);
                } else {
                    $libelleSTR = $config['sql_libelle'];
                }
                $filtresSTR = implode(',', array_map('implode', $config["filtres"]));
                $operatorsSTR = implode('', $config["operators"]);
                $champsSTR = implode('', $config["speedfilters"]);
                $nameFile = generate_id_cache("getData" . $data_view['source'] . "." . $data_view['database'] . "." . $data_view['table'] . "." . $libelleSTR . $config['sql_type'] . $champsSTR . $config['typecalcul'] . $config['typecalculchamp'] . $config['minimum'] . $config['maximum'] . $filtresSTR . $operatorsSTR);

                $timestamp_database = $this->DataV2_model->getUpdateTime($db, $data_view['database'], $data_view['table']);
                $cache = $this->Cache_model->getDataCache($nameFile);
                if (check_validity_data_cache($cache, $timestamp_database, $nameFile)) {
                    //Recuperation du fichier cache
                    $json = get_cache($nameFile);
                } else {
                    $json = json_encode($this->DataV2_model->getData($db, $config));
                    $updateMode = false;
                    if ($cache != null) {
                        $updateMode = true;
                    }
                    save_in_cache($this, $nameFile, $data_view['source'], $data_view['database'], $data_view['table'], $json, $timestamp_database, $updateMode);
                }
            }
            if ($data_view['idfilesave'] != "") {
                purge_old_graph_tmp();
                save_graph($data_view['idfilesave'], $json);
            }

            print $json;
        }
    }

    public function getDataRAW($debug = null)
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        //Augmente la mémoire allouée possible pendant l'exécution de la requête
        ini_set('memory_limit', '4G');
        $data_view = array();
        $data["email"]=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->helper('datacache');
        $this->load->helper('file');
        if($this->input->get("minimum")!=null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        }else{
            $config['minimum'] = "";
        }
        if($this->input->get("maximum")!=null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
        }else{
            $config['maximum'] = "";
        }
        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul")));
        }else{
            $config['typecalcul'] = "sum";
        }

        if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        } else if ($config['typecalcul'] == "count" && $this->input->get("typecalculchamp") == "") {
            $config['typecalculchamp'] = "";
        }else{
            $config['typecalculchamp'] = "nb_enveloppe";
        }

        if ($this->input->get("source") != null && $this->input->get("database") != null && $this->input->get("table") != null) {
            $data_view['source'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("source"));
            $data_view['database'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("database"));
            $data_view['table'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("table"));
            $this->load->model('Database_model');
            $source = $this->Database_model->getBdd($data_view['source']);
            if ($source == null) {
                echo "[]";
                return;
            }
            $engine = $source->engine;
        }else{
            //Pas de base de donnée indiqué donc pas de requete
            echo "[]";
            return;
        }

        $config["filtres"] = [];
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        $config["speedfilters"] = [];
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        $config["operators"] = [];
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        $config['champs'] = [];
        if(is_array($this->input->get('champs'))){
            $config['champs'] = $this->input->get('champs');
        }

        if ($this->input->get("idfilesave") != null) {
            $data_view['idfilesave'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("idfilesave"));
        } else {
            $data_view['idfilesave'] = "";
        }

        if ($engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            $json = elastic_getdataraw($source->url, $data_view['table'], $config, $debug);
        } else {
            //Recuperation des données sur la BDD
            $this->load->model('DataV2_model');
            $this->load->model('Cache_model');
            $db = $this->DataV2_model->loadDatabase($source->url);
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['database'], $data_view['table']);
            $config['database'] = $data_view['database'];
            $config['table'] = $data_view['table'];
            $config['resultColumn'] = $data_view['resultColumn'];


            $filtresSTR = implode(',', array_map('implode', $config["filtres"]));
            $operatorsSTR = implode('', $config["operators"]);
            $champsSTR = implode('', $config["champs"]) . implode('', $config["speedfilters"]);
            $nameFile = generate_id_cache("getDataRAW" . $data_view['source'] . "." . $data_view['database'] . "." . $data_view['table'] . "." . $champsSTR . $config['typecalcul'] . $config['typecalculchamp'] . $config['minimum'] . $config['maximum'] . $filtresSTR . $operatorsSTR);


            $timestamp_database = $this->DataV2_model->getUpdateTime($db, $data_view['database'], $data_view['table']);
            $cache = $this->Cache_model->getDataCache($nameFile);
            if (check_validity_data_cache($cache, $timestamp_database, $nameFile)) {
                //Recuperation du fichier cache
                $json = get_cache($nameFile);
            } else {
                $json = json_encode($this->DataV2_model->getDataRAW($db, $config));
                $updateMode = false;
                if ($cache != null) {
                    $updateMode = true;
                }
                save_in_cache($this, $nameFile, $data_view['source'], $data_view['database'], $data_view['table'], $json, $timestamp_database, $updateMode);
            }
        }
        if ($data_view['idfilesave'] != "") {
            purge_old_graph_tmp();
            save_graph($data_view['idfilesave'], $json);
        }
        print $json;
    }


    public function elasticsearch()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        ini_set('memory_limit', '4G');
        $data_view = array();
        $data["email"] = $this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->helper('datacache');
        if ($this->input->get("minimum") != null && preg_match('/^\d+$/', $this->input->get("minimum"))) {
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        } else {
            $config['minimum'] = "";
        }
        if ($this->input->get("maximum") != null && preg_match('/^\d+$/', $this->input->get("maximum"))) {
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
        } else {
            $config['maximum'] = "";
        }
        if ($this->input->get("typecalcul") != null) {
            $config['typecalcul'] = strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul")));
        } else {
            $config['typecalcul'] = strtolower("SUM");
        }

        if ($this->input->get("typecalculchamp") != null) {
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        } else if ($config['typecalcul'] == "COUNT" && $this->input->get("typecalculchamp") == "") {
            $config['typecalculchamp'] = "";
        } else {
            $config['typecalculchamp'] = "nb_enveloppe";
        }

        if ($this->input->get("bdd") != null) {
            $data_view['bdd'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("bdd"));
        } else {
            $data_view['bdd'] = "";
            //Pas de base de donnée indiqué donc pas de requete
            echo "[]";
            return;
        }
        $config["filtres"] = [];
        if (is_array($this->input->get("filtres"))) {
            $config["filtres"] = $this->input->get("filtres");
        }
        $config["speedfilters"] = [];
        if (is_array($this->input->get("speedfilters"))) {
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        $config["operators"] = [];
        if (is_array($this->input->get("operators"))) {
            $config["operators"] = $this->input->get("operators");
        }
        $config['champs'] = [];
        if (is_array($this->input->get('champs'))) {
            $config['champs'] = $this->input->get('champs');
        }

        $bdd = "dernier_passage_pf";
        $type = "_search";


        $param = new stdClass();
        $param->size = 0;
        $param->_source = new stdClass();
        $param->_source->excludes = [];
        $param->aggs = new stdClass();
        $var = $param->aggs;
        $champs = array_keys(array_count_values(array_merge($config['champs'], $config['speedfilters'])));
        //print_r($champs);
        foreach ($champs as $key => $value) {
            $value_array = explode('::', $value);
            $value = $value_array[0];
            if (isset($value_array[1]) && in_array($value_array[1], ['YEAR', 'MONTH', 'YEARMONTH', 'DAY', 'DATE', 'DATEHOUR'])) {
                $value_type = $value_array[1];
                $var->$value = new stdClass();
                $var->$value->date_histogram = new stdClass();
                $var->$value->date_histogram->field = $value;
                if (in_array($value_array[1], ['YEAR'])) {
                    $var->$value->date_histogram->interval = "1y";
                } else if (in_array($value_array[1], ['MONTH', 'YEARMONTH'])) {
                    $var->$value->date_histogram->interval = "1M";
                } else if ($value_array[1] == "DATEHOUR") {
                    $var->$value->date_histogram->interval = "1h";
                } else {
                    $var->$value->date_histogram->interval = "1d";
                }
                $var->$value->date_histogram->time_zone = "Europe/Paris";
                $var->$value->date_histogram->min_doc_count = 1;
                //$var->$value->date_histogram->order = new stdClass();
                $var->$value->aggs = new stdClass();
                if ($config['typecalcul'] == "count") {
                    //$var->$value->date_histogram->order->_count = "desc";
                } else {
                    //$var->$value->date_histogram->order->result = "desc";

                    $var->$value->aggs->result = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul'] = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul']->field = $config['typecalculchamp'];
                }
                $var = $var->$value->aggs;
            } else {
                $var->$value = new stdClass();
                $var->$value->terms = new stdClass();
                $var->$value->terms->field = $value . ".keyword";
                $var->$value->terms->size = 999999999;
                $var->$value->terms->order = new stdClass();
                $var->$value->aggs = new stdClass();
                if ($config['typecalcul'] == "count") {
                    $var->$value->terms->order->_count = "desc";
                } else {
                    $var->$value->terms->order->result = "desc";

                    $var->$value->aggs->result = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul'] = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul']->field = $config['typecalculchamp'];
                }
                $var->$value->terms->missing = "NULL";
                $var = $var->$value->aggs;
            }
        }

        if ($config["filtres"] != []) {
            $param->query = new stdClass();
            $param->query->bool = new stdClass();
            $where = [];
            foreach ($config["operators"] as $key => $value) {
                if (!isset($where[$value])) {
                    $where[$value] = [];
                }

                $where[$value][$key] = [];
                foreach ($config["filtres"][$key] as $keyF => $valueF) {
                    $where[$value][$key][] = $valueF;
                }
            }
            foreach ($where as $keyType => $valueType) {
                if ($keyType == "exclure") {
                    if (!isset($param->query->bool->must_not)) {
                        $param->query->bool->must_not = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must_not[] = $champ;
                    }
                } else if ($keyType == "inclure") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must[] = $champ;
                    }
                } else if ($keyType == "date") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }
                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->range = new stdClass();
                        $champ->range->date = new stdClass();
                        //Date min
                        if ($valueC[0] != "") {
                            $champ->range->date->gte = $valueC[0];

                        } else {
                            $champ->range->date->gt = null;
                        }
                        //Date max
                        if ($valueC[1] != "") {
                            $champ->range->date->lte = $valueC[1];
                        } else {
                            $champ->range->date->lte = null;
                        }
                        $champ->range->date->time_zone = "Europe/Paris";
                        $param->query->bool->must[] = $champ;
                    }
                }
            }

        }


        $param = json_encode($param);
        //print_r($param);
        //echo "<br><br>";
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ELASTIC_URL . $bdd . "/" . $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponseHeaders = (isset($info["header_size"])) ? substr($response, 0, $info["header_size"]) : "";
        //print_r($actualResponseHeaders);
        //echo "<br><br>";
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        //print_r($actualResponse);
        //echo "<br><br>";
        curl_close($ch);
        $result = json_decode($actualResponse);

        //print_r($champs);
        //echo "<br><br>";

        $this->load->helper('elasticsearch');
        $array = elastic_result_to_json($result, $champs, $config, 0);
        echo json_encode($array);

        //echo "<br><br>";
        //print_r($champs);
        //echo "<br><br>";
        //print_r($config["filtres"]);
        //echo "<br><br>";
        //echo $actualResponse;
        //echo "<br><br>";
        //echo $param;
        //echo "<br><br>";
    }

    /**
     * Generation d'un Json contenant les valeurs differentes pour une column avec Elastic Search
     **/
    public function getDiffValueColumnElastic()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        //Augmente la mémoire allouée possible pendant l'exécution de la requête
        ini_set('memory_limit', '4G');
        $this->load->helper('datacache');
        $data_view = array();
        $data["email"] = $this->session->email;
        session_write_close();
        $sql_column = "";
        if ($this->input->get("column") != null) {
            $sql_column = $this->input->get("column");
        } else if ($this->input->post("column")) {
            $sql_column = $this->input->post("column");
        } else {
            die("ErreurColumn");
        }
        $bdd = "";
        if ($this->input->get("bdd") != null) {
            $bdd = $this->input->get("bdd");
        } else if ($this->input->post("bdd")) {
            $bdd = $this->input->post("bdd");
        } else {
            die("ErreurBDD");
        }
        if ($this->input->get("database") != null) {
            $database = $this->input->get("database");
        } else if ($this->input->post("database")) {
            $database = $this->input->post("database");
        } else {
            $database = "";
        }
        $debug = "";
        if ($this->input->get("debug") != null) {
            $debug = $this->input->get("debug");
        } else if ($this->input->post("debug")) {
            $debug = $this->input->post("debug");
        } else {
            $debug = false;
        }
        $this->load->helper('elasticsearch');
        echo elastic_diff_value($bdd, $sql_column, $debug);
    }

    public function getElasticCheck()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("url") != null) {
            $url = $this->input->post("url");
        } else {
            echo "ERROR_URL_EMPTY";
            return;

        }
        $this->load->helper('elasticsearch');
        echo elastic_check($url);
    }

    public function purgeOldGraphTmp()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        $this->load->helper('datacache');
        $this->load->helper('file');
        purge_old_graph_tmp();
    }

    public function getMysqlCheck()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("url") != null) {
            $url = $this->input->post("url");
        } else {
            echo "ERROR_URL_EMPTY";
            return;

        }
        $this->load->helper('database');
        if (mysql_check($this, $url)) {
            echo 'SUCCESS';
        } else {
            echo 'ERROR';
        }

    }

    public function getMysqlCheckByID()
    {
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        if ($this->input->post("id") != null) {
            $id = $this->input->post("id");
        } else {
            echo "ERROR_ID_EMPTY";
            return;

        }
        $this->load->helper('database');
        if (mysql_check_by_id($this, $id)) {
            echo 'SUCCESS';
        } else {
            echo 'ERROR';
        }

    }

    /**
     * Recupération de la liste des tables autorisées
     */
    public function getlistAuthorizedData()
    {
        header('Content-Type: application/json');
        if (user_check($this->session, ['admin', 'createur', 'lecteur'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_LOGGED, ERROR_MSG_USER_NOT_LOGGED);
            return;
        }
        $this->load->model('Database_model');
        echo json_encode($this->Database_model->getListAuthorizedData());
    }
}

