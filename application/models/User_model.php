<?php
/**
 * Class Graphs_model
 * Contient les requetes SQL qui vont permettre de gérer les utilisateurs
 */
class User_model extends CI_Model
{
    public static $table = 'user';
    public static $table_group = 'group';
    public static $table_user_in_group = 'user_in_group';
    public static $database = 'hadoopviewer';
    public static $config_table = 'config';

    public function getUser($email)
    {
        $this->load->database();
        return $this->db->get_where(self::$table, array('email' => $email))->row();
    }

    public function getUserHalf($email)
    {
        $this->load->database();
        return $this->db->select('id, email, type, actual_source, actual_database, actual_table')->get_where(self::$table, array('email' => $email))->row();
    }
    public function getUserID($id)
    {
        $this->load->database();
        return $this->db->get_where(self::$table, array('id' => $id))->row();
    }
    public function getConfig($key)
    {
        $this->load->database();
        return $this->db->get_where(self::$config_table, array('key' => $key))->row()->value;
    }
    public function removeUser($id)
    {
        $this->load->database();
        $this->db->where('id', $id);
        $this->db->delete(self::$table);
    }

    public function addUser($email, $password, $type, $actual_source, $actual_database, $actual_table)
    {
        $this->load->database();
        //Hachage du mot de passe
        $password = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->set('email', $email)
            ->set('password', $password)
            ->set('type', $type)
            ->set('actual_source', $actual_source)
            ->set('actual_database', $actual_database)
            ->set('actual_table', $actual_table)
            ->insert(self::$table);
    }
    public function addGroup($name)
    {
        $this->load->database();
        return $this->db->set('name', $name)
            ->insert(self::$table_group);
    }
    public function removeGroup($id)
    {
        $this->load->database();
        $this->db->where('id', $id);
        $this->db->delete(self::$table_group);
    }
    public function listGroup(){
        $this->load->database();
        return $this->db->select("id, name")
            ->from(self::$table_group)
            ->order_by("name")
            ->get()
            ->result();
    }

    public function getGroup($id)
    {
        $this->load->database();
        return $this->db->get_where(self::$table_group, array('id' => $id))->row();
    }
    public function searchGroup($name)
    {
        $this->load->database();
        return $this->db->get_where(self::$table_group, array('name' => $name))->row();
    }
    public function addUserInGroup($userID, $groupID)
    {
        $this->load->database();
        return $this->db->set('user_id', $userID)
            ->set('group_id', $groupID)
            ->insert(self::$table_user_in_group);
    }
    public function removeAllUserInGroup($id)
    {
        $this->load->database();
        $this->db->where('group_id', $id);
        $this->db->delete(self::$table_user_in_group);
    }
    public function addGroupInUser($groupID, $userID)
    {
        $this->load->database();
        return $this->db->set('user_id', $userID)
            ->set('group_id', $groupID)
            ->insert(self::$table_user_in_group);
    }
    public function removeAllGroupInUser($id)
    {
        $this->load->database();
        $this->db->where('user_id', $id);
        $this->db->delete(self::$table_user_in_group);
    }
    public function getUsers_In_Group($id)
    {
        $this->load->database();
        $resultsSQL = $this->db->get_where(self::$table_user_in_group, array('group_id' => $id))->result();
        $result = [];
        foreach ($resultsSQL as $resultSQL) {
            $result[] = $resultSQL->user_id;
        }
        return $result;
    }
    public function getGroup_In_User($id)
    {
        $this->load->database();
        $resultsSQL = $this->db->get_where(self::$table_user_in_group, array('user_id' => $id))->result();
        $result = [];
        foreach ($resultsSQL as $resultSQL) {
            $result[] = $resultSQL->group_id;
        }
        return $result;
    }
    public function listUsers_In_Group(){
        $this->load->database();
        return $this->db->select("*")
            ->from(self::$table_user_in_group)
            ->get()
            ->result();
    }
    public function countUsersInGroup(){
        $this->load->database();
        $this->db->select("group_id, count(*) as nb");
        $this->db->from(self::$table_user_in_group);
        $this->db->group_by("group_id");
        $list = $this->db->get()->result();
        $result = [];
        foreach ($list as $row){
            $result[$row->group_id] = $row->nb;
        }
        return $result;
    }
    public function listUser(){
        $this->load->database();
        return $this->db->select("id, email, type, actual_table")
            ->from(self::$table)
            ->get()
            ->result();
    }

    public function changeBDD($email, $source, $database, $table)
    {
        $this->load->database();
        $data = array(
            'actual_source' => $source,
            'actual_database' => $database,
            'actual_table' => $table
        );
        $this->db->where('email', $email);
        $this->db->update(self::$table, $data);
    }
    public function changepassword($id, $newpassword){
        $this->load->database();
        $newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
        $data = array(
            'password' => $newpassword
        );
        $this->db->where('id', $id);
        $this->db->update(self::$table, $data);
    }

    public function changedefaultBDD($newSource, $newDatabase, $newTable)
    {
        $this->load->database();
        $data = array(
            'value' => $newTable
        );
        $this->db->where('key', 'default_table');

        $this->db->update(self::$config_table, $data);

        $data = array(
            'value' => $newSource
        );
        $this->db->where('key', 'default_source');
        $this->db->update(self::$config_table, $data);
        $data = array(
            'value' => $newDatabase
        );
        $this->db->where('key', 'default_database');
        $this->db->update(self::$config_table, $data);
    }
    public function changedescriptiondatabase($description){
        $this->load->database();
        $data = array(
            'value' => $description
        );
        $this->db->where('key', 'descriptiondatabase');
        $this->db->update(self::$config_table, $data);
    }
    public function changedefaulttypecalcul($config){
        $this->load->database();
        $data = array(
            'value' => $config
        );
        $this->db->where('key', 'default_typecalcul');
        $this->db->update(self::$config_table, $data);
    }
    public function switchType($id){
        $this->load->database();
        $user = $this->getUserID($id);
        $type = "";
        if($user==NULL) {
            return;
        } else if($user->type=="admin"){
            $type = "lecteur";
        } else {
            $type = "admin";
        }
        $data = array(
            'type' => $type
        );
        $this->db->where('id', $id);
        $this->db->update(self::$table, $data);
        return $user;
    }
    public function changeType($id, $type){
        $this->load->database();
        $user = $this->getUserID($id);
        $typeVerifier = "";
        if($user==NULL) {
            return;
        } else if($type=="admin" || $type=="lecteur" || $type=="createur"){
            $typeVerifier = $type;
        }else{
            return;
        }
        $data = array(
            'type' => $typeVerifier
        );
        $this->db->where('id', $id);
        $this->db->update(self::$table, $data);
        return $user;
    }
}