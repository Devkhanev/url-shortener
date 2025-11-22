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

if($method === 'POST' && $path === '/shorten'){
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $url = $data['url'];

    $result = $shortener->shorten($url);
    header('Content-Type: application/json');
    echo json_encode(['short_code' => $result['short_code']]);
    exit;
}

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