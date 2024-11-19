<?php

// Activer le rapport d'erreurs (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//start session
session_start();

// Charger l'autoloader
require __DIR__ . '/vendor/autoload.php';

// Charger les constantes de config
require __DIR__ . '/config/config.php';

//Charger utils function
require __DIR__ . '/utils/responseFormat.php';
require __DIR__ . '/utils/Auth_token_generator.php';

// Définir les en-têtes pour l'API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Charger les routes
require __DIR__ . '/routes/api.php';

session_write_close();
