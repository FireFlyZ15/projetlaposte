<?php
if (!function_exists('elastic_result_to_json')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_result_to_json($root, $champs, $config, $position = 0)
    {
        $array = [];
        if ($position == 0) {
            if (!isset($root->aggregations)) {
                //Mauvais resultat de la part de elasticsearch
                return $array;
            }
            $root = $root->aggregations;
        }
        $date_mode = false;
        $value_array = explode('::', $champs[$position]);
        $champ_value = $value_array[0];
        if (isset($value_array[1]) && in_array($value_array[1], ['YEAR', 'MONTH', 'YEARMONTH', 'DAY', 'DATE', 'DATEHOUR'])) {
            $champ_type_value = $value_array[1];
            $date_mode = true;
        }

        if (isset($root->$champ_value)) {
            foreach ($root->$champ_value->buckets as $key => $value) {

                if (count($champs) == $position + 1) {
                    $arrayChild = [];
                    if ($date_mode) {
                        $time = $value->key / 1000;
                        if (in_array($value_array[1], ['YEAR'])) {
                            $arrayChild[$champs[$position]] = date("Y", $time);
                        } else if (in_array($value_array[1], ['MONTH', 'YEARMONTH'])) {
                            $arrayChild[$champs[$position]] = date("Y-m", $time);
                        } else if ($value_array[1] == "DATEHOUR") {
                            $arrayChild[$champs[$position]] = date("Y-m-dTH:00:00Z", $time);
                        } else {
                            $arrayChild[$champs[$position]] = date("Y-m-d", $time);
                        }

                    } else {
                        $arrayChild[$champs[$position]] = $value->key;
                    }

                    $arrayKey = $config['typecalcul'] . "(" . $config['typecalculchamp'] . ")";
                    if ($config['typecalcul'] == "count") {
                        $arrayChild[$arrayKey] = $value->doc_count;
                    } else {
                        $arrayChild[$arrayKey] = $value->result->value;
                    }
                    $array[] = $arrayChild;
                }
                if (count($champs) > $position + 1) {
                    $value_array2 = explode('::', $champs[$position + 1]);
                    if (isset($value->$value_array2[0])) {
                        $array2 = elastic_result_to_json($value, $champs, $config, $position + 1);
                        for ($i = 0; $i < count($array2); $i++) {
                            if ($date_mode) {
                                $time = $value->key / 1000;
                                if (in_array($value_array[1], ['YEAR'])) {
                                    $array2[$i][$champs[$position]] = date("Y", $time);
                                } else if (in_array($value_array[1], ['MONTH', 'YEARMONTH'])) {
                                    $array2[$i][$champs[$position]] = date("Y-m", $time);
                                } else if ($value_array[1] == "DATEHOUR") {
                                    $array2[$i][$champs[$position]] = date("Y-m-dTH:00:00Z", $time);
                                } else {
                                    $array2[$i][$champs[$position]] = date("Y-m-d", $time);
                                }
                            } else {
                                $array2[$i][$champs[$position]] = $value->key;
                            }
                        }
                        $array = array_merge($array, $array2);
                    }
                }

            }


        }
        return $array;
    }
}

if (!function_exists('elastic_result_to_json_diffvalue')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_result_to_json_diffvalue($root, $champ)
    {
        $array = [];
        if (!isset($root->aggregations)) {
            return $array;
        }
        $root = $root->aggregations;
        $date_mode = false;
        $value_array = explode('::', $champ);
        $champ_value = $value_array[0];
        if (isset($value_array[1]) && in_array($value_array[1], ['YEAR', 'MONTH', 'YEARMONTH', 'DAY', 'DATE', 'DATEHOUR'])) {
            $champ_type_value = $value_array[1];
            $date_mode = true;
        }

        if (isset($root->$champ_value)) {
            foreach ($root->$champ_value->buckets as $key => $value) {
                $arrayChild = [];
                if ($date_mode) {
                    $time = $value->key / 1000;
                    if (in_array($value_array[1], ['YEAR'])) {
                        $arrayChild["value"] = date("Y", $time);
                    } else if (in_array($value_array[1], ['MONTH', 'YEARMONTH'])) {
                        $arrayChild["value"] = date("Y-m", $time);
                    } else if ($value_array[1] == "DATEHOUR") {
                        $arrayChild["value"] = date("Y-m-dTH:00:00Z", $time);
                    } else {
                        $arrayChild["value"] = date("Y-m-d", $time);
                    }

                } else {
                    $arrayChild["value"] = $value->key;
                }

                $array[] = $arrayChild;
            }


        }
        return $array;
    }
}

if (!function_exists('elastic_map_list')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_map_list($url)
    {
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        curl_close($ch);
        $result = json_decode($actualResponse);
        $databaseinfo = [];
        foreach ($result as $key => $value) {
            if (strpos($key, ':') == false && $key[0] != ".") {
                $databaseinfo[$key] = [];
                if (isset($value->mappings)) {
                    foreach ($value->mappings as $keyType => $valueType) {
                        foreach ($value->mappings->$keyType->properties as $keyC => $valueC) {
                            if ($keyC[0] != "@") {
                                // echo $keyC." : ".$valueC->type."<br>";
                                $arr = new stdClass();
                                $arr->name = $keyC;
                                $arr->type = $valueC->type;
                                $arr->type_mapping = $keyType;
                                $databaseinfo[$key][] = $arr;
                            }
                        }
                    }

                }
            }
        }
        return $databaseinfo;
    }
}

if (!function_exists('elastic_last_update')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_last_update($url, $index)
    {
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $param = new stdClass();
        $param->aggs = new stdClass();
        $param->aggs->max_timetamp = new stdClass();
        $param->aggs->max_timetamp->max = new stdClass();
        $param->aggs->max_timetamp->max->field = "@timestamp";
        $param = json_encode($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $index . "/_search?size=0");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        curl_close($ch);
        $result = json_decode($actualResponse);
        $databaseinfo = [];

        //foreach ($result as $key=>$value){
        //    if(strpos($key, ':')==false && $key[0]!="."){
        //        $databaseinfo[$key] = [];
        //        foreach ($value->mappings->data->properties as $keyC=>$valueC){
        //            // echo $keyC." : ".$valueC->type."<br>";
        //            $arr = new stdClass();
        //            $arr->name=$keyC;
        //            $arr->type=$valueC->type;
        //            $databaseinfo[$key][] = $arr;
        //        }
//
        //    }
//
        //}
        return date("Y-m-d H:i:s", $result->aggregations->max_timetamp->value / 1000);
    }
}

if (!function_exists('elastic_diff_value')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_diff_value($url, $index, $column, $debug = false)
    {
        $type = "_search";
        $param = new stdClass();
        $param->size = 0;
        $param->_source = new stdClass();
        $param->_source->excludes = [];
        $param->aggs = new stdClass();
        $var = $param->aggs;
        $value_array = explode('::', $column);
        $value = $value_array[0];
        if (isset($value_array[1]) && in_array($value_array[1], ['YEAR', 'MONTH', 'YEARMONTH', 'DAY', 'DATE', 'DATEHOUR'])) {
            $value_type = $value_array[1];
            $var->$value = new stdClass();
            $var->$value->date_histogram = new stdClass();
            $var->$value->date_histogram->field = $value;
            if (in_array($value_type, ['YEAR'])) {
                $var->$value->date_histogram->interval = "1y";
            } else if (in_array($value_type, ['MONTH', 'YEARMONTH'])) {
                $var->$value->date_histogram->interval = "1M";
            } else if ($value_type == "DATEHOUR") {
                $var->$value->date_histogram->interval = "1h";
            } else {
                $var->$value->date_histogram->interval = "1d";
            }
            $var->$value->date_histogram->time_zone = "Europe/Paris";
            $var->$value->date_histogram->min_doc_count = 1;
            //$var->$value->date_histogram->order = new stdClass();
            $var->$value->aggs = new stdClass();
            $var = $var->$value->aggs;
        } else {
            $var->$value = new stdClass();
            $var->$value->terms = new stdClass();
            $var->$value->terms->field = $value;
            $var->$value->terms->size = 999999999;
            $var->$value->aggs = new stdClass();
            $var->$value->terms->missing = "NULL";
            $var->$value->terms->order = new stdClass();
            $var->$value->terms->order->_term = "asc";
            $var = $var->$value->aggs;
        }

        $param = json_encode($param);
        if ($debug) {
            echo "<h1>Paramètres</h1>";
            echo ELASTIC_URL . $index . "/" . $type . "<br/>";
            echo $param;
        }
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $index . "/" . $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        curl_close($ch);
        $result = json_decode($actualResponse);
        if ($debug) {
            echo "<h1>Résultat</h1>";
            echo $actualResponse;
        }
        $array = elastic_result_to_json_diffvalue($result, $column);
        echo json_encode($array);
    }
}

if (!function_exists('elastic_getdataraw')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_getdataraw($url, $index, $config, $debug)
    {
        $type = "_search";

        $param = new stdClass();
        $param->size = 0;
        $param->_source = new stdClass();
        $param->_source->excludes = [];
        $param->aggs = new stdClass();
        $var = $param->aggs;
        $champs = array_keys(array_count_values(array_merge($config['champs'], $config['speedfilters'])));
        //print_r($champs);
        foreach ($champs as $key => $value) {
            $value_array = explode('::', $value);
            $value = $value_array[0];
            if (isset($value_array[1]) && in_array($value_array[1], ['YEAR', 'MONTH', 'YEARMONTH', 'DAY', 'DATE', 'DATEHOUR'])) {
                $value_type = $value_array[1];
                $var->$value = new stdClass();
                $var->$value->date_histogram = new stdClass();
                $var->$value->date_histogram->field = $value;
                if (in_array($value_array[1], ['YEAR'])) {
                    $var->$value->date_histogram->interval = "1y";
                } else if (in_array($value_array[1], ['MONTH', 'YEARMONTH'])) {
                    $var->$value->date_histogram->interval = "1M";
                } else if ($value_array[1] == "DATEHOUR") {
                    $var->$value->date_histogram->interval = "1h";
                } else {
                    $var->$value->date_histogram->interval = "1d";
                }
                $var->$value->date_histogram->time_zone = "Europe/Paris";
                $var->$value->date_histogram->min_doc_count = 1;
                //$var->$value->date_histogram->order = new stdClass();
                $var->$value->aggs = new stdClass();
                if ($config['typecalcul'] == "count") {
                    //$var->$value->date_histogram->order->_count = "desc";
                } else {
                    //$var->$value->date_histogram->order->result = "desc";

                    $var->$value->aggs->result = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul'] = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul']->field = $config['typecalculchamp'];
                }
                $var = $var->$value->aggs;
            } else {
                $var->$value = new stdClass();
                $var->$value->terms = new stdClass();
                $var->$value->terms->field = $value;
                #$var->$value->terms->field = $value . ".keyword";
                $var->$value->terms->size = 999999999;
                $var->$value->terms->order = new stdClass();
                $var->$value->aggs = new stdClass();
                if ($config['typecalcul'] == "count") {
                    $var->$value->terms->order->_count = "desc";
                } else {
                    $var->$value->terms->order->result = "desc";

                    $var->$value->aggs->result = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul'] = new stdClass();
                    $var->$value->aggs->result->$config['typecalcul']->field = $config['typecalculchamp'];
                }
                $var->$value->terms->missing = "NULL";
                $var = $var->$value->aggs;
            }
        }

        if ($config["filtres"] != []) {
            $param->query = new stdClass();
            $param->query->bool = new stdClass();
            $where = [];
            foreach ($config["operators"] as $key => $value) {
                if (!isset($where[$value])) {
                    $where[$value] = [];
                }

                $where[$value][$key] = [];
                foreach ($config["filtres"][$key] as $keyF => $valueF) {
                    $where[$value][$key][] = $valueF;
                }
            }
            foreach ($where as $keyType => $valueType) {
                if ($keyType == "exclure") {
                    if (!isset($param->query->bool->must_not)) {
                        $param->query->bool->must_not = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must_not[] = $champ;
                    }
                } else if ($keyType == "inclure") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must[] = $champ;
                    }
                } else if ($keyType == "date") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }
                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->range = new stdClass();
                        $champ->range->date = new stdClass();
                        //Date min
                        if ($valueC[0] != "") {
                            $champ->range->date->gte = $valueC[0];

                        } else {
                            $champ->range->date->gt = null;
                        }
                        //Date max
                        if ($valueC[1] != "") {
                            $champ->range->date->lte = $valueC[1];
                        } else {
                            $champ->range->date->lte = null;
                        }
                        $champ->range->date->time_zone = "Europe/Paris";
                        $param->query->bool->must[] = $champ;
                    }
                }
            }

        }

        $param = json_encode($param);
        if ($debug == "debug") {
            echo "<h2>Requete</h2>";
            echo $url . $index . "/" . $type . "<br>";
            print_r($param);
            echo "<br><br>";
        }

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $index . "/" . $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponseHeaders = (isset($info["header_size"])) ? substr($response, 0, $info["header_size"]) : "";
        //print_r($actualResponseHeaders);
        //echo "<br><br>";
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        if ($debug == "debug") {
            echo "<h2>Resultat brut elasticsearch</h2>";
            print_r($actualResponse);
            echo "<br><br>";
        }
        curl_close($ch);
        $result = json_decode($actualResponse);

        //print_r($champs);
        //echo "<br><br>";

        $array = elastic_result_to_json($result, $champs, $config, 0);
        return json_encode($array);
    }
}
if (!function_exists('elastic_sample')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_sample($url, $index, $config, $nb, $debug)
    {
        $type = "_search";
        if ($nb > 500 || $nb <= 0) {
            $nb = 500;
        }
        $param = new stdClass();
        $param->size = $nb;
        $param->_source = new stdClass();
        $param->_source->excludes = [];
        $param->aggs = new stdClass();

        if ($config["filtres"] != [] || $config["filtres2"] != []) {

            $param->query = new stdClass();
            $param->query->bool = new stdClass();
            $where = [];
            foreach ($config["operators"] as $key => $value) {
                if (!isset($where[$value])) {
                    $where[$value] = [];
                }

                $where[$value][$key] = [];
                foreach ($config["filtres"][$key] as $keyF => $valueF) {
                    $where[$value][$key][] = $valueF;
                }
            }
            foreach ($config["filtres2"] as $keyF => $valueF) {
                $keyF_array = explode('::', $keyF);
                if (count($keyF_array) == 2) {
                    if (!isset($where["dateF2"])) {
                        $where["dateF2"] = [];
                    }
                    $where["dateF2"][$keyF][] = $valueF;
                } else {
                    $where["inclure"][$keyF_array[0]][] = $valueF;
                }

            }
            foreach ($where as $keyType => $valueType) {
                if ($keyType == "exclure") {
                    if (!isset($param->query->bool->must_not)) {
                        $param->query->bool->must_not = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must_not[] = $champ;
                    }
                } else if ($keyType == "inclure") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $match_phrase = new stdClass();
                            $match_phrase->match_phrase = new stdClass();
                            $nameKeyElastic = $keyC . ".keyword";
                            $match_phrase->match_phrase->$nameKeyElastic = $valueV;
                            $champ->bool->should[] = $match_phrase;
                        }
                        $param->query->bool->must[] = $champ;
                    }
                } else if ($keyType == "date") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }
                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->range = new stdClass();
                        $champ->range->date = new stdClass();
                        //Date min
                        if ($valueC[0] != "") {
                            $champ->range->date->gte = $valueC[0];

                        } else {
                            $champ->range->date->gt = null;
                        }
                        //Date max
                        if ($valueC[1] != "") {
                            $champ->range->date->lte = $valueC[1];
                        } else {
                            $champ->range->date->lte = null;
                        }
                        $champ->range->date->time_zone = "Europe/Paris";
                        $param->query->bool->must[] = $champ;
                    }
                } else if ($keyType == "dateF2") {
                    if (!isset($param->query->bool->must)) {
                        $param->query->bool->must = [];
                    }

                    foreach ($valueType as $keyC => $valueC) {
                        $champ = new stdClass();
                        $champ->bool = new stdClass();
                        $champ->bool->should = [];
                        foreach ($valueC as $keyV => $valueV) {
                            $keyV_array = explode('::', $keyC);
                            if ($keyV_array[1] == "DATE") {
                                $champ = new stdClass();
                                $champ->range = new stdClass();
                                $champ->range->date = new stdClass();
                                $champ->range->date->gte = $valueV . "||/d";
                                $champ->range->date->lte = $valueV . "||/d";

                                $champ->range->date->time_zone = "Europe/Paris";
                                $param->query->bool->must[] = $champ;
                            } else if ($keyV_array[1] == "YEAR") {
                                $champ = new stdClass();
                                $champ->range = new stdClass();
                                $champ->range->date = new stdClass();
                                $champ->range->date->gte = $valueV . "||/y";
                                $champ->range->date->lte = $valueV . "||/y";

                                $champ->range->date->time_zone = "Europe/Paris";
                                $param->query->bool->must[] = $champ;
                            } else if ($keyV_array[1] == "YEARMONTH") {
                                $champ = new stdClass();
                                $champ->range = new stdClass();
                                $champ->range->date = new stdClass();
                                $champ->range->date->gte = $valueV . "||/M";
                                $champ->range->date->lte = $valueV . "||/M";

                                $champ->range->date->time_zone = "Europe/Paris";
                                $param->query->bool->must[] = $champ;
                            }

                        }
                        $param->query->bool->must[] = $champ;
                    }
                }
            }

        }

        $param = json_encode($param);

        if ($debug == "debug") {
            echo "<h2>Requete</h2>";
            echo $url . $index . "/" . $type;
            echo "<br>";
            print_r($param);
            echo "<br><br>";
        }

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $index . "/" . $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponseHeaders = (isset($info["header_size"])) ? substr($response, 0, $info["header_size"]) : "";
        //print_r($actualResponseHeaders);
        //echo "<br><br>";
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        if ($debug == "debug") {
            echo "<h2>Resultat brut elasticsearch</h2>";
            print_r($actualResponse);
            echo "<br><br>";
        }
        curl_close($ch);
        $result = json_decode($actualResponse);
        $column_date = [];
        foreach (elastic_map_list($url . $index . "/_mapping")[$index] as $key => $value) {
            if ($value->type == "date") {
                $column_date[] = $value->name;
            }
        }
        $resultJson = [];
        //Permet la modification des timezones qui est par defaut sur UTC sur les système elasticsearch
        $input_timezone = new DateTimeZone('UTC');
        $output_timezone = new DateTimeZone('Europe/Berlin');
        foreach ($result->hits->hits as $value) {
            $ligne = new stdClass();
            foreach ($value->_source as $keyC => $valueC) {

                if (in_array($keyC, $column_date)) {
                    $ligne->$keyC = new DateTime($valueC, $input_timezone);
                    $ligne->$keyC->setTimezone($output_timezone);
                    $ligne->$keyC = $ligne->$keyC->format('Y-m-d H:i:s');
                } else if ($keyC[0] != "@") {
                    $ligne->$keyC = $valueC;
                }
            }
            $resultJson[] = $ligne;
        }

        //$array = elastic_result_to_json($result, $champs, $config, 0);
        return json_encode($resultJson);
    }
}

if (!function_exists('elastic_check')) {
    /**
     * Transforme le resultat de elasticsearch vers un json classique
     * @param $xml
     * @return mixed
     */
    function elastic_check($url)
    {
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $actualResponse = (isset($info["header_size"])) ? substr($response, $info["header_size"]) : "";
        curl_close($ch);
        if ($info['download_content_length'] != -1 && isset($actualResponse)) {
            $json = json_decode($actualResponse);
            if (isset($json->version->lucene_version)) {
                return true;
            }
        }
        return false;
    }
}