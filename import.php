<?php
// Connect to SQLite
$db = new PDO('sqlite:database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if it doesn't exist with updated columns
$db->exec("CREATE TABLE IF NOT EXISTS liqueurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    distiller TEXT,
    bottle TEXT,
    type TEXT,
    category TEXT,
    region TEXT,
    cost REAL,
    proof REAL,
    age TEXT,
    sub_region TEXT,
    discontinued INTEGER,
    price_half_oz REAL,
    price_1_oz REAL,
    image TEXT,
    description TEXT,
    grade TEXT
)");

// Create indexes for filtering performance
$db->exec("CREATE INDEX IF NOT EXISTS idx_category ON liqueurs(category)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_type ON liqueurs(type)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_region ON liqueurs(region)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_cost ON liqueurs(cost)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_proof ON liqueurs(proof)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_price_half ON liqueurs(price_half_oz)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_price_one ON liqueurs(price_1_oz)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_distiller ON liqueurs(distiller)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_bottle ON liqueurs(bottle)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_discontinued ON liqueurs(discontinued)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_sub_region ON liqueurs(sub_region)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_age ON liqueurs(age)");

echo "Database, table, and indexes created successfully.";
