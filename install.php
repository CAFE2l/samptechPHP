<?php
// Script para criar o banco de dados SampTech

$host = 'localhost';
$username = 'root';
$password = 'mysql';

try {
    // Conectar sem selecionar banco
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao MySQL!<br><br>";
    
    // Ler o arquivo SQL
    $sql = file_get_contents('create_database.sql');
    
    // Executar cada comando
    $pdo->exec($sql);
    
    echo "✅ Banco de dados 'samptech' criado com sucesso!<br>";
    echo "✅ Todas as tabelas foram criadas!<br>";
    echo "✅ Serviços padrão inseridos!<br>";
    echo "✅ Usuário admin criado (email: admin@samptech.com, senha: admin123)<br><br>";
    echo "🎉 <strong>Configuração concluída! Você pode usar o sistema agora.</strong><br><br>";
    echo "<a href='pages/cadastro.php'>Ir para Cadastro</a> | <a href='pages/login.php'>Ir para Login</a>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
