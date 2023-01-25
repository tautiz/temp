<?php

use Appsas\App;
use Appsas\ExceptionHandler;
use Appsas\Output;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/larapack/dd/src/helper.php';
require __DIR__ . '/../src/bootstrap.php';

try {
    session_start();
    App::run();
}
catch (Exception $e) {
    $log = App::resolve(Logger::class);
    $log->pushHandler(new StreamHandler('../logs/klaidos.log', Logger::INFO));
    $output = App::resolve(Output::class);

    $handler = new ExceptionHandler($output, $log);
    $handler->handle($e);
    $output->print();
}
