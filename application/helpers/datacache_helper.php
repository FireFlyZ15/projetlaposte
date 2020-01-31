<?php
if ( ! function_exists('save_in_cache')){
    /**
     * Sauvegarde les données généré dans un cache
     * @param $context Mettre $this
     * @param $nameFile Identifiant du cache
     * @param $json Contenu des données à mettre en cache
     * @param $updateMode true : Modifier la valeur en BDD | false : inseré la valeur en bdd
     * @param $timestamp_database Timestamp de la derniere modification de la base de donnée
     */
    function save_in_cache($context, $nameFile, $source, $database, $table, $json, $timestamp_database, $updateMode)
    {
        if (!is_dir(CACHE_FOLDER))
        {
            mkdir(CACHE_FOLDER, 0777, true);
        }
        $context->load->model('Cache_model');
        //Mise en cache du resultat
        if($updateMode){
            //Mise en cache du resultat
            write_file(CACHE_FOLDER.$nameFile, $json,'w');
            $context->Cache_model->updateDataCache($nameFile, $timestamp_database);
        }else{
            write_file(CACHE_FOLDER.$nameFile, $json,'w');
            $context->Cache_model->saveDataCache($nameFile, $source, $database, $table, $timestamp_database);
        }
    }
}

if (!function_exists('save_graph')) {
    /**
     * Sauvegarde temporairement les données générées d'un graphique
     * @param $id Identifiant du cache
     * @param $json Contenu des données à mettre en cache
     */
    function save_graph($id, $json)
    {
        if (!is_dir(FILE_FOLDER)) {
            mkdir(FILE_FOLDER, 0777, true);
        }
        if (!is_dir(GRAPH_FOLDER)) {
            mkdir(GRAPH_FOLDER, 0777, true);
        }
        write_file(GRAPH_FOLDER . $id . ".tmp", $json, 'w');
    }
}

if (!function_exists('purge_old_graph_tmp')) {
    /**
     * Netoyage des anciens graphiques qui n'ont pas été sauvegardés
     */
    function purge_old_graph_tmp()
    {

        $fileList = get_dir_file_info(GRAPH_FOLDER);
        foreach ($fileList as $file) {
            //Suppression des fichiers tmp qui ont été créé il y plus de 12H
            if (preg_match("/.tmp$/", $file['name']) && (time() - $file['date']) >= 43200) {
                unlink(GRAPH_FOLDER . $file['name']);
            }
        }
    }
}
if ( ! function_exists('check_validity_data_cache')){
    /**
     * Verification si un cache existe pour le traitement demandé et s'il est toujours valide
     * @param $cache ligne MySQL d'information concernant le cache
     * @param $timestamp_database Contenu des données à mettre en cache
     * @param $nameFile Identifiant du cache
     * @return Boolean true : Modifier la valeur en BDD | false : inséré la valeur en bdd
     */
    function check_validity_data_cache($cache,$timestamp_database,$nameFile) {
        $value=false;
        if($cache!=null){
            $cache_time = strtotime($cache->date)+CACHE_DELAY;
            $now_timetamp = time();
            if(($cache->date>=$timestamp_database || $cache_time>=$now_timetamp) && get_file_info(CACHE_FOLDER.$nameFile)){
                $value=true;
            }
        }
        return $value;
    }
}

if ( ! function_exists('get_cache')){
    /**
     * Va chercher le fichier contenant le cache demandé
     * @param $nameFile Identifiant du cache
     * @return String Donnéee du cache en chaine de caractère
     */
    function get_cache($nameFile) {
        return read_file(CACHE_FOLDER.$nameFile);
    }
}

if ( ! function_exists('generate_id_cache')){
    /**
     * Génération d'un identifiant à partir d'une chaine de caractère
     * @param $name Chaine de caractère contenant des informations sur votre requête
     * @return string Chaine de caractère Hashé
     */
    function generate_id_cache($name) {
        return hash(ALGO_HASH_CACHE_KEY,$name);
    }
}