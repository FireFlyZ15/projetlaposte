<?php
if (!defined('BASEPATH')) exit ('No direct script access allowed')

require_one APPATH."/third_party/PHPExcel-1.8.php"
    
class Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}