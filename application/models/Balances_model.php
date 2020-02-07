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
        $this->db->db_select($db);
        $this->db->select("id");
        $this->db->from($database.".".$table);
        $this->db->where('libelle', $libelle);
        return $this->db->get()->result();
    }
    
    public function getIdEntite($db, $database, $table, $code)
    {
        $this->db->db_select($db);
        return $this->db->select("id")->from($database.".".$table)->where("codeRegate", $code)->get()->result();
    }
}