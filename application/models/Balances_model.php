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
}