<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define("PROJECT_NAME", "360° Enveloppe");
define("VERSION", "v2019.03-1.1");
define("ENVIRONEMENT","");
/*
 * Gestion des dates
 */
define("DATE_FORMAT_FR_LIST",json_encode(array("Année (1991)","Mois (11)","Mois (1991-12)","Jour (25)","Jour (1991-11-25)")));
define("DATE_FORMAT_FR_LIST_TIMETAMPS",json_encode(array("Année (1991)","Mois (11)","Mois (1991-11)","Jour (25)","Jour (1991-11-25)","Heure (1991-11-25 10:00)")));
define("DATE_FORMAT_LIST",json_encode(array("YEAR","MONTH","YEARMONTH","DAY","DATE","DATEHOUR")));

/*
 * Formulaires de création de graphiques
 */
define("GRAPH_CONSTRUCTION_TITLE","Construction du graphique");
define("TABLE_CONSTRUCTION_TITLE","Construction du tableau");
define("EXPORT_CONSTRUCTION_TITLE","Construction de l'export");
define("GRAPH_FORM_LABEL","Forme du graphique");
define("STACKED_LABEL","Cumuler");

define("DATA_TITLE","Données");
define("VALUE_LABEL","Valeur à visualiser");
define("VALUE_SEPARATE_LABEL","Critère de séparation");
define("CALC_LABEL","Calcul");

define("COLUMN_LABEL","Colonne");
define("LINE_LABEL","Ligne");

define("FIELD_LABEL","Champs");

define("FILTERING_TITLE","Filtrage du diagramme");
define("MIN_LABEL","Nombre minimum");
define("MAX_LABEL","Nombre maximum");
define("FILTER_CHOICE_LABEL","Choix du filtre");


define("COLOR_CHANGE_TITLE","Changement des couleurs");

define("SAVE_TITLE","Enregistrement");
define("NAME_GRAPH_LABEL","Nom du graphique");
define("DESCRIPTION_GRAPH_LABEL","Description");
define("GROUP_USER_GRAPH_LABEL","Groupe de publication");
define("PUBLIC_MODE_LABEL","Rendre visible le graphique pour d'autres personnes");
define("LIVE_MODE_LABEL","Actualisation dynamique");



/*
 * MESSAGES AJAX
 */
define("SEARCH_RESULT","Recherche des résultats");

/*
 * Cache requetes BDD
 */
define("CACHE_DELAY",600);
define("ALGO_HASH_CACHE_KEY",'sha256');
//CACHE_FOLDER doit toujours terminer par /
define("CACHE_FOLDER",'./cache_bdd/');
define("FILE_FOLDER", './file/');
define("GRAPH_FOLDER", './file/data_graph/');
define("GRAPH_FOLDER_URL", 'file/data_graph/');

/*
 * MESSAGES ERREUR PHP
 */
define("ERROR_CODE_NAME", "errorcode");

define("ELASTIC_OFFLINE_REDIRECT_ACCOUNT_ID", 500);

# 0 à 9 : Gestion d'utilisateur
define("ERROR_CODE_USER_NOT_LOGGED", 0);
define("ERROR_MSG_USER_NOT_LOGGED", "L'utilisateur n'est pas connecté!");

define("ERROR_CODE_USER_NOT_ALLOWED", 1);
define("ERROR_MSG_USER_NOT_ALLOWED", "L'utilisateur n'est pas autorisé à faire cette action!");

define("ERROR_CODE_TABLE_NOT_ALLOWED", 2);
define("ERROR_MSG_TABLE_NOT_ALLOWED", "Vous avez choisi une table qui n'est pas autorisé ! Changer votre choix dans le formulaire Source de donnée utilisée.");

define("ERROR_CODE_TABLE_NOT_EXIST", 4);
define("ERROR_MSG_TABLE_NOT_EXIST", "Vous avez choisi une table qui n'existe plus ! Changer votre choix dans le formulaire Source de donnée utilisée.");

define("ERROR_MSG_GRAPH_TABLE_NOT_ALLOWED", "L'administrateur a interdit l'accès aux données de cette table. Le dernier cache va être affiché.");
define("ERROR_MSG_GRAPH_TABLE_NOT_EXIST", "La table permetant de créer le graphique n'existe plus. Le dernier cache va être affiché.");

#10 à 19 : Gestion des GET/POST
define("ERROR_CODE_NO_GET_POST_VALUE", 10);
define("ERROR_MSG_NO_GET_POST_VALUE", "Il manque des données en entrée!");

define("ERROR_CODE_BAD_GET_POST_VALUE", 11);
define("ERROR_MSG_BAD_GET_POST_VALUE", "L'une des données en entrée est fausse!");

#20 à 29 : Gestion des connexions distants

/*
 * MESSAGES ERREUR PHP
 */
define("ELASTIC_OFFLINE", "Le serveur Elasticsearch ne répond plus.");
define("ELASTIC_OFFLINE_REDIRECT_ACCOUNT", "Le serveur Elasticsearch ne répond plus. Changer la source de donnée par défaut pour faire de nouveaux graphiques.");
define("ELASTIC_NODATA", "no data elk index");

define("ELASTIC_NODATA_REDIRECT_ACCOUNT", "La table sur  Elasticsearch ne contient pas de données. Changer la source de donnée par défaut pour faire de nouveaux graphiques.");
