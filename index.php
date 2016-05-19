<?php

require_once 'helpers.php';
//require_once 'models.php';
//require_once 'controllers.php';

 //$connect = db_open_connexion();
try {
  //$current_user = current_user();

//-------- ROUTES -------
    $enterRoute = false;
    $uri = get_path_info();
    $routes = file_get_contents("init/routes.json");
    $routes = json_decode($routes, true);

    foreach ($routes as $route) {
        if ($route["name"] == $uri) {
            require "controller/" . $route["controller"] . "Controller.php";
            $functionName = $route["action"];
            $functionName();
            $enterRoute = true;
            break;
        }
    }

    if (!$enterRoute) {
        header('Status: 404 Not Found');
        require "views/404.html";
    }

} catch (Exception $e) {
    db_close_connexion($connect);
    throw $e;
}

db_close_connexion($connect);

?>
