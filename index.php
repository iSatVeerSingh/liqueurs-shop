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

$mysql_host     = getenv('MYSQL_HOST');
$mysql_dbname   = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

$dsn = "mysql:host=$mysql_host;dbname=$mysql_dbname;charset=utf8mb4";

// Define a simple route
$app->get('/', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  return $twig->render($response, 'home.twig', ['navbarData' => $navbarData]);
});

$app->get('/wishlist', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  return $twig->render($response, 'wishlist.twig', ['navbarData' => $navbarData]);
});

$app->get('/search', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  $sidebarData = getSidebarData();
  return $twig->render($response, 'search.twig', ['navbarData' => $navbarData, 'sidebarData' => $sidebarData]);
});

$app->get('/liquors/{id}', function (Request $request, Response $response, $args) use ($twig, $dsn, $mysql_username, $mysql_password) {
  $id = (int)$args['id'];
  $navbarData = getNavbarData();

  // Connect to MySQL
  $db = new PDO($dsn, $mysql_username, $mysql_password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch the liquor by id
  $stmt = $db->prepare("SELECT * FROM liquors WHERE id = :id");
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $liquor = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$liquor) {
    $data = ['error' => 'Product not found'];
    $response->getBody()->write(json_encode($data));
    return $response->withStatus(404)
      ->withHeader('Content-Type', 'application/json');
  }

  return $twig->render($response, 'product.twig', ['navbarData' => $navbarData, 'product' => $liquor]);
  // (The following lines will not be executed)
  $response->getBody()->write(json_encode($liquor));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/contact', function ($request, $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  return $twig->render($response, 'contact.twig', ['navbarData' => $navbarData]);
});

$app->get('/api', function (Request $request, Response $response, $args) {
  $params = $request->getQueryParams();
  $payload = getFilteredliquors($params);
  $response->getBody()->write(json_encode($payload));
  return $response->withHeader('Content-Type', 'application/json');
});

// POST /api/upload - Import endpoint using MySQL
$app->post('/api/upload', function (Request $request, Response $response) use ($dsn, $mysql_username, $mysql_password) {
  try {
    // Read JSON from request body
    $jsonData = $request->getBody()->getContents();
    if (empty($jsonData)) {
      throw new Exception("No JSON data provided.");
    }

    // Decode JSON
    $items = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new Exception("Invalid JSON format.");
    }

    // Connect to MySQL
    $db = new PDO($dsn, $mysql_username, $mysql_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Begin transaction
    $db->beginTransaction();

    // Delete all existing records
    $db->exec("DELETE FROM liquors");

    // Prepare SQL statement for insertion using new JSON keys
    $stmt = $db->prepare("
          INSERT INTO liquors (
              distiller,
              bottle,
              type,
              category,
              region,
              cost,
              proof,
              age,
              sub_region,
              discontinued,
              price_half_oz,
              price_1_oz,
              image,
              description,
              grade
          ) VALUES (
              :distiller,
              :bottle,
              :type,
              :category,
              :region,
              :cost,
              :proof,
              :age,
              :sub_region,
              :discontinued,
              :price_half_oz,
              :price_1_oz,
              :image,
              :description,
              :grade
          )
      ");

    foreach ($items as $item) {
      $stmt->execute([
        ':distiller'      => $item['distiller']      ?? null,
        ':bottle'         => $item['bottle']         ?? null,
        ':type'           => $item['type']           ?? null,
        ':category'       => $item['category']       ?? null,
        ':region'         => $item['region']         ?? null,
        ':cost'           => isset($item['cost']) ? (float)$item['cost'] : null,
        ':proof'          => isset($item['proof']) ? (float)$item['proof'] : null,
        ':age'            => $item['age']            ?? null,
        ':sub_region'     => $item['sub_region']     ?? '',
        ':discontinued'   => isset($item['discontinued']) ? ($item['discontinued'] ? 1 : 0) : 0,
        ':price_half_oz'  => isset($item['price_half_oz']) ? (float)$item['price_half_oz'] : null,
        ':price_1_oz'     => isset($item['price_1_oz']) ? (float)$item['price_1_oz'] : null,
        ':image'          => $item['image']          ?? null,
        ':description'    => $item['description']    ?? null,
        ':grade'          => $item['grade']          ?? null,
      ]);
    }

    // Commit transaction
    $db->commit();

    $result = ['message' => 'Data updated successfully.'];
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
  } catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
      $db->rollBack();
    }
    $error = ['error' => true, 'message' => $e->getMessage()];
    $response->getBody()->write(json_encode($error));
    return $response->withStatus(400)
      ->withHeader('Content-Type', 'application/json');
  }
});

$app->get('/{routes:.+}', function (Request $request, Response $response, $args) use ($twig) {
  $navbarData = getNavbarData();
  return $twig->render($response, '404.twig', ['navbarData' => $navbarData]);
});

$app->run();
