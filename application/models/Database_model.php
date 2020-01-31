<?php

/**
 * Class Database_model
 * Contiens les requêtes SQL qui vont permettre de générer les sources de données et les tables autorisées sur le site
 */
class Database_model extends CI_Model
{

    /**
     * Affiche toutes les sources de données
     * @return mixed Liste MySQL contenant les données sur les sources de données
     */
    public function getListBdd()
    {
        $this->load->database();
        return $this->db->select("*")
            ->from("database")
            ->get()
            ->result();
    }

    /**
     * Récupération des informations de la base de données voulus
     * @return mixed Résultat de la requête
     */
    public function getBdd($id)
    {
        $this->load->database();
        return $this->db->get_where("database", array('id' => $id))->row();
    }

    /**
     * Récupération des informations de la base de données voulus
     * @return mixed Résultat de la requête
     */
    public function getBddByName($name)
    {
        $this->load->database();
        return $this->db->get_where("database", array('name' => $name))->row();
    }

    /**
     * Affiche toutes les tables autorisées
     * @return mixed Tableau contenant les données sur les bases de données
     */
    public function getListAuthorizedData()
    {
        $this->load->database();
        $data = $this->db->select("*")
            ->from("authorized_table")
            ->order_by('source', 'ASC')
            ->order_by('database', 'ASC')
            ->order_by('table', 'ASC')
            ->get()
            ->result();
        $result = [];
        $lastSource = "";
        $lastDatabase = "";
        foreach ($data as $row) {
            if ($row->source != $lastSource) {
                $result[$row->source] = [];
                $lastSource = $row->source;
                $lastDatabase = "";
            }
            if ($lastDatabase != $row->database) {
                $result[$row->source][$row->database] = [];
                $lastDatabase = $row->database;

            }
            $result[$row->source][$row->database][$row->table] = [];
            $result[$row->source][$row->database][$row->table]['calc_type'] = $row->calc_type;
            $result[$row->source][$row->database][$row->table]['default_colomn'] = $row->default_colomn;
        }
        return $result;
    }

    /**
     * Affiche toutes les tables autorisé avec des informations sur la source de données
     * @return mixed Tableau contenant les données sur les bases de données
     */
    public function getListAuthorizedDataExpert()
    {
        $this->load->database();
        $data = $this->db->select("*, database.engine, database.name")
            ->from("authorized_table")
            ->join("database", "database.id = authorized_table.source")
            ->order_by('name', 'ASC')
            ->order_by('database', 'ASC')
            ->order_by('table', 'ASC')
            ->get()
            ->result();
        $result = [];
        foreach ($data as $row) {
            if (!array_key_exists($row->source, $result)) {
                $result[$row->source] = new stdClass();
                $result[$row->source]->engine = $row->engine;
                $result[$row->source]->source_name = $row->name;
                $result[$row->source]->tables = [];
            }
            $result[$row->source]->tables[] = array(
                'database' => $row->database,
                'table' => $row->table,
                'calc_type' => $row->calc_type,
                'default_colomn' => $row->default_colomn
            );
        }
        return $result;
    }

    /**
     * Affiche toutes les tables autorisé avec des informations sur la source de donnée
     * @return mixed Liste MySQL contenant les données sur les bases de données
     */
    public function getAuthorizedData($source, $database, $table)
    {
        $this->load->database();
        return $this->db->select("*")
            ->from("authorized_table")
            ->join("database", "database.id = authorized_table.source")
            ->where("authorized_table.source", $source)
            ->where("table", $table)
            ->where("database", $database)
            ->get()
            ->row();
    }

    /**
     * Suppression de toutes les tables autorisées pour une source
     * @param $source Identifiant de la source
     * @return mixed Résultat de la requête
     */
    public function removeAllAuthorizedDataForSource($source)
    {
        $this->load->database();
        $this->db->where('source', $source)
            ->delete("authorized_table");
    }

    /**
     * Ajoute un lot de nouvelle table à autoriser
     * @param $data Liste de données à ajouter [[source, database, table, calc_type, default_colomn],[]]
     */
    public function insertBatchAllAuthorizedDataForSource($data)
    {
        $this->db->insert_batch('authorized_table', $data);
    }

    /**
     * Sauvegarde de la nouvelle base de donnée
     * @param $name nom de la base de donnée
     * @param $engine Type de base de donnée
     * @param $url Url de connexion pour la base de donnée
     */
    public function saveBDD($name, $engine, $url)
    {
        $this->load->database();
        $this->db->set('id', uniqid("ds_", true))
            ->set('name', $name)
            ->set('engine', $engine)
            ->set('url', $url)
            ->insert('database');
    }

    /**
     * Supprime dans la table database la ligne concernant la base de donnée choisi
     * @param $id Identifiant de la base de données
     */
    public function removeBDD($id)
    {
        $this->load->database();
        $this->db->where('id', $id);
        $this->db->delete("database");
    }

}