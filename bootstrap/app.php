<?php

require __DIR__ . '/../vendor/autoload.php';

$container = new Slim\Container();
require __DIR__ . '/../app/container.php';

$app = new Slim\App($container);
require __DIR__ . '/../app/routes.php';

return $app;