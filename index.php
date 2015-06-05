<?php

require_once './MainController.php';

ini_set('display_errors', 'off');
ini_set('session.gc_maxlifetime', 1260000);
header('Content-Type: application/json');
header("HTTP/1.1 200 OK");
header("Access-Control-Allow-Headers:origin, content-type, accept, api-key, api-secret, x-requested-with");
header("Access-Control-Allow-Methods:POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
date_default_timezone_set("Brazil/East"); // para aws 
set_time_limit(0);

new MainController();

function echod($array) {
    echo '<pre>';
    print_r($array);
    die();
}
