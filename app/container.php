<?php

use App\Services\FileService;
use Slim\Container;
use Slim\Http\Response;

$container['notFoundHandler'] = function (Container $c) {
    return function ($request, Response $response) {
        return $response
            ->withJson(['message' => 'Invalid route'], 404);
    };
};

$container['base_dir'] = __DIR__ . '/../files';
$container['file_service'] = function (Container $c) {
    return new FileService($c->get('base_dir'));
};