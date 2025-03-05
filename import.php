<?php
// Connect to SQLite
$db = new PDO('sqlite:database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if it doesn't exist with updated columns
$db->exec("CREATE TABLE IF NOT EXISTS liquors (
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
$db->exec("CREATE INDEX IF NOT EXISTS idx_category ON liquors(category)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_type ON liquors(type)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_region ON liquors(region)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_cost ON liquors(cost)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_proof ON liquors(proof)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_price_half ON liquors(price_half_oz)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_price_one ON liquors(price_1_oz)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_distiller ON liquors(distiller)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_bottle ON liquors(bottle)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_discontinued ON liquors(discontinued)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_sub_region ON liquors(sub_region)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_age ON liquors(age)");

echo "Database, table, and indexes created successfully.";
