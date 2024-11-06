<?php
//routes config

$baseUrl = 'index.php?ctrl=';
$action = '&action=';

$routes = [
    'signup' => $baseUrl . 'user' . $action . 'signup',
    'login' => $baseUrl.'user'.$action.'login',
];
