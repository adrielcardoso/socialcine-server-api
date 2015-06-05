<?php

require_once './MainController.php';

ini_set('display_errors', 'off');
ini_set('session.gc_maxlifetime', 1260000);
date_default_timezone_set('America/Sao_Paulo');

new MainController();

function echod($array){
    echo '<pre>';
    print_r($array);
    die();
}



