<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Graphs_model
 * Contient les requetes SQL qui vont permettre de gérer les graphiques
 */
class Entite_model extends CI_Model
{
    public function getAllType($db, $database, $table)
    {
        return $db->select("type")->distinct()->from($database.".".$table)->get()->result();
    }
}