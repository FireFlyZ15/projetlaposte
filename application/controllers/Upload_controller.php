<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Upload_controller extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }
    
    public function do_upload(){
        $config = array(
            'upload_path' => "./uploads/",
            'allowed_types' => "gif|jpg|jpeg|png|iso|dmg|zip|rar|doc|docx|xls|xlsx|ppt|pptx|csv|ods|odt|odp|pdf|rtf|sxc|sxi|txt|exe|avi|mpeg|mp3|mp4|3gp",
            'overwrite' => TRUE,
            'max_size' => "10048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
            'max_height' => "10000",
            'max_width' => "10000"
        );
        $this->load->library('upload', $config);
        if($this->upload->do_upload())
        {
            $data = $this->upload->data();
            redirect('/crud/ajout/'.$data['full_path']);
        }
        else
        {
            $error = array('error' => $this->upload->display_errors());
            var_dump($error);
            exit;
        }
    }
}