<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Get all expenses (services + products)
try {
    // Services
    $stmt = $pdo->prepare("
        SELECT a.id, s.nome, a.valor_orcamento as valor, a.data_agendamento as data, 'Serviço' as tipo
        FROM agendamentos a
        JOIN servicos s ON a.servico_id = s.id
        WHERE a.usuario_id = ? AND a.status IN ('concluido', 'confirmado')
        ORDER BY a.data_agendamento DESC
    ");
    $stmt->execute([$usuario_id]);
    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = array_sum(array_column($gastos, 'valor'));
} catch (PDOException $e) {
    $gastos = [];
    $total = 0;
}

$titulo_pagina = "Meus Gastos - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <a href="minha-conta.php" class="text-gray-400 hover:text-white transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar para Minha Conta
                </a>
            </div>

            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4">Meus Gastos</h1>
                <p class="text-gray-400">Histórico completo de serviços e produtos</p>
            </div>

            <!-- Total Card -->
            <div class="glass-effect rounded-2xl p-8 mb-8 text-center">
                <p class="text-gray-400 mb-2">Total Gasto</p>
                <h2 class="text-5xl font-bold text-white mb-4">
                    R$ <?php echo number_format($total / 1000, 1, ',', '.'); ?>k
                </h2>
                <p class="text-gray-500">Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></p>
            </div>

            <!-- Expenses List -->
            <?php if (empty($gastos)): ?>
                <div class="glass-effect rounded-2xl p-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-white mb-2">Nenhum gasto registrado</h3>
                    <p class="text-gray-400 mb-6">Seus serviços e produtos aparecerão aqui</p>
                    <a href="servicos.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 inline-block">
                        Ver Serviços
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($gastos as $gasto): ?>
                        <div class="glass-effect rounded-xl p-6 hover:bg-gray-900 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-<?php echo $gasto['tipo'] == 'Serviço' ? 'laptop-medical' : 'box'; ?> text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white"><?php echo htmlspecialchars($gasto['nome']); ?></h3>
                                        <div class="flex items-center space-x-3 text-sm">
                                            <span class="text-gray-400">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?php echo date('d/m/Y', strtotime($gasto['data'])); ?>
                                            </span>
                                            <span class="px-2 py-1 bg-gray-800 text-gray-300 rounded-full text-xs">
                                                <?php echo $gasto['tipo']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-white">
                                        R$ <?php echo number_format($gasto['valor'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Summary by Type -->
                <div class="grid md:grid-cols-2 gap-6 mt-8">
                    <div class="glass-effect rounded-xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Resumo por Tipo</h3>
                        <div class="space-y-3">
                            <?php
                            $tipos = array_count_values(array_column($gastos, 'tipo'));
                            foreach ($tipos as $tipo => $count):
                            ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400"><?php echo $tipo; ?>s</span>
                                    <span class="text-white font-semibold"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="glass-effect rounded-xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Estatísticas</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Média por item</span>
                                <span class="text-white font-semibold">
                                    R$ <?php echo number_format($total / max(count($gastos), 1), 2, ',', '.'); ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total de itens</span>
                                <span class="text-white font-semibold"><?php echo count($gastos); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php require_once '../footer.php'; ?>
