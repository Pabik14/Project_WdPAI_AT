<?php



require_once 'src/controllers/AppController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';




$routing = [
    'login' => [
        'controller' => 'SecurityController',
        'action' => 'login',
        'access' => ['guest']
    ],
    'logout' => [
        'controller' => 'SecurityController',
        'action' => 'logout',
        'access' => ['user', 'admin']
    ],
    'register' => [
        'controller' => 'SecurityController',
        'action' => 'register',
        'access' => ['guest']
    ],
    'dashboard' => [
        'controller' => 'DashboardController',
        'action' => 'dashboard',
        'access' => ['user', 'admin']
    ],
    'home' => [
        'controller' => 'HomeController',
        'action' => 'home',
        'access' => ['guest']
    ],
    'animelist' => [
        'controller' => 'AnimeController',
        'action' => 'showlist',
        'access' => ['user', 'admin']
    ],
    'searchAnime' => [
        'controller' => 'AnimeController',
        'action' => 'searchAnime',
        'access' => ['user','admin']
    ],'deleteAnime' => [
        'controller' => 'AnimeController',
        'action' => 'deleteAnime',
        'access' => ['user', 'admin']
    ],'getAnimeStats' => [
        'controller' => 'DashboardController',
        'action' => 'getAnimeStats',
        'access' => ['user', 'admin']
    ],    
    'adminPanel' => [
        'controller' => 'AdminController',
        'action' => 'adminPanel',
        'access' => ['admin']
    ],
    'addAnime' => [
        'controller' => 'AnimeAddController',
        'action' => 'addAnime',
        'access' => ['user', 'admin']
    ],
    'showAddAnimeForm' => [
        'controller' => 'AnimeAddController',
        'action' => 'showAddAnimeForm',
        'access' => ['user', 'admin']
    ],'deleteUser' => [
        'controller' => 'AdminController',
        'action' => 'deleteUser',
        'access' => ['admin']
    ],
    'downloadDatabase' => [
        'controller' => 'AdminController',
        'action' => 'downloadDatabase',
        'access' => ['admin']
    ],
];
$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);
$action = explode("/", $path)[0];

if ($action === '') {
    header('Location: /home');
    exit();
}


if (array_key_exists($action, $routing)) {
    $controllerName = $routing[$action]['controller'];
    $actionName = $routing[$action]['action'];

    require_once "src/controllers/{$controllerName}.php";
    $controller = new $controllerName();
    $controller->$actionName();
} else {
    $controller = new AppController();
    $controller->render('404'); 
}
