<?php
// update_database.php - Script to add foto_perfil column
require_once 'config/database.php';

try {
    // Check if foto_perfil column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'foto_perfil'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Add foto_perfil column
        $sql = "ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) NULL";
        $pdo->exec($sql);
        echo "✅ Column 'foto_perfil' added successfully to usuarios table!<br>";
    } else {
        echo "ℹ️ Column 'foto_perfil' already exists in usuarios table.<br>";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/profiles/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "✅ Created uploads/profiles/ directory!<br>";
    } else {
        echo "ℹ️ uploads/profiles/ directory already exists.<br>";
    }
    
    echo "<br>🎉 Database update completed successfully!";
    
} catch (Exception $e) {
    echo "❌ Error updating database: " . $e->getMessage();
}
?>