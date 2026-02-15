<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

// Get all users
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Admin</title>
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
            <a href="servicos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-tools"></i>
                <span>Serviços</span>
            </a>
            <a href="produtos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-box"></i>
                <span>Produtos</span>
            </a>
            <a href="usuarios.php" class="flex items-center space-x-3 px-4 py-3 bg-white text-black rounded-xl mb-2 font-semibold">
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
            <div class="mb-8">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">Usuários</h2>
                <p class="text-gray-400">Gerenciar todos os usuários do sistema</p>
            </div>

            <div class="glass-effect rounded-2xl p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800 text-left">
                                <th class="py-3 px-4 text-gray-400 font-medium">ID</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Nome</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Email</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Telefone</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Tipo</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Cadastro</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $user): ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900 transition-all">
                                <td class="py-4 px-4 text-gray-500">#<?php echo $user['id']; ?></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-900 rounded-full flex items-center justify-center font-bold">
                                            <?php echo strtoupper(substr($user['nome'], 0, 2)); ?>
                                        </div>
                                        <span class="font-semibold"><?php echo htmlspecialchars($user['nome']); ?></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-gray-400"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="py-4 px-4 text-gray-400"><?php echo htmlspecialchars($user['telefone'] ?? '-'); ?></td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $user['tipo'] == 'admin' ? 'bg-purple-900/30 text-purple-400' : 'bg-blue-900/30 text-blue-400'; ?>">
                                        <?php echo ucfirst($user['tipo']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-400"><?php echo date('d/m/Y', strtotime($user['data_cadastro'])); ?></td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo isset($user['ativo']) && $user['ativo'] ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'; ?>">
                                        <?php echo isset($user['ativo']) && $user['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
