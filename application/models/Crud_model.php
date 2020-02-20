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
    
    function UpdateBalance($codeActif, $date, $codeRegate, $statutBalance, $db, $table, $idEntiteFromEntite, $idEntiteFromBalance){
        $id = "";
        if($idEntiteFromEntite != $idEntiteFromBalance){
            $id = $idEntiteFromEntite;
        }else{
            $id = $idEntiteFromBalance;
        }
        $data = array(
            'dateVerification' => $date,
            'codeRegate' => $codeRegate,
            'statutVerification' => $statutBalance,
            'idEntite' => $id
        );
        $this->db->db_select($db);
        $this->db->where('codeActif', $codeActif);
        $this->db->update($table, $data);
    }
    
    function Create($db, $table, $data){
        $this->db->db_select($db);
        $this->db->insert($table, $data);
    }
}