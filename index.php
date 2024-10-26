<?php
require_once "application/route/Route.php";
require_once "application/assets/Mysql/DB.php";
require_once "application/assets/Header/HttpHeadersManager.php";

use Application\Route\Route;
use Application\Database\DB;


DB::DBInit();

Route::initialize($_SERVER, $_POST);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


include "Controllers/UserController.php";








Route::handle();
