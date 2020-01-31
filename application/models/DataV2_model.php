<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class DataV2_model
 * Contient les requetes SQL qui vont permettre de générer les graphiques à partir de n'importe quel base de donnée MySQL
 */
class DataV2_model extends CI_Model
{
    /**
     * Permet d'ouvrir une connexion vers la base de donnée à requêter.
     * @param string $url L'URL de connexion vers la base de données (mysql://totoro:mon_voisin@localhost).
     * S'il n’y a aucun paramètre, la base de données configurée dans config/database.php sera utilisé.
     * @return mixed Connexion à la source de donnée à utiliser
     */
    public function loadDatabase($url = "local")
    {
        if ($url == "local") {
            $this->load->database();
            return $this->db;
        } else {
            return $this->load->database($url, true);
        }
    }

    /**
     * Affichage des bases de données et des tables contenu dans la source de donnée
     * Les bases créé pour MySQL (mysql, performance_schema, information_schema) et pour ce site (hadoopviewer)
     * ne sont pas affiché car ils contiennent des données très sensibles (mot de passes)
     * @param $db Connexion à la source de donnée à utiliser
     * @return mixed Résultat de la requête
     */
    public function getDatabaseInfo($db)
    {
        if ($db == null) {
            return;
        }
        return $db->select('TABLE_SCHEMA, TABLE_NAME, TABLE_TYPE, TABLE_ROWS, CREATE_TIME, UPDATE_TIME')
            ->from("information_schema.tables")
            ->where_not_in("TABLE_SCHEMA", ['mysql', 'performance_schema', 'information_schema', 'hadoopviewer', 'hadoopviewer_recette'])
            ->order_by('TABLE_SCHEMA', 'ASC')
            ->order_by('TABLE_NAME', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Affichage d'informations sur la table selectionnée
     * @param $db Connexion à la source de donnée à utiliser
     * @param $database Base de donnée de la table
     * @param $table Nom de la table à utiliser
     * @return mixed Résultat de la requête
     */
    public function getColumnsNameExpert($db, $database, $table)
    {
        return $db->field_data($database . "." . $table);
    }

    /**
     * Récupération de la date de création et de mise à jour des données de la table selectionnée
     * @param $db Connexion à la source de donnée à utiliser
     * @param $database Base de donnée de la table
     * @param $table Nom de la table à utiliser
     * @return mixed Résultat de la requête
     */
    public function getUpdateTime($db, $database, $table)
    {
        $result = $db->select('CREATE_TIME, UPDATE_TIME')
            ->from("information_schema.tables")
            ->where("TABLE_SCHEMA = '" . $database . "' AND TABLE_NAME = '" . $table . "'")
            ->get()
            ->result();
        if (count($result) == 0) {
            return null;
        } else {
            return ($result[0]->UPDATE_TIME != "") ? $result[0]->UPDATE_TIME : $result[0]->CREATE_TIME;
        }

    }

    /**
     * Affiche les données differentes pour une colonne d'une table
     * @param $db Connexion à la source de donnée à utiliser
     * @param $database Base de données de la table
     * @param $table Nom de la table à utiliser
     * @param $column Nom de la colonne à analyser
     * @return mixed Résultat de la requête
     */
    public function getDiffValueColumn($db, $database, $table, $column)
    {
        //Récupération des informations sur la table
        $columnName = $this->DataV2_model->getColumnsNameExpert($db, $database, $table);
        $column_select = $column;
        //Mise au format date des timestamp pour éviter de tuer le serveur SQL
        foreach ($columnName as $row) {
            if ($row->type == "timestamp" && $row->name == $column) {
                $column_select = "DATE(`$column`)";
            }
        }
        return $db->select($column_select . " as value")
            ->from($database . "." . $table)
            ->group_by("value")
            ->order_by("value")
            ->get()
            ->result();
    }
    
    public function getAll($db ,$database, $table){
        return $db->select("*")->from($database . "." . $table)->get()->result();
    }

    /**
     * Affiche toutes les données qui respectent les filtres mis en place
     * pour la génération de graphique pour les histogrammes et les diagrammes circulaires
     * @param $db Connexion à la source de donnée à utiliser
     * @param $config Configuration de la requête
     * @return mixed Résultat de la requête
     */
    public function getData($db, $config)
    {
        $sql_libelle_raw = $config['sql_libelle'];
        $filtres = $config['filtres'];
        $minimum = $config['minimum'];
        $maximum = $config['maximum'];
        $database = $config['database'];
        $table = $config['table'];
        $typecalcul = $config['typecalcul'];
        $typecalculchamp = $config['typecalculchamp'];
        $dataColumn = $config['resultColumn'];
        $operators = $config['operators'];
        if (isset($config['speedfilters'])) {
            $speedfilters = $config['speedfilters'];
        } else {
            $speedfilters = [];
        }
        $formatChamps = [];
        foreach ($dataColumn as $row) {
            $formatChamps[$row->name] = $row->type;
        }

        if (isset($config['sql_type']) && $config['sql_type'] != "") {
            $sql_type_array = explode('::', $config['sql_type']);
            $sql_type = $sql_type_array[0];
            if (isset($sql_type_array[1])) {
                $sql_type_type = $sql_type_array[1];
            } else {
                $sql_type_type = "";
            }
            foreach ($dataColumn as $row) {
                if (($row->type == "timestamp" || $row->type == "datetime" || $row->type == "date") && $sql_type != "couleur" && $row->type == "timestamp" && $row->name == $sql_type) {
                    if ($sql_type_type == "YEAR" || $sql_type_type == "MONTH" || $sql_type_type == "DAY" || $sql_type_type == "DATE") {
                        $sql_type = $sql_type_type . "(`$sql_type`)";
                    } else if ($sql_type_type == "YEARMONTH") {
                        $sql_type = "DATE_FORMAT(`$sql_type`, '%Y-%m')";
                    } else if ($sql_type_type == "DATEHOUR") {
                        $sql_type = "DATE_FORMAT(`$sql_type`, '%Y-%m-%dT%H:00:00Z')";
                    } else {
                        $sql_type = "DATE(`$sql_type`)";
                    }

                }
            }
        } else {
            $sql_type = "'couleur'";
        }

        if (is_array($sql_libelle_raw)) {
            foreach ($sql_libelle_raw as $value) {
                $sql_libelle_array = explode('::', $value);
                $sql_libelle_tmp = $sql_libelle_array[0];
                if (isset($sql_libelle_array[1])) {
                    $sql_libelle_type = $sql_libelle_array[1];
                } else {
                    $sql_libelle_type = "";
                }
                foreach ($dataColumn as $row) {

                    if (($row->type == "timestamp" || $row->type == "datetime" || $row->type == "date") && $row->name == $sql_libelle_tmp) {
                        if ($sql_libelle_type == "YEAR" || $sql_libelle_type == "MONTH" || $sql_libelle_type == "DAY" || $sql_libelle_type == "DATE") {
                            $sql_libelle_tmp = $sql_libelle_type . "(`$sql_libelle_tmp`)";
                        } else if ($sql_libelle_type == "YEARMONTH") {
                            $sql_libelle_tmp = "DATE_FORMAT(`$sql_libelle_tmp`, '%Y-%m')";
                        } else if ($sql_libelle_type == "DATEHOUR") {
                            $sql_libelle_tmp = "DATE_FORMAT(`$sql_libelle_tmp`, '%Y-%m-%dT%H:00:00Z')";
                        } else {
                            $sql_libelle_tmp = "DATE(`$sql_libelle_tmp`)";
                        }

                    }
                }

                $sql_libelles[] = $sql_libelle_tmp;

            }
            $sql_libelle = "CONCAT(";
            foreach ($sql_libelles as $key => $value) {
                if ($key == 0) {
                    $sql_libelle .= $value;
                } else {
                    $sql_libelle .= ",' - '," . $value;
                }

            }
            $sql_libelle .= ")";
        } else {
            $sql_libelle_array = explode('::', $sql_libelle_raw);
            $sql_libelle = $sql_libelle_array[0];
            if (isset($sql_libelle_array[1])) {
                $sql_libelle_type = $sql_libelle_array[1];
            } else {
                $sql_libelle_type = "";
            }

            foreach ($dataColumn as $row) {

                if (($row->type == "timestamp" || $row->type == "datetime" || $row->type == "date") && $row->name == $sql_libelle) {
                    if ($sql_libelle_type == "YEAR" || $sql_libelle_type == "MONTH" || $sql_libelle_type == "DAY" || $sql_libelle_type == "DATE") {
                        $sql_libelle = $sql_libelle_type . "(`$sql_libelle`)";
                    } else if ($sql_libelle_type == "YEARMONTH") {
                        $sql_libelle = "DATE_FORMAT(`$sql_libelle`, '%Y-%m')";
                    } else if ($sql_libelle_type == "DATEHOUR") {
                        $sql_libelle = "DATE_FORMAT(`$sql_libelle`, '%Y-%m-%dT%H:00:00Z')";
                    } else {
                        $sql_libelle = "DATE(`$sql_libelle`)";
                    }

                }
            }
        }


        if (($typecalcul == "COUNT" || $typecalcul == "count") && $typecalculchamp == "") {
            $typecalculchamp = "*";
        } else if ($typecalcul == "COUNT" || $typecalcul == "count") {
            $typecalculchamp = "DISTINCT " . $typecalculchamp;
        }
        $db->select("$sql_libelle as libellesGraph, $sql_type as typesGraph, $typecalcul($typecalculchamp) as nb");

        $db->from($database . "." . $table)
            ->group_by("libellesGraph, typesGraph")
            ->order_by("libellesGraph");

        foreach ($speedfilters as $speedfilter) {
            $champ_array = explode('::', $speedfilter);
            $champ_libelle = $champ_array[0];
            if (isset($champ_array[1])) {
                $champ_type = $champ_array[1];
            } else {
                $champ_type = "";
            }
            if ($formatChamps[$champ_libelle] == "timestamp" || $formatChamps[$champ_libelle] == "date") {

                if ($champ_type == "YEAR" || $champ_type == "MONTH" || $champ_type == "DAY" || $champ_type == "DATE") {
                    $sql_type = $champ_type . "(`$champ_libelle`)";
                } else if ($champ_type == "YEARMONTH") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m')";
                } else if ($champ_type == "DATEHOUR") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m-%dT%H:00:00Z')";
                } else {
                    $sql_type = "DATE(`$champ_libelle`)";
                }
                $db->select($sql_type . " as '$speedfilter'");
                $db->group_by($sql_type);
            } else {
                $db->select($speedfilter);
                $db->group_by($speedfilter);
            }
        }

        $this->load->helper('database');
        generateFilter($filtres, $dataColumn, $operators, $db);

        if ($minimum > 0) {
            $db->having('nb >= ' . $minimum);
        }
        if ($maximum > 0 && $maximum != "") {
            $db->having("$typecalcul($typecalculchamp) <= $maximum");
        }
        return $db->get()->result();
    }

    /**
     * Affiche toutes les données qui respectent les filtres mis en place
     * pour la génération de tableaux et de treemap
     * @param $db Connexion à la source de donnée à utiliser
     * @param $config Configuration de la requête
     * @return mixed Résultat de la requête
     */
    public function getDataRAW($db, $config)
    {
        $filtres = $config['filtres'];
        if (isset($config['speedfilters'])) {
            $speedfilters = $config['speedfilters'];
        } else {
            $speedfilters = [];
        }
        $champs = $config['champs'];
        $minimum = $config['minimum'];
        $maximum = $config['maximum'];
        $database = $config['database'];
        $table = $config['table'];
        $typecalcul = $config['typecalcul'];
        $typecalculchamp = $config['typecalculchamp'];
        $dataColumn = $config['resultColumn'];
        $operators = $config['operators'];
        $formatChamps = [];
        foreach ($dataColumn as $row) {
            $formatChamps[$row->name] = $row->type;
        }
        if ($typecalcul == "count" && $typecalculchamp == "") {
            $typecalculchamp = "*";
        } else if ($typecalcul == "COUNT" || $typecalcul == "count") {
            $typecalculchamp = "DISTINCT " . $typecalculchamp;
        }

        foreach ($champs as $champ) {
            $champ_array = explode('::', $champ);
            $champ_libelle = $champ_array[0];
            if (isset($champ_array[1])) {
                $champ_type = $champ_array[1];
            } else {
                $champ_type = "";
            }
            if ($formatChamps[$champ_libelle] == "timestamp" || $formatChamps[$champ_libelle] == "date") {

                if ($champ_type == "YEAR" || $champ_type == "MONTH" || $champ_type == "DAY" || $champ_type == "DATE") {
                    $sql_type = $champ_type . "(`$champ_libelle`)";
                } else if ($champ_type == "YEARMONTH") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m')";
                } else if ($champ_type == "DATEHOUR") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m-%dT%H:00:00Z')";
                } else {
                    $sql_type = "DATE(`$champ_libelle`)";
                }
                $db->select($sql_type . " as '$champ'");
                $db->group_by($sql_type);
            } else {
                $db->select($champ);
                $db->group_by($champ);
            }

        }
        foreach ($speedfilters as $speedfilter) {
            $champ_array = explode('::', $speedfilter);
            $champ_libelle = $champ_array[0];
            if (isset($champ_array[1])) {
                $champ_type = $champ_array[1];
            } else {
                $champ_type = "";
            }
            if ($formatChamps[$champ_libelle] == "timestamp" || $formatChamps[$champ_libelle] == "date") {

                if ($champ_type == "YEAR" || $champ_type == "MONTH" || $champ_type == "DAY" || $champ_type == "DATE") {
                    $sql_type = $champ_type . "(`$champ_libelle`)";
                } else if ($champ_type == "YEARMONTH") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m')";
                } else if ($champ_type == "DATEHOUR") {
                    $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m-%dT%H:00:00Z')";
                } else {
                    $sql_type = "DATE(`$champ_libelle`)";
                }
                $db->select($sql_type . " as '$speedfilter'");
                $db->group_by($sql_type);
            } else {
                $db->select($speedfilter);
                $db->group_by($speedfilter);
            }
        }
        $db->select("$typecalcul($typecalculchamp) as '$typecalcul($typecalculchamp)'");
        $db->from($database . "." . $table);

        $this->load->helper('database');
        generateFilter($filtres, $dataColumn, $operators, $db);

        if ($minimum > 0) {
            $db->having("$typecalcul($typecalculchamp) >= $minimum");
        }
        if ($maximum > 0 && $maximum != "") {
            $db->having("$typecalcul($typecalculchamp) <= $maximum");
        }
        return $db->get()->result();
    }

    /**
     * Affiche 100 lignes de la table
     * @param $db Connexion à la source de donnée à utiliser
     * @param $database Base de données de la table
     * @param $table Nom de la table à utiliser
     * @return mixed Résultat de la requête
     */
    public function getSampleData($db, $database, $table)
    {
        return $db->select('*')
            ->from($database . "." . $table)
            ->limit(100)
            ->get()
            ->result();
    }

    /**
     * Affiche toutes les données qui respectent les filtres mis en place
     * @param $db Connexion à la source de donnée à utiliser
     * @param $config Configuration de la requête
     * @param bool $limit Mode limitation à 500 lignes au maximum
     * @return mixed Résultat de la requête
     */
    public function getAllData($db, $config, $limit = false)
    {
        $filtres = $config['filtres'];
        $filtres2 = $config['filtres2'];
        $database = $config['database'];
        $table = $config['table'];
        $operators = $config['operators'];
        $nbocc = $config['nbocc'];
        $dataColumn = $config['resultColumn'];
        $formatChamps = [];


        $db->select("*");
        $db->from($database . "." . $table);

        $this->load->helper('database');
        generateFilter($filtres, $dataColumn, $operators, $this->db);

        foreach ($filtres2 as $column_key => $column_value) {
            /*if($column_value=="NULL"){
                $this->db->where($column_key." is null");
            }else{
                $this->db->where($column_key,$column_value);
            }*/
            $db->group_start();

            foreach ($dataColumn as $key_info => $column_info) {
                if ($column_info->name == $column_key && ($column_info->type == "bit" || $column_info->type == "tinyint" || $column_info->type == "smallint" || $column_info->type == "mediumint" || $column_info->type == "int" || $column_info->type == "bigint")) {
                    if (is_array($column_value)) {
                        foreach ($column_value as $key2 => $value2) {
                            $column_value[$key2] = (int)$value2;
                        }
                    } else {
                        $column_value = (int)$column_value;
                    }

                    break;
                }
            }

            if (!is_array($column_value)) {
                $column_array = explode('::', $column_key);
                $champ_libelle = $column_array[0];
                if (isset($column_array[1])) {
                    $champ_type = $column_array[1];
                    if ($champ_type == "YEAR" || $champ_type == "MONTH" || $champ_type == "DAY" || $champ_type == "DATE") {
                        $sql_type = $champ_type . "(`$champ_libelle`)";
                    } else if ($champ_type == "YEARMONTH") {
                        $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m')";
                    } else if ($champ_type == "DATEHOUR") {
                        $sql_type = "DATE_FORMAT(`$champ_libelle`, '%Y-%m-%dT%H:00:00Z')";
                    } else {
                        $sql_type = "DATE(`$champ_libelle`)";
                    }
                    $db->where("$sql_type = '$column_value'");
                } else if ($column_value == "null" || $column_value == "NULL") {
                    $db->where($column_key . " is null");
                } else if ($column_value == "vide" || $column_value == "VIDE") {
                    $db->where($column_key, "");
                } else {
                    $db->where($column_key, $column_value);
                }


            }

            $db->group_end();
        }
        if ($limit && ($nbocc > 500 || $nbocc < 0)) {
            $db->limit(500);
        } else {
            $db->limit($nbocc);
        }
        return $db->get()->result();
    }

    /**
     * Permets de lancer une requête SQL directement sur la source de donnée
     * @param $db Connexion à la source de donnée à utiliser
     * @param $request La requete SQL à lancer
     * @return mixed Résultat de la requête
     */
    public function getDataExpert($db, $request)
    {
        return $db->query($request)->result();

    }
}
