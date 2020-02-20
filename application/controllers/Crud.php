<?php

class Crud extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('table');
        $this->load->helper('url');
    }
    
    
    public function ajout(){
        
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        $config = [];
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        if($data_view['user']->actual_table == 'commande' || $data_view['user']->actual_table == 'tarifverificationdeplacement' || $data_view['user']->actual_table == 'reporting_prestataire' || $data_view['user']->actual_table == 'tarif' || $data_view['user']->actual_table == 'test' || $data_view['user']->actual_table == 'A_verifier' || $data_view['user']->actual_table == 'historique')
        {
            redirect(site_url()."user/myaccount", 'refresh');
        }

        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        
        $data_view['config'] = $config;
        
        $this->db->db_select('projetlaposte');
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $data_view['table'] = $this->db->list_tables();
        $data_view['test'] = $this->DataV2_model->getAll($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['test2'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        if($data_view['source']->table == 'balance'){
            $data_view['statuts'] = $this->Balances_model->getAllStatut($db, $data_view['source']->database, $data_view['source']->table);
            $data_view["idModele"] = $this->Balances_model->getAllIdModele($db, $data_view['source']->database, "modele");
            $data_view["idEntite"] = $this->Balances_model->getAllIdEntite($db, $data_view['source']->database, "entite");
            $data_view["utilisations"] = $this->Balances_model->getAllUtilisation($db, $data_view['source']->database, "balance");
            $data_view["tranches"] = $this->Balances_model->getAllTranche($db, $data_view['source']->database, "balance");
        }
        if($data_view['source']->table == 'entite'){
            $data_view["typeEntite"] = $this->Entite_model->getAllType($db, $data_view['source']->database, $data_view['source']->table);
            $data_view['idPrestataire'] = $this->Prestataire_model->getAllPrestataire($db, $data_view['source']->database, 'prestataire');
        }
        if($data_view['source']->table == 'prixdeplacement'){
            $data_view['idPrestataire'] = $this->Prestataire_model->getAllPrestataire($db, $data_view['source']->database, 'prestataire');
        }
        if($data_view['source']->table == 'prixverification'){
            $data_view['idPrestataire'] = $this->Prestataire_model->getAllPrestataire($db, $data_view['source']->database, 'prestataire');
        }
        if($data_view['source']->table == 'prestataire' || $data_view['source']->table == 'entite'){
            $data_view["idLot"] = $this->Prestataire_model->getAllLot($db, $data_view['source']->database, 'lot');
        }
        if($data_view['source']->table == 'verification'){
            $data_view['idBalance'] = $this->Balances_model->getAllIdBalance($db, $data_view['source']->database, 'balance');
        }
        $data_view['data'] = $this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6)."/".$this->uri->segment(7)."/".urldecode($this->uri->segment(8));
        $data_view['columns'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['test'] = $this->DataV2_model->getAll($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['titre'] = "Ajout/Modification/Suppression des données de la BDD (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "crud";
        $this->load->view('template_header', $data_view);
        $this->load->view('metrologie/crud_ajout', $data_view);
    }    
    
    public function ajoutExcel(){
        set_time_limit(60);
        $this->load->library('excel');
        $this->load->helper('url');
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('DataV2_model');
        $this->load->model('Balances_model');
        $config = [];
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select("projetlaposte");
        $excel = $this->input->post('excel');
        $username = $this->input->post('username');
        $files = 'C:/'.$username.'/Documents/'.$excel;
        $this->db->insert('historique', array('fichier' => $files, 'dateAjout' => date("Y-m-d H:i:s")));
        $inputFileName = '/'.$excel;
        //Suppression du fichier log précédent
        if(file_exists('/var/www/html/360/application/logs/importExcel.txt')){
            unlink('/var/www/html/360/application/logs/importExcel.txt');
        }
        //Création d'un fichier log
        $fileLog = fopen('/var/www/html/360/application/logs/importExcel.txt', 'c+b');
        echo $inputFileName;
        try
        {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        }catch(Exception $e)
        {
            echo "fichier introuvable";
            exit();
        }
        
        $arrayExcel = [];
        
        // On sélectionne la bonne feuille
        $sheet = $objPHPExcel->getSheet(0);
        //On sauvegarde le nombre de lignnes du document
        $highestRow = $sheet->getHighestRowAndColumn()['row'];
        //On sauvegarde le nombre de colonnes du document
        $highestColumn = $sheet->getHighestColumn();
        if($data_view['user']->actual_table == 'balance')
        {
            $row = 13;
            for($row; $row <= $highestRow; $row++)
            {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            
            if($rowData[7] == '05768' || $rowData[7] == '05791' || $rowData[7] == '85809' || $rowData[7] == '86087' || $rowData[7] == '86095' || $rowData[7] == '86910' || $rowData[7] == '86913' || $rowData[7] == '86934' || $rowData[7] == '86949' || $rowData[7] == '86950' || $rowData[7] == '89767' || $rowData[7] == '89907' || $rowData[7] == '91932' || $rowData[7] == '96598' || $rowData[7] == '100212' || $rowData[7] == '103673' || $rowData[7] == '103840' || $rowData[7] == '105092' || $rowData[7] == '115106' || $rowData[7] == '127339' || $rowData[7] == '127341' || $rowData[7] == '129498' || $rowData[7] == '134334'){
                fwrite($fileLog, date("Y-m-d H:i:s")+'La balance avec le code article ' +$rowData[7]+ " n'a pas été ajouter \r\n");
                continue;
            }
                if($rowData[0] == ''){
                    break;
                }
            
            $continue = 0;
            $pass = 0;
            $balance = "balance";
            $modele = "modele";
            $database = "projetlaposte";
            $reqCodeActif =  $this->Balances_model->getCodeActif($db, $database, $balance);
            $reqCodeArticle = $this->Balances_model->getCodeArticle($db, $database, $modele);
            
            foreach($reqCodeActif as $code){
                if($rowData[6] == $code->codeActif){
                    fwrite($fileLog, date("Y-m-d H:i:s")+': La balance avec le code actif ' +$rowData[6]+ " n'a pas été ajouter car la balance est déja présente dans la base de données\r\n");
                    $continue = 1;
                }
            }
                
            foreach($reqCodeArticle as $code){
                if($rowData[7] == $code->codeArticle){
                    fwrite($fileLog, date("Y-m-d H:i:s")+': La balance avec le code article ' +$rowData[7]+ " n'a pas été ajouter car la balance est déja présente dans la base de données\r\n");
                    $pass = 1;
                }   
            }
            
            if($continue == 1){
                continue;
            }
            $date = date('Y-m-d',strtotime(str_replace('/','-',$rowData[17])));
            $dataBalance = array(
                'codeActif' => $rowData[6],     
                'codeArticle' => $rowData[7], 
                'codeRegate' => $rowData[4],
                'numeroSerie' => $rowData[9], 
                'statut' => $rowData[3], 
                'dateVerification' => $date, 
                'localisation' => $rowData[18],
                'utilisation' => $rowData[19],
                'tranche' => $rowData[1]
            );
            $dataModele = array(
                'codeArticle' => $rowData[7],
                'libelle' => $rowData[8]
            );
            
            if($rowData[4] == "ALIENATION"){
                fwrite($fileLog, date("Y-m-d H:i:s")+': La balance avec le code article ' +$rowData[7]+ " n'a pas été ajouter car la balance est aliéné\r\n");
                continue;
            }
            
            $this->db->insert('balance', $dataBalance);
            if($pass == 1){
                continue;
            }
            $this->db->insert('modele', $dataModele);
                
                
        }
        $this->db->query("DELETE balance FROM balance WHERE statut != 'En service' AND statut != 'Maintenance' AND statut != 'Nouveau' AND statut != 'Réceptionné' AND statut != 'Hors service';");
        $this->db->query("DELETE balance FROM balance WHERE codeActif LIKE 'AFF%';"); 
        $this->db->query("DELETE balance FROM balance WHERE utilisation LIKE 'NPV%';"); 
        $this->db->query('UPDATE balance SET idEntite = (SELECT id FROM entite WHERE entite.codeRegate = balance.codeRegate LIMIT 1);');
        $this->db->query('UPDATE balance SET idModele = (SELECT id FROM modele WHERE modele.codeArticle = balance.codeArticle LIMIT 1);');
        }else if($data_view['user']->actual_table == "entite")
        {
            $row = 13;
        // nous parcourrons les lignes du document.
        $this->db->db_select('projetlaposte');
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            
            $tab = explode(" ", $rowData[5]);
            $type = $tab[count($tab)-1];
            
            $data = array(
                'codeRegate' => $rowData[4],     
                'libelle' => $rowData[5], 
                'adresse' => $rowData[10],
                'ville' => $rowData[12], 
                'codePostal' => $rowData[13], 
                'codeSource' => $rowData[14], 
                'type' => $type
            );
            
            if($rowData[4] == "ALIENATION"){
                continue;
            }
            
            $this->db->insert('entite', $data);
        }
        $this->db->query("DELETE entite 
                        FROM entite
                        LEFT OUTER JOIN (
                            SELECT MIN(id) as id, codeRegate
                            FROM entite
                            GROUP BY codeRegate
                        ) AS table_1 
                        ON entite.id = table_1.id
                        WHERE table_1.id IS NULL");
        $this->db->query("
                    DELETE entite FROM entite WHERE codeRegate = 'ALIENATION';");
        $this->db->query("UPDATE entite SET type = 'MAINT' WHERE type = 'AMI' OR type = 'TEAM' OR type = 'AMIND' OR type = 'AMISU';");
        $this->db->query("UPDATE entite SET idLot = (SELECT id FROM lot WHERE SUBSTR(codeRegate, 1, 2) = SUBSTR(SUBSTR(departements, LOCATE(SUBSTR(codeRegate,1,2), departements)),1,2));");
        $this->db->query("UPDATE entite SET idPrestataire = (SELECT id FROM prestataire WHERE entite.idLot = prestataire.idLot);");
        }else if($data_view['user']->actual_table == "prestataire")
        {
            $row = 2;
        // nous parcourrons les lignes du document.
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            
            
            $reqLot = $this->db->query("SELECT id FROM lot WHERE numeroLot = '".$rowData[1]."'");
            foreach($reqLot->result() as $data)
            {
                $idLot = $data->id;
            }
            
            $req = 'INSERT INTO prestataire(libelle, idLot) VALUES (:libelle, :lot)';
            $data = array(
                'libelle' => $rowData[0],
                'idLot' => $idLot
            );
            
            $this->db->insert('prestataire', $data);
        }
        $this->db->query('DELETE prestataire FROM prestataire
                        LEFT OUTER JOIN (
                            SELECT MIN(id) as id, idLot
                            FROM prestataire
                            GROUP BY idLot
                        ) as t1 
                    ON prestataire.id = t1.id
                    WHERE t1.id IS NULL;');
        }else if($data_view['user']->actual_table == "lot")
        {
            $row = 2;
        // nous parcourrons les lignes du document.
        $departements1 = '';
        $departements2 = '';
        $departements3 = '';
        $departements4 = '';
        $departements5 = '';
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            
            $data = array(
                'departements' => '',
                'numeroLot' => $rowData[2]
            );
            
            
            $this->db->insert('lot', $data);
            
            $numeroDepartements = $rowData[3];
            $numeroLot = $rowData[2];
            if($numeroLot == '1'){
                $departements1 = $departements1.$numeroDepartements.',';
            }else if($numeroLot == '2'){
                $departements2 = $departements2.$numeroDepartements.',';
            }else if($numeroLot == '3'){
                $departements3 = $departements3.$numeroDepartements.',';
            }else if($numeroLot == '4'){
                $departements4 = $departements4.$numeroDepartements.',';
            }else if($numeroLot == '5'){
                $departements5 = $departements5.$numeroDepartements.',';
            }
        }
        $array1 = explode(',', $departements1);
        $array1 = array_unique($array1); 
        $departements1 = implode(',', $array1);

        $array2 = explode(',', $departements2);
        $array2 = array_unique($array2); 
        $departements2 = implode(',', $array2);

        $array3 = explode(',', $departements3);
        $array3 = array_unique($array3); 
        $departements3 = implode(',', $array3);

        $array4 = explode(',', $departements4);
        $array4 = array_unique($array4); 
        $departements4 = implode(',', $array4);

        $array5 = explode(',', $departements5);
        $array5 = array_unique($array5); 
        $departements5 = implode(',', $array5);

        $data1 = array( 'departements' => $departements1, 'numeroLot' => '1');
        $this->db->replace('lot', $data1);
            
        $data2 = array( 'departements' => $departements2, 'numeroLot' => '2');
        $this->db->replace('lot', $data2);
        
        $data3 = array( 'departements' => $departements3, 'numeroLot' => '3');
        $this->db->replace('lot', $data3);
            
        $data4 = array( 'departements' => $departements4, 'numeroLot' => '4');
        $this->db->replace('lot', $data4);
            
        $data5 = array( 'departements' => $departements5, 'numeroLot' => '5');
        $this->db->replace('lot', $data5);

        $this->db->query('DELETE FROM lot WHERE departements = "";');
        }
        else if($data_view['user']->actual_table == "verification")
        {
            $row = 2;
        // nous parcourrons les lignes du document.
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            
            if($rowData[15] == 'oui'){
                $statutBalance = 'Balance accepté';
                if($rowData[18] == 'oui'){
                    $statutBalance = 'Balance accepté, mais manque le carnet métrologique';
                }else if($rowData[19] == 'oui'){
                    $statutBalance = "Balance accepté, mais besoin d'un ajustage";
                }else if($rowData[21] == 'oui'){
                    $statutBalance = "Balance accepté, mais non prévue";
                }
            }else if($rowData[16] == 'oui'){
                if($rowData[21] == 'oui'){
                    $statutBalance = "Balance refusé et non prévue, code refus:".$rowData[17];
                }else{
                    $statutBalance = 'Balance refusé, code refus:'.$rowData[17];
                }
            }else if($rowData[18] == 'oui'){
                $statutBalance = 'Fourniture carnet métrologique';
            }else if($rowData[19] == 'oui'){
                $statutBalance = 'Ajustage de la balance requis';
            }else if($rowData[20] == 'oui'){
                $statutBalance = 'Balance absente';
            }else if($rowData[21] == 'oui'){
                if($rowData[16] == 'oui'){
                    $statutBalance = 'Balance non prévue et accepté';
                }else if($rowData[17] == 'oui'){
                    $statutBalance = 'Balance non prévue et refusé';
                }
            }else if($rowData[22] == 'oui'){
                $statutBalance = 'Bureau Fermé';
            }
            $idBalance = "";
            $ids = $this->db->query("SELECT id FROM balance WHERE codeActif = '".$rowData[6]."'");
            if($ids == null){
                fwrite($fileLog, date("Y-m-d H:i:s")+': La balance avec le code actif ' +$rowData[6]+ " n'a pas été ajouter car la balance n'existe pas dans le fichier de enova\r\n");
            }else{
                foreach($ids->result() as $id)
                {
                    $idBalance = $id->id;
                }
            }  
            echo "Date : ".date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($rowData[14]));
            echo "Statut : ".$statutBalance;
            echo "</br>";
            $data = array(
                'statutVerification' => 'VERIF',
                'dateVerification' => date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($rowData[14])),
                'statutBalance' => $statutBalance,
                'idBalance' => $idBalance,
                'codeRegate' => $rowData[1],
                'codeActif' => $rowData[6]
            );
            
            $this->db->insert('verification', $data);
        }
        $this->db->query('DELETE verification FROM verification
                        LEFT OUTER JOIN (
                            SELECT MIN(id) as id, statutBalance, idBalance
                            FROM verification
                            GROUP BY statutBalance,idBalance
                        ) as t1 
                    ON verification.id = t1.id
                    WHERE t1.id IS NULL;');
        }
        else if($data_view['user']->actual_table == "prixdeplacement")
        {
        $sheet = $objPHPExcel->getSheet(1);
        //On sauvegarde le nombre de lignnes du document
        $highestRow = $sheet->getHighestRowAndColumn()['row'];
        //On sauvegarde le nombre de colonnes du document
        $highestColumn = $sheet->getHighestColumn();
            $row = 3;
        // nous parcourrons les lignes du document.
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            $numeroDepartementDeplacement = explode(')',explode('(', $rowData[0])[1])[0];
            
            $departements = $this->db->query("SELECT departements, p.id FROM lot l 
            INNER JOIN prestataire p ON p.idLot = l.id;");
            $array = [];
            foreach($departements->result() as $departement){
                $array = explode(",", $departement->departements);
                var_dump($array);
                $i = 0;
                for($i; $i < count($array); $i++){
                    if($array[$i] == $numeroDepartementDeplacement){
                        $idPrestataireDeplacement = $departement->id;
                        break;
                    }
                }
            }
            
            $data = array(
                'tranche0à30' => $rowData[1],
                'tranche31à200' => $rowData[2],
                'tranche201à600' => $rowData[3],
                'tranche601à1500' => $rowData[4],
                'tranche1501à3000' => $rowData[5],
                'tranche3001à6000' => $rowData[6],
                'tranche6001à10000' => $rowData[7],
                'automates' => $rowData[8],
                'numeroDepartement' => $numeroDepartementDeplacement,
                'idPrestataire' => $idPrestataireDeplacement,
            );
            
            $this->db->insert('prixdeplacement', $data);
        }
        }else if($data_view['user']->actual_table == "prixverification")
        {
        $sheet = $objPHPExcel->getSheet(1);
        //On sauvegarde le nombre de lignnes du document
        $highestRow = $sheet->getHighestRowAndColumn()['row'];
        //On sauvegarde le nombre de colonnes du document
        $highestColumn = $sheet->getHighestColumn();
            $row = 3;
        // nous parcourrons les lignes du document.
        for ($row; $row <= $highestRow; $row++)
        {
            // On range la ligne dans l'ordre 'normal'
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);

            // rowData est un tableau contenant les données de la ligne
            $rowData = $rowData[0];
            $numeroDepartementDeplacement = explode(')',explode('(', $rowData[0])[1])[0];
            
            $departements = $this->db->query("SELECT departements, p.id FROM lot l 
            INNER JOIN prestataire p ON p.idLot = l.id;");
            $array = [];
            foreach($departements->result() as $departement){
                $array = explode(",", $departement->departements);
                var_dump($array);
                $i = 0;
                for($i; $i < count($array); $i++){
                    if($array[$i] == $numeroDepartementDeplacement){
                        $idPrestataireDeplacement = $departement->id;
                        break;
                    }
                }
            }
            
            $data = array(
                'tranche0à30' => $rowData[11],
                'tranche31à200' => $rowData[12],
                'tranche201à600' => $rowData[13],
                'tranche601à1500' => $rowData[14],
                'tranche1501à3000' => $rowData[15],
                'tranche3001à6000' => $rowData[16],
                'tranche6001à10000' => $rowData[17],
                'automates' => $rowData[18],
                'numeroDepartement' => $numeroDepartementDeplacement,
                'idPrestataire' => $idPrestataireDeplacement,
            );
            
            $this->db->insert('prixverification', $data);
        }
        }
        fclose($fileLog);
        echo 1;
        exit;
    }
    
    public function modifsuppr(){
        
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Balances_model');
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['public'] = false;
        $data_view['id'] = "";
        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        if($data_view['user']->actual_table == 'commande' || $data_view['user']->actual_table == 'tarifverificationdeplacement' || $data_view['user']->actual_table == 'reporting_prestataire' || $data_view['user']->actual_table == 'tarif' || $data_view['user']->actual_table == 'test' || $data_view['user']->actual_table == 'A_verifier' || $data_view['user']->actual_table == 'historique')
        {
            redirect(site_url()."user/myaccount", 'refresh');
        }

        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $config['source_name'] = $data_view['source']->name;
        
        $config['engine'] = $data_view['source']->engine;
        $data_view['config'] = $config;
        
        $this->load->model('DataV2_model');
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
        $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        
        $data_view['test'] = $this->DataV2_model->getAll($db, $data_view['source']->database, $data_view['source']->table);
        if($data_view['source']->table == 'balance'){
            $data_view['statuts'] = $this->Balances_model->getAllStatut($db, $data_view['source']->database, $data_view['source']->table);
            $data_view["idModele"] = $this->Balances_model->getAllIdModele($db, $data_view['source']->database, "modele");
            $data_view["idEntite"] = $this->Balances_model->getAllIdEntite($db, $data_view['source']->database, "entite");
            $data_view["utilisations"] = $this->Balances_model->getAllUtilisation($db, $data_view['source']->database, "balance");
            $data_view["tranches"] = $this->Balances_model->getAllTranche($db, $data_view['source']->database, "balance");
        }
        $data_view['titre'] = "Ajout/Modification/Suppression des données de la BDD (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "crud";
        $this->load->view('template_header', $data_view);
        $this->load->view('metrologie/crud_modif_suppr', $data_view);
    }
    
    public function update(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->model('Crud_model');
        $this->load->model('Entite_model');
        $this->load->model('Balances_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        if($data_view['user']->actual_table == "verification"){
            $codeActif = $this->input->post('codeActif');
            $date = $this->input->post('date');
            $codeRegate = $this->input->post('codeRegate');
            $statutBalance = $this->input->post('statutBalance');
            $table = "balance";
            $idEntiteFromEntite = $this->Entite_model->getIdByCodeRegate($db, $data_view['user']->actual_database, "entite", $codeRegate);
            foreach($idEntiteFromEntite as $id)
            {
                    $idEntiteFromEntite = $id->id;
            }
            echo $idEntiteFromEntite;
            $idEntiteFromBalance = $this->Balances_model->getIdEntiteWithCodeActif($db, $data_view['user']->actual_database, "balance", $codeActif);
            foreach($idEntiteFromBalance as $id)
            {
                    $idEntiteFromBalance = $id->id;
            }
            $this->Crud_model->UpdateBalance($codeActif, $date, $codeRegate, $statutBalance, $db, $table, $idEntiteFromEntite, $idEntiteFromBalance);
        }else{
            $id = $this->input->post('id');
            $field = $this->input->post('field');
            $value = $this->input->post('value');
            $this->Crud_model->Update($id, $field, $value, $db, $table);
        }
        
        
        echo 1;
        exit;
    }
    
    public function delete(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->model('Crud_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        $id = $this->input->post('id');
        
        //Delete
        $this->Crud_model->Delete($id, $db, $table);
        
        echo $id;
        echo $db;
        echo $table;
        echo 1;
        exit;
    }
    
    public function create(){
        $this->load->model('DataV2_model');
        $this->load->model('Database_model');
        $this->load->model('Balances_model');
        $this->load->model('Prestataire_model');
        $this->load->model('User_model');
        $this->load->model('Crud_model');
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        $fields = $this->input->post('field');
        $value = $this->input->post('value');
        $fields = explode(",", $fields);
        array_pop($fields);
        $value = explode(",", $value);
        array_pop($value);
        if($data_view['source']->table == "balance"){
            $dataIdModele = end($value);
            array_pop($value);
            $dataIdEntite = end($value);
            array_pop($value);
            $idModele = $this->Balances_model->getIdModele($db, $data_view['source']->database, "modele", $dataIdModele);
            $idEntite = $this->Balances_model->getIdEntite($db, $data_view['source']->database, "entite", $dataIdEntite);
        }else if($data_view['source']->table == "entite"){
            $dataIdPrestataire = end($value);
            array_pop($value);
            $dataIdLot = end($value);
            array_pop($value);
            $idPrestataire = $this->Prestataire_model->getIdPrestataire($db, $data_view['source']->database, "prestataire", $dataIdPrestataire);
            $idLot = $this->Balances_model->getIdLot($db, $data_view['source']->database, "lot", $dataIdLot);
        }else if($data_view['source']->table == "prestataire"){
            $dataIdLot = end($value);
            array_pop($value);
            $idLot = $this->Balances_model->getIdLot($db, $data_view['source']->database, "lot", $dataIdLot);
        }else if($data_view['source']->table == "prixdeplacement" || $data_view['source']->table == "prixverification"){
            $dataIdPrestataire = end($value);
            array_pop($value);
            $idPrestataire = $this->Prestataire_model->getIdPrestataire($db, $data_view['source']->database, "prestataire", $dataIdPrestataire);
        }else if($data_view['source']->table == "verification"){
            $dataIdBalance = end($value);
            array_pop($value);
            $idBalance = $this->Balances_model->getIdBalance($db, $data_view['source']->database, "prestataire", $dataIdBalance);
        }
        
        if($data_view['source']->table == "balance")
        {
            array_push($value, $idEntite[0]->id, $idModele[0]->id);
        }else if($data_view['source']->table == "entite")
        {
            array_push($value, $idLot[0]->id, $idPrestataire[0]->id);
        }
        else if($data_view['source']->table == "prestataire")
        {
            array_push($value, $idLot[0]->id);
        }
        else if($data_view['source']->table == "prixdeplacement" || $data_view['source']->table == "prixverification")
        {
            array_push($value, $idPrestataire[0]->id);
        }
        else if($data_view['source']->table == "verification")
        {
            array_push($value, $idBalance[0]->id);
        }
        $data = array();
        $i = 0;
        foreach($fields as $field){
            $data[$field] = $value[$i];
            $i++;
        }
        
        //Create
        $this->Crud_model->Create($db, $table, $data);

        echo 1;
        exit;
    }
    
    public function consultation($id = NULL){
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        ini_set('max_input_vars', '100M');
        ini_set('post_max_size', '100M');
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $graph = "";
        $config = [];
        $data_view['name'] = "";
        $data_view['description'] = "";
        $data_view['image_name'] = "";
        $data_view['public'] = false;
        $data_view['id'] = "";
        $data_view['public'] = false;
        $data_view['live'] = false;

        $data_view['user'] = $this->User_model->getUserHalf($email);
        if($data_view['user']==null){
            redirect(site_url() . "/user/logout", 'refresh');
        }
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;

        $config["filtres"]=[];
        $config["speedfilters"]=[];
        $config["champs"] = [];
        $config['request'] = "";
        $config['expertmode'] = false;

        if($id !=null){
            $graph = $this->Graphs_model->getGraph($id);
            $getGroup_In_User = $this->User_model->getGroup_In_User($data_view['user']->id);
            if(!is_allowed_modify_graph($data_view['user'], $graph,$getGroup_In_User)){
                redirect(site_url()."/Graph/", 'refresh');
            }
            $config = json_decode($graph->config, true);

            $data_view['name'] = $graph->name;
            $data_view['description'] = $graph->description;
            $data_view['image_name'] = $graph->image_name;
            $data_view['public'] = $graph->public;
            $data_view['id'] = $id;
            $data_view['group'] = $graph->group;
            //TMP
            if (!isset($config['source'])) {
                $config['source'] = "local";
            }
            if (!isset($config['database'])) {
                $config['database'] = "hadoopviewer_data";
            }
            if (!isset($config['table'])) {
                $config['table'] = $config['bdd'];
                unset($config['bdd']);
            }
            //Ajout le champ minimum pour les anciennes config
            if(!isset($config["minimum"])){
                $config["minimum"]=0;
            }
            if(!isset($config["maximum"])){
                $config["maximum"]="";
            }

            if(!isset($graphConfig["filtres"])){
                $config["filtres"]=[];
            }
            if(!isset($graphConfig["speedfilters"])){
                $config["speedfilters"]=[];
            }

        }
        if (!$config['expertmode']) {
            $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        } else {
            $data_view['source'] = $this->Database_model->getBdd($config['source']);
        }
        if ($data_view['source'] == null) {
            redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ERROR_CODE_TABLE_NOT_ALLOWED . "&errortable=" . $config['source'] . ":" . $config['database'] . "." . $config['table'], 'refresh');
            return;
        }
        $config['source_name'] = $data_view['source']->name;
        $config['engine'] = $data_view['source']->engine;

        if($this->input->get("expertmode")!=null){
            $config['expertmode'] = true;
        }

        if($config['expertmode']){
            $config["table"] = "expertmode";
        }

        if($this->input->get("request")!=null){
            $config['request'] = $this->input->get("request", true);
        }
        //Si maximum existe dans le get et si c'est un entier positif
        if($this->input->get("maximum") != null && preg_match('/^\d+$/',$this->input->get("maximum"))){
            $config['maximum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("maximum"));
            //Ecrase la config de l'ancien graphique
        }else if($graph !=""){
        }else{
            $config['maximum'] = "";
        }
        if($this->input->get("minimum") != null && preg_match('/^\d+$/',$this->input->get("minimum"))){
            $config['minimum'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("minimum"));
            //Ecrase la config de l'ancien graphique
            $config["operators"] = [];
            $config["filtres"] = [];
            $config["speedfilters"] = [];
        }else if($graph !=""){
        }else{
            $config['minimum'] = 0;
        }
        if($this->input->get("typecalcul")!=null){
            $config['typecalcul'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalcul"));
        }else if($graph !=""){
        } else if (isset($data_view['source']->calc_type)) {
            $config['typecalcul'] = $data_view['source']->calc_type;
        }else{
            $config['typecalcul'] ="COUNT";
        }

        if(!isset($config['typecalcul'])){
            $config['typecalculchamp'] = "";
        }else if($this->input->get("typecalculchamp")!=null){
            $config['typecalculchamp'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->input->get("typecalculchamp"));
        }else if($config['typecalcul']=="COUNT" && $this->input->get("typecalculchamp")==""){
            $config['typecalculchamp'] = "";
        }else if($graph !=""){
        } else if (isset($data_view['source']->default_colomn)) {
            $config['typecalculchamp'] = $data_view['source']->default_colomn;
        }else{
            $config['typecalculchamp'] = "";
        }
        if(is_array($this->input->get("champs"))){
            $config["champs"] = $this->input->get("champs");
        }

        if(is_array($this->input->get("filtres"))){
            $config["filtres"] = $this->input->get("filtres");
        }
        if(is_array($this->input->get("speedfilters"))){
            $config["speedfilters"] = $this->input->get("speedfilters");
        }
        if(is_array($this->input->get("operators"))){
            $config["operators"] = $this->input->get("operators");
        }
        if (!isset($config["idfilesave"])) {
            $config["idfilesave"] = uniqid();
        }
        $data_view['config'] = $config;
        $data_view['champs'] = $config['champs'];
        if($data_view['image_name']==""){
            $data_view['image_name'] = 'image_' . $config["idfilesave"] . '.png';
        }

        //Recuperation des données sur la BDD
        $this->load->model('Graphs_model');
        //Recuperation de la derniere date de modification de la table
        if ($config['engine'] == "elasticsearch") {
            $this->load->helper('elasticsearch');
            if (!elastic_check($data_view['source']->url)) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID, 'refresh');
                return;
            }
            $data_view['resultColumn'] = elastic_map_list($data_view['source']->url . "_mapping")[$data_view['source']->table];

            //Remise à zero des données si le moteur choisie est elastocsearcj et si le mode expert est activé
            if ($config['expertmode']) {
                redirect(site_url() . "/graph/table1D_generator", 'refresh');
                return;
            } else if ($data_view['resultColumn'] == null) {
                redirect(site_url() . "/user/myaccount?" . ERROR_CODE_NAME . "=" . ELASTIC_NODATA, 'refresh');
                return;
            }
            $data_view['update_time_table'] = elastic_last_update($data_view['source']->url, $data_view['source']->table);
        } else if ($config["table"] != "expertmode") {
            $this->load->model('DataV2_model');
            $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
            $data_view['update_time_table'] = $this->DataV2_model->getUpdateTime($db, $data_view['source']->database, $data_view['source']->table);
            $data_view['resultColumn'] = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $data_view['source']->table);
        }else{
            $data_view['update_time_table'] = "";
            $data_view['resultColumn'] = [];
        }
        $data_view['titre'] = "Générateur de Tableau 1D (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "table1D";
        $this->load->view('template_header', $data_view);
        $this->load->view('metrologie/consultation', $data_view);
    }
    
    public function historique(){
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->helper('graph');
        $this->load->model('Balances_model');
        $this->load->model('Database_model');
        $this->load->model('User_model');
        $this->load->model('DataV2_model');
        
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['group'] = "";
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $database = $data_view['user']->actual_database;
        $table = "historique";
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $data_view['historique'] = $this->Balances_model->getHistorique($db, $database, $table);
        
        $data_view['titre'] = "Historique d'ajout de données avec excel";
        $data_view['type'] = "historique";
        $this->load->view('template_header', $data_view);
        $this->load->view('metrologie/historique', $data_view);
    }
    
    public function test(){
        $this->load->view('metrologie/test');
    }
}