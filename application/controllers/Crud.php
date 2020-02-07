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
        
        if($data_view['user']->actual_table == 'commande' || $data_view['user']->actual_table == 'tarifverificationdeplacement' || $data_view['user']->actual_table == 'reporting_prestataire' || $data_view['user']->actual_table == 'tarif' || $data_view['user']->actual_table == 'test')
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
        if($data_view['source']->table == 'balance'){
            $data_view['statuts'] = $this->Balances_model->getAllStatut($db, $data_view['source']->database, $data_view['source']->table);
            $data_view["idModele"] = $this->Balances_model->getAllIdModele($db, $data_view['source']->database, "modele");
            $data_view["idEntite"] = $this->Balances_model->getAllIdEntite($db, $data_view['source']->database, "entite");
            $data_view["utilisations"] = $this->Balances_model->getAllUtilisation($db, $data_view['source']->database, "balance");
            $data_view["tranches"] = $this->Balances_model->getAllTranche($db, $data_view['source']->database, "balance");
        }
        if($data_view['source']->table == 'entite'){
            $data_view["typeEntite"] = $this->Entite_model->getAllType($db, $data_view['source']->database, $data_view['source']->table);
        }
        $data_view['titre'] = "Ajout/Modification/Suppression des données de la BDD (source utilisée : " . $data_view['source']->name . ", base de donnée utilisée : " . $config['database'] . ", table utilisée : " . $config['table'] . ", moteur : " . $data_view['source']->engine . ")";
        $data_view['type'] = "crud";
        $this->load->view('template_header', $data_view);
        $this->load->view('metrologie/crud_ajout', $data_view);
    }    
    
    public function ajoutExcel(){
        $this->load->library('excel');
        $data_view = array();
        $email=$this->session->email;
        session_write_close();
        $this->load->model('User_model');
        $config = [];
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $inputFileName = $_FILES['monfichier']['name'];
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
            $this->db->db_select('projetlaposte');
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
                continue;
            }
            
            $continue = 0;
            $pass = 0;
            $reqCodeActif = $this->db->select("SELECT codeActif FROM balance"); 
            $reqCodeArticle = $this->db->select("SELECT codeArticle FROM modele");
            
            foreach($reqCodeActif->result() as $code){
                if($codeA == $code->codeActif){
                    $continue = 1;
                }
            }
                
            foreach($reqCodeArticle->result() as $code){
                if($codeAr == $code->codeArticle){
                    $pass = 1;
                }   
            }
                
            if($continue == 1 && $pass == 1){
                continue;
            }
                
            if(strlen($rowData[4]) > 8){
                continue;
            }
            
            $req = 'INSERT INTO balance(codeActif, codeArticle, codeRegate, numeroSerie, statut, dateVerification, localisation, utilisation, tranche) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
            
            $reqModele = 'INSERT INTO modele(codeArticle, libelle) VALUES (:codeArticle, :libelle)';
                
            $this->db->query($req, array($rowData[6],$rowData[7],$rowData[4],$rowData[9],$rowData[3],$rowData[17],$rowData[18],$rowData[19],$rowData[1]));
                
            $this->db->query($reqModele, array($rowData[6],$rowData[8]));
        }
        $this->db->query("DELETE FROM balance
                    WHERE statut != 'En service' AND statut != 'Maintenance' AND statut != 'Nouveau' AND statut != 'Réceptionné' AND statut != 'Hors service';
                    DELETE FROM balance WHERE codeActif LIKE 'AFF%' ;
                    DELETE FROM balance WHERE utilisation LIKE 'NPV%';
                    DELETE FROM balance WHERE dateVerification NOT LIKE '__/__/____' AND dateVerification NOT LIKE '____/__/__' AND dateVerification NOT LIKE '__/____' AND dateVerification NOT LIKE '__/__/__'
                    AND dateVerification NOT LIKE '_/_/__' AND dateVerification NOT LIKE '____/__' AND dateVerification NOT LIKE '____' AND dateVerification NOT LIKE '____ __ __' AND dateVerification NOT LIKE '___/__/__' AND dateVerification NOT LIKE '__/_/__'
                    AND dateVerification NOT LIKE '_/__/____' AND dateVerification NOT LIKE '';
                    UPDATE balance SET idEntite = (SELECT id FROM entite WHERE          entite.codeRegate = balance.codeRegate LIMIT 1);
                    UPDATE balance SET idModele = (SELECT id FROM modele WHERE          modele.codeArticle = balance.codeArticle LIMIT 1);
                    UPDATE balance SET dateVerification = CONCAT(SUBSTRING(dateVerification, 6), SUBSTRING(dateVerification, 2,4), CONCAT('0',SUBSTRING(dateVerification, 1,1))) WHERE dateVerification LIKE '_/%/____';
                    UPDATE balance SET dateVerification = CONCAT(SUBSTRING(dateVerification, 7), SUBSTRING(dateVerification, 3,4), SUBSTRING(dateVerification, 1,2)) WHERE dateVerification LIKE '%/%/____';
                    UPDATE balance SET dateVerification = CONCAT(SUBSTRING(dateVerification, 4), CONCAT('/',SUBSTRING(dateVerification, 1,3)), '01') WHERE dateVerification LIKE '__/____';
                    UPDATE balance SET dateVerification = CONCAT(dateVerification, '/01') WHERE dateVerification LIKE '____/__';
                    UPDATE balance SET dateVerification = CONCAT(dateVerification, '/01/01') WHERE dateVerification LIKE '____';
                    UPDATE balance SET dateVerification = REPLACE(dateVerification, ' ', '/') WHERE dateVerification LIKE '____ __ __';
                    UPDATE balance SET dateVerification = CONCAT(CONCAT('20',SUBSTRING(dateVerification, 5)), CONCAT('/0', SUBSTRING(dateVerification, 3,2)), SUBSTRING(dateVerification, 1,1)) WHERE dateVerification LIKE '_/_/__';
                    UPDATE balance SET dateVerification = REPLACE(dateVerification, '217', '2017') WHERE dateVerification LIKE '___/__/__';
                    UPDATE balance SET dateVerification = CONCAT(CONCAT('20',SUBSTRING(dateVerification, 7)), SUBSTRING(dateVerification, 3,4), SUBSTRING(dateVerification, 1,2))  WHERE dateVerification LIKE '__/__/__';
                    UPDATE balance SET dateVerification = CONCAT(CONCAT('20',SUBSTRING(dateVerification, 6)), CONCAT('0', SUSBTRING(dateVerification,4,2)), SUBSTRING(dateVerification, 1,3)) WHERE dateVerification LIKE '__/_/__';");
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
            
            $req = 'INSERT INTO entite(codeRegate, libelle, adresse, ville, codePostal, codeSource, type) VALUES (:codeRegate, :libelle, :adresse, :ville, :codePostal, :codeSource, :type)';
            
            $tab = explode(" ", $rowData[5]);
            $type = $tab[count($tab)-1];
            
            if($rowData[4] == "ALIENATION"){
                continue;
            }
            
            $this->db->query($req, $rowData[4], $rowData[5], $rowData[10], $rowData[12], $rowData[13], $rowData[14], $type);
        }
        
        $this->db->query("DELETE entite FROM entite
                    LEFT OUTER JOIN (
                    SELECT MIN(id) as id, codeRegate, libelle, codeSource, adresse, ville
                    FROM entite
                    GROUP BY codeRegate, libelle, codeSource, adresse, ville
                    ) as t1 
                    ON entite.id = t1.id
                    WHERE t1.id IS NULL;
                    DELETE FROM entite WHERE codeRegate = 'ALIENATION';
                    UPDATE entite SET type = 'MAINT' WHERE type = 'AMI' OR type = 'TEAM' OR type = 'AMIND' OR type = 'AMISU'; 
                    UPDATE entite SET idLot = (SELECT id FROM lot WHERE SUBSTR(codeRegate, 1, 2) = SUBSTR(SUBSTR(departements, LOCATE(SUBSTR(codeRegate,1,2), departements)),1,2));
                    UPDATE entite SET idPrestataire = (SELECT id FROM prestataire WHERE
                    entite.idLot = prestataire.idLot);");
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
            
            $req = 'INSERT INTO prestataire(libelle, idLot) VALUES (:libelle, :lot)';
            $reqLot = 'SELECT id FROM lot WHERE numeroLot = :numeroLot';
            
            $this->db->query($reqLot, $rowData[1]);
            foreach($reqLot->result() as $data)
            {
                $idLot = $data['id'];
            }
            
            $this->db->query($req, array($rowData[0], $idLot));
        }
        $bdd->exec('DELETE prestataire FROM prestataire
                    LEFT OUTER JOIN (
                    SELECT MIN(id) as id, libelle, idLot
                    FROM prestataire
                    GROUP BY libelle, idLot
                    ) as t1 
                    ON prestataire.id = t1.id
                    WHERE t1.id IS NULL; ');
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
            
            $req = 'INSERT INTO lot(departements, numeroLot) VALUES (:departement, :lot)';
            
            $this->db->query($req, array($rowData[2], ''))
            
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

        $reqD1 = 'UPDATE lot SET departements = :departements WHERE numeroLot = 1';
        $this->db->query($reqD1, array($departements1));
            
        $reqD2 = 'UPDATE lot SET departements = :departements WHERE numeroLot = 2';
        $this->db->query($reqD2, array($departements2));
        
        $reqD3 = 'UPDATE lot SET departements = :departements WHERE numeroLot = 3';
        $this->db->query($reqD3, array($departements3));
        
        $reqD4 = 'UPDATE lot SET departements = :departements WHERE numeroLot = 4';
        $this->db->query($reqD4, array($departements4));
        
        $reqD5 = 'UPDATE lot SET departements = :departements WHERE numeroLot = 5';
        $this->db->query($reqD5, array($departements5));

        $this->db->query('DELETE lot FROM lot
                    LEFT OUTER JOIN (
                    SELECT MIN(id) as id, departements, numeroLot
                    FROM lot
                    GROUP BY departements, numeroLot
                    ) as t1 
                    ON lot.id = t1.id
                    WHERE t1.id IS NULL');
        }else if($data_view['user']->actual_table == "verification")
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
            
            if($rowData[0] == ''){
                break;
            }
            
            $req = 'INSERT INTO verification(statutVerification, dateVerification, statutBalance, idBalance) VALUES (:statutVerif, :dateVerif, :statutBalance, :idBalance)';
            
            $id = 'SELECT id FROM balance WHERE codeActif = :codeActif';
            $ids = $this->db->query($id, array($rowData[6]));
            foreach($ids->result() as $id)
            {
                $idBalance = $data['id'];
            }
            
            if($rowData[15] == 'oui'){
                $statutBalance = 'Balance accepté';
                if($rowData[18] == 'oui'){
                    $statutBalance = 'Balance accepté, mais manque le carnet métrologique';
                }
            }else if($rowData[16] == 'oui'){
                $statutBalance = 'Balance refusé, code refus:'.$rowData[17];
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
            
            $date = PHPExcel_Style_NumberFormat::toFormattedString($rowData[14], 'YYYY/MM/DD');
            
            $this->db->query($req, array($rowData[8], $date, $statutBalance, $idBalance));
        }
        $this->db->query('DELETE FROM verification
                        LEFT OUTER JOIN (
                            SELECT MIN(id) as id, idBalance
                            FROM table
                            GROUP BY idBalance
                        ) as t1 
                    ON verification.id = t1.id
                    WHERE t1.id IS NULL');
        }
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
        
        if($data_view['user']->actual_table == 'commande' || $data_view['user']->actual_table == 'tarifverificationdeplacement' || $data_view['user']->actual_table == 'reporting_prestataire' || $data_view['user']->actual_table == 'tarif' || $data_view['user']->actual_table == 'test')
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
        
        $email = $this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        $db = $data_view['source']->database;
        $table = $data_view['source']->table;
        
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        
        //Update
        $this->Crud_model->Update($id, $field, $value, $db, $table);
        
        
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
        $fields = explode(",", $fields);
        array_pop($fields);
        $value = $this->input->post('value');
        $value = explode(",", $value);
        var_dump($value);
        array_pop($value);
        $dataIdModele = end($value);
        array_pop($value);
        $dataIdEntite = end($value);
        array_pop($value);
        
        $idModele = $this->Balances_model->getIdModele($db, $data_view['source']->database, "modele", $dataIdModele);
        $idEntite = $this->Balances_model->getIdEntite($db, $data_view['source']->database, "entite", $dataIdEntite);
        array_push($value, $idEntite[0]->id, $idModele[0]->id);
        
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
}