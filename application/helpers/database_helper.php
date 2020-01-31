<?php
if ( ! function_exists('generateFilter')){
    /**
     * @param $filtres
     * @param $dataColumn
     * @param $operators
     */
    function generateFilter($filtres, $dataColumn, $operators,$db)
    {
        foreach ($filtres as $column_key => $column_value) {
            $db->group_start();

            foreach ($dataColumn as $key_info => $column_info) {
                if ($column_info->name == $column_key && ($column_info->type == "bit" || $column_info->type == "tinyint" || $column_info->type == "smallint" || $column_info->type == "mediumint" || $column_info->type == "int" || $column_info->type == "bigint")) {

                    foreach ($column_value as $key2 => $value2) {
                        $column_value[$key2] = intval($value2);
                    }

                    break;
                }
            }
            if (isset($operators[$column_key]) && $operators[$column_key] == "date") {
                if ($column_value["min"] != "") {
                    $db->where("date(" . $column_key . ')>=', $column_value["min"]);
                }
                if ($column_value["max"] != "") {
                    $db->where("date(" . $column_key . ')<=', $column_value["max"]);
                }
            } else if (isset($operators[$column_key]) && $operators[$column_key] == "exclure") {
                $key = array_search("NULL", $column_value, true);
                if ($key !== false) {
                    unset($column_value[$key]);
                    if (count($column_value) > 0) {
                        $db->where_not_in($column_key, $column_value);
                        $db->where($column_key . " is not null");
                    } else {
                        $db->where($column_key . " is not null");
                    }

                } else {
                    $db->where_not_in($column_key, $column_value);
                }
            } else {
                //Verification si "NULL" est dans la liste des choix
                $key = array_search("NULL", $column_value, true);
                if ($key !== false) {
                    //gestion du "NULL" pour mysql
                    unset($column_value[$key]);
                    if (count($column_value) > 0) {
                        $db->where_in($column_key, $column_value);
                        $db->or_where($column_key, null);
                    } else {
                        $db->where($column_key, null);
                    }
                } else {
                    $db->where_in($column_key, $column_value);


                }
            }

            $db->group_end();
        }
    }
}

if (!function_exists('mysql_check')) {
    /**
     * Verifie si le serveur mysql est contactable
     * @param $xml
     * @return mixed
     */
    function mysql_check($root, $url)
    {
        try {
            $root->db1 = $root->load->database($url, true);
        } catch (Exception $e) {
            return false;
        }
        //print_r($this->db1->conn_id);
        if ($root->db1->conn_id) {
            $test = $root->db1->select('*')
                ->from("information_schema.tables")
                ->get()
                ->result();
            return true;
            //print_r($test);
        } else {
            return false;
        }
    }
}

if (!function_exists('mysql_check_by_id')) {
    /**
     * Verifie si le serveur mysql est contactable depuis un id
     * @param $xml
     * @return mixed
     */
    function mysql_check_by_id($root, $id)
    {
        if ($id == "local") {
            return true;
        }
        $root->load->model('Database_model');
        $url_raw = $root->Database_model->getBdd($id);
        if ($url_raw == null) {
            return false;
        }
        $url = $url_raw->url;
        if ($url_raw->url == null) {
            return false;
        }
        try {
            $root->db1 = $root->load->database($url, true);
        } catch (Exception $e) {
            return false;
        }
        if ($root->db1->conn_id) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('mysql_get_url_by_id')) {
    /**
     * Verifie si le serveur mysql est contactable
     * @param $xml
     * @return mixed
     */
    function mysql_get_url_by_id($root, $id)
    {
        if ($id == "local") {
            return "local";
        }
        $root->load->model('Database_model');
        $url_raw = $root->Database_model->getBdd($id);
        if ($url_raw == null) {
            return false;
        }
        $url = $url_raw->url;
        if ($url_raw->url == null) {
            return false;
        }
        return $url;

    }
}
if (!function_exists('mysql_get_url_by_name')) {
    /**
     * Recupération de l'url de la base de donnée à partir du nom
     * @param $xml
     * @return mixed
     */
    function mysql_get_url_by_name($root, $name)
    {
        if ($name == "local") {
            return "local";
        }
        $root->load->model('Database_model');
        $url_raw = $root->Database_model->getBddByName($name);
        if ($url_raw == null) {
            return false;
        }
        $url = $url_raw->url;
        if ($url_raw->url == null) {
            return false;
        }
        return $url;

    }
}