<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
/**
 * Class Graph
 * Controleur pour la gestion des graphiques
 */
class Graph extends CI_Controller
{
    /**
    * Page de génération des histogrammes
     * test
    **/
    public function histogram_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->model('User_model');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('Database_model');


        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;
        $data_view['id'] = "";
        //Recuperation de l'utilisateur
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }

        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"] = [];
        $config["speedfilters"]=[];
        $config["operators"] = [];

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph, $getGroup_In_User) || $graph->type!="histogram"){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);
            $config = $graphConfig;
            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['live'] = $graph->live;
            $data_view['group'] = $graph->group;

            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $graphConfig['bdd'];
                unset($config['bdd']);
            }

            if($this->input->get("group") != null && $config['group']!=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"))){
                unset($config['color']);
            }
            $data_view['id'] = $id;
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            //Ajout le champ maximum pour les anciennes config
            if(!isset($config["maximum"])){
                $config["maximum"]="";
            }
            if(!isset($config["speedfilters"])){
                $config["speedfilters"]=[];
            }
        }
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;

        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['engine'] = $data_view['source']->engine;
        if(is_array($this->input->get("wording[]"))){
            $config['wording'] = $this->input->get("wording[]");
        }else if($this->input->get("wording") != null && $this->input->get("wording") != "nb"){
            $config['wording'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config['wording'] = "";
        }

        if(isset($_GET["group"])){
            $config['group'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"));
        }else if($graph !=""){
        }else{
            $config['group'] = "";
        }


        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }

        if($this->input->get("mode")!=null){
            $config['mode'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("mode"));
        }else if($graph !=""){
        }else{
            $config['mode'] = "bar";
        }
        if($this->input->get("stacked")!=null){
            $config['stacked'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("stacked"));
        }else if($graph !=""){
        }else{
            $config['stacked'] = "aucun";
        }
        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("minimum")  != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }

        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
        }else if($graph !=""){
        }else{
            $config['maximum'] = "";
        }
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }

        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }

        $data_view['config'] = $config;
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }
        //Recuperation de la derniere date de modification de la table

        if ($data_view['source']->engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {

                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Redirection pour changer de base de données
            if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);

        }

        $data_view['titre'] = "Générateur de graphes (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "histogram";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_histogram', $data_view);
    }
    public function table_generator($id = NULL){
        $this->table2D_generator($id);
    }
    public function table2D_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;
        $data_view['id'] = "";
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"] = [];
        $config["speedfilters"]=[];
        $config["operators"] = [];

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph,$getGroup_In_User) || !($graph->type=="table" || $graph->type="table2D")){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);
            $config = $graphConfig;
            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['live'] = $graph->live;
            $data_view['group'] = $graph->group;
            $data_view['id'] = $id;
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $graphConfig['bdd'];
                unset($config['bdd']);
            }

            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            if(!isset($config["maximum"])){
                $config["maximum"]="";
            }
            if(!isset($config["speedfilters"])){
                $config["speedfilters"]=[];
            }
        }
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['engine'] = $data_view['source']->engine;
        if($this->input->get("wording") != null && $this->input->get("wording") != "nb"){
            $config['wording'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config['wording'] = "";
        }

        if(isset($_GET["group"])){
            $config['group'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"));
        }else if($graph !=""){
        }else{
            $config['group'] = "";
        }

        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }

        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }
        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
            //Ecrase la config de l'ancien graphique
        }else if($graph !=""){
        }else{
            $config['maximum'] = "";
        }
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }
        $data_view['config'] = $config;
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }
        //Suppression du " and" en trop

        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');
        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Redirection pour changer de base de données
            if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }

        $data_view['titre'] = "Générateur de Tableau 2D (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "table2D";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_table2D', $data_view);
    }
    public function table1D_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        ini_set('max_input_vars', '100M');
        ini_set('post_max_size', '100M');
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['id'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;

        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"]=[];
        $config["speedfilters"]=[];
        $config["champs"] = [];
        $config['request'] = "";
        $config['expertmode'] = false;

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph,$getGroup_In_User)){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $config = json_decode($graph->config, true);

            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['id'] = $id;
            $data_view['group'] = $graph->group;
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $config['bdd'];
                unset($config['bdd']);
            }
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            if(!isset($config["maximum"])){
                $config["maximum"]="";
            }

            if(!isset($graphConfig["filtres"])){
                $config["filtres"]=[];
            }
            if(!isset($graphConfig["speedfilters"])){
                $config["speedfilters"]=[];
            }

        }
        if (!$config['expertmode']) {
            $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        } else {
            $data_view['source'] = $this->Database_model->getBdd($config['source']);
        }
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['source_name'] = $data_view['source']->name;
        $config['engine'] = $data_view['source']->engine;

        if($this->input->get("expertmode")!=null){
            $config['expertmode'] = true;
        }

        if($config['expertmode']){
            $config["table"] = "expertmode";
        }

        if($this->input->get("request")!=null){
            $config['request'] = $this->input->get("request", true);
        }
        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
            //Ecrase la config de l'ancien graphique
        }else if($graph !=""){
        }else{
            $config['maximum'] = "";
        }
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }
        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if(!isset($config['typecalcul'])){
            $config['typecalculchamp'] = "";
        }else if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }
        if(is_array($this->input->get("champs"))){
            $config["champs"] = $this->input->get("champs");
        }

        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }
        $data_view['config'] = $config;
        $data_view['champs'] = $config['champs'];
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }

        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');
        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Remise à zero des données si le moteur choisie est elastocsearcj et si le mode expert est activé
            if ($config['expertmode']) {
                redirect(site_url() . "/graph/table1D_generator", 'refresh');
                return;
            } else if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else if ($config["table"] != "expertmode") {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }else{
            $data_view['update_time_table'] = "";
            $data_view['resultColumn'] = [];
        }
        $data_view['titre'] = "Générateur de Tableau 1D (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "table1D";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_table1D', $data_view);
    }
    public function exportcsv_excel($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['public'] = false;
        $data_view['id'] = "";
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"]=[];
        $config["champs"] = [];

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph,$getGroup_In_User)){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);

            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['public'] = $graph->public;
            $data_view['id'] = $id;
            $data_view['group'] = $graph->group;
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $graphConfig['bdd'];
                unset($config['bdd']);
            }
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            //Ajout le champ maximum pour les anciennes config
            if(!isset($config["maximum"])){
                $config["maximum"]=0;
            }
            if(isset($graphConfig['wording'])){
                $config["champs"][] = $graphConfig['wording'];
            }
            if(isset($graphConfig['group'])){
                $config["champs"][] = $graphConfig['group'];
            }
            if(!isset($graphConfig["filtres"])){
                $config["filtres"]=[];
            }else{
                $config["filtres"] = $graphConfig["filtres"];
            }
        }
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['engine'] = $data_view['source']->engine;
        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }
        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
        }else if($graph !=""){
        }else{
            $config['maximum'] = "";
        }
        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }
        if($_GET){
            $config["filtres"]=[];
            $config["champs"] = [];
            if($this->input->get('wording')){
                $config["champs"][] =  preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$this->input->get('wording'));
            }
            if($this->input->get('group')){
                $config["champs"][] =  preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$this->input->get('group'));
            }
            foreach($_GET as $key => $value){
                if($key=="champs"){
                    if(is_array($value)){
                        foreach ($value as $value2){
                            $config["champs"][] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value2);
                        }
                    }else{
                        $config["champs"][] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                    }
                }
            }
        }

        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        $data_view['config'] = $config;
        $data_view['champs'] = $config['champs'];

        //Suppression du " and" en trop

        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');
        $this->load->model('DataV2_model');
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Redirection pour changer de base de données
            if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }
        $data_view['titre'] = "Générateur de CSV/EXCEL (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "exportcsv_excel";
        $this->load->view('template_header', $data_view);
        $this->load->view('exportcsv_excel', $data_view);
    }
    
    /**
    * Page principale pour voir les graphiques
    **/
    public function pie_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;
        $data_view['id'] = "";
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"] = [];
        $config["speedfilters"]=[];
        $config["operators"] = [];

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph,$getGroup_In_User) || $graph->type!="pie"){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);
            $config = $graphConfig;
            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['live'] = $graph->live;
            $data_view['group'] = $graph->group;
            if($this->input->get("wording") != null && $config['wording']!=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"))){
                unset($config['color']);
            }
            $data_view['id'] = $id;
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            //Ajout le champ maximum pour les anciennes config
            if(!isset($config["maximum"])){
                $config["maximum"]=0;
            }
            if(!isset($config["speedfilters"])){
                $config["speedfilters"]=[];
            }
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $graphConfig['bdd'];
                unset($config['bdd']);
            }
        }
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['engine'] = $data_view['source']->engine;
        if($this->input->get("wording") != null && $this->input->get("wording") != "nb"){
            $config["wording"] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config["wording"] = "";
        }
        if($this->input->get("mode") != null && $this->input->get("mode") != "nb"){
            $config["mode"] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("mode"));
        }else if($graph !=""){
        }else{
            $config["mode"] = "pie";
        }
        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }
        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }

        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
        }else if($graph !=""){
        }else{
            $config['maximum'] = 0;
        }
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }

        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }

        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }
        $data_view['config'] = $config;
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }
        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');
        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Redirection pour changer de base de données
            if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }

        $data_view['titre'] = "Générateur de diagramme circulaire (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "pie";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_pie', $data_view);
    }
    /**
     * Page principale pour voir les graphiques
     **/
    public function treemap_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $graph = "";
        $config = [];
        $data_view['id'] = "";
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"] = [];
        $config["speedfilters"]=[];
        $config["operators"] = [];

        //Mode edition de graphique
        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph, $getGroup_In_User) || $graph->type!="treemap"){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);
            $config = $graphConfig;
            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['live'] = $graph->live;
            $data_view['group'] = $graph->group;
            if($this->input->get("group") != null && $config['group']!=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"))){
                unset($config['color']);
            }
            $data_view['id'] = $id;
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $graphConfig['bdd'];
                unset($config['bdd']);
            }
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            //Ajout le champ maximum pour les anciennes config
            if(!isset($config["maximum"])){
                $config["maximum"]="";
            }
            if(!isset($config["speedfilters"])){
                $config["speedfilters"]=[];
            }
        }
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['engine'] = $data_view['source']->engine;
        if($this->input->get("wording") != null && $this->input->get("wording") != "nb"){
            $config["wording"] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("wording"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config["wording"] = "";
        }
        if(isset($_GET["group"])){
            $config["group"] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("group"));
        }else if($graph !=""){
        }else{
            $config["group"] = "";
        }

        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if($this->input->get("typecalculchamp") != null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }

        //$where = "";
        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }
        //Si minimum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
        }else if($graph !=""){
        }else{
            $config['maximum'] = 0;
        }
        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }
        $data_view['config'] = $config;
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }
        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');

        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url($data_view['source']->url) . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Redirection pour changer de base de données
            if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            if ($data_view['update_time_table'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_EXIST . "&errortable=" . $data_view['source']->name . ":" . $data_view['source']->database . "." . $data_view['source']->table, 'refresh');
                return;
            }
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }
        $data_view['titre'] = "Générateur de TreeMap (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "treemap";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_treemap', $data_view);
    }

    public function frame_generator($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin', 'createur']);
        $data_view = array();
        $email = $this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('User_model');
        $graph = "";
        $config = [];
        $data_view['id'] = "";
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $data_view['url'] = "";
        if ($data_view['user'] == null) {
            redirect(site_url() . "/user/logout", 'refresh');
        }

        if ($id != null) {
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if (!is_allowed_modify_graph($data_view['user'], $graph, $getGroup_In_User) || $graph->type != "treemap") {
                redirect(site_url() . "/Graph/", 'refresh');
            }
            $graphConfig = json_decode($graph->config, true);
            $config = $graphConfig;
            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['live'] = $graph->live;
            $data_view['group'] = $graph->group;
            $data_view['id'] = $id;
            $data_view['url'] = $graphConfig['url'];
        }


        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        //Mode edition de graphique


        $data_view['config'] = $config;

        $data_view['update_time_table'] = "";
        $data_view['titre'] = "Générateur de Frame";
        $data_view['type'] = "frame";
        $this->load->view('template_header', $data_view);
        $this->load->view('generator_frame', $data_view);
    }
    /**
    * Affichage d'un graphe enregistré
    **/
    public function index()
    {
        $this->listGraph();
    }

    /**
     * Affichage des graphes disponnible à l'utilisateur
     */
    public function listGraph(){
        user_redirect($this->session, site_url('/'), ['admin','createur','lecteur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        $data["search"] = ($this->input->get("search"))? $this->security->xss_clean($this->input->get("search")) : "";
        $data["database"] = ($this->input->get("database"))? $this->security->xss_clean($this->input->get("database")) : "";
        $data["type"] = ($this->input->get("type"))? $this->security->xss_clean($this->input->get("type")) : "";
        $data["order_name"] = ($this->input->get("order_name")) ? $this->security->xss_clean($this->input->get("order_name")) : "";
        $data["order_type"] = ($this->input->get("order_type")) ? $this->security->xss_clean($this->input->get("order_type")) : "";

        $this->load->model('User_model');
        $data['user'] = $this->User_model->getUserHalf($email);
        if($data['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data['getGroup_In_User'] = $this->User_model->getGroup_In_User($data['user']->id);
        if($data['getGroup_In_User']==null){
            $data['getGroup_In_User']=[];
        }
        $this->load->model('Graphs_model');
        $data['typeGet'] = [];
        if($this->input->get("type")!=null){
            $data['typeGet'] = $this->input->get("type");
        }
        $data['tableGet'] = [];
        if ($this->input->get("table") != null) {
            $data['tableGet'] = $this->input->get("table");
        }
        $data['userGet'] = [];
        if($this->input->get("userForm")!=null){
            $data['userGet'] = $this->input->get("userForm");
        }
        $data['groupGet'] = [];
        if($this->input->get("groupForm")!=null){
            $data['groupGet'] = $this->input->get("groupForm");
        }
        $data['nbGraph'] = $this->Graphs_model->countNbGraph($data["search"], false, $data['user']->id, $data['typeGet'], $data['tableGet'], $data['userGet'], $data['groupGet'], $data['getGroup_In_User']);
        //'/^\d*$/' si la chaine est un entier
        if($this->input->get("nbPage") != null && preg_match('/^\d*$/', $this->input->get("nbPage")) && ($this->input->get("nbPage")-1)*8<=$data['nbGraph']){
            $data['nbPage'] = $this->input->get("nbPage");
        }else{
            $data['nbPage'] = 1;
        }
        //ceil permet d'arrondir un nombre
        $data['nbPageMax'] = ceil($data['nbGraph']/8);
        $data['listGraph'] = $this->Graphs_model->listGraph($data["search"], false, $data['user']->id, $data['typeGet'], $data['tableGet'], $data['userGet'], $data['nbPage'], $data['groupGet'], $data['getGroup_In_User'], $data["order_name"], $data["order_type"]);
        $data['difftype'] = $this->Graphs_model->getDiffValueColumn("type");
        $data['difftable'] = $this->Graphs_model->getDiffTable();
        $data['diffgroup'] = $this->Graphs_model->getDiffValueColumn("group","group","id","name");
        $data['diffuser'] = $this->Graphs_model->getDiffValueColumn("user","user", "id", "email");
        $data['titre'] = "Liste des graphes";
        $data['type'] = "list";

        $this->load->view('template_header', $data);
        $this->load->view('listgraph', $data);
    }
    public function viewpie($id = NULL)
    {
        $this->viewGraph($id);
    }
    public function viewhistogram($id = NULL)
    {
        $this->viewGraph($id);
    }
    /**
    * Affichage d'un graphe enregistré
    **/
    public function viewGraph($id = NULL)
    {
        $this->load->helper('url');
        user_redirect($this->session, site_url('/?lasturl='.urlencode(current_url())), ['admin','createur','lecteur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        if($id==NULL){
            redirect(site_url()."/Graph/", 'refresh');
        }
        $this->load->model('User_model');
        $data['user'] = $this->User_model->getUserHalf($email);
        if($data['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $this->load->model('Graphs_model');
        $data['graph'] = $this->Graphs_model->getGraph($id);
        /**
         * Redirection si :
         * - le graphique n'existe pas
         * - L'utilisateur n'est pas le créateur du graphique et :
         *      - Le graphique n'est pas visible
         *      - L'utilisateur n'est pas du même groupe
         */
        if($data['graph']==null) {
            redirect(site_url() . "/Graph/", 'refresh');
        }
        if($data['graph']->user!=$data['user']->id){
            $getGroup_In_User = $this->User_model->getGroup_In_User($data['user']->id);
            if(!is_allowed_view_graph($data['user'],$data['graph'],$getGroup_In_User)){
                redirect(site_url() . "/Graph/", 'refresh');
            }

        }

        $data['id'] = $id;
        $data['titre'] = "Visualisation du graphe ".$data['graph']->name;
        $data['type'] = "Graph";
        $data['config'] = json_decode($data['graph']->config);
        //TMP
        if (!isset($data['config']->source)) {
            $data['config']->source = "local";
        }
        if (!isset($data['config']->database)) {
            $data['config']->database = "hadoopviewer_data";
        }
        if (!isset($data['config']->table)) {
            $data['config']->table = $data['config']->bdd;
            unset($data['config']->bdd);
        }
        $this->load->model('Database_model');
        $data['source'] = $this->Database_model->getAuthorizedData($data['config']->source, $data['config']->database, $data['config']->table);
        $data['update_time_table'] = "";
        if ($data['source'] == null) {
            if ($data['graph']->live) {
                //Message pour le mode live
                $data['graph']->live = 0;
                $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_ALLOWED;
                $data['update_time_table'] = "La table n'est plus autorisé !";
            }
        } else if ($data['source']->engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data['source']->url)) {

                $data['update_time_table'] = "ELK n'est pas disponible !";
            } else {
                $data['update_time_table'] = elastic_last_update($data['source']->url, $data['source']->table);
            }

        } else if ($data['graph']->live) {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data['source']->url);
            $data['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data['config']->database, $data['config']->table);
            if ($data['update_time_table'] == null) {
                $data['graph']->live = 0;
                $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_EXIST;
                $data['update_time_table'] = "";
            }
        }

        $data['ux'] = "";
        if ($this->input->get("ux") == "mini" || $this->input->get("ux") == "half") {
            $data['ux'] = $this->input->get("ux");
            $this->load->view('template_header_no_ux', $data);
        } else {
            $this->load->view('template_header', $data);
        }
        $this->load->view('view', $data);

    }
    public function viewtable1D($id = NULL){
        $this->viewTable($id);
    }
    public function viewtable2D($id = NULL){
        $this->viewTable($id);
    }
    /**
     * Affichage d'un graphe enregistré
     **/
    public function viewTable($id = NULL)
    {
        $this->load->helper('url');
        user_redirect($this->session, site_url('/?lasturl='.urlencode(current_url())), ['admin','createur','lecteur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        if($id==NULL){
            redirect(site_url()."Graph/", 'refresh');
        }
        $data = array();
        $this->load->model('User_model');
        $data['user'] = $this->User_model->getUserHalf($email);
        if($data['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $this->load->model('Graphs_model');
        $data['graph'] = $this->Graphs_model->getGraph($id);
        /**
         * Redirection si :
         * - le graphique n'existe pas
         * - L'utilisateur n'est pas le créateur du graphique et :
         *      - Le graphique n'est pas visible
         *      - L'utilisateur n'est pas du même groupe
         */
        if($data['graph']==null) {
            redirect(site_url() . "/Graph/", 'refresh');
        }
        if($data['graph']->user!=$data['user']->id){
            $getGroup_In_User = $this->User_model->getGroup_In_User($data['user']->id);
            if(!is_allowed_view_graph($data['user'],$data['graph'],$getGroup_In_User)){
                redirect(site_url() . "/Graph/", 'refresh');
            }

        }
        $data['titre'] = "Visualisation du tableau ".$data['graph']->name;
        $data['type'] = "Table";
        $data['config'] = json_decode($data['graph']->config);
        //TMP
        if (!isset($data['config']->source)) {
            $data['config']->source = "local";
        }
        if (!isset($data['config']->database)) {
            $data['config']->database = "hadoopviewer_data";
        }
        if (!isset($data['config']->table)) {
            $data['config']->table = $data['config']->bdd;
            unset($data['config']->bdd);
        }
        if ($data['config']->table == "expertmode") {
            $data['update_time_table'] = "";
        } else {
            $this->load->model('Database_model');
            $data['source'] = $this->Database_model->getAuthorizedData($data['config']->source, $data['config']->database, $data['config']->table);
            if ($data['source'] == null) {
                if ($data['graph']->live) {
                    //Message pour le mode live
                    $data['graph']->live = 0;
                    $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_ALLOWED;
                    $data['update_time_table'] = "La table n'est plus autorisé !";
                }
            } else if ($data['source']->engine == "elasticsearch") {
                $this->load->helper('elasticsearch');
                if (!elastic_check($data['source']->url)) {

                    $data['update_time_table'] = "ELK n'est pas disponible !";
                } else {
                    $data['update_time_table'] = elastic_last_update($data['source']->url, $data['source']->table);
                }

            } else if ($data['graph']->live) {
                $this->load->model('DataV2_model');
                $db = $this->DataV2_model->loadDatabase($data['source']->url);
                $data['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data['config']->database, $data['config']->table);
                if ($data['update_time_table'] == null) {
                    $data['graph']->live = 0;
                    $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_EXIST;
                    $data['update_time_table'] = "";
                }
            }

        }
        $data['ux'] = "";
        if ($this->input->get("ux") == "mini" || $this->input->get("ux") == "half") {
            $data['ux'] = $this->input->get("ux");
            $this->load->view('template_header_no_ux', $data);
        } else {
            $this->load->view('template_header', $data);
        }

        $this->load->view('viewTable', $data);

    }
    /**
     * Affichage d'un graphe enregistré
     * test
     **/
    public function viewTreeMap($id = NULL)
    {
        $this->load->helper('url');
        user_redirect($this->session, site_url('/?lasturl='.urlencode(current_url())), ['admin','createur','lecteur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        if($id==NULL){
            redirect(site_url()."/Graph/", 'refresh');
        }
        $data = array();
        $this->load->model('User_model');
        $data['user'] = $this->User_model->getUserHalf($email);
        if($data['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $this->load->model('Graphs_model');
        $data['graph'] = $this->Graphs_model->getGraph($id);
        /**
         * Redirection si :
         * - le graphique n'existe pas
         * - L'utilisateur n'est pas le créateur du graphique et :
         *      - Le graphique n'est pas visible
         *      - L'utilisateur n'est pas du même groupe
         */
        if($data['graph']==null) {
            redirect(site_url() . "/Graph/", 'refresh');
        }
        if($data['graph']->user!=$data['user']->id){
            $getGroup_In_User = $this->User_model->getGroup_In_User($data['user']->id);
            if(!is_allowed_view_graph($data['user'],$data['graph'],$getGroup_In_User)){
                redirect(site_url() . "/Graph/", 'refresh');
            }

        }



        $data['config'] = json_decode($data['graph']->config);
        //TMP
        if (!isset($data['config']->source)) {
            $data['config']->source = "local";
        }
        if (!isset($data['config']->database)) {
            $data['config']->database = "hadoopviewer_data";
        }
        if (!isset($data['config']->table)) {
            $data['config']->table = $data['config']->bdd;
            unset($data['config']->bdd);
        }
        $this->load->model('Database_model');
        $data['source'] = $this->Database_model->getAuthorizedData($data['config']->source, $data['config']->database, $data['config']->table);
        if ($data['source'] == null) {
            if ($data['graph']->live) {
                //Message pour le mode live
                $data['graph']->live = 0;
                $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_ALLOWED;
                $data['update_time_table'] = "La table n'est plus autorisé !";
            }
        } else if ($data['source']->engine == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data['source']->url)) {
                $data['update_time_table'] = "ELK n'est pas disponible !";
            } else {
                $data['update_time_table'] = elastic_last_update($data['source']->url, $data['source']->table);
            }

        } else if ($data['graph']->live) {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data['source']->url);
            $data['update_time_table'] = $data['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data['config']->database, $data['config']->table);
            if ($data['update_time_table'] == null) {
                $data['graph']->live = 0;
                $data['graph']->error_msg = ERROR_MSG_GRAPH_TABLE_NOT_EXIST;
                $data['update_time_table'] = "";
            }
        }
        $data['titre'] = "Visualisation du TreeMap ".$data['graph']->name;
        $data['type'] = "Table";
        $data['ux'] = "";
        if ($this->input->get("ux") == "mini" || $this->input->get("ux") == "half") {
            $data['ux'] = $this->input->get("ux");
            $this->load->view('template_header_no_ux', $data);
        } else {
            $this->load->view('template_header', $data);
        }
        $this->load->view('viewTreeMap', $data);

    }

    /**
     * Affichage d'un graphe enregistré
     * test
     **/
    public function viewFrame($id = NULL)
    {
        $this->load->helper('url');
        user_redirect($this->session, site_url('/?lasturl=' . urlencode(current_url())), ['admin', 'createur', 'lecteur']);
        $data = array();
        $email = $this->session->email;
        session_write_close();
        $this->load->helper('graph');
        if ($id == NULL) {
            redirect(site_url() . "/Graph/", 'refresh');
        }
        $data = array();
        $this->load->model('User_model');
        $data['user'] = $this->User_model->getUser($email);
        if ($data['user'] == null) {
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $this->load->model('Graphs_model');
        $data['graph'] = $this->User_model->getUserHalf($email);
        /**
         * Redirection si :
         * - le graphique n'existe pas
         * - L'utilisateur n'est pas le créateur du graphique et :
         *      - Le graphique n'est pas visible
         *      - L'utilisateur n'est pas du même groupe
         */
        if ($data['graph'] == null) {
            redirect(site_url() . "/Graph/", 'refresh');
        }
        if ($data['graph']->user != $data['user']->id) {
            $getGroup_In_User = $this->User_model->getGroup_In_User($data['user']->id);
            if (!is_allowed_view_graph($data['user'], $data['graph'], $getGroup_In_User)) {
                redirect(site_url() . "/Graph/", 'refresh');
            }

        }


        $data['config'] = json_decode($data['graph']->config);
        $data['update_time_table'] = date("Y-m-d H:i:s");
        $data['titre'] = "Visualisation de la Frame " . $data['graph']->name;
        $data['type'] = "Frame";
        $data['ux'] = "";
        if ($this->input->get("ux") == "min" || $this->input->get("ux") == "half") {
            $data['ux'] = $this->input->get("ux");
            $this->load->view('template_header_no_ux', $data);
        } else {
            $this->load->view('template_header', $data);
        }
        $this->load->view('viewFrame', $data);

    }
    /**
     * Duplication d'un graphe enregistré
     **/
    public function duplicate($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        if($id==NULL){
            redirect(site_url()."/Graph/", 'refresh');
        }

        $this->load->model('User_model');
        $this->load->model('Graphs_model');
        $user = $this->User_model->getUser($email);
        if($user==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $graph = $this->Graphs_model->getGraph($id);
        $data['getGroup_In_User'] = $this->User_model->getGroup_In_User($user->id);
        if(!is_allowed_duplicate_graph($user, $graph,$data['getGroup_In_User'])){
            redirect(site_url()."/Graph/", 'refresh');
        }

        $idfilesave = uniqid();
        $configJson = json_decode($graph->config, true);
        $oldidfilesave = "";
        if (isset($configJson['idfilesave'])) {
            $oldidfilesave = $configJson['idfilesave'];
        }
        $configJson['idfilesave'] = $idfilesave;
        $this->Graphs_model->duplicateGraph($id, $user, 'image_' . $idfilesave . '.png', json_encode($configJson));


        if ($oldidfilesave != "" && file_exists(GRAPH_FOLDER . $oldidfilesave . ".json")) {
            copy(GRAPH_FOLDER . $oldidfilesave . ".json", GRAPH_FOLDER . $idfilesave . ".json");
        } else {
            write_file(GRAPH_FOLDER . $idfilesave . ".json", $graph->script);
        }
        if (($graph->image_name != null || $graph->image_name != "") && file_exists('./uploads/' . $graph->image_name)) {

            copy('./uploads/' . $graph->image_name, './uploads/' . 'image_' . $idfilesave . '.png');
        }
        if(isset($_SERVER['HTTP_REFERER']) && strpos( $_SERVER['HTTP_REFERER'], site_url("graph/")."?search=" ) !== false){
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }else{
            redirect(site_url("graph/"), 'refresh');
        };
    }
    /**
    * Suppression d'un graphe enregistré
    **/
    public function delete($id = NULL)
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data = array();
        $email=$this->session->email;
        session_write_close();
        if($id==NULL){
            redirect(site_url()."/Graph/", 'refresh');
        }

        $this->load->model('User_model');
        $this->load->model('Graphs_model');
        $user = $this->User_model->getUser($email);
        if($user==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $graph = $this->Graphs_model->getGraph($id);
        $getGroup_In_User = $this->User_model->getGroup_In_User($user->id);
        if(!is_allowed_modify_graph($user, $graph, $getGroup_In_User)){
            redirect(site_url()."/Graph/", 'refresh');
        }
        $this->load->helper('file');
        if(($graph->image_name !=null || $graph->image_name !="") && file_exists('./uploads/'.$graph->image_name)){

            unlink('./uploads/'.$graph->image_name);
        }
        $config = json_decode($graph->config, true);
        if (($config['idfilesave'] != null || $config['idfilesave'] != "") && file_exists(GRAPH_FOLDER . $config['idfilesave'] . ".save")) {
            unlink(GRAPH_FOLDER . $config['idfilesave'] . ".save");
        }

        $this->Graphs_model->deleteGraph($id);
        if(isset($_SERVER['HTTP_REFERER']) && strpos( $_SERVER['HTTP_REFERER'], site_url("graph/")."?search=" ) !== false){
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }else{
            redirect(site_url("graph/"), 'refresh');
        };
    }
    /**
    * Enregistrement du graphe
    **/
    public function saveGraph()
    {
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        ini_set('memory_limit', '4G');
        ini_set('max_input_vars', '1G');
        ini_set('post_max_size', '1G');
        ini_set('upload_max_filesize', '1G');
        $data = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        if (!$this->input->post("config")) {
            echo "no config";
            return;
        }
        $configDe = json_decode($this->input->post("config"), true);
        if ($this->input->post("type") == "frame") {
            $user = $this->User_model->getUser($email);
            $public = ($this->input->post("public") == "on") ? 1 : 0;
            $live = ($this->input->post("live") == "on") ? 1 : 0;
            $nameFile = $this->input->post("image_name");
            $group = $this->input->post("group");
            $config = $this->input->post("config");
            if ($this->input->post("id") != "" && $this->input->post("id") != null) {
                $this->Graphs_model->updateGraph($this->input->post("id"), $this->input->post("name"), $this->input->post("type"),
                    $this->input->post("description"), "web", $config, "frame", "", "", $public, $live, $nameFile, $group);
            } else {
                $this->Graphs_model->saveGraph($this->input->post("name"), $this->input->post("type"),
                    $this->input->post("description"), "web", $config, $user->id, "frame", "", "", $public, $live, $nameFile, $group);
            }

        } else {
            //Ne sauvegarde pas le graphique s'il n'y a pas de donnée
            if (!isset($configDe['idfilesave'])) {
                echo "script is []";
                //redirect(site_url('graph/'), 'refresh');
                return;
            }


            $user = $this->User_model->getUser($email);
            if ($user == null) {
                echo "l'utilisateur n'existe pas";
                //redirect(site_url() . "/user/logout", 'refresh');
            }

            $public = ($this->input->post("public") == "on") ? 1 : 0;
            $live = ($this->input->post("live") == "on") ? 1 : 0;
            $nameFile = $this->input->post("image_name");
            $group = $this->input->post("group");
            $config = $this->input->post("config");

            if ($this->input->post("id") != "" && $this->input->post("id") != null) {
                $this->Graphs_model->updateGraph($this->input->post("id"), $this->input->post("name"), $this->input->post("type"),
                    $this->input->post("description"), "", $config, $configDe['source'], $configDe['database'], $configDe['table'], $public, $live, $nameFile, $group);
            } else {
                $this->Graphs_model->saveGraph($this->input->post("name"), $this->input->post("type"),
                    $this->input->post("description"), "", $config, $user->id, $configDe['source'], $configDe['database'], $configDe['table'], $public, $live, $nameFile, $group);
            }
            if (!is_dir('uploads')) {
                mkdir('./uploads', 0777, true);
            }
            if ($this->input->post("image")) {
                list(, $data) = explode(',', $this->input->post("image"));
                $data = base64_decode($data);
                write_file('./uploads/' . $nameFile, $data);
            }
            if (is_file(GRAPH_FOLDER . $configDe['idfilesave'] . ".tmp")) {
                rename(GRAPH_FOLDER . $configDe['idfilesave'] . ".tmp", GRAPH_FOLDER . $configDe['idfilesave'] . ".json");
            } else if (!is_file(GRAPH_FOLDER . $configDe['idfilesave'] . ".json")) {
                echo "file tmp not exist";
            }
        }

        redirect(site_url('graph/'), 'refresh');
    }
}