<?php

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$app = AppFactory::create();

// Set up Twig
$twig = Twig::create(__DIR__ . '/templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

// Define a simple route
$app->get('/', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();

  return $twig->render($response, 'home.twig', ['navbarData' => $navbarData]);
});

$app->get('/search', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  $categoriesAndTypes = getUniqueCategoriesAndTypes();

  return $twig->render($response, 'search.twig', ['navbarData' => $navbarData, 'sidebarData' => $categoriesAndTypes]);
});

$app->get('/api/liqueurs', function (Request $request, Response $response, $args) {
  // Retrieve query parameters and set up pagination
  $params = $request->getQueryParams();
  $payload = getFilteredLiqueurs($params);

  $response->getBody()->write(json_encode($payload));
  return $response->withHeader('Content-Type', 'application/json');
});

// $app->get('/ui-data', function (Request $request, Response $response, $args) {

// });



// Run the app
$app->run();
