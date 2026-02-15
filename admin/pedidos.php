<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

// Get all orders
$stmt = $pdo->query("
    SELECT p.*, u.nome as cliente_nome, u.email, u.telefone
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.data_pedido DESC
");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Admin</title>
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
            <a href="pedidos.php" class="flex items-center space-x-3 px-4 py-3 bg-white text-black rounded-xl mb-2 font-semibold">
                <i class="fas fa-shopping-cart"></i>
                <span>Pedidos</span>
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
            <div class="mb-8">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">Pedidos de Produtos</h2>
                <p class="text-gray-400">Gerencie todos os pedidos de produtos</p>
            </div>

            <div class="glass-effect rounded-2xl p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800 text-left">
                                <th class="py-3 px-4 text-gray-400 font-medium">ID</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Cliente</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Contato</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Data</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Total</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Status</th>
                                <th class="py-3 px-4 text-gray-400 font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pedidos as $pedido): ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900 transition-all">
                                <td class="py-4 px-4 text-gray-500">#<?php echo $pedido['id']; ?></td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold"><?php echo htmlspecialchars($pedido['cliente_nome']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($pedido['email']); ?></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex gap-2">
                                        <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $pedido['telefone']); ?>" target="_blank" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm transition-all">
                                            <i class="fab fa-whatsapp mr-1"></i><?php echo htmlspecialchars($pedido['telefone']); ?>
                                        </a>
                                        <a href="mailto:<?php echo htmlspecialchars($pedido['email']); ?>" 
                                           class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-lg text-sm transition-all">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm"><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                <td class="py-4 px-4 font-bold">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                <td class="py-4 px-4">
                                    <form method="POST" action="../api/update_pedido_status.php" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $pedido['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="bg-gray-800 text-white px-3 py-1 rounded text-sm">
                                            <option value="processando" <?php echo $pedido['status'] == 'processando' ? 'selected' : ''; ?>>Processando</option>
                                            <option value="enviado" <?php echo $pedido['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                            <option value="entregue" <?php echo $pedido['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                            <option value="cancelado" <?php echo $pedido['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="py-4 px-4">
                                    <button onclick="viewItems(<?php echo $pedido['id']; ?>)" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-lg text-sm transition-all">
                                        <i class="fas fa-eye mr-1"></i>Ver Itens
                                    </button>
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

<script>
function viewItems(id) {
    window.location.href = 'pedido_detalhes.php?id=' + id;
}
</script>

</body>
</html>
