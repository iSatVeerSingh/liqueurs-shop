<?php

$mysql_host     = getenv('MYSQL_HOST');
$mysql_dbname   = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

$dsn = "mysql:host=$mysql_host;dbname=$mysql_dbname;charset=utf8mb4";

// Function to get filtered liquors from MySQL
function getFilteredliquors($params)
{
  global $dsn, $mysql_username, $mysql_password;

  // Connect to MySQL
  $db = new PDO($dsn, $mysql_username, $mysql_password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Select specific columns excluding description
  $query    = "SELECT id, distiller, bottle, type, category, region, cost, proof, age, sub_region, discontinued, price_half_oz, price_1_oz, image, grade FROM liquors WHERE 1=1";
  $bindings = [];

  // Filter by categories (OR within group)
  if (!empty($params['categories'])) {
    $categories   = explode(',', $params['categories']);
    $placeholders = [];
    foreach ($categories as $i => $cat) {
      $key            = ":category{$i}";
      $placeholders[] = $key;
      $bindings[$key] = trim($cat);
    }
    $query .= " AND category IN (" . implode(',', $placeholders) . ")";
  }

  // Filter by types
  if (!empty($params['types'])) {
    $types        = explode(',', $params['types']);
    $placeholders = [];
    foreach ($types as $i => $type) {
      $key            = ":type{$i}";
      $placeholders[] = $key;
      $bindings[$key] = trim($type);
    }
    $query .= " AND type IN (" . implode(',', $placeholders) . ")";
  }

  // Filter by region
  if (!empty($params['region'])) {
    $regions      = explode(',', $params['region']);
    $placeholders = [];
    foreach ($regions as $i => $region) {
      $key            = ":region{$i}";
      $placeholders[] = $key;
      $bindings[$key] = trim($region);
    }
    $query .= " AND region IN (" . implode(',', $placeholders) . ")";
  }

  // Filter by distiller
  if (!empty($params['distiller'])) {
    $distillers   = explode(',', $params['distiller']);
    $placeholders = [];
    foreach ($distillers as $i => $distiller) {
      $key            = ":distiller{$i}";
      $placeholders[] = $key;
      $bindings[$key] = trim($distiller);
    }
    $query .= " AND distiller IN (" . implode(',', $placeholders) . ")";
  }

  // Filter by keyword search in bottle or distiller (case-insensitive)
  if (!empty($params['keyword'])) {
    $query .= " AND (bottle LIKE :keyword OR distiller LIKE :keyword)";
    $bindings[':keyword'] = '%' . trim($params['keyword']) . '%';
  }

  // Filter by price range on cost (numeric column)
  if (!empty($params['price'])) {
    $price = trim($params['price']);
    if (strpos($price, '-') !== false) {
      list($min, $max) = explode('-', $price);
      $query .= " AND cost BETWEEN :price_min AND :price_max";
      $bindings[':price_min'] = (float)$min;
      $bindings[':price_max'] = (float)$max;
    } elseif (substr($price, -1) === '+') {
      $min = rtrim($price, '+');
      $query .= " AND cost >= :price_min";
      $bindings[':price_min'] = (float)$min;
    }
  }

  // Filter by price range on price_1_oz
  if (!empty($params['price_1_oz'])) {
    $price1 = trim($params['price_1_oz']);
    if (strpos($price1, '-') !== false) {
      list($min1, $max1) = explode('-', $price1);
      $query .= " AND price_1_oz BETWEEN :price1oz_min AND :price1oz_max";
      $bindings[':price1oz_min'] = (float)$min1;
      $bindings[':price1oz_max'] = (float)$max1;
    } elseif (substr($price1, -1) === '+') {
      $min1 = rtrim($price1, '+');
      $query .= " AND price_1_oz >= :price1oz_min";
      $bindings[':price1oz_min'] = (float)$min1;
    }
  }

  // Filter by price range on price_half_oz
  if (!empty($params['price_half_oz'])) {
    $priceHalf = trim($params['price_half_oz']);
    if (strpos($priceHalf, '-') !== false) {
      list($minHalf, $maxHalf) = explode('-', $priceHalf);
      $query .= " AND price_half_oz BETWEEN :pricehalfoz_min AND :pricehalfoz_max";
      $bindings[':pricehalfoz_min'] = (float)$minHalf;
      $bindings[':pricehalfoz_max'] = (float)$maxHalf;
    } elseif (substr($priceHalf, -1) === '+') {
      $minHalf = rtrim($priceHalf, '+');
      $query .= " AND price_half_oz >= :pricehalfoz_min";
      $bindings[':pricehalfoz_min'] = (float)$minHalf;
    }
  }

  // Filter by proof range
  if (!empty($params['proof'])) {
    $proof = trim($params['proof']);
    if (strpos($proof, '-') !== false) {
      list($minProof, $maxProof) = explode('-', $proof);
      $query .= " AND proof BETWEEN :proof_min AND :proof_max";
      $bindings[':proof_min'] = (float)$minProof;
      $bindings[':proof_max'] = (float)$maxProof;
    } elseif (substr($proof, -1) === '+') {
      $minProof = rtrim($proof, '+');
      $query .= " AND proof >= :proof_min";
      $bindings[':proof_min'] = (float)$minProof;
    }
  }

  // Filter by age range (excluding 'NAS'; assuming numeric age when not NAS)
  if (!empty($params['age'])) {
    $age = trim($params['age']);
    if (strpos($age, '-') !== false) {
      list($minAge, $maxAge) = explode('-', $age);
      $query .= " AND age != 'NAS' AND CAST(age AS DECIMAL(10,2)) BETWEEN :age_min AND :age_max";
      $bindings[':age_min'] = (float)$minAge;
      $bindings[':age_max'] = (float)$maxAge;
    } elseif (substr($age, -1) === '+') {
      $minAge = rtrim($age, '+');
      $query .= " AND age != 'NAS' AND CAST(age AS DECIMAL(10,2)) >= :age_min";
      $bindings[':age_min'] = (float)$minAge;
    }
  }

  // Sorting: sort parameter format: sort=field,direction
  if (!empty($params['sort'])) {
    $sortParts = explode(',', $params['sort']);
    if (count($sortParts) === 2) {
      $sortField = strtolower(trim($sortParts[0]));
      $sortDir   = (strtolower(trim($sortParts[1])) === 'dsc') ? 'DESC' : 'ASC';

      if ($sortField === 'bottle') {
        $query .= " ORDER BY bottle " . $sortDir;
      } elseif ($sortField === 'price') {
        // Sorting by cost column
        $query .= " ORDER BY cost " . $sortDir;
      }
    }
  }

  // No pagination clause is appended
  $stmt = $db->prepare($query);
  foreach ($bindings as $key => $value) {
    $stmt->bindValue($key, $value);
  }
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $results;
}



function getNavbarData()
{
  global $dsn, $mysql_username, $mysql_password;
  $db = new PDO($dsn, $mysql_username, $mysql_password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $navbarData = [
    'spirits'    => [], // Each category key will hold an array of top 10 bottle names
    'distillers' => [], // Unique distiller names
    'regions'    => []  // Unique region names
  ];

  // 1. Retrieve unique categories and for each get top 10 bottles (sorted by count)
  $stmt = $db->query("SELECT DISTINCT category FROM liquors");
  $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
  foreach ($categories as $category) {
    $query = "SELECT bottle, COUNT(*) as cnt 
              FROM liquors 
              WHERE category = :category 
              GROUP BY bottle 
              ORDER BY cnt DESC 
              LIMIT 10";
    $stmtCategory = $db->prepare($query);
    $stmtCategory->bindValue(':category', $category);
    $stmtCategory->execute();
    $bottles = $stmtCategory->fetchAll(PDO::FETCH_COLUMN, 0);
    $navbarData['spirits'][$category] = $bottles;
  }

  // 2. Retrieve unique distillers
  $stmt = $db->query("SELECT DISTINCT distiller FROM liquors");
  $navbarData['distillers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // 3. Retrieve unique regions
  $stmt = $db->query("SELECT DISTINCT region FROM liquors");
  $navbarData['regions'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

  return $navbarData;
}

function getSidebarData()
{
  global $dsn, $mysql_username, $mysql_password;
  $db = new PDO($dsn, $mysql_username, $mysql_password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $db->query("SELECT DISTINCT category FROM liquors");
  $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

  $stmt = $db->query("SELECT DISTINCT type FROM liquors");
  $types = $stmt->fetchAll(PDO::FETCH_COLUMN);

  $stmt = $db->query("SELECT DISTINCT region FROM liquors");
  $regions = $stmt->fetchAll(PDO::FETCH_COLUMN);

  return [
    'categories' => $categories,
    'types'      => $types,
    'regions'    => $regions
  ];
}
