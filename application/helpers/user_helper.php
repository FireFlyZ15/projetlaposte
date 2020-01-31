<?php
if ( ! function_exists('connected_redirect')){
    /**
     * Redirection selon la session
     * @param $session La session de l'utilisateur
     * @param $isconnected Si la connexion de l'utilisateur doit etre active ou nom pour être redirigé
     * @param $redirect Lien de la page de la redirection
     */
    function connected_redirect($session, $isconnected, $redirect) {
        if($session->has_userdata('logged_in') == $isconnected) {
            redirect($redirect, 'refresh');
        }
    }
}

if ( ! function_exists('admin_redirect')){
    /**
     * Redirection si l'utilisateur n'est pas un administrateur
     * @param $session La session de l'utilisateur
     * @param $redirect Lien de la page de la redirection
     */
    function admin_redirect($user, $redirect) {
        if($user == null || $user->type != "admin") {
            redirect($redirect, 'refresh');
        }
    }
}

if ( ! function_exists('user_redirect')){
    /**
     * Redirection si l'utilisateur n'est pas un type autorisé
     * @param $session La session de l'utilisateur
     * @param $redirect Lien de la page de la redirection
     * @param $type Liste des utilisateurs autorisés
     */
    function user_redirect($user, $redirect, $type) {
        if($user == null || !in_array($user->type, $type)) {
            redirect($redirect, 'refresh');
        }
    }
}
if (!function_exists('user_check')) {
    /**
     * Verifie si l'utilisateur n'est pas un type autorisé
     * @param $session La session de l'utilisateur
     * @param $type Liste des utilisateurs autorisés
     */
    function user_check($user, $type)
    {
        if ($user == null || !in_array($user->type, $type)) {
            return true;
        }
        return false;
    }
}
if ( ! function_exists('is_allowed_modify_graph')){
    /**
     * True si l'utilisateur est autorisé à modifier le graphique
     * @param $user L'utilistateur
     * @param $graph Graphique a modifier
     */
    function is_allowed_modify_graph($user, $graph, $group_In_User=[]) {
        $result=false;
        if((($user->type=="admin") && ($graph->group == null || $graph->group=="0" || in_array($graph->group, $group_In_User)) && $graph->public=="1") || $user->id==$graph->user){
            $result=true;
        }
        return $result;
    }
}

if ( ! function_exists('is_allowed_duplicate_graph')){
    /**
     * True si l'utilisateur est autorisé à dupliquer le graphique
     * @param $user L'utilistateur
     * @param $graph Graphique a modifier
     */
    function is_allowed_duplicate_graph($user, $graph, $group_In_User=[]) {
        $result=false;
        if((($user->type=="admin" || $user->type=="createur") && ($graph->group == null || $graph->group=="0" || in_array($graph->group, $group_In_User)) && $graph->public=="1") || $user->id==$graph->user){
            $result=true;
        }
        return $result;
    }
}

if ( ! function_exists('is_allowed_view_graph')){
    /**
     * True si l'utilisateur est autorisé à voir le graphique
     * @param $user L'utilistateur
     * @param $graph Graphique a modifier
     */
    function is_allowed_view_graph($user, $graph, $group_In_User=[]) {
        $result=false;
        if($user->id==$graph->user || ($graph->public=="1" && ($graph->group == "" || $graph->group == null || $graph->group=="0" || in_array($graph->group, $group_In_User)))){
            $result=true;
        }
        return $result;
    }
}
if ( ! function_exists('generate_create_user_form')){
    /**
     * Generation d'un formulaire de création d'utilisateur
     * @param $session La session de l'utilisateur
     * @param $redirect Lien de la page de la redirection
     */
    function generate_create_user_form($action, $error) {
        $formReturn = "";
        $data_form_button = array(
            'name'          => 'registration',
            'id'            => 'registration',
            'value'         => 'true',
            'type'          => 'submit',
            'content'       => 'Connexion',
            'class'         => 'form-control',
            'onclick'      => 'return checkRegiterForm(\'email_registration\',\'password_registration\',\'password_registration_verification\')'
        );
        $data_form_email = array(
            'name'          => 'email_registration',
            'id'            => 'email_registration',
            'type'          => 'email',
            'class'         => 'form-control',
            'placeholder'   => 'Email',
            'required' => 'required'
        );
        $data_form_password = array(
            'name'          => 'password_registration',
            'id'            => 'password_registration',
            'type'          => 'password',
            'class'         => 'form-control',
            'placeholder'   => 'Mot de passe',
            'required' => 'required',
            'rules'   => 'trim|required|callback_oldpassword_check'
        );
        $data_form_password_verification = array(
            'name'          => 'password_registration_verification',
            'id'            => 'password_registration_verification',
            'type'          => 'password',
            'class'         => 'form-control',
            'placeholder'   => 'Mot de passe',
            'required' => 'required',
            'onchange'      => 'checkPassword(\'password_registration\',\'password_registration_verification\');'
        );
        $formReturn .= form_open($action);
        if($error) $formReturn .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
        $formReturn .= form_label('Email', 'email_registration');
        $formReturn .= form_input($data_form_email);
        $formReturn .= form_error('email_registration', '<div class="error">', '</div>');
        $formReturn .= form_label('Mot de passe', 'password_registration');
        $formReturn .= form_password($data_form_password);
        $formReturn .= form_label('Confirmation du mot de passe', 'password_registration_verification');
        $formReturn .= form_password($data_form_password_verification);
        $formReturn .= form_error('password_registration_verification', '<div class="error">', '</div>');

        $formReturn .= form_button($data_form_button)."<br>";
        $formReturn .= form_close();
        return $formReturn;
    }
}