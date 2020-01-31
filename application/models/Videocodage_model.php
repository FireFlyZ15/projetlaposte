<?php

/**
 * Created by PhpStorm.
 * User: pslj484
 * Date: 04/04/2017
 * Time: 14:16
 */
class Videocodage_model extends CI_Model
{
    public static $table = 'user';
    public static $table_EnVideocodage = 'EnVideocodage';
    public static $table_referentiel_adresse = 'referentiel_adresse';
    public static $table_user_in_group = 'user_in_group';
    public static $database = 'hadoop_viewer';
    public static $database_data = 'hadoopviewer_data';
    public static $config_table = 'config';
    public static $database_videocodage = 'videocodage';
    public static $table_plateform = 'plateformetri_mere';

    public function getEnveloppe($isie)
    {
        $this->load->database();
        return $this->db->get_where(self::$database_data . "." . self::$table_EnVideocodage, array('isie' => $isie))->row();
    }

    public function getAdresse($idrao)
    {
        $this->load->database();
        return $this->db->get_where(self::$database_data . "." . self::$table_referentiel_adresse, array('code_rao' => $idrao), 1)->row();
    }

    public function getRegate($code_roc)
    {
        $this->load->database();
        return $this->db->get_where(self::$database_videocodage . "." . self::$table_plateform, array('code_roc' => $code_roc))->row();
    }

    public function getAllPlateforme()
    {
        $this->load->database();
        $this->db->select("*");
        $this->db->from(self::$database_videocodage . "." . self::$table_plateform);
        return $this->db->get()->result();
    }

    public function getAlEnVideocodage()
    {
        $this->load->database();
        $this->db->select("isie, format, urgence, lettre_verte, date_demande, etat_enveloppe, id_rao, type_id_rao, id_plateforme_tri,nom_plateforme_tri, expires, rfx");
        $this->db->from(self::$database_data . "." . self::$table_EnVideocodage);
        $this->db->where("etat_videocodage", "EN ATTENTE DE VIDEOCODAGE");
        $this->db->order_by('date_demande');
        return $this->db->get()->result();
    }

    public function getRandumEnveloppe($etat = null)
    {
        $this->load->database();
        $this->db->select("*");
        $this->db->from(self::$database_data . "." . self::$table_EnVideocodage);
        if ($etat != null) {
            $this->db->where("etat_videocodage", $etat);
        }
        $this->db->order_by('RAND()');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getRandumEnveloppev2($etat = null, $type = null, $roc = null, $mode = null)
    {
        $this->load->database();
        $where_type = "";
        if ($type == "rfx") {
            $where_type = "and rfx=1";
        } else if ($type == "norfx") {
            $where_type = "and rfx=0";
        } else if ($type == "urgent") {
            $where_type = "and rfx=0 and urgence='Urgent'";
        } else if ($type == "nonurgent") {
            $where_type = "and rfx=0 and urgence='Non urgent'";
        } else {
            $where_type = "";
        }
        if ($roc != null) {
            $where_type .= " and id_plateforme_tri='$roc'";
        }
        if ($mode == "cp") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE CP NATIONAL'";
        } else if ($mode == "adresse") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE'";
        }
        if ($etat != null) {
            //$query="select * from (select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type LIMIT 500) t1  ORDER BY RAND() LIMIT 0,1";
            $query = "select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type  ORDER BY RAND() LIMIT 0,1";
            return $this->db->query($query)->row();
        } else {
            $query = 'select * from (select * from ' . self::$database_data . '.' . self::$table_EnVideocodage . ' LIMIT 500) t1  ORDER BY RAND() LIMIT 0,1;';
            return $this->db->query($query)->row();
        }
    }

    public function getLastEnveloppev2($etat = null, $type = null, $roc = null, $mode = null)
    {
        $this->load->database();
        $where_type = "";
        if ($type == "rfx") {
            $where_type = "and rfx=1";
        } else if ($type == "norfx") {
            $where_type = "and rfx=0";
        } else if ($type == "urgent") {
            $where_type = "and rfx=0 and urgence='Urgent'";
        } else if ($type == "nonurgent") {
            $where_type = "and rfx=0 and urgence='Non urgent'";
        } else {
            $where_type = "";
        }
        if ($roc != null) {
            $where_type .= " and id_plateforme_tri='$roc'";
        }
        if ($mode == "cp") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE CP NATIONAL'";
        } else if ($mode == "adresse") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE'";
        }
        if ($etat != null) {
            //$query="select * from (select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type LIMIT 500) t1  ORDER BY RAND() LIMIT 0,1";
            $query = "select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type  ORDER BY date_demande desc LIMIT 0,1";
            return $this->db->query($query)->row();
        } else {
            $query = 'select * from ' . self::$database_data . '.' . self::$table_EnVideocodage . '  ORDER BY date_demande desc LIMIT 0,1;';
            return $this->db->query($query)->row();
        }
    }

    public function getEnveloppeFIFO($etat = null, $type = null, $roc = null, $mode = null)
    {
        $this->load->database();
        $where_type = "";
        if ($type == "rfx") {
            $where_type = "and rfx=1";
        } else if ($type == "norfx") {
            $where_type = "and rfx=0";
        } else if ($type == "urgent") {
            $where_type = "and rfx=0 and urgence='Urgent'";
        } else if ($type == "nonurgent") {
            $where_type = "and rfx=0 and urgence='Non urgent'";
        } else {
            $where_type = "";
        }
        if ($roc != null) {
            $where_type .= " and id_plateforme_tri='$roc'";
        }
        if ($mode == "cp") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE CP NATIONAL'";
        } else if ($mode == "adresse") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE'";
        }

        if ($etat != null) {
            //$query="select * from (select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type LIMIT 500) t1  ORDER BY RAND() LIMIT 0,1";
            $query = "select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type  ORDER BY date_demande asc LIMIT 0,1";
            return $this->db->query($query)->row();
        } else {
            $query = 'select * from ' . self::$database_data . '.' . self::$table_EnVideocodage . '  ORDER BY date_demande asc LIMIT 0,1;';
            return $this->db->query($query)->row();
        }
    }

    public function getEnveloppeFEFO($etat = null, $type = null, $roc = null, $mode = null)
    {
        $this->load->database();
        $where_type = "";
        if ($type == "rfx") {
            $where_type = "and rfx=1";
        } else if ($type == "norfx") {
            $where_type = "and rfx=0";
        } else if ($type == "urgent") {
            $where_type = "and rfx=0 and urgence='Urgent'";
        } else if ($type == "nonurgent") {
            $where_type = "and rfx=0 and urgence='Non urgent'";
        } else {
            $where_type = "";
        }
        if ($roc != null) {
            $where_type .= " and id_plateforme_tri='$roc'";
        }
        if ($mode == "cp") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE CP NATIONAL'";
        } else if ($mode == "adresse") {
            $where_type .= " and etat_enveloppe='EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE'";
        }
        if ($etat != null) {
            //$query="select * from (select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type LIMIT 500) t1  ORDER BY RAND() LIMIT 0,1";
            $query = "select * from hadoopviewer_data.EnVideocodage where etat_videocodage=\"$etat\" $where_type and expires-UTC_TIMESTAMP()>60 ORDER BY expires asc LIMIT 0,1";
            return $this->db->query($query)->row();
        } else {
            $query = 'select * from ' . self::$database_data . '.' . self::$table_EnVideocodage . ' where expires-UTC_TIMESTAMP()>60 ORDER BY expires asc LIMIT 0,1;';
            return $this->db->query($query)->row();
        }
    }
}