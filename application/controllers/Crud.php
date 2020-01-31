<?php

class Crud extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('table');
        $this->load->helper('url');
    }
    
    
    public function index(){
        
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Balances_model');
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
        $this->db->db_select($data_view['source']->database);
        $data_view['query'] = $this->db->query('SELECT * FROM '.$data_view['source']->table);
        $data_view['test'] = $this->DataV2_model->getAll($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['statuts'] = $this->Balances_model->getAllStatut($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['titre'] = "Ajout/Modification/Suppression des données de la BDD (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "crud";
        $this->load->view('template_header', $data_view);
        $this->load->view('crud', $data_view);
    }
    
    public function add(){
        $this->load->model('Crud_model');
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $config = [];
        $data_view = [];
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('codeActif', 'codeActif', 'required');
        $email = $this->session->email;
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        if($this->form_validation->run() == FALSE){
            $config['source'] = $data_view['user']->actual_source;
            $config['database'] = $data_view['user']->actual_database;
            $config['table'] = $data_view['user']->actual_table;
            $this->load->view('crud_ajout');
        }else{
            $config['source'] = $data_view['user']->actual_source;
            $config['database'] = $data_view['user']->actual_database;
            $config['table'] = $data_view['user']->actual_table;
            $key = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
            $data['config'] = $config;
            $data = array();
            foreach ($key as $row) {
                $data[$row->name] = $this->input->post($row->name);
            }
            $data['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            $this->Crud_model->form_insert($data, $data_view['source']->database, $data_view['source']->table);
            $data['message'] = 'Data Inserted Successfully';
            $this->load->view('crud_ajout', $data);
        }
    }
    
    public function show(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $key = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $order = $this->input->post("order");
        $search= $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if(!empty($order))
        {
            foreach($order as $o)
            {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }
        
        if($dir !="asc" && $dir != "desc")
        {
            $dir = "desc";
        }
        $valid_columns = array();
        $i = 0;
        foreach($key as $row){
            $valid_columns[$i] = $row->name;
            $i++;
        }
        if(!isset($valid_columns[$col]))
        {
            $order = null;
        }
        else
        {
            $order = $valid_columns[$col];
        }
        $this->db->db_select($data_view['source']->database);
        if($order != null)
        {
            $this->db->order_by($order, $dir);
        }
        
        if(!empty($search))
        {
            $x = 0;
            foreach($valid_columns as $sterm)
            {
                if($x==0)
                {
                    $this->db->like($sterm, $search);
                }
                else
                {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);
        $element = $this->db->get($data_view['source']->table);
        $data = array();
        foreach($element->result() as $rows)
        {
            $data[] = array(
            "id" => $rows->id,
            "codeActif" => $rows->codeActif,
            "codeArticle" => $rows->codeArticle,
            "codeRegate" => $rows->codeRegate,
            "numeroSerie" => $rows->numeroSerie,
            "statut" => $rows->statut,
            "dateVerification" => $rows->dateVerification,
            "localisation" => $rows->localisation,
            "utilisation" => $rows->utilisation,
            "tranche" => $rows->tranche,
            "idEntite" => $rows->idEntite,
            "idModele" => $rows->idModele,
            );
        }
        $total = $this->total();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function total()
    {
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        
        $email = $this->session->email;
        $this->db->db_select('hadoopviewer');
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        
        $this->db->db_select($data_view['source']->database);
        $query = $this->db->select("COUNT(*) as num")->get($data_view['source']->table);
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }
    
    public function update(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->model('Crud_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        
        //Update
        $this->Crud_model->Update($id, $field, $value, $db, $table);
        
        
        echo 1;
        exit;
    }
    
    public function delete(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->model('Crud_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        $id = $this->input->post('id');
        
        //Update
        $this->Crud_model->Delete($id, $db, $table);
        
        echo $id;
        echo $db;
        echo $table;
        echo 1;
        exit;
    }
}