<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

// Update status
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header('Location: agendamentos.php');
    exit();
}

// Get all bookings
$stmt = $pdo->query("
    SELECT a.*, u.nome as cliente_nome, u.email, u.telefone, s.nome as servico_nome
    FROM agendamentos a
    JOIN usuarios u ON a.usuario_id = u.id
    JOIN servicos s ON a.servico_id = s.id
    ORDER BY a.data_agendamento DESC
");
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white">

<div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-900 border-r border-gray-800">
        <div class="p-6">
            <h1 class="text-2xl font-bold">SampTech Admin</h1>
        </div>
        <nav class="px-4">
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-lg mb-2">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="agendamentos.php" class="flex items-center space-x-3 px-4 py-3 bg-gray-800 text-white rounded-lg mb-2">
                <i class="fas fa-calendar"></i>
                <span>Agendamentos</span>
            </a>
            <a href="servicos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-lg mb-2">
                <i class="fas fa-tools"></i>
                <span>Serviços</span>
            </a>
            <a href="usuarios.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-lg mb-2">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-lg mt-8">
                <i class="fas fa-home"></i>
                <span>Voltar ao Site</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-8">
            <h2 class="text-3xl font-bold mb-8">Gerenciar Agendamentos</h2>

            <div class="bg-gray-900 rounded-xl p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800">
                                <th class="text-left py-3 px-4">ID</th>
                                <th class="text-left py-3 px-4">Cliente</th>
                                <th class="text-left py-3 px-4">Contato</th>
                                <th class="text-left py-3 px-4">Serviço</th>
                                <th class="text-left py-3 px-4">Data</th>
                                <th class="text-left py-3 px-4">Valor</th>
                                <th class="text-left py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($agendamentos as $ag): ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-800">
                                <td class="py-3 px-4">#<?php echo $ag['id']; ?></td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold"><?php echo htmlspecialchars($ag['cliente_nome']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($ag['email']); ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $ag['telefone']); ?>" target="_blank" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm transition-all" title="WhatsApp">
                                            <i class="fab fa-whatsapp mr-1"></i><?php echo htmlspecialchars($ag['telefone']); ?>
                                        </a>
                                        <a href="mailto:<?php echo htmlspecialchars($ag['email']); ?>" 
                                           class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-lg text-sm transition-all" title="Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($ag['servico_nome']); ?></td>
                                <td class="py-3 px-4"><?php echo date('d/m/Y H:i', strtotime($ag['data_agendamento'])); ?></td>
                                <td class="py-3 px-4">R$ <?php echo number_format($ag['valor_orcamento'], 2, ',', '.'); ?></td>
                                <td class="py-3 px-4">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $ag['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="bg-gray-800 text-white px-3 py-1 rounded text-sm">
                                            <option value="pendente" <?php echo $ag['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                            <option value="confirmado" <?php echo $ag['status'] == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                            <option value="em_andamento" <?php echo $ag['status'] == 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                            <option value="concluido" <?php echo $ag['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                                            <option value="cancelado" <?php echo $ag['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
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
