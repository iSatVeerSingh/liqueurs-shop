<?php
// Connect to SQLite
$db = new PDO('sqlite:database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS liqueurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    distiller TEXT,
    bottle TEXT,
    type TEXT,
    category TEXT,
    region TEXT,
    value TEXT,
    proof TEXT,
    age TEXT,
    price_half_oz TEXT,
    price_1_oz TEXT,
    image TEXT,
    description TEXT,
    grade TEXT
)");

// Read JSON file
$jsonData = file_get_contents('data.json');
$items = json_decode($jsonData, true);
if (!$items) {
  die("Failed to decode JSON data.");
}

// Prepare SQL statement for insertion
$stmt = $db->prepare("INSERT INTO liqueurs (
    distiller, bottle, type, category, region, value, proof, age, price_half_oz, price_1_oz, image, description, grade
) VALUES (
    :distiller, :bottle, :type, :category, :region, :value, :proof, :age, :price_half_oz, :price_1_oz, :image, :description, :grade
)");

// Begin transaction for faster inserts
$db->beginTransaction();

foreach ($items as $item) {
  // Map JSON keys to SQL parameters; note the transformation for the price field.
  $stmt->execute([
    ':distiller'     => $item['distiller'],
    ':bottle'        => $item['bottle'],
    ':type'          => $item['type'],
    ':category'      => $item['category'],
    ':region'        => $item['region'],
    ':value'         => $item['value'],
    ':proof'         => $item['proof'],
    ':age'           => $item['age'],
    ':price_half_oz' => $item['price_1/2_oz'], // transforming key name
    ':price_1_oz'    => $item['price_1_oz'],
    ':image'         => $item['image'],
    ':description'   => $item['description'],
    ':grade'         => $item['grade']
  ]);
}

// Commit transaction
$db->commit();

echo "Data imported successfully.";
