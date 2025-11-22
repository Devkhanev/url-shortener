<?php

require __DIR__ . '/../vendor/autoload.php';

use Khanev\UrlShortener\Database;
use Khanev\UrlShortener\ShortCodeGenerator;
use Khanev\UrlShortener\UrlShortener;

$db = new Database();
$generator = new ShortCodeGenerator($db);
$shortener = new UrlShortener($db, $generator);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


//                   -------post-------
if($method === 'POST' && $path === '/shorten'){
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $url = $data['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid url']);
        exit;
    }

    $result = $shortener->shorten($url);
    $fullData = $shortener->getAllData($result['id']);
    http_response_code(201);
//    header('Content-Type: application/json');

    echo json_encode([
        'id' => $fullData['id'],
        'url' => $fullData['url'],
        'shortCode' => $fullData['short_code'],
        'createdAt' => $fullData['created_at'],
        'updatedAt' => $fullData['updated_at']
    ]);
    exit;
}


//                   -------get-------
if($method === 'GET' && str_starts_with($path, '/shorten')){
    $code = str_replace('/shorten/', '', $path);
    $url = $shortener->getDataByShortCode($code);
    if(!empty($url)){
        http_response_code(200);
        echo json_encode([
            'id' => $url['id'],
            'url' => $url['url'],
            'shortCode' => $url['short_code'],
            'createdAt' => $url['created_at'],
            'updatedAt' => $url['updated_at'],
            'accessCount' => $url['access_count']
        ]);
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'short code not found']);
    }
    exit;
}

//                   -------redirect-------
if($method === 'GET'){
    $code = trim($path, '/');
    $url = $shortener->getUrl($code);

    if($url !== null){
        $shortener->incrementAccessCount($code);
        header('location:' . $url);
        exit;
    }
    else{
        http_response_code(404);
        echo "not found";
        exit;
    }
}


//                   -------update-------

if($method === 'PUT' && str_starts_with($path, '/shorten')) {
    $code = str_replace('/shorten/', '', $path);
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $url = $data['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'invalid url']);
        exit;
    }

    $result = $shortener->updateUrl($code, $url);
    if($result !== null){
        http_response_code(200);
//        header('Content-Type: application/json');
        echo json_encode([
            'id' => $result['id'],
            'url' => $result['url'],
            'shortCode' => $result['short_code'],
            'createdAt' => $result['created_at'],
            'updatedAt' => $result['updated_at'],

        ]);
    }
    else{
            http_response_code(404);
            echo json_encode(['error' => 'short URL not found']);
    }
}

//                   -------delete-------

if($method === 'DELETE' && str_starts_with($path, '/shorten')) {
    $code = str_replace('/shorten/', '', $path);
    $result = $shortener->deleteUrl($code);
    if($result == true){
        http_response_code(204);
    }
    else{
        http_response_code(404);
        echo json_encode(['error' => 'short code not found']);
    }
    exit;
}

