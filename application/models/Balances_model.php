<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Graphs_model
 * Contient les requetes SQL qui vont permettre de gérer les graphiques
 */
class Balances_model extends CI_Model
{
    private static $table_balance = 'balance';

    public function getAllStatut($db, $database, $table)
    {
        return $db->select("statut")->distinct()->from($database . "." .$table)->get()->result();
    }
    
    public function getAllIdModele($db, $database, $table)
    {
        return $db->select("libelle")->distinct()->from($database . "." .$table)->get()->result();
    }
    
    public function getAllIdEntite($db, $database, $table)
    {
        return $db->select("codeRegate")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getAllIdBalance($db, $database, $table)
    {
        return $db->select("codeActif")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getAllTranche($db, $database, $table)
    {
        return $db->select("tranche")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getAllUtilisation($db, $database, $table)
    {
        return $db->select("utilisation")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getIdModele($db, $database, $table, $libelle)
    {
        $this->db->select("id");
        $this->db->from($database.".".$table);
        $this->db->where('libelle', $libelle);
        return $this->db->get()->result();
    }
    
    public function getIdBalance($db, $database, $table, $codeActif)
    {
        $this->db->select("id");
        $this->db->from($database.".".$table);
        $this->db->where('codeActif', $codeActif);
        return $this->db->get()->result();
    }
    
    public function getIdEntiteWithCodeActif($db, $database, $table, $codeActif)
    {
        $this->db->select("idEntite");
        $this->db->from($database.".".$table);
        $this->db->where('codeActif', $codeActif);
        return $this->db->get()->result();
    }
    
    public function getCodeArticle($db, $database, $table)
    {
        $this->db->select("codeArticle");
        $this->db->from($database.".".$table);
        return $this->db->get()->result();
    }
    
    public function getIdEntite($db, $database, $table, $code)
    {
        $this->db->db_select($db);
        return $this->db->select("id")->from($database.".".$table)->where("codeRegate", $code)->get()->result();
    }
    
    public function getIdLot($db, $database, $table, $code)
    {
        $this->db->db_select($db);
        return $this->db->select("id")->from($database.".".$table)->where("numeroLot", $code)->get()->result();
    }
    
    public function getCodeActif($db, $database, $table)
    {
        return $this->db->select("codeActif")->from($database.".".$table)->get()->result();
    }
    
    public function getHistorique($db, $database, $table)
    {
        return $this->db->select("*")->from($database.".".$table)->get()->result();
    }
}