<?php

/**
 * Class User
 * Controleur pour la gestion des utilisateurs
 */
class User extends CI_Controller
{
    /**
     * Page par defaut
     */
    public function index(){
        $this->connexion();
    }

    /**
     * Page de connexion
     */
    public function connexion(){
        $this->load->helper('url');
        //Verification si l'utilisateur n'est pas connecté
        connected_redirect($this->session, True, site_url('graph/'));
        //
        $data_view = [];
        $data_view['error'] = "";
        $data_view['lasturl'] = "";
        if($this->input->get('lasturl')){
            $data_view['lasturl'] = $this->input->get('lasturl');
        }

        //Gestion de la demande de connexion
        if($this->input->post('email_connexion') && $this->input->post('password_connexion')){
            $this->load->model('User_model');

            $user = $this->User_model->getUser($this->input->post('email_connexion'));
            //Verification si l'utilisateur existe et que le mot de passe est bon
            if(!empty($user) && password_verify($this->input->post('password_connexion'),$user->password)){
                //Création de la session
                $newdata = array(
                    'email'     => $user->email,
                    'type'      => $user->type,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($newdata);
                if($data_view['lasturl']!=""){
                    redirect($data_view['lasturl'], 'refresh');
                }else{
                    redirect(site_url('user/myaccount'), 'refresh');
                }

                return;
            }
            $data_view['error'] = "L'email ou le mot de passe est incorect !";
        }

        $data_view['titre'] = "Connexion";
        $data_view['type'] = "connexion";
        $this->load->view('template_header', $data_view);
        $this->load->view('connexion_user', $data_view);
    }

    /**
     * Page de deconnexion
     */
    public function logout(){
        //Suppression de la session
        $this->session->sess_destroy();
        redirect(site_url(), 'refresh');
    }

    /**
     * Page de création de compte
     */
    public function register()
    {
        //Verification si l'utilisateur n'est pas connecté
        connected_redirect($this->session, True, site_url('graph/'));
        $data_view = [];
        $data_view['error'] = "";

        //Gestion de la demande de création de compte
        if($this->input->post('email_registration') && $this->input->post('password_registration')){
            $this->load->model('User_model');
            $user = $this->User_model->getUser($this->input->post('email_registration'));
            //Verification si l'utilisateur n'existe pas
            if(empty($user)){
                //Verification de la coherence des mots de passes
                if($this->input->post('password_registration')==$this->input->post('password_registration_verification')){
                    //Création de l'utilisateur
                    $this->User_model->addUser($this->input->post('email_registration'), $this->input->post('password_registration'), "lecteur", $this->User_model->getConfig("default_source"), $this->User_model->getConfig("default_database"), $this->User_model->getConfig("default_table"));
                    //Création de la session
                    $newdata = array(
                        'email'     => $this->input->post('email_registration'),
                        'type'      => 'lecteur',
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($newdata);
                    redirect(site_url(), 'refresh');
                    return;
                }else{
                    $data_view['error'] = "Les mots de passes sont differents";
                }

            }else{
                $data_view['error'] = "L'utilisateur ".$this->input->post('email_registration')." existe déjà";
            }

        }

        $data_view['titre'] = "Inscription";
        $data_view['type'] = "newUser";
        $this->load->view('template_header', $data_view);
        $this->load->view('create_user', $data_view);
    }

    public function passwordReset(){
        connected_redirect($this->session, True, site_url('graph/'));
        $data_view = [];
        $data_view['error'] = "";
        $data_view['titre'] = "Inscription";
        $data_view['type'] = "newUser";
        $data_view['error'] = "";
        $this->load->view('template_header', $data_view);
        $this->load->view('resetpassword', $data_view);

    }

    public function passwordsend(){
        connected_redirect($this->session, True, site_url('graph/'));
        $this->load->model('User_model');
        $user = $this->User_model->getUser($this->input->post('email_reset'));
        if($user==null){
            redirect(site_url(""), 'refresh');
        }
        print_r($user);
        $this->load->library('email');
        $this->email->from('sebastien.villalon@laposte.fr', 'Bot 360 Videocodage');
        $this->email->to($user->email);
        $this->email->subject('Changement de votre adresse mail');
        $this->email->message('Testing the email class.');
        $this->email->send();

        //redirect(site_url(""), 'refresh');

    }

    public function about(){
        $data_view = [];
        $data_view['titre'] = "A propos";
        $data_view['type'] = "about";
        if($this->session->has_userdata('logged_in')){
            $this->load->model('User_model');
            $data_view['user'] = $this->User_model->getUser($this->session->email);
        }
        $this->load->view('template_header', $data_view);
        $this->load->view('about', $data_view);
    }
    public function admin(){
        $data_view = [];
        $data_view['titre'] = "Administration";
        //Charge les functions liée au graphiques
        $this->load->helper('graph');
        $this->load->helper('database_helper');
        $this->load->model('User_model');
        $this->load->model('Graphs_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        $data_view['error'] = "";
        $data_view['default_table'] = $this->User_model->getConfig("default_table");
        $data_view['default_database'] = $this->User_model->getConfig("default_database");
        $data_view['default_source'] = $this->User_model->getConfig("default_source");
        $data_view['descriptiondatabase'] = $this->User_model->getConfig("descriptiondatabase");


        //Gestion de la demande de création de compte
        if($this->input->post('email_registration') && $this->input->post('password_registration')){
            $this->load->model('User_model');
            $user = $this->User_model->getUser($this->input->post('email_registration'));
            //Verification si l'utilisateur n'existe pas
            if(empty($user)){
                //Verification de la coherence des mots de passes
                if($this->input->post('password_registration')==$this->input->post('password_registration_verification')){
                    //Création de l'utilisateur
                    $this->User_model->addUser($this->input->post('email_registration'), $this->input->post('password_registration'), "lecteur", $data_view['default_source'], $data_view['default_database'], $data_view['default_table']);
                }else{
                    $data_view['error'] = "Les mots de passes sont differents";
                }

            }else{
                $data_view['error'] = "L'utilisateur ".$this->input->post('email_registration')." existe déjà";
            }

        }

        $data_view['listUser'] = $this->User_model->listUser();
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['countGraphByGroup'] = $this->Graphs_model->countGraphByGroup();
        $data_view['countUsersInGroup'] = $this->User_model->countUsersInGroup();

        $data_view['titre'] = "Administration";
        $data_view['type'] = "admin";


        $this->load->model('Database_model');

        $data_view['listDatabase'] = $this->Database_model->getListBdd();
        $data_view['listAuthorizedData'] = $this->Database_model->getListAuthorizedDataExpert();

        $this->load->view('template_header', $data_view);
        $this->load->view('admin', $data_view);
    }

    /**
     * Affiche une page qui permet de nettoyer le cache disponible
     * @param bool $all Si true alors supprime tous le cache
     */
    public function purge_cache($all = false){
        $data_view = [];
        //Charge les functions liée au graphiques
        $this->load->helper('file');
        $this->load->helper('datacache');
        $this->load->model('User_model');
        $this->load->model('Cache_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        session_write_close();



        if($all=="true"){
            //Suppression complète du cache
            $this->Cache_model->removeAllCache();
            $fileList = get_dir_file_info(CACHE_FOLDER);
            foreach ($fileList as $file){
                if(!is_dir(CACHE_FOLDER.$file['name']) && preg_match("/^\w*$/", $file['name'])){
                    unlink(CACHE_FOLDER.$file['name']);
                }
            }
            redirect(site_url("user/admin"), 'refresh');
            return;
        }else{
            //Suppression du cache périmé
            //Recuperation des informations des tables
            $this->load->model('Database_model');
            $listBDD = $this->Database_model->getListBdd();
            $listAuthorizedData = $this->Database_model->getListAuthorizedData();
            $bdds = [];
            foreach ($listBDD as $bdd) {
                $bdds[$bdd->id] = [];
                if ($bdd->engine == "mysql") {
                    $this->load->model('DataV2_model');
                    $db = $this->DataV2_model->loadDatabase($bdd->url);
                    $databaseInfoRaw = $this->DataV2_model->getDatabaseInfo($db);
                    foreach ($databaseInfoRaw as $table) {
                        if (!key_exists($table->TABLE_SCHEMA, $bdds[$bdd->id])) {
                            $bdds[$bdd->id][$table->TABLE_SCHEMA] = [];
                        }
                        //Verification si la table est autorisé
                        if (array_key_exists($bdd->id, $listAuthorizedData) && array_key_exists($table->TABLE_SCHEMA, $listAuthorizedData[$bdd->id]) && array_key_exists($table->TABLE_NAME, $listAuthorizedData[$bdd->id][$table->TABLE_SCHEMA])) {
                            if ($table->UPDATE_TIME == "") {
                                $bdds[$bdd->id][$table->TABLE_SCHEMA][$table->TABLE_NAME] = $table->CREATE_TIME;
                            } else {
                                $bdds[$bdd->id][$table->TABLE_SCHEMA][$table->TABLE_NAME] = $table->UPDATE_TIME;
                            }
                        }


                    }

                }
            }
            //Récupération des caches disponibles
            $listCache = $this->Cache_model->getListCache();
            $cachesAvailable = [];
            foreach ($listCache as $cache) {

                if (!array_key_exists($cache->source, $bdds) || !array_key_exists($cache->database, $bdds[$cache->source]) || !array_key_exists($cache->table, $bdds[$cache->source][$cache->database])) {
                    //Supprime les caches ne sont pas dans la liste des tables autorisées
                    //echo "remove nobdd ".$cache->table."<br>";
                    unlink(CACHE_FOLDER.$cache->id);
                    $this->Cache_model->removeCache($cache->id);
                }else{
                    if (!check_validity_data_cache($cache, $bdds[$cache->source][$cache->database][$cache->table], $cache->id)) {
                        //Suppression des tables périmé (+10 minutes de retard par rapport au dernier ajout de donnée dans la table
                        //echo "remove ".$cache->id.".".$cache->table."<br>";
                        unlink(CACHE_FOLDER.$cache->id);
                        $this->Cache_model->removeCache($cache->id);
                    } else {
                        //Liste blanche des données cache à garder
                        $cachesAvailable[] = $cache->id;
                    }
                }
            }
            //Recuperation des fichiers cache disponibles
            $fileList = get_dir_file_info(CACHE_FOLDER);
            //Suppression des fichiers qui ne sont pas dans la base de donnée
            foreach ($fileList as $file){
                if(preg_match("/^\w*$/", $file['name']) && !in_array($file['name'], $cachesAvailable)){

                    //echo "remove alone file ".$file['name']."<br>";
                    unlink(CACHE_FOLDER.$file['name']);
                }
            }
            redirect(site_url("user/admin"), 'refresh');
            return;
        }
    }

    public function user_management($id = NULL){
        if(!$id){
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }

        $data_view = [];
        $this->load->model('User_model');
        $this->load->model('Graphs_model');
        $data_view['user'] = $this->User_model->getUserHalf($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        session_write_close();
        $data_view['listUser'] = $this->User_model->listUser();
        $data_view['listGroup'] = $this->User_model->listGroup();
        $data_view['getGroup_In_User'] = $this->User_model->getGroup_In_User($id);
        $data_view['nbGraph'] = $this->Graphs_model->countNbGraph("",false, "", [],[],[$id],[]);
        //'/^\d*$/' si la chaine est un entier
        if($this->input->get("nbPage") && preg_match('/^\d*$/', $this->input->get("nbPage")) && ($this->input->get("nbPage")-1)*8<=$data_view['nbGraph']){
            $data_view['nbPage'] = $this->input->get("nbPage");
        }else{
            $data_view['nbPage'] = 1;
        }
        $data_view['nbPageMax'] = ceil($data_view['nbGraph']/8);
        $data_view['listGraph'] = $this->Graphs_model->listGraph("",true, "", [],[],[$id],$data_view['nbPage'],[]);
        $data_view['iduser'] = $id;
        $data_view['user'] = $this->User_model->getUserID($id);
        $data_view['titre'] = "Gestion du groupe";
        $data_view['type'] = "group";
        $this->load->view('template_header', $data_view);
        $this->load->view('user', $data_view);
    }
    public function group($id = NULL){
        if ($id == NULL) {
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }
        $data_view = [];
        $this->load->model('User_model');
        $this->load->model('Graphs_model');
        $data_view['user'] = $this->User_model->getUserHalf($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        session_write_close();
        $data_view['listUser'] = $this->User_model->listUser();
        $data_view['getUsers_In_Group'] = $this->User_model->getUsers_In_Group($id);
        $data_view['nbGraph'] = $this->Graphs_model->countNbGraph("",false, "", [],[],[],[$id]);
        //'/^\d*$/' si la chaine est un entier
        if($this->input->get("nbPage") && preg_match('/^\d*$/', $this->input->get("nbPage")) && ($this->input->get("nbPage")-1)*8<=$data_view['nbGraph']){
            $data_view['nbPage'] = $this->input->get("nbPage");
        }else{
            $data_view['nbPage'] = 1;
        }
        $data_view['nbPageMax'] = ceil($data_view['nbGraph']/8);
        $data_view['listGraph'] = $this->Graphs_model->listGraph("",true, "", [],[],[],$data_view['nbPage'],[$id]);
        $data_view['idgroup'] = $id;
        $data_view['url'] = "";
        $data_view['group'] = $this->User_model->getGroup($id);
        $data_view['titre'] = "Gestion du groupe";
        $data_view['type'] = "group";
        $this->load->view('template_header', $data_view);
        $this->load->view('group', $data_view);
    }
    public function myaccount()
    {
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUserHalf($this->session->email);
        $data_view['descriptiondatabase'] = $this->User_model->getConfig("descriptiondatabase");
        $data_view['success'] = "";
        $data_view['error'] = "";
        $data_view['errormsg'] = "";
        if ($this->input->get("errorcode") == ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID) {
            $data_view['errormsg'] = ELASTIC_OFFLINE_REDIRECT_ACCOUNT;
        } else if ($this->input->get("errorcode") == ELASTIC_NODATA) {
            $data_view['errormsg'] = ELASTIC_NODATA_REDIRECT_ACCOUNT;
        } else if ($this->input->get("errorcode") == ERROR_CODE_TABLE_NOT_ALLOWED) {
            $data_view['errormsg'] = ERROR_MSG_TABLE_NOT_ALLOWED . " (" . $this->input->get("errortable") . ")";
        } else if ($this->input->get("errorcode") == ERROR_CODE_TABLE_NOT_EXIST) {
            $data_view['errormsg'] = ERROR_MSG_TABLE_NOT_EXIST . " (" . $this->input->get("errortable") . ")";
        }

        //Gestion du changement de mot de passe
        if($this->input->post('password_registration') && $this->input->post('password_registration_verification')){
            if($this->input->post('password_registration')==$this->input->post('password_registration_verification')){
                $this->User_model->changepassword($data_view['user']->id, $this->input->post('password_registration'));
                $data_view['success'] = "Mot de passe modifié !";
            }else{
                $data_view['error'] = "Les mots de passes ne sont pas identique !";
            }
        }

        $data_view['titre'] = "Mon compte";
        $data_view['type'] = "myaccount";

        $this->load->model('Database_model');
        $data_view['bdd'] = $this->Database_model->getBdd(0);
        $data_view['listAuthorizedData'] = $this->Database_model->getListAuthorizedDataExpert();

        $this->load->view('template_header', $data_view);
        $this->load->view('myaccount', $data_view);
    }
    /**
     * Gestion requete Post : Creation d'un nouveau groupe d'utilisateur
     */
    public function addgroup(){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);

        if(!$this->input->post('group_name')){
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }

        $this->load->model('User_model');
        //$this->security->xss_clean() protège le Cross Site Scripting en modifiant les champs textes (<script>)
        if(empty($this->User_model->searchGroup($this->security->xss_clean($this->input->post('group_name'))))){
            $this->User_model->addGroup($this->security->xss_clean($this->input->post('group_name')));
        }
        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Gestion requete Post : Suppression du groupe
     */
    public function removegroup($id = NULL){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);

        if(!$id){
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }
        $this->load->model('User_model');
        //$this->security->xss_clean() protège le Cross Site Scripting en modifiant les champs textes (<script>)
        $this->User_model->removegroup($this->security->xss_clean($id));


        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Gestion requete Post : Creation d'un nouveau groupe d'utilisateur
     */
    public function changeuseringroup($id = NULL){
        connected_redirect($this->session, False, site_url('graph/'));
        if(!$id){
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);


        $data_view['group'] = $this->User_model->getGroup($id);
        if(!$data_view['group']){
            connected_redirect($this->session, True, site_url('user/admin'));
        }
        print_r($_POST);
        $this->User_model->removeAllUserInGroup($id);
        if($this->input->post('users')){
            foreach ($this->input->post('users') as $userID){
                $this->User_model->addUserInGroup($userID,$id);
            }
        }
        redirect(site_url("user/group/".$id), 'refresh');
    }
    /**
     * Gestion requete Post : Creation d'un nouveau groupe d'utilisateur
     */
    public function changegroupinuser($id = NULL){
        connected_redirect($this->session, False, site_url('graph/'));
        if(!$id){
            connected_redirect($this->session, True, site_url('user/admin'));
            return;
        }
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);


        $data_view['user'] = $this->User_model->getUserID($id);
        if(!$data_view['user']){
            connected_redirect($this->session, True, site_url('user/admin'));
        }
        $this->User_model->removeAllGroupInUser($id);
        if($this->input->post('groups')){
            foreach ($this->input->post('groups') as $groupID){
                $this->User_model->addGroupInUser($groupID,$id);
            }
        }
        redirect(site_url("user/user_management/".$id), 'refresh');
    }
    /**
     * Gestion requete Post : Changement de la bdd de l'utilisateur actuel
     */
    public function changebdd(){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin','createur']);
        if (!$this->input->post('table') || !$this->input->post('source')) {
            connected_redirect($this->session, True, site_url('graph/'));
        }
        $this->load->model('User_model');
        $array_table = explode(".", $this->input->post('table'));
        if (count($array_table) == 2) {
            $this->User_model->changeBDD($this->session->email, $this->input->post('source'), $array_table[0], $array_table[1]);
        }


        redirect(site_url("user/myaccount"), 'refresh');
    }
    /**
     * Gestion requete Post : Changement de la bdd des nouveaux utilisteur
     */
    public function changedefaultbdd(){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if (!$this->input->post('table') || !$this->input->post('source')) {
            connected_redirect($this->session, True, site_url('user/admin'));
        }
        $newSource = $this->input->post('source');
        $array_table = explode(".", $this->input->post('table'));
        if (count($array_table) == 2) {
            $this->User_model->changedefaultBDD($newSource, $array_table[0], $array_table[1]);
        }
        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Gestion requete Post : Changement de la description des bases de données
     */
    public function changedescriptiondatabase(){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if(!$this->input->post('description_database')){
            connected_redirect($this->session, True, site_url('graph/'));
            return;
        }
        $this->load->model('User_model');
        //$this->security->xss_clean() protège le Cross Site Scripting en modifiant les champs textes (<script>)
        $this->User_model->changedescriptiondatabase($this->security->xss_clean($this->input->post('description_database')));


        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Gestion requete Post : Changement des calcules par defauts des bases de données
     */
    public function changedefaulttypecalcul(){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin'])

        ;
        if(!$this->input->post('typecalcul') && !$this->input->post('typecalculchamp')){
            connected_redirect($this->session, True, site_url('graph/'));
            //echo "ERROR";
            return;
        }
        $conf['typecalcul'] = $this->input->post('typecalcul');
        $conf['typecalculchamp'] = $this->input->post('typecalculchamp');
        $conf['typecalcul_elastic'] = $this->input->post('typecalcul_elastic');
        $conf['typecalculchamp_elastic'] = $this->input->post('typecalculchamp_elastic');
        //echo json_encode($conf);
        $this->load->model('User_model');
        //$this->security->xss_clean() protège le Cross Site Scripting en modifiant les champs textes (<script>)
        $this->User_model->changedefaulttypecalcul($this->security->xss_clean(json_encode($conf)));


        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Page de suppression d'un utilisateur
     * @param null $id ID de l'utilisateur
     */
    public function remove($id = NULL){
        connected_redirect($this->session, False, site_url('graph/'));
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if($id !=NULL){
            $this->load->model('User_model');
            $this->User_model->removeUser($id);
        }
        redirect(site_url("user/admin"), 'refresh');
    }

    /**
     * Gestion requete Post : Changement de la bdd de l'utilisateur actuel
     */
    public function switchtype($id = NULL){
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if($id==NULL){
            connected_redirect($this->session, True, site_url('graph/'));
        }

        $this->load->model('User_model');
        $this->User_model->switchType($id);

        redirect(site_url("user/admin"), 'refresh');
    }
    /**
     * Gestion requete Post : Changement de la bdd de l'utilisateur actuel
     */
    public function changetype($id = NULL, $type = NULL){
        $data_view = [];
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if($id==NULL || $id==NULL){
            connected_redirect($this->session, True, site_url('graph/'));
        }

        $this->load->model('User_model');
        $this->User_model->changeType($id,$type);

        redirect(site_url("user/admin"), 'refresh');
    }

    /**
     * Ajout de la nouvelle base de données
     */
    public function adddatasource()
    {
        $this->load->model('User_model');
        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);

        $name = $this->input->post('database_name');
        $url = $this->input->post('database_url');
        $engine = $this->input->post('database_engine');


        //echo $name." ".$url." ".$engine;

        if ($name != null && $url != null && $engine != null && $name != "" && $url != "" && $engine != "") {
            $this->load->model('Database_model');
            if ($engine == "elasticsearch" && substr($url, -1) != "/") {
                //Ajoute un / dans l'url d'elk s'il n'y en a pas.
                $url .= "/";
            }
            $this->Database_model->saveBDD($name, $engine, $url);
        }
        redirect(site_url("user/admin"), 'refresh');
    }

    /**
     * Ajout de la nouvelle base de données
     */
    public function removedatasource($id = NULL)
    {
        $this->load->model('User_model');

        $data_view['user'] = $this->User_model->getUser($this->session->email);
        user_redirect($this->session, site_url('/'), ['admin']);
        if ($id != null && $id != "") {
            $this->load->model('Database_model');
            $this->Database_model->removeBDD($id);
        }
        redirect(site_url("user/admin"), 'refresh');
    }

    public function uuid()
    {
        echo uniqid("ds_", true);
    }

    /**
     * Mode AJAX
     * @param $source
     */
    public function modify_authorized($source)
    {
        if (user_check($this->session, ['admin'])) {
            $this->load->helper('error');
            echo error_json(ERROR_CODE_USER_NOT_ALLOWED, ERROR_MSG_USER_NOT_ALLOWED);
            return;
        }
        //echo $source."<br/>";
        $database = $this->input->post('database');
        $table = $this->input->post('table');
        $calc_type = $this->input->post('calc_type');
        $default_colomn = $this->input->post('default_colomn');
        $data = [];
        $this->load->model('Database_model');
        $this->Database_model->removeAllAuthorizedDataForSource($source);
        if (is_array($database) && is_array($table) && is_array($calc_type) && is_array($default_colomn)) {
            $nb = count($database);
            for ($i = 0; $i < $nb; $i++) {
                if (isset($database[$i]) && isset($table[$i]) && isset($calc_type[$i]) && isset($default_colomn[$i])) {
                    echo $database[$i] . "." . $table[$i] . "<br>";
                    $data[] = array(
                        'source' => $source,
                        'database' => $database[$i],
                        'table' => $table[$i],
                        'calc_type' => $calc_type[$i],
                        'default_colomn' => $default_colomn[$i]
                    );
                }
                //echo $database[$i]." ".$table[$i]." ".$calc_type[$i]." ".$default_colomn[$i]."<br/>";
            }
            $this->Database_model->insertBatchAllAuthorizedDataForSource($data);
        }
        //print_r($this->input->post('database'));
        redirect(site_url("user/admin"), 'refresh');
    }
    
    public function exportAllBalance()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "balance";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllEntite()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "entite";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllModele()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "modele";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllPrestataire()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "prestataire";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllLot()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "lot";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllVerification()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "verification";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllPrixDeplacement()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "prixdeplacement";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllPrixVerification()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "prixverification";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function exportAllCommande()
    {
        $this->load->library('excel');
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $table = "balance";
        $this->excel->setActiveSheetIndex(0);
        $columns = $this->DataV2_model->getColumnsNameExpert($db, $data_view['source']->database, $table);
        $cellL = 0;
        $cellC = 1;
        foreach($columns as $column)
        {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($cellL, $cellC, $column->name);
            $cellL++;
        }
        $this->excel->getActiveSheet()->setTitle($table);
            
        $datas = $this->DataV2_model->getAll($db, $data_view['source']->database, $table);
        $datas = json_decode(json_encode($datas), True);
        $this->excel->getActiveSheet()->fromArray($datas, null, 'A2');

        $filename = $table.".xls";
            
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vns.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control : must-revalidate, post-chock=0, pre-check=0');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
        
        echo 1;
        exit;
    }
    
    public function deleteAllData()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.verification AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.prixdeplacement AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.prixverification AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.balance AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.modele AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.entite AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.prestataire AUTO_INCREMENT = 0');
        $this->db->query('ALTER TABLE projetlaposte.lot AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.verification');
        $this->db->query('DELETE FROM projetlaposte.prixdeplacement');
        $this->db->query('DELETE FROM projetlaposte.prixverification');
        $this->db->query('DELETE FROM projetlaposte.balance');
        $this->db->query('DELETE FROM projetlaposte.modele');
        $this->db->query('DELETE FROM projetlaposte.entite');
        $this->db->query('DELETE FROM projetlaposte.prestataire');
        $this->db->query('DELETE FROM projetlaposte.lot');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllBalance()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.balance AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.balance');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllEntite()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.entite AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.entite');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllPrestataire()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.prestataire AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.prestataire');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllLot()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.lot AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.lot');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllModele()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.modele AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.modele');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllVerification()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.verification AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.verification');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllPrixDeplacement()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.prixdeplacement AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.prixdeplacement');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function deleteAllPrixVerification()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        $tables = $this->db->list_tables();
        
        $this->db->query('SET foreign_key_checks = 0');
        $this->db->query('ALTER TABLE projetlaposte.prixverification AUTO_INCREMENT = 0');
        $this->db->query('DELETE FROM projetlaposte.prixverification');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        echo 1;
        exit;
    }
    
    public function vuegeneration()
    {
        $this->load->helper('graph');
        $this->load->model('Graphs_model');
        $this->load->model('User_model');
        $this->load->model('Database_model');
        $this->load->model('Prestataire_model');
        $this->load->model('Balances_model');
        $this->load->model('DataV2_model');
        $this->load->model('Entite_model');
        
        $email=$this->session->email;
        $data_view['user'] = $this->User_model->getUserHalf($email);
            
        $config['source'] = $data_view['user']->actual_source;
        $config['database'] = $data_view['user']->actual_database;
        $config['table'] = $data_view['user']->actual_table;
        
        $data_view['source'] = $this->Database_model->getAuthorizedData($config['source'], $config['database'], $config['table']);
        
        $db = $this->DataV2_model->loadDatabase($data_view['source']->url);
        $this->db->db_select('projetlaposte');
        
        $this->db->query("CREATE OR REPLACE VIEW commande AS select `projetlaposte`.`prestataire`.`libelle` AS `Prestataire`,`projetlaposte`.`lot`.`numeroLot` AS `numeroLot`,substr(`projetlaposte`.`entite`.`codeRegate`,1,2) AS `Departement`,`projetlaposte`.`entite`.`codeRegate` AS `codeRegate`,`projetlaposte`.`entite`.`libelle` AS `Libellé_Entite`,`projetlaposte`.`balance`.`codeArticle` AS `codeArticle`,`projetlaposte`.`modele`.`libelle` AS `Libellé_Article`,`projetlaposte`.`balance`.`numeroSerie` AS `numeroSerie`,`projetlaposte`.`balance`.`codeActif` AS `codeActif`,`projetlaposte`.`balance`.`tranche` AS `tranche`,`projetlaposte`.`entite`.`adresse` AS `adresse`,`projetlaposte`.`entite`.`codePostal` AS `codePostal`,`projetlaposte`.`entite`.`ville` AS `ville`,`projetlaposte`.`entite`.`type` AS `type`,`projetlaposte`.`balance`.`dateVerification` AS `dateVerification`,month(`projetlaposte`.`balance`.`dateVerification`) AS `mois` from ((((`projetlaposte`.`prestataire` join `projetlaposte`.`lot` on(`projetlaposte`.`lot`.`id` = `projetlaposte`.`prestataire`.`idLot`)) join `projetlaposte`.`entite` on(`projetlaposte`.`entite`.`idPrestataire` = `projetlaposte`.`prestataire`.`id`)) join `projetlaposte`.`balance` on(`projetlaposte`.`balance`.`idEntite` = `projetlaposte`.`entite`.`id`)) join `projetlaposte`.`modele` on(`projetlaposte`.`modele`.`id` = `projetlaposte`.`balance`.`idModele`));");
        $this->db->query("CREATE OR REPLACE VIEW reporting_prestataire AS select `projetlaposte`.`prestataire`.`libelle` AS `Prestataire`,`projetlaposte`.`lot`.`numeroLot` AS `numeroLot`,substr(`projetlaposte`.`entite`.`codeRegate`,1,2) AS `Departement`,`projetlaposte`.`entite`.`codeRegate` AS `codeRegate`,`projetlaposte`.`entite`.`libelle` AS `Libelle_Entité`,`projetlaposte`.`balance`.`codeArticle` AS `codeArticle`,`projetlaposte`.`modele`.`libelle` AS `Libellé_Article`,`projetlaposte`.`balance`.`numeroSerie` AS `numeroSerie`,`projetlaposte`.`balance`.`codeActif` AS `codeActif`,`projetlaposte`.`balance`.`tranche` AS `tranche`,`projetlaposte`.`verification`.`statutVerification` AS `statutVerification`,`projetlaposte`.`entite`.`adresse` AS `adresse`,`projetlaposte`.`entite`.`type` AS `type`,`projetlaposte`.`balance`.`dateVerification` AS `Précédente_VP`,month(`projetlaposte`.`balance`.`dateVerification`) AS `Mois_Précédente_VP`,`projetlaposte`.`verification`.`dateVerification` AS `VP_Faite`,month(`projetlaposte`.`verification`.`dateVerification`) AS `Mois_VP_Faite`,`projetlaposte`.`verification`.`statutBalance` AS `statutBalance` from (((((`projetlaposte`.`prestataire` join `projetlaposte`.`lot` on(`projetlaposte`.`lot`.`id` = `projetlaposte`.`prestataire`.`idLot`)) join `projetlaposte`.`entite` on(`projetlaposte`.`entite`.`idLot` = `projetlaposte`.`lot`.`id`)) join `projetlaposte`.`balance` on(`projetlaposte`.`balance`.`idEntite` = `projetlaposte`.`entite`.`id`)) join `projetlaposte`.`modele` on(`projetlaposte`.`modele`.`id` = `projetlaposte`.`balance`.`idModele`)) join `projetlaposte`.`verification` on(`projetlaposte`.`verification`.`idBalance` = `projetlaposte`.`balance`.`id`));");
        $this->db->query("CREATE OR REPLACE VIEW test AS select case when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixverification`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 0-30KG') then `projetlaposte`.`prixverification`.`tranche0à30` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixverification`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 31-200KG') then `projetlaposte`.`prixverification`.`tranche31à200` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixverification`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 201-600KG') then `projetlaposte`.`prixverification`.`tranche201à600` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixverification`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCES 601-1500KG') then `projetlaposte`.`prixverification`.`tranche601à1500` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixverification`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE SUPERIEURE A 1500 KG') then `projetlaposte`.`prixverification`.`tranche1501à3000` end AS `prixVerification`,case when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixdeplacement`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 0-30KG') then `projetlaposte`.`prixdeplacement`.`tranche0à30` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixdeplacement`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 31-200KG') then `projetlaposte`.`prixdeplacement`.`tranche31à200` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixdeplacement`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE 201-600KG') then `projetlaposte`.`prixverification`.`tranche201à600` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixdeplacement`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCES 601-1500KG') then `projetlaposte`.`prixdeplacement`.`tranche601à1500` when (substr(`projetlaposte`.`entite`.`codeRegate`,1,2) = `projetlaposte`.`prixdeplacement`.`numeroDepartement` and `projetlaposte`.`balance`.`tranche` = 'BALANCE SUPERIEURE A 1500 KG') then `projetlaposte`.`prixverification`.`tranche1501à3000` end AS `prixDeplacement`,`projetlaposte`.`prixverification`.`numeroDepartement` AS `numeroDepartement`,month(`projetlaposte`.`verification`.`dateVerification`) AS `mois`,`projetlaposte`.`prestataire`.`libelle` AS `Libellé_Prestataire`,`projetlaposte`.`lot`.`numeroLot` AS `numeroLot`,`projetlaposte`.`entite`.`codeRegate` AS `codeRegate`,`projetlaposte`.`balance`.`codeArticle` AS `codeArticle`,`projetlaposte`.`balance`.`tranche` AS `tranche`,`projetlaposte`.`modele`.`libelle` AS `Modele_Libellé`,`projetlaposte`.`verification`.`id` AS `idVerif` from (((((((`projetlaposte`.`prestataire` join `projetlaposte`.`prixverification` on(`projetlaposte`.`prestataire`.`id` = `projetlaposte`.`prixverification`.`idPrestataire`)) join `projetlaposte`.`lot` on(`projetlaposte`.`lot`.`id` = `projetlaposte`.`prestataire`.`idLot`)) join `projetlaposte`.`entite` on(`projetlaposte`.`entite`.`idPrestataire` = `projetlaposte`.`prestataire`.`id`)) join `projetlaposte`.`balance` on(`projetlaposte`.`balance`.`idEntite` = `projetlaposte`.`entite`.`id`)) join `projetlaposte`.`modele` on(`projetlaposte`.`modele`.`id` = `projetlaposte`.`balance`.`idModele`)) join `projetlaposte`.`prixdeplacement` on(`projetlaposte`.`prestataire`.`id` = `projetlaposte`.`prixdeplacement`.`idPrestataire`)) join `projetlaposte`.`verification` on(`projetlaposte`.`balance`.`id` = `projetlaposte`.`verification`.`idBalance`));");
        $this->db->query("CREATE OR REPLACE VIEW tarifverificationdeplacement As select `test`.`prixVerification` AS `prixVerification`,`test`.`prixDeplacement` AS `prixDeplacement`,`test`.`numeroDepartement` AS `numeroDepartement`,`test`.`mois` AS `mois`,`test`.`Libellé_Prestataire` AS `Libellé_Prestataire`,`test`.`numeroLot` AS `numeroLot`,`test`.`codeRegate` AS `codeRegate`,`test`.`codeArticle` AS `codeArticle`,`test`.`tranche` AS `tranche`,`test`.`Modele_Libellé` AS `Modele_Libellé`,`test`.`idVerif` AS `idVerif` from `projetlaposte`.`test` where `test`.`prixVerification` is not null and `test`.`prixDeplacement` is not null;");
        echo "Vue créer";
        redirect(site_url("user/admin"), 'refresh');
        return;
    }

}