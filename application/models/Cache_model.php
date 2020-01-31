<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Cache_model
 * Contiens les requêtes SQL qui vont permettre de générer le système de cache
 */
class Cache_model extends CI_Model
{
    /**
     * Sauvegarde des informations sur le nouveau cache
     * @param $id Identifiant du cache
     * @param $source Source de données concernée par le cache
     * @param $database Base de données concernée par le cache
     * @param $table Table concernée par le cache
     * @param $date Date des données
     */
    public function saveDataCache($id, $source, $database, $table, $date)
    {
        $this->load->database();
        $this->db->set('id', $id)
            ->set('date', $date)
            ->set('source', $source)
            ->set('database', $database)
            ->set('table', $table)
            ->insert('data_cache');
    }

    /**
     * Mise à jour de la date du cache pour un identifiant
     * @param $id Identifiant du cache
     * @param $date Date des données
     */
    public function updateDataCache($id, $date)
    {
        $this->load->database();
        $data = array(
            'date' => $date
        );
        $this->db->where('id', $id);
        $this->db->update('data_cache', $data);
    }

    /**
     * Récupération de l'information disponible pour l'identifiant du cache demandé
     * @param $id Identifiant du cache
     * @return mixed Information sur le cache
     */
    public function getDataCache($id)
    {
        $this->load->database();
        return $this->db->get_where("data_cache", array('id' => $id))->row();
    }

    /**
     * Affiche tous les caches disponibles
     * @return mixed Liste MySQL contenant les données sur le cache disponible
     */
    public function getListCache()
    {
        $this->load->database();
        return $this->db->select("*")
            ->from("data_cache")
            ->get()
            ->result();
    }

    /**
     * Suppression de toutes les lignes de la table data_cache
     */
    public function removeAllCache()
    {
        $this->load->database();
        $this->db->empty_table("data_cache");
    }

    /**
     * Supprime dans la table data_cache la ligne concernant le cache choisi
     * @param $id Identifiant du cache
     */
    public function removeCache($id)
    {
        $this->load->database();
        $this->db->where('id', $id);
        $this->db->delete("data_cache");
    }
}
