<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
    if ($uri === '') $uri = '/';
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$routes = [
    ['GET',  '/',                 'MNController@index'],

    ['GET',  '/movies',           'MoviesController@index'],
    ['POST', '/movies/create',    'MoviesController@create'],
    ['GET',  '/movies/edit',      'MoviesController@edit'],
    ['POST', '/movies/update',    'MoviesController@update'],
    ['POST', '/movies/delete',    'MoviesController@delete'],

    ['GET',  '/locations',        'LocationsController@index'],
    ['POST', '/locations/create', 'LocationsController@create'],
    ['GET',  '/locations/edit',   'LocationsController@edit'],
    ['POST', '/locations/update', 'LocationsController@update'],
    ['POST', '/locations/delete', 'LocationsController@delete'],

    ['GET',  '/mn',               'MNController@index'],
    ['POST', '/mn/create',        'MNController@create'],
    ['GET',  '/mn/edit',          'MNController@edit'],
    ['POST', '/mn/update',        'MNController@update'],
    ['POST', '/mn/delete',        'MNController@delete'],
];

foreach ($routes as [$m, $path, $handler]) {
    if ($m === $method && $path === $uri) {
        [$cls, $fn] = explode('@', $handler, 2);
        $controllerFile = __DIR__ . '/../src/controllers/' . $cls . '.php';

        if (!is_file($controllerFile)) {
            http_response_code(500);
            echo "Controller missing: " . htmlspecialchars($controllerFile);
            exit;
        }

        require_once $controllerFile;

        if (!class_exists($cls) || !method_exists($cls, $fn)) {
            http_response_code(500);
            echo "Handler missing: " . htmlspecialchars($handler);
            exit;
        }

        (new $cls())->$fn();
        exit;
    }
}

http_response_code(404);
echo "404 Not Found";
