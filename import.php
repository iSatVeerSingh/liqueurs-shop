<?php

$mysql_host     = getenv('MYSQL_HOST');
$mysql_dbname   = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');


// Create a DSN string for MySQL
$dsn = "mysql:host=$mysql_host;dbname=$mysql_dbname;charset=utf8mb4";

// Create PDO connection
try {
    $db = new PDO($dsn, $mysql_username, $mysql_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("MySQL Connection failed: " . $e->getMessage());
}

// Create table if it doesn't exist with updated columns
$db->exec("CREATE TABLE IF NOT EXISTS liquors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    distiller VARCHAR(255),
    bottle VARCHAR(255),
    type VARCHAR(255),
    category VARCHAR(255),
    region VARCHAR(255),
    cost DECIMAL(10,2),
    proof DECIMAL(10,2),
    age VARCHAR(10),
    sub_region VARCHAR(255) DEFAULT '',
    discontinued TINYINT(1) DEFAULT 0,
    price_half_oz DECIMAL(10,2),
    price_1_oz DECIMAL(10,2),
    image TEXT,
    description TEXT,
    grade VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Create indexes for filtering performance
$db->exec("CREATE INDEX idx_category ON liquors(category)");
$db->exec("CREATE INDEX idx_type ON liquors(type)");
$db->exec("CREATE INDEX idx_region ON liquors(region)");
$db->exec("CREATE INDEX idx_cost ON liquors(cost)");
$db->exec("CREATE INDEX idx_proof ON liquors(proof)");
$db->exec("CREATE INDEX idx_price_half ON liquors(price_half_oz)");
$db->exec("CREATE INDEX idx_price_one ON liquors(price_1_oz)");
$db->exec("CREATE INDEX idx_distiller ON liquors(distiller)");
$db->exec("CREATE INDEX idx_bottle ON liquors(bottle)");
$db->exec("CREATE INDEX idx_discontinued ON liquors(discontinued)");
$db->exec("CREATE INDEX idx_sub_region ON liquors(sub_region)");
$db->exec("CREATE INDEX idx_age ON liquors(age)");

echo "Database, table, and indexes created successfully.";
