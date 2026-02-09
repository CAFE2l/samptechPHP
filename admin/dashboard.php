<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

// Get statistics
$stats = [
    'total_usuarios' => $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'cliente'")->fetchColumn(),
    'total_servicos' => $pdo->query("SELECT COUNT(*) FROM agendamentos")->fetchColumn(),
    'servicos_pendentes' => $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE status = 'pendente'")->fetchColumn(),
    'receita_total' => $pdo->query("SELECT SUM(valor_orcamento) FROM agendamentos WHERE status IN ('concluido', 'confirmado')")->fetchColumn() ?: 0
];

// Recent bookings
$stmt = $pdo->query("
    SELECT a.*, u.nome as cliente_nome, u.email as cliente_email, s.nome as servico_nome
    FROM agendamentos a
    JOIN usuarios u ON a.usuario_id = u.id
    JOIN servicos s ON a.servico_id = s.id
    ORDER BY a.data_agendamento DESC
    LIMIT 10
");
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SampTech</title>
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
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 bg-white text-black rounded-xl mb-2 font-semibold">
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
            <div class="mb-8">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">Dashboard</h2>
                <p class="text-gray-400">Visão geral do sistema</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <span class="text-4xl font-bold"><?php echo $stats['total_usuarios']; ?></span>
                    </div>
                    <p class="text-gray-400 text-sm">Total Clientes</p>
                    <div class="mt-2 text-green-400 text-xs">
                        <i class="fas fa-arrow-up mr-1"></i>Ativos
                    </div>
                </div>

                <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <span class="text-4xl font-bold"><?php echo $stats['total_servicos']; ?></span>
                    </div>
                    <p class="text-gray-400 text-sm">Total Serviços</p>
                    <div class="mt-2 text-blue-400 text-xs">
                        <i class="fas fa-chart-line mr-1"></i>Todos os tempos
                    </div>
                </div>

                <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <span class="text-4xl font-bold"><?php echo $stats['servicos_pendentes']; ?></span>
                    </div>
                    <p class="text-gray-400 text-sm">Pendentes</p>
                    <div class="mt-2 text-yellow-400 text-xs">
                        <i class="fas fa-exclamation-circle mr-1"></i>Requer atenção
                    </div>
                </div>

                <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <span class="text-4xl font-bold">R$ <?php echo number_format($stats['receita_total'] / 1000, 1); ?>k</span>
                    </div>
                    <p class="text-gray-400 text-sm">Receita Total</p>
                    <div class="mt-2 text-purple-400 text-xs">
                        <i class="fas fa-wallet mr-1"></i>Faturamento
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="glass-effect rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold">Agendamentos Recentes</h3>
                    <a href="agendamentos.php" class="text-sm text-gray-400 hover:text-white transition-all">
                        Ver todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800 text-left">
                                <th class="py-3 px-4 text-gray-400 font-medium">Cliente</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Serviço</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Data</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Status</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($agendamentos)): ?>
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Nenhum agendamento ainda</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($agendamentos as $ag): ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900 transition-all">
                                <td class="py-4 px-4">
                                    <div class="font-semibold"><?php echo htmlspecialchars($ag['cliente_nome']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($ag['cliente_email']); ?></div>
                                </td>
                                <td class="py-4 px-4"><?php echo htmlspecialchars($ag['servico_nome']); ?></td>
                                <td class="py-4 px-4 text-sm"><?php echo date('d/m/Y H:i', strtotime($ag['data_agendamento'])); ?></td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php 
                                        echo $ag['status'] == 'pendente' ? 'bg-yellow-900/30 text-yellow-400' : 
                                            ($ag['status'] == 'confirmado' ? 'bg-blue-900/30 text-blue-400' : 
                                            ($ag['status'] == 'concluido' ? 'bg-green-900/30 text-green-400' : 'bg-gray-800 text-gray-400'));
                                    ?>">
                                        <?php echo ucfirst($ag['status']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-bold">R$ <?php echo number_format($ag['valor_orcamento'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
