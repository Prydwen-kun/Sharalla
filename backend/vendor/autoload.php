<?php

function loadController(string $controller)
{
    if (file_exists('app/controllers/' . $controller . '.php')) {
        require_once 'app/controllers/' . $controller . '.php';
    }
}

spl_autoload_register('loadController');


function loadModel(string $model)
{
    if (file_exists('app/models/' . $model . '.php')) {
        require_once 'app/models/' . $model . '.php';
    }
}

spl_autoload_register('loadModel');


function loadClass(string $class)
{
    if (file_exists('app/class/' . $class . '.php')) {
        require_once 'app/class/' . $class . '.php';
    }
}

spl_autoload_register('loadClass');
