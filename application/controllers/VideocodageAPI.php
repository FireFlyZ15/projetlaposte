<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VideocodageAPI extends CI_Controller
{

    public function index()
    {
        //this->load->view('welcome_message');
        $this->load->helper('html');
        echo doctype();
        echo "<h1>API Videocodage (alpha)</h1>";
        echo "Ajouter '?console=true' à la fin de l'url pour générer un json";
        echo "<h2>Voir une enveloppe à videocoder/videocodée</h2>";
        echo "http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_picture_isie/[isie]<br>";
        echo "<h2>Avoir une enveloppe aléatoire à videocoder</h2>";
        echo "http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_random_videocoding_request/<br>";
        echo "<h2>Avoir une enveloppe aléatoire à videocoder de type (rfx|urgent|nonurgent)</h2>";
        echo "http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_random_videocoding_request/[type]<br>";
        echo "[type]='all'|'rfx'|'norfx'|'urgent'|'nonurgent'<br>";
        echo "[type] par defaut c'est all";
        echo "<h2>Avoir une enveloppe à videocoder de type (rfx|urgent|nonurgent) et d'origine la PIC suivante (code_roc)</h2>";
        echo "Mode aléatoire : http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_random_videocoding_request/[mode]/[type]/[code_roc]<br>";
        echo "Mode LIFO : http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_videocoding_request_lifo/[mode]/[type]/[code_roc]<br>";
        echo "Mode FIFO : http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_videocoding_request_fifo/[mode]/[type]/[code_roc]<br>";
        echo "Mode FEFO : http://150.60.64.30/360/Videocodage_recette/index.php/VideocodageAPI/get_videocoding_request_fefo/[mode]/[type]/[code_roc]<br>";
        echo "[mode]='all'|'cp'|'adresse'<br>";
        echo "[type]='all'|'rfx'|'norfx'|'urgent'|'nonurgent'<br>";
        echo "[code_roc]='code_roc'<br>";
        echo "<table>";
        echo "<thead><th>code_roc</th><th>nom_plateforme_tri</th></thead>";
        echo "<tr><td>A01033</td><td>NICE COTE D AZUR PIC</td>";
        echo "<tr><td>A03320</td><td>BOURGES CTC</td>";
        echo "<tr><td>A03717</td><td>AJACCIO CTC</td>";
        echo "<tr><td>A03718</td><td>BASTIA CTC</td>";
        echo "<tr><td>A03930</td><td>LONGVIC DIJON PIC</td>";
        echo "<tr><td>A04943</td><td>VALENCE VALLEE DU RHONE PIC</td>";
        echo "<tr><td>A05505</td><td>GUIPAVAS BREST FINISTERE PIC</td>";
        echo "<tr><td>A08870</td><td>ORVAULT NANTES ATLANTIQUE PIC</td>";
        echo "<tr><td>A09189</td><td>FLEURY LOIRET PIC</td>";
        echo "<tr><td>A09831</td><td>ANGERS PIC</td>";
        echo "<tr><td>A12599</td><td>LEMPDES CLERMONT AUVERGNE PIC</td>";
        echo "<tr><td>A15254</td><td>CRAN GEVRIER HAUTE SAVOIE PIC</td>";
        echo "<tr><td>A17429</td><td>AVIGNON PPDC</td>";
        echo "<tr><td>A18048</td><td>LIMOGES CTC</td>";
        echo "<tr><td>A19595</td><td>ST DENIS DE LA REUNION PIC</td>";
        echo "<tr><td>A21048</td><td>LA VALETTE DU VAR TOULON PIC</td>";
        echo "<tr><td>A21530</td><td>VITROLLES MARSEILLE PROVENCE ALPES PIC</td>";
        echo "<tr><td>A21618</td><td>MONDEVILLE CAEN PIC</td>";
        echo "<tr><td>A22014</td><td>SAINT GIBRIEN PIC</td>";
        echo "<tr><td>A24984</td><td>HOLTZHEIM STRASBOURG EUROPE PIC</td>";
        echo "<tr><td>A25316</td><td>ROUEN MADRILLET PIC</td>";
        echo "<tr><td>A25635</td><td>GONESSE PARIS NORD PIC</td>";
        echo "<tr><td>A25759</td><td>SASSENAGE ISERE PIC</td>";
        echo "<tr><td>A26479</td><td>LOGNES PIC</td>";
        echo "<tr><td>A37580</td><td>PAGNY LES GOIN LORRAINE PIC</td>";
        echo "<tr><td>A37668</td><td>SORIGNY TOURS PIC</td>";
        echo "<tr><td>A37845</td><td>ST PRIEST PIC</td>";
        echo "<tr><td>A38276</td><td>WISSOUS PARIS SUD PIC</td>";
        echo "<tr><td>A38909</td><td>MIGNE AUXANCES POITIERS PIC</td>";
        echo "<tr><td>A39002</td><td>CASTELNAU MIDI PYRENEES PIC</td>";
        echo "<tr><td>A39289</td><td>LESQUIN LILLE PIC</td>";
        echo "<tr><td>A39376</td><td>CESTAS BORDEAUX PIC</td>";
        echo "<tr><td>A39660</td><td>ROISSY HUB BSCC PIC</td>";
        echo "<tr><td>A39831</td><td>MAUGUIO LANGUEDOC PIC</td>";
        echo "<tr><td>A41794</td><td>TREMBLAY EN FRANCE ROISSY PIAC</td>";
        echo "<tr><td>A41974</td><td>BOIS D ARCY PIC</td>";
        echo "<tr><td>A45612</td><td>VILLENEUVE LA GARENNE PIC</td>";
        echo "<tr><td>A46451</td><td>NOYAL CHATILLON RENNES ARMORIQUE PIC</td>";
        echo "<tr><td>A71043</td><td>ROYE PIC</td>";
        echo "<tr><td>A71210</td><td>BOBIGNY DRL CTEDI</td>";
        echo "</table>";

    }

    public function get_picture_isie($isie = NULL, $code_roc = NULL)
    {
        session_write_close();
        $this->load->helper('html');

        $this->load->model('Videocodage_model');
        $enveloppe = $this->Videocodage_model->getEnveloppe($isie);
        if ($enveloppe == null) {
            echo "L'isie $isie n'est pas dans la liste des plis à videocoder";
            return;
        }
        if ($code_roc == NULL) {
            $plateform = $this->Videocodage_model->getRegate($enveloppe->id_plateforme_tri);
            if ($plateform == null) {
                echo "La plateforme $enveloppe->id_plateforme_tri n'existe pas";
                return;
            }
            $regate = $plateform->regate;
        } else {
            $plateform = $this->Videocodage_model->getRegate($code_roc);
            if ($plateform == null) {
                echo "La plateforme $code_roc n'existe pas";
                return;
            }
            $regate = $plateform->regate;
        }

        if ($this->input->get("console")) {
            $console = $this->input->get("console");
        } else {
            $console = false;
        }
        if ($console) {
            header('Content-Type: application/json');
            $conf = new stdClass();
            $conf->image_url = "http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg";
            $conf->isie = $enveloppe->isie;
            $conf->etat_enveloppe = $enveloppe->etat_enveloppe;
            $conf->id_rao = $enveloppe->id_rao;
            $conf->type_id_rao = $enveloppe->type_id_rao;
            if ($conf->type_id_rao == "rejet" || $conf->type_id_rao == "defaut" || $conf->type_id_rao == "export") {
                $conf->departement = "";
            } else {
                $conf->departement = str_split($enveloppe->id_rao, 2)[0];
            }
            $conf->id_plateforme_tri = $enveloppe->id_plateforme_tri;
            $conf->nom_plateforme_tri = $enveloppe->nom_plateforme_tri;
            $conf->expires = $enveloppe->expires;
            print json_encode($conf);
        } else {
            echo doctype();
            echo "<h1>$isie - $regate</h1>";
            print_r($enveloppe);
            echo "<br>";
            print_r($plateform);
            echo "<br>";
            echo img("http://ute.$regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg");
            echo "<br>";
            $myxmlfilecontent = file_get_contents("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie");
            $this->load->helper('xml');
            echo xml_format($myxmlfilecontent);
        }

    }

    public function get_random_videocoding_request($mode = "all", $type = "all", $roc = "")
    {
        session_write_close();
        $this->load->helper('html');
        if ($type != "all" && $type != "rfx" && $type != "norfx" && $type != "urgent" && $type != "nonurgent") {
            echo "ERREUR : Le type " . $type . " n'existe pas.";
            return;
        }
        $this->load->model('Videocodage_model');
        if ($roc != "" && $this->Videocodage_model->getRegate($roc) == null) {
            echo "ERREUR : Le code roc " . $roc . " n'existe pas.";
            return;
        }

        if ($this->input->get("console")) {
            $console = $this->input->get("console");
        } else {
            $console = false;
        }

        if ($mode != "all" && $mode != "cp" && $mode != "adresse") {
            echo "ERREUR : Le mode " . $mode . " n'existe pas.";
            return;
        }
        $enveloppe = $this->Videocodage_model->getRandumEnveloppev2("EN ATTENTE DE VIDEOCODAGE", $type, $roc, $mode);
        $plateform = $this->Videocodage_model->getRegate($enveloppe->id_plateforme_tri);

        if ($console) {
            header('Content-Type: application/json');
            $conf = new stdClass();
            $conf->image_url = "http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg";
            $conf->isie = $enveloppe->isie;
            $conf->etat_enveloppe = $enveloppe->etat_enveloppe;
            $conf->id_rao = $enveloppe->id_rao;
            $conf->type_id_rao = $enveloppe->type_id_rao;
            if ($conf->type_id_rao == "rejet" || $conf->type_id_rao == "defaut" || $conf->type_id_rao == "export") {
                $conf->departement = "";
            } else {
                $conf->departement = str_split($enveloppe->id_rao, 2)[0];
            }
            $conf->id_plateforme_tri = $enveloppe->id_plateforme_tri;
            $conf->nom_plateforme_tri = $enveloppe->nom_plateforme_tri;
            $conf->expires = $enveloppe->expires;
            if (!in_array($enveloppe->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                $adresse = $this->Videocodage_model->getAdresse($enveloppe->id_rao);
                $conf->code_postal = $adresse->code_postal;
            } else {
                $conf->code_postal = "";
            }
            print json_encode($conf);
        } else {
            echo doctype();
            print_r($enveloppe);
            echo "<br>";
            print_r($plateform);
            echo "<br>";
            echo img("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg");
            echo "<br>";
            $myxmlfilecontent = file_get_contents("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie");
            $this->load->helper('xml');
            echo xml_format($myxmlfilecontent);
        }

    }

    public function get_videocoding_request_lifo($mode = "all", $type = "all", $roc = "")
    {
        session_write_close();
        $this->load->helper('html');
        if ($type != "all" && $type != "rfx" && $type != "norfx" && $type != "urgent" && $type != "nonurgent") {
            echo "ERREUR : Le type " . $type . " n'existe pas.";
            return;
        }
        $this->load->model('Videocodage_model');
        if ($roc != "" && $this->Videocodage_model->getRegate($roc) == null) {
            echo "ERREUR : Le code roc " . $roc . " n'existe pas.";
            return;
        }

        if ($this->input->get("console")) {
            $console = $this->input->get("console");
        } else {
            $console = false;
        }
        if ($mode != "all" && $mode != "cp" && $mode != "adresse") {
            echo "ERREUR : Le mode " . $mode . " n'existe pas.";
            return;
        }


        $enveloppe = $this->Videocodage_model->getLastEnveloppev2("EN ATTENTE DE VIDEOCODAGE", $type, $roc, $mode);
        $plateform = $this->Videocodage_model->getRegate($enveloppe->id_plateforme_tri);
        if ($console) {
            header('Content-Type: application/json');
            $conf = new stdClass();
            $conf->image_url = "http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg";
            $conf->isie = $enveloppe->isie;
            $conf->etat_enveloppe = $enveloppe->etat_enveloppe;
            $conf->id_rao = $enveloppe->id_rao;
            $conf->type_id_rao = $enveloppe->type_id_rao;
            if ($conf->type_id_rao == "rejet" || $conf->type_id_rao == "defaut" || $conf->type_id_rao == "export") {
                $conf->departement = "";
            } else {
                $conf->departement = str_split($enveloppe->id_rao, 2)[0];
            }
            $conf->id_plateforme_tri = $enveloppe->id_plateforme_tri;
            $conf->nom_plateforme_tri = $enveloppe->nom_plateforme_tri;
            $conf->expires = $enveloppe->expires;
            if (!in_array($enveloppe->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                $adresse = $this->Videocodage_model->getAdresse($enveloppe->id_rao);
                $conf->code_postal = $adresse->code_postal;
            } else {
                $conf->code_postal = "";
            }
            print json_encode($conf);
        } else {
            echo doctype();
            print_r($enveloppe);
            echo "<br>";
            print_r($plateform);
            echo "<br>";
            echo img("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg");
            echo "<br>";
            $myxmlfilecontent = file_get_contents("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie");
            $this->load->helper('xml');
            echo xml_format($myxmlfilecontent);
        }

    }

    public function get_videocoding_request_fefo($mode = "all", $type = "all", $roc = "")
    {
        session_write_close();
        $this->load->helper('html');
        if ($type != "all" && $type != "rfx" && $type != "norfx" && $type != "urgent" && $type != "nonurgent") {
            echo "ERREUR : Le type " . $type . " n'existe pas.";
            return;
        }
        $this->load->model('Videocodage_model');
        if ($roc != "" && $this->Videocodage_model->getRegate($roc) == null) {
            echo "ERREUR : Le code roc " . $roc . " n'existe pas.";
            return;
        }
        if ($mode != "all" && $mode != "cp" && $mode != "adresse") {
            echo "ERREUR : Le mode " . $mode . " n'existe pas.";
            return;
        }
        if ($this->input->get("console")) {
            $console = $this->input->get("console");
        } else {
            $console = false;
        }


        $enveloppe = $this->Videocodage_model->getEnveloppeFEFO("EN ATTENTE DE VIDEOCODAGE", $type, $roc, $mode);
        $plateform = $this->Videocodage_model->getRegate($enveloppe->id_plateforme_tri);
        if ($console) {
            header('Content-Type: application/json');
            $conf = new stdClass();
            $conf->image_url = "http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg";
            $conf->isie = $enveloppe->isie;
            $conf->etat_enveloppe = $enveloppe->etat_enveloppe;
            $conf->id_rao = $enveloppe->id_rao;
            $conf->type_id_rao = $enveloppe->type_id_rao;
            if ($conf->type_id_rao == "rejet" || $conf->type_id_rao == "defaut" || $conf->type_id_rao == "export") {
                $conf->departement = "";
            } else {
                $conf->departement = str_split($enveloppe->id_rao, 2)[0];
            }
            $conf->id_plateforme_tri = $enveloppe->id_plateforme_tri;
            $conf->nom_plateforme_tri = $enveloppe->nom_plateforme_tri;
            $conf->expires = $enveloppe->expires;
            if (!in_array($enveloppe->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                $adresse = $this->Videocodage_model->getAdresse($enveloppe->id_rao);
                $conf->code_postal = $adresse->code_postal;
            } else {
                $conf->code_postal = "";
            }
            print json_encode($conf);
        } else {
            echo doctype();
            print_r($enveloppe);
            echo "<br>";
            print_r($plateform);
            echo "<br>";
            echo img("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg");
            echo "<br>";
            $myxmlfilecontent = file_get_contents("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie");
            $this->load->helper('xml');
            echo xml_format($myxmlfilecontent);
        }

    }

    public function get_videocoding_request_fifo($mode = "all", $type = "all", $roc = "")
    {
        session_write_close();
        $this->load->helper('html');
        if ($type != "all" && $type != "rfx" && $type != "norfx" && $type != "urgent" && $type != "nonurgent") {
            echo "ERREUR : Le type " . $type . " n'existe pas.";
            return;
        }
        $this->load->model('Videocodage_model');
        if ($roc != "" && $this->Videocodage_model->getRegate($roc) == null) {
            echo "ERREUR : Le code roc " . $roc . " n'existe pas.";
            return;
        }
        if ($mode != "all" && $mode != "cp" && $mode != "adresse") {
            echo "ERREUR : Le mode " . $mode . " n'existe pas.";
            return;
        }
        if ($this->input->get("console")) {
            $console = $this->input->get("console");
        } else {
            $console = false;
        }


        $enveloppe = $this->Videocodage_model->getEnveloppeFIFO("EN ATTENTE DE VIDEOCODAGE", $type, $roc, $mode);
        $plateform = $this->Videocodage_model->getRegate($enveloppe->id_plateforme_tri);
        if ($console) {
            header('Content-Type: application/json');
            $conf = new stdClass();
            $conf->image_url = "http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg";
            $conf->isie = $enveloppe->isie;
            $conf->etat_enveloppe = $enveloppe->etat_enveloppe;
            $conf->id_rao = $enveloppe->id_rao;
            $conf->type_id_rao = $enveloppe->type_id_rao;
            if ($conf->type_id_rao == "rejet" || $conf->type_id_rao == "defaut" || $conf->type_id_rao == "export") {
                $conf->departement = "";
            } else {
                $conf->departement = str_split($enveloppe->id_rao, 2)[0];
            }
            $conf->id_plateforme_tri = $enveloppe->id_plateforme_tri;
            $conf->nom_plateforme_tri = $enveloppe->nom_plateforme_tri;
            $conf->expires = $enveloppe->expires;
            if (!in_array($enveloppe->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                $adresse = $this->Videocodage_model->getAdresse($enveloppe->id_rao);
                $conf->code_postal = $adresse->code_postal;
            } else {
                $conf->code_postal = "";
            }
            print json_encode($conf);
        } else {
            echo doctype();
            print_r($enveloppe);
            echo "<br>";
            print_r($plateform);
            echo "<br>";
            echo img("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie/image?format=jpeg");
            echo "<br>";
            $myxmlfilecontent = file_get_contents("http://ute.$plateform->regate.prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/$enveloppe->isie");
            $this->load->helper('xml');
            echo xml_format($myxmlfilecontent);
        }

    }

    public function test_pass()
    {
        echo hash('sha256', 'dernier_passage_pf_code_postal_2018.libelle_plateforme_suivante_code_roc');
    }

    public function check_videocodage_redis()
    {
        session_write_close();
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "<h1>Videocodage CP : " . $redis->lLen("video:cp") . "</h1>";
        foreach ($redis->lrange("video:cp", 0, 20) as $value) {

            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo "EXPIRE<br>";
            }

        }
        echo "<h1>Videocodage ADRESSE : " . $redis->lLen("video:adresse") . "</h1>";
        foreach ($redis->lrange("video:adresse", 0, 20) as $value) {
            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo "EXPIRE<br>";
            }
        }

        echo "<h1>Videocodage RFX : " . $redis->lLen("video:rfx") . "</h1>";
        foreach ($redis->lrange("video:rfx", 0, 20) as $value) {
            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo "EXPIRE<br>";
            }
        }

        $redis->close();
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée stat : " . ($dateend->format('U') - $datebegin->format('U'));
    }

    public function check_videocodage_redis_v2()
    {
        session_write_close();
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "<h1>Videocodage CP : " . $redis->lLen("video2:cp") . "</h1>";
        foreach ($redis->lrange("video2:cp", 0, 20) as $value) {
            $videoJSON = json_decode($value);
            $date2 = new DateTime($videoJSON->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');

            if ($timeout > 0) {
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $videoJSON->isie . " " . "EXPIRE<br>";
            }
        }
        echo "<h1>Videocodage ADRESSE : " . $redis->lLen("video2:adresse") . "</h1>";
        foreach ($redis->lrange("video2:adresse", 0, 20) as $value) {
            $videoJSON = json_decode($value);
            $date2 = new DateTime($videoJSON->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');

            if ($timeout > 0) {
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $videoJSON->isie . " " . "EXPIRE<br>";
            }
        }

        echo "<h1>Videocodage RFX : " . $redis->lLen("video2:rfx") . "</h1>";
        foreach ($redis->lrange("video2:rfx", 0, 20) as $value) {
            $videoJSON = json_decode($value);
            $date2 = new DateTime($videoJSON->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');

            if ($timeout > 0) {
                echo $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $videoJSON->isie . " " . "EXPIRE<br>";
            }
        }

        $redis->close();
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée stat : " . ($dateend->format('U') - $datebegin->format('U'));
    }

    public function purge_videocodage_expire_redis()
    {
        session_write_close();
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "<h1>Videocodage CP : " . $redis->lLen("video:cp") . "</h1>";
        $nbmin = 0;
        $nbmax = 20;
        foreach ($redis->lrange("video:cp", $nbmin, -1) as $key => $value) {
            $video = $redis->get($value);
            if ($video == null) {
                $redis->lRem("video:cp", $value, 1);
            }


        }
        foreach ($redis->lrange("video:cp", $nbmin, $nbmax) as $key => $value) {

            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $key . " : " . $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";

            }

        }
        echo "<h1>Videocodage ADRESSE : " . $redis->lLen("video:adresse") . "</h1>";
        foreach ($redis->lrange("video:adresse", $nbmin, -1) as $key => $value) {
            $video = $redis->get($value);
            if ($video == null) {
                $redis->lRem("video:adresse", $value, 1);
            }


        }
        foreach ($redis->lrange("video:adresse", $nbmin, $nbmax) as $key => $value) {
            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $key . " : " . $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";
            }
        }

        echo "<h1>Videocodage RFX : " . $redis->lLen("video:rfx") . "</h1>";
        foreach ($redis->lrange("video:rfx", $nbmin, -1) as $key => $value) {
            $video = $redis->get($value);
            if ($video == null) {
                $redis->lRem("video:rfx", $value, 1);
            }


        }
        foreach ($redis->lrange("video:rfx", $nbmin, $nbmax) as $key => $value) {
            $video = $redis->get($value);
            if ($video != null) {
                $videoJSON = json_decode($video);
                echo $key . " : " . $videoJSON->isie . " " . $videoJSON->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";
            }
        }

        $redis->close();
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée purge : " . ($dateend->format('U') - $datebegin->format('U'));
    }

    public function purge_videocodage_expire_redis_v2()
    {
        session_write_close();
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "<h1>Videocodage CP : " . $redis->lLen("video2:cp") . "</h1>";
        $nbmin = 0;
        $nbmax = 20;
        foreach ($redis->lrange("video2:cp", $nbmin, -1) as $key => $value) {
            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 <= 0) {
                $redis->lRem("video2:cp", $value, 1);
            }


        }
        foreach ($redis->lrange("video2:cp", $nbmin, $nbmax) as $key => $value) {

            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 > 0) {
                echo $key . " : " . $video->isie . " " . $video->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";

            }

        }
        echo "<h1>Videocodage ADRESSE : " . $redis->lLen("video2:adresse") . "</h1>";
        foreach ($redis->lrange("video2:adresse", $nbmin, -1) as $key => $value) {
            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 <= 0) {
                $redis->lRem("video2:adresse", $value, 1);
            }


        }
        foreach ($redis->lrange("video2:adresse", $nbmin, $nbmax) as $key => $value) {
            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 > 0) {
                echo $key . " : " . $video->isie . " " . $video->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";
            }
        }

        echo "<h1>Videocodage RFX : " . $redis->lLen("video2:rfx") . "</h1>";
        foreach ($redis->lrange("video2:rfx", $nbmin, -1) as $key => $value) {
            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 <= 0) {
                $redis->lRem("video2:rfx", $value, 1);
            }


        }
        foreach ($redis->lrange("video2:rfx", $nbmin, $nbmax) as $key => $value) {
            $video = json_decode($value);
            $date2 = new DateTime($video->expires, new DateTimeZone("UTC"));
            $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
            $timeout = $date2->format('U') - $date3->format('U');
            if ($timeout - 21600 > 0) {
                echo $key . " : " . $video->isie . " " . $video->expires . "<br>";
            } else {
                echo $key . " : " . "EXPIRE<br>";
            }
        }

        $redis->close();
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée purge : " . ($dateend->format('U') - $datebegin->format('U'));
    }

    public function other_database()
    {

    }
    public function generate_videocodage_redis($nb = -1)
    {
        session_write_close();
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        //phpinfo();
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->load->model('Videocodage_model');

        $plateformesRAW = $this->Videocodage_model->getAllPlateforme();
        $plateformes = [];
        foreach ($plateformesRAW as $plateforme) {
            $plateformes[$plateforme->code_roc] = $plateforme->regate;
        }
        unset($plateformesRAW);
        //echo "<br><br>";
        $videocodage = $this->Videocodage_model->getAlEnVideocodage();
        $i = 0;
        $redis->del("video:cp");
        $redis->del("video:adresse");
        $redis->del("video:rfx");
        foreach ($videocodage as $item) {
            //print_r($item);
            if ($item->expires != "") {
                $date2 = new DateTime($item->expires, new DateTimeZone("UTC"));
                $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
                $timeout = $date2->format('U') - $date3->format('U');
                if ($timeout > 0) {
                    $video = new stdClass();
                    $video->image_url = "http://ute." . $plateformes[$item->id_plateforme_tri] . ".prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/" . $item->isie . "/image?format=jpeg\"";
                    $video->isie = $item->isie;
                    $video->etat_enveloppe = $item->etat_enveloppe;
                    $video->id_rao = $item->id_rao;
                    $video->type_id_rao = $item->type_id_rao;
                    $video->id_plateforme_tri = $item->id_plateforme_tri;
                    $video->nom_plateforme_tri = $item->nom_plateforme_tri;
                    $video->expires = $item->expires;
                    $video->rfx = $item->rfx;
                    if (!in_array($item->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                        //$adresse=$this->Videocodage_model->getAdresse($item->id_rao);
                        $video->derpartement = substr($item->id_rao, 0, 2);
                        if (isset($adresse->code_postal)) {
                            // $video->code_postal = $adresse->code_postal;
                            $video->code_postal = "";
                        } else {
                            //echo "Pas de code postal pour ".$item->id_rao."<br>";
                            $video->code_postal = "";
                        }

                    } else {
                        $video->code_postal = "";
                        $video->derpartement = "";
                    }
                    if ($video->rfx == 1) {
                        $type = "rfx";
                    } else if ($video->etat_enveloppe == "EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE") {
                        $type = "adresse";
                    } else {
                        $type = "cp";
                    }
                    $key = uniqid("videocodage:" . $type . ":");

                    //echo $date2->format('Y-m-d H:i:s')." - ".$date3->format('Y-m-d H:i:s')."=".$value;
                    if ($timeout - 21600 <= 0) {
                        $timeout = 5;
                    }
                    $redis->set($key, json_encode($video), $timeout);
                    $redis->lPush("video:" . $type, $key);
                    //echo "<br><br>";
                    //print_r($video);
                    //echo "<br><br>";
                }
            }
            $i++;
            if ($nb != -1 && $i > $nb) {
                break;
            }
        }
        echo "Insertion en base de " . $i . "/" . count($videocodage) . " fait";
        $redis->close();
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée d'insertion : " . ($dateend->format('U') - $datebegin->format('U'));
        //echo count($videocodage);

        //echo $redis->get('totoro');

    }

    public function generate_videocodage_redis_v2($nb = -1)
    {
        session_write_close();
        $datebegin = new DateTime('NOW', new DateTimeZone("UTC"));
        //phpinfo();
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->load->model('Videocodage_model');

        $plateformesRAW = $this->Videocodage_model->getAllPlateforme();
        $plateformes = [];
        foreach ($plateformesRAW as $plateforme) {
            $plateformes[$plateforme->code_roc] = $plateforme->regate;
        }
        unset($plateformesRAW);
        //echo "<br><br>";
        $videocodage = $this->Videocodage_model->getAlEnVideocodage();
        $i = 0;
        $redis->del("video2:cp");
        $redis->del("video2:adresse");
        $redis->del("video2:rfx");
        foreach ($videocodage as $item) {
            //print_r($item);
            if ($item->expires != "") {
                $date2 = new DateTime($item->expires, new DateTimeZone("UTC"));
                $date3 = new DateTime('NOW', new DateTimeZone("UTC"));
                $timeout = $date2->format('U') - $date3->format('U');
                if ($timeout > 0) {
                    $video = new stdClass();
                    $video->image_url = "http://ute." . $plateformes[$item->id_plateforme_tri] . ".prd.sie.courrier.intra.laposte.fr/UTE/enveloppe/v1/" . $item->isie . "/image?format=jpeg\"";
                    $video->isie = $item->isie;
                    $video->etat_enveloppe = $item->etat_enveloppe;
                    $video->id_rao = $item->id_rao;
                    $video->type_id_rao = $item->type_id_rao;
                    $video->id_plateforme_tri = $item->id_plateforme_tri;
                    $video->nom_plateforme_tri = $item->nom_plateforme_tri;
                    $video->expires = $item->expires;
                    $video->rfx = $item->rfx;
                    if (!in_array($item->id_rao, ['R1-RX', '', 'R0-DFAD', 'R2-DFAD', 'EXPORT'])) {
                        //$adresse=$this->Videocodage_model->getAdresse($item->id_rao);
                        $video->derpartement = substr($item->id_rao, 0, 2);
                        if (isset($adresse->code_postal)) {
                            //$video->code_postal = $adresse->code_postal;
                            $video->code_postal = "";
                        } else {
                            //echo "Pas de code postal pour ".$item->id_rao."<br>";
                            $video->code_postal = "";
                        }

                    } else {
                        $video->code_postal = "";
                        $video->derpartement = "";
                    }
                    if ($video->rfx == 1) {
                        $type = "rfx";
                    } else if ($video->etat_enveloppe == "EN VIDEOCODAGE ADRESSE GEOGRAPHIQUE") {
                        $type = "adresse";
                    } else {
                        $type = "cp";
                    }
                    //$key = uniqid("videocodage:".$type.":");

                    //echo $date2->format('Y-m-d H:i:s')." - ".$date3->format('Y-m-d H:i:s')."=".$value;
                    //$redis->set($key,json_encode($video),$timeout);
                    $redis->lPush("video2:" . $type, json_encode($video));
                    //echo "<br><br>";
                    //print_r($video);
                    //echo "<br><br>";
                }
            }
            $i++;
            if ($nb != -1 && $i > $nb) {
                break;
            }
        }
        echo "Insertion en base de " . $i . "/" . count($videocodage) . " fait";
        $redis->close();
        //echo count($videocodage);
        $dateend = new DateTime('NOW', new DateTimeZone("UTC"));
        echo "Durée d'insertion : " . ($dateend->format('U') - $datebegin->format('U'));
        //echo $redis->get('totoro');

    }
}
