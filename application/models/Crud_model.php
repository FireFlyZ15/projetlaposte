<?php

class Crud_model extends CI_Model{
    
    function __construct(){
        parent::__construct();
    }
    
    function form_insert($data, $db, $table){
        $this->db->db_select($db);
        $this->db->insert($table, $data);
    }
    
    function Delete($id, $db, $table){
        $this->db->db_select($db);
        $this->db->where('id', $id);
        $this->db->delete($table);
    }
    
    function Update($id, $field, $value, $db, $table){
        $data = array($field => $value);
        $this->db->db_select($db);
        $this->db->where('id', $id);
        $this->db->update($table, $data);
    }
}