<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Conexão com banco estabelecida!<br>";
    
    // Testar consulta
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    
    echo "✅ Total de usuários: " . $result['total'] . "<br>";
    
    // Listar usuários
    $stmt = $db->query("SELECT email FROM usuarios");
    echo "✅ Usuários cadastrados:<br>";
    while ($row = $stmt->fetch()) {
        echo "- " . $row['email'] . "<br>";
    }
} else {
    echo "❌ Erro na conexão!";
}
?>