<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

// Get all services
$stmt = $pdo->query("SELECT * FROM servicos ORDER BY categoria, nome");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Serviços - SampTech Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            background: rgba(17, 17, 17, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-black text-white">

<div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 glass-effect border-r border-gray-800">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SampTech</h1>
            <p class="text-xs text-gray-500 mt-1">Admin Dashboard</p>
        </div>
        <nav class="p-4">
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="agendamentos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-calendar"></i>
                <span>Agendamentos</span>
            </a>
            <a href="servicos.php" class="flex items-center space-x-3 px-4 py-3 bg-white text-black rounded-xl mb-2 font-semibold">
                <i class="fas fa-tools"></i>
                <span>Serviços</span>
            </a>
            <a href="produtos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-box"></i>
                <span>Produtos</span>
            </a>
            <a href="usuarios.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
            <div class="border-t border-gray-800 my-4"></div>
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl transition-all">
                <i class="fas fa-home"></i>
                <span>Voltar ao Site</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">Gerenciar Serviços</h2>
                    <p class="text-gray-400">Visualize e gerencie todos os serviços</p>
                </div>
                <button onclick="openAddModal()" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-plus mr-2"></i>Novo Serviço
                </button>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($servicos as $servico): ?>
                <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tools text-white text-xl"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $servico['ativo'] ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'; ?>">
                            <?php echo $servico['ativo'] ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($servico['nome']); ?></h3>
                    <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                    
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Categoria:</span>
                            <span class="text-white font-medium"><?php echo htmlspecialchars($servico['categoria']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Preço:</span>
                            <span class="text-white font-bold">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tempo:</span>
                            <span class="text-white"><?php echo htmlspecialchars($servico['tempo_estimado']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Garantia:</span>
                            <span class="text-white"><?php echo htmlspecialchars($servico['garantia']); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 pt-4 border-t border-gray-800">
                        <button onclick="editService(<?php echo $servico['id']; ?>)" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                        <button onclick="deleteService(<?php echo $servico['id']; ?>)" class="flex-1 bg-red-900/30 text-red-400 hover:bg-red-900/50 px-4 py-2 rounded-lg transition-all">
                            <i class="fas fa-trash mr-1"></i>Deletar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    alert('Funcionalidade de adicionar serviço em desenvolvimento');
}

function editService(id) {
    window.location.href = 'editar_servico.php?id=' + id;
}

function deleteService(id) {
    if(confirm('Tem certeza que deseja deletar este serviço? Esta ação não pode ser desfeita.')) {
        fetch('../api/delete_servico.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Erro ao deletar serviço');
            }
        });
    }
}
</script>

</body>
</html>
