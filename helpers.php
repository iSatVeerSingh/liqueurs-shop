<?php
// helpers.php

// function getFilteredLiqueurs($params)
// {
//   $page   = (isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0) ? (int)$params['page'] : 1;
//   $limit  = 50;
//   $offset = ($page - 1) * $limit;

//   // Connect to the SQLite database
//   $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
//   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//   // Build the dynamic query for fetching data
//   $query    = "SELECT * FROM liqueurs WHERE 1=1";
//   $bindings = [];

//   // Filter by categories (OR within group)
//   if (!empty($params['categories'])) {
//     $categories   = explode(',', $params['categories']);
//     $placeholders = [];
//     foreach ($categories as $i => $cat) {
//       $key            = ":category{$i}";
//       $placeholders[] = $key;
//       $bindings[$key] = trim($cat);
//     }
//     $query .= " AND category IN (" . implode(',', $placeholders) . ")";
//   }

//   // Filter by types
//   if (!empty($params['types'])) {
//     $types        = explode(',', $params['types']);
//     $placeholders = [];
//     foreach ($types as $i => $type) {
//       $key            = ":type{$i}";
//       $placeholders[] = $key;
//       $bindings[$key] = trim($type);
//     }
//     $query .= " AND type IN (" . implode(',', $placeholders) . ")";
//   }

//   // Filter by region
//   if (!empty($params['region'])) {
//     $regions      = explode(',', $params['region']);
//     $placeholders = [];
//     foreach ($regions as $i => $region) {
//       $key            = ":region{$i}";
//       $placeholders[] = $key;
//       $bindings[$key] = trim($region);
//     }
//     $query .= " AND region IN (" . implode(',', $placeholders) . ")";
//   }

//   // Filter by distiller
//   if (!empty($params['distiller'])) {
//     $distillers   = explode(',', $params['distiller']);
//     $placeholders = [];
//     foreach ($distillers as $i => $distiller) {
//       $key            = ":distiller{$i}";
//       $placeholders[] = $key;
//       $bindings[$key] = trim($distiller);
//     }
//     $query .= " AND distiller IN (" . implode(',', $placeholders) . ")";
//   }

//   // Filter by bottle keyword search (case-insensitive)
//   if (!empty($params['bottle'])) {
//     $query .= " AND bottle LIKE :bottle";
//     $bindings[':bottle'] = '%' . trim($params['bottle']) . '%';
//   }

//   // Filter by price range on cost (numeric column)
//   if (!empty($params['price'])) {
//     $price = trim($params['price']);
//     if (strpos($price, '-') !== false) {
//       list($min, $max) = explode('-', $price);
//       $query .= " AND cost BETWEEN :price_min AND :price_max";
//       $bindings[':price_min'] = (float)$min;
//       $bindings[':price_max'] = (float)$max;
//     } elseif (substr($price, -1) === '+') {
//       $min = rtrim($price, '+');
//       $query .= " AND cost >= :price_min";
//       $bindings[':price_min'] = (float)$min;
//     }
//   }

//   // Filter by age range (excluding 'NAS'; assuming numeric age when not NAS)
//   if (!empty($params['age'])) {
//     $age = trim($params['age']);
//     if (strpos($age, '-') !== false) {
//       list($min, $max) = explode('-', $age);
//       $query .= " AND age != 'NAS' AND CAST(age AS REAL) BETWEEN :age_min AND :age_max";
//       $bindings[':age_min'] = (float)$min;
//       $bindings[':age_max'] = (float)$max;
//     } elseif (substr($age, -1) === '+') {
//       $min = rtrim($age, '+');
//       $query .= " AND age != 'NAS' AND CAST(age AS REAL) >= :age_min";
//       $bindings[':age_min'] = (float)$min;
//     }
//   }

//   // Sorting: sort parameter format: sort=field,direction
//   if (!empty($params['sort'])) {
//     $sortParts = explode(',', $params['sort']);
//     if (count($sortParts) === 2) {
//       $sortField = strtolower(trim($sortParts[0]));
//       $sortDir   = (strtolower(trim($sortParts[1])) === 'dsc') ? 'DESC' : 'ASC';

//       if ($sortField === 'bottle') {
//         $query .= " ORDER BY bottle " . $sortDir;
//       } elseif ($sortField === 'price') {
//         // Sorting by cost column now
//         $query .= " ORDER BY cost " . $sortDir;
//       }
//     }
//   }

//   // Append pagination clause
//   $query .= " LIMIT :limit OFFSET :offset";

//   // Prepare and execute the main query
//   $stmt = $db->prepare($query);
//   foreach ($bindings as $key => $value) {
//     $stmt->bindValue($key, $value);
//   }
//   $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//   $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//   $stmt->execute();
//   $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//   // Build a separate count query for pagination metadata (reapply filter conditions)
//   $countQuery = "SELECT COUNT(*) FROM liqueurs WHERE 1=1";
//   if (!empty($params['categories'])) {
//     $categories = explode(',', $params['categories']);
//     $placeholders = [];
//     foreach ($categories as $i => $cat) {
//       $key = ":category{$i}";
//       $placeholders[] = $key;
//     }
//     $countQuery .= " AND category IN (" . implode(',', $placeholders) . ")";
//   }
//   if (!empty($params['types'])) {
//     $types = explode(',', $params['types']);
//     $placeholders = [];
//     foreach ($types as $i => $type) {
//       $key = ":type{$i}";
//       $placeholders[] = $key;
//     }
//     $countQuery .= " AND type IN (" . implode(',', $placeholders) . ")";
//   }
//   if (!empty($params['region'])) {
//     $regions = explode(',', $params['region']);
//     $placeholders = [];
//     foreach ($regions as $i => $region) {
//       $key = ":region{$i}";
//       $placeholders[] = $key;
//     }
//     $countQuery .= " AND region IN (" . implode(',', $placeholders) . ")";
//   }
//   if (!empty($params['distiller'])) {
//     $distillers = explode(',', $params['distiller']);
//     $placeholders = [];
//     foreach ($distillers as $i => $distiller) {
//       $key = ":distiller{$i}";
//       $placeholders[] = $key;
//     }
//     $countQuery .= " AND distiller IN (" . implode(',', $placeholders) . ")";
//   }
//   if (!empty($params['bottle'])) {
//     $countQuery .= " AND bottle LIKE :bottle";
//   }
//   if (!empty($params['price'])) {
//     $price = trim($params['price']);
//     if (strpos($price, '-') !== false) {
//       $countQuery .= " AND cost BETWEEN :price_min AND :price_max";
//     } elseif (substr($price, -1) === '+') {
//       $countQuery .= " AND cost >= :price_min";
//     }
//   }
//   if (!empty($params['age'])) {
//     $age = trim($params['age']);
//     if (strpos($age, '-') !== false) {
//       $countQuery .= " AND age != 'NAS' AND CAST(age AS REAL) BETWEEN :age_min AND :age_max";
//     } elseif (substr($age, -1) === '+') {
//       $countQuery .= " AND age != 'NAS' AND CAST(age AS REAL) >= :age_min";
//     }
//   }

//   $stmtCount = $db->prepare($countQuery);
//   // Rebind filter parameters (skip :limit and :offset)
//   foreach ($bindings as $key => $value) {
//     if ($key === ':limit' || $key === ':offset') continue;
//     $stmtCount->bindValue($key, $value);
//   }
//   $stmtCount->execute();
//   $total = (int)$stmtCount->fetchColumn();

//   // Build the response payload
//   return [
//     'current_page' => $page,
//     'per_page'     => $limit,
//     'total_items'  => $total,
//     'total_pages'  => ceil($total / $limit),
//     'data'         => $results
//   ];
// }

function getFilteredLiqueurs($params)
{
  // Connect to the SQLite database
  $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Build the dynamic query for fetching data
  $query    = "SELECT * FROM liqueurs WHERE 1=1";
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

  // Filter by bottle keyword search (case-insensitive)
  if (!empty($params['bottle'])) {
    $query .= " AND bottle LIKE :bottle";
    $bindings[':bottle'] = '%' . trim($params['bottle']) . '%';
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

  // Filter by age range (excluding 'NAS'; assuming numeric age when not NAS)
  if (!empty($params['age'])) {
    $age = trim($params['age']);
    if (strpos($age, '-') !== false) {
      list($min, $max) = explode('-', $age);
      $query .= " AND age != 'NAS' AND CAST(age AS REAL) BETWEEN :age_min AND :age_max";
      $bindings[':age_min'] = (float)$min;
      $bindings[':age_max'] = (float)$max;
    } elseif (substr($age, -1) === '+') {
      $min = rtrim($age, '+');
      $query .= " AND age != 'NAS' AND CAST(age AS REAL) >= :age_min";
      $bindings[':age_min'] = (float)$min;
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
        // Sorting by cost column now
        $query .= " ORDER BY cost " . $sortDir;
      }
    }
  }

  // No pagination clause is appended

  // Prepare and execute the main query
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
  // Connect to the SQLite database
  $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $navbarData = [
    'spirits'    => [], // Each category key will hold an array of top 10 bottle names
    'distillers' => [], // Unique distiller names
    'regions'    => []  // Unique region names
  ];

  // 1. Retrieve unique categories and for each get top 10 bottles (sorted by count)
  $stmt = $db->query("SELECT DISTINCT category FROM liqueurs");
  $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
  foreach ($categories as $category) {
    $query = "SELECT bottle, COUNT(*) as cnt 
                    FROM liqueurs 
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
  $stmt = $db->query("SELECT DISTINCT distiller FROM liqueurs");
  $navbarData['distillers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // 3. Retrieve unique regions
  $stmt = $db->query("SELECT DISTINCT region FROM liqueurs");
  $navbarData['regions'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

  return $navbarData;
}

function getUniqueCategoriesAndTypes()
{
  // Establish PDO connection to SQLite database
  $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Query for unique categories
  $stmt = $db->query("SELECT DISTINCT category FROM liqueurs");
  $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // Query for unique types
  $stmt = $db->query("SELECT DISTINCT type FROM liqueurs");
  $types = $stmt->fetchAll(PDO::FETCH_COLUMN);

  return [
    'categories' => $categories,
    'types'      => $types,
  ];
}
