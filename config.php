<?php
require '_includes/autoloader.class.php'; 
Autoloader::register(); 

//affiche les lignes de debug dans les logs
define('DEBUG', false); //default -> false 
define('PATH_GED','/home/krolanta.fr/files');
define('TOKEN','RANDOM');
define('EXTERNE_URL','http://api.krolanta.fr/files');

DEFINE('BDD_IP','localhost'); //default -> localhost
DEFINE('BDD_USER','xxxxx');
DEFINE('BDD_PASS','xxxxx');
DEFINE('BDD_SCHEMA','j_krolanta'); //default -> okovision

define('LOGFILE',__DIR__.'/_logs/api.krolanta.fr.log');
date_default_timezone_set('Europe/Paris');


