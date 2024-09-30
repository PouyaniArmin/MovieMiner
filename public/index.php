<?php

use App\Controllers\TestController;
use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

require_once __DIR__ . "/../vendor/autoload.php";
$router = new Router(new Request,new Response);
$app = new App($router);

$app->router->get('/',[TestController::class,'index']);
$app->router->get('/test/{id}',[TestController::class,'test']);
$app->router->get('/home',function(){
    return "home";
});
$app->run();
