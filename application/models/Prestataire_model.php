<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Graphs_model
 * Contient les requetes SQL qui vont permettre de gérer les graphiques
 */
class Prestataire_model extends CI_Model
{
    public function getAllLot($db, $database, $table)
    {
        return $db->select("numerolot")->distinct()->from($database.".".$table)->get()->result();
    }
    public function getAllIdEntite($db, $database, $table)
    {
        return $db->select("codeRegate")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getAllPrestataire($db, $database, $table)
    {
        return $db->select("libelle")->distinct()->from($database.".".$table)->get()->result();
    }
    
    public function getIdPrestataire($db, $database, $table, $codeRegate)
    {
        $this->db->select("id");
        $this->db->from($database.".".$table);
        $this->db->where('libelle', $codeRegate);
        return $this->db->get()->result();
    }
}