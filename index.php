<?php

require './model/pdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
header('Access-Control-Allow-Origin: *');
header("Content-type:multipart/form-data");
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-  Disposition, Content-Description');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//error_reporting(E_ALL); ini_set("display_errors", 1);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    //Main Server API
    $r->addRoute('GET', '/', 'index');

    $r->addRoute('POST', '/user', 'makeUser');
    $r->addRoute('POST', '/review', 'makeReview');
    $r->addRoute('POST', '/comment', 'writeComment');
    $r->addRoute('POST', '/review/like', 'makeReviewLike');
    $r->addRoute('POST', '/comment/like', 'makeCommentLike');

    $r->addRoute('GET', '/test', 'test');
    $r->addRoute('GET', '/a', 'a');
    $r->addRoute('GET', '/review/total', 'reviewCount');

    $r->addRoute('GET', '/event', 'eventAll');

    $r->addRoute('GET', '/review/all', 'reviewAll');
    $r->addRoute('GET', '/review/eyes', 'reviewEyes');
    $r->addRoute('GET', '/review/breast', 'reviewBreast');
    $r->addRoute('GET', '/review/nose', 'reviewNose');
    $r->addRoute('GET', '/review/face', 'reviewFace');
    $r->addRoute('GET', '/review/body', 'reviewBody');
    $r->addRoute('GET', '/review/etc', 'reviewEtc');
    $r->addRoute('GET', '/review/best', 'reviewBest');


    $r->addRoute('POST', '/reviewdelete', 'reviewDelete');
    $r->addRoute('POST', '/commentdelete', 'commentDelete');
    $r->addRoute('POST', '/userdelete', 'userDelete');

    $r->addRoute('GET', '/user/{userno}', 'userAll');
    $r->addRoute('GET', '/review/content/{reviewno}', 'reviewContent');
    $r->addRoute('GET', '/review/content/comment/{reviewno}', 'reviewComment');
    $r->addRoute('GET', '/search/{reviewsearch}', 'searchReview');

    $r->addRoute('POST','/file','fileUpload');


//    $r->addRoute('GET', '/logs/error', 'ERROR_LOGS');
//    $r->addRoute('GET', '/logs/access', 'ACCESS_LOGS');


//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
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

// 로거 채널 생성
$accessLogs =  new Logger('BIGS_ACCESS');
$errorLogs =  new Logger('BIGS_ERROR');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; $vars = $routeInfo[2];
        require './controller/mainController.php';

        break;
}




