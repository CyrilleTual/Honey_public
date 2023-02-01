<?php
session_start();

/** lancement de l'autoload
 */
require_once 'autoload.php';
Autoload::register();

/** appel au router 
 */
$router = new Services\Router();
