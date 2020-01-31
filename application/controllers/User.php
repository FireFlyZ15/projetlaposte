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
                    redirect(site_url('graph/'), 'refresh');
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

}