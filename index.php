<?php
session_start();
require_once './controller/BaseController.php';
new BaseController();


$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$actionName     = isset($_GET['action'])     ? $_GET['action']     : 'index';

$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile  = './controller/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerClass();

    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
        exit;   // ngắt luôn sau khi thực thi
    } else {
        echo "Không tìm thấy action <strong>$actionName</strong> của controller $controllerClass";
        exit;
    }
} else {
    echo "Không tìm thấy controller <strong>$controllerClass</strong>";
    exit;
}