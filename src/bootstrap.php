<?php

use Appsas\App;
use Appsas\Authenticator;
use Appsas\Configs;
use Appsas\Container;
use Appsas\Controllers\AdminController;
use Appsas\Controllers\KontaktaiController;
use Appsas\Controllers\PersonController;
use Appsas\Controllers\PradziaController;
use Appsas\Database;
use Appsas\Output;
use Appsas\Router;
use Monolog\Logger;

$container = new Container();

App::setContainer($container);

App::bind(Database::class, new Database(App::resolve(Configs::class)));
App::bind(Logger::class, new Logger('Portfolios'));
App::bind(Router::class, new Router(App::resolve(Output::class)));

$router = App::resolve(Router::class);
$log = App::resolve(Logger::class);

$authenticator = App::resolve(Authenticator::class);
$adminController = new AdminController($authenticator);
$contactsController = new KontaktaiController($log);
$personController = new PersonController();

$router->get('', [new PradziaController(), 'index']);
$router->get('admin', [$adminController, 'index']);
$router->post('login', [$adminController, 'login']);
$router->get('logout', [$adminController, 'logout']);
$router->get('kontaktai', [$contactsController, 'index']);
$router->get('persons', [$personController, 'list']);
$router->get('person/new', [$personController, 'new']);
$router->get('person/delete', [$personController, 'delete']);
$router->get('person/edit', [$personController, 'edit']);
$router->get('person/show', [$personController, 'show']);
$router->post('person', [$personController, 'store']);
$router->post('person/update', [$personController, 'update']);