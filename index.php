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

$app->get('/liqueurs', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  $categoriesAndTypes = getUniqueCategoriesAndTypes();

  return $twig->render($response, 'liqueurs.twig', ['navbarData' => $navbarData, 'sidebarData' => $categoriesAndTypes]);
});

$app->get('/api', function (Request $request, Response $response, $args) {
  // Retrieve query parameters and set up pagination
  $params = $request->getQueryParams();
  $payload = getFilteredLiqueurs($params);

  $response->getBody()->write(json_encode($payload));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/{id}', function (Request $request, Response $response, $args) use ($twig) {
  $id = (int)$args['id'];
  $navbarData = getNavbarData();
  // Connect to SQLite
  $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch the liqueur by id
  $stmt = $db->prepare("SELECT * FROM liqueurs WHERE id = :id");
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $liqueur = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$liqueur) {
    $data = ['error' => 'Product not found'];
    $response->getBody()->write(json_encode($data));
    return $response->withStatus(404)
      ->withHeader('Content-Type', 'application/json');
  }

  return $twig->render($response, 'product.twig', ['navbarData' => $navbarData, 'product' => $liqueur]);
  $response->getBody()->write(json_encode($liqueur));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/contact', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();

  return $twig->render($response, 'contact.twig', ['navbarData' => $navbarData]);
});



// Run the app
$app->run();
