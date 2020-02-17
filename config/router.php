<?php

// Настройки DI
use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager as DB;

//new app\core\ErrorHandler();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(

);
try {
    $container = $containerBuilder->build();
} catch (Exception $e) {
    echo 'ContainerBuilder error: ' . $e->getMessage();
}


// Настройки Роутера
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {

    // Генерация фейковых данных для всех моделей
    $r->addRoute('GET', '/fakeGenerate', ['api\controllers\FakerController', 'runFakeGenerateAction']);// CRUD для модели News

    // CRUD для модели News
    $r->addRoute('GET', '/news', ['api\controllers\NewsController', 'indexAction']);
    $r->addRoute('POST', '/news', ['api\controllers\NewsController', 'createAction']);
    $r->addRoute('GET', '/news/{id:\d+}', ['api\controllers\NewsController', 'readAction']);
    $r->addRoute('PUT', '/news/{id:\d+}', ['api\controllers\NewsController', 'updateAction']);
    $r->addRoute('DELETE', '/news/{id:\d+}', ['api\controllers\NewsController', 'deleteAction']);

    // CRUD для модели Category
    $r->addRoute('GET', '/category', ['api\controllers\CategoryController', 'indexAction']);
    $r->addRoute('POST', '/category', ['api\controllers\CategoryController', 'createAction']);
    $r->addRoute('GET', '/category/{id:\d+}', ['api\controllers\CategoryController', 'readAction']);
    $r->addRoute('PUT', '/category/{id:\d+}', ['api\controllers\CategoryController', 'updateAction']);
    $r->addRoute('DELETE', '/category/{id:\d+}', ['api\controllers\CategoryController', 'deleteAction']);

    // CRUD для модели User
    $r->addRoute('GET', '/user', ['api\controllers\UserController', 'indexAction']);
    $r->addRoute('POST', '/user', ['api\controllers\UserController', 'createAction']);
    $r->addRoute('GET', '/user/{id:\d+}', ['api\controllers\UserController', 'readAction']);
    $r->addRoute('PUT', '/user/{id:\d+}', ['api\controllers\UserController', 'updateAction']);
    $r->addRoute('DELETE', '/user/{id:\d+}', ['api\controllers\UserController', 'deleteAction']);

    // Загрузка картинок
    $r->addRoute('POST', '/{nameModel}/{id:\d+}/upload', ['api\controllers\UploadController', 'uploadAction']);


    // Дополнительные задачи

    // выдача всех новостей конкретного автора
    $r->addRoute('GET', '/user/{id:\d+}/news', ['api\controllers\UserController', 'newsUserAction']);

    // выдача списка всех новостей, которые относятся к указанной рубрике
    $r->addRoute('GET', '/category/{id:\d+}/news', ['api\controllers\CategoryController', 'newsCategoryAction']);


    // поиск новости по названию (или совпадению через оператор LIKE) (есть обязательные GET-параметры onfield и search)
    // Например api.newstoday/search/news?onfield=title&search=Спар
    $r->addRoute('GET', '/search/news', ['api\controllers\SearchController', 'newsByLikeAction']);

    // искать новости по рубрике, не включая дочерние (есть обязательные GET-параметры onfield и search)
    // Например api.newstoday/search/newsByCategory?search=прир
    $r->addRoute('GET', '/search/newsByCategory', ['api\controllers\SearchController', 'newsByCategoryAction']);


    // Чистка картинок, не привязанных ни к одной сущности User или News
//    $r->addRoute('DELETE', '/{namePath}/clear', ['api\controllers\UploadController', 'clearImagesAction']);


});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:

        header("Content-Type: application/json");
        header("HTTP/1.1 " . 404 . " Not Found" );
        echo json_encode(["errors" => "Page not found"], JSON_UNESCAPED_UNICODE);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];

        header("Content-Type: application/json");
        header("HTTP/1.1 " . 405 . " METHOD NOT ALLOWED" );
        echo json_encode(["errors" => "METHOD NOT ALLOWED"], JSON_UNESCAPED_UNICODE);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // ... call $handler with $vars

        //$container = new Container();

        $container->call($handler, $vars);

        break;
}