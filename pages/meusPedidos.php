<?php
require_once '../config/session.php';
require_once '../config.php';
require_once '../models/Pedido.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pedidoModel = new Pedido();
$pedidos = $pedidoModel->buscarPorUsuario($_SESSION['usuario_id']);

$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_email = $_SESSION['usuario_email'] ?? '';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$titulo_pagina = "Meus Pedidos - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <h1 class="text-6xl font-black mb-6">
                <span class="bg-gradient-to-r from-gray-200 via-white to-gray-300 bg-clip-text text-transparent">
                    Meus Pedidos
                </span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Acompanhe o status dos seus pedidos em tempo real
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <?php if (empty($pedidos)): ?>
                <div class="glass-effect rounded-3xl p-12 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Nenhum pedido encontrado</h3>
                    <p class="text-gray-400 mb-8">Você ainda não fez nenhum pedido. Que tal começar agora?</p>
                    <a href="produtos.php" class="bg-white text-black px-8 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                        <i class="fas fa-shopping-cart mr-2"></i>Ver Produtos
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php 
                        $items = json_decode($pedido['items'], true);
                        $statusColors = [
                            'pendente' => 'bg-yellow-600/20 text-yellow-300 border-yellow-600/30',
                            'processando' => 'bg-blue-600/20 text-blue-300 border-blue-600/30',
                            'enviado' => 'bg-purple-600/20 text-purple-300 border-purple-600/30',
                            'entregue' => 'bg-green-600/20 text-green-300 border-green-600/30',
                            'cancelado' => 'bg-red-600/20 text-red-300 border-red-600/30'
                        ];
                        $statusColor = $statusColors[$pedido['status']] ?? $statusColors['pendente'];
                        ?>
                        
                        <div class="glass-effect rounded-2xl p-8 hover:bg-gray-900/50 transition-all">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-2xl font-bold text-white mb-2">Pedido #<?php echo $pedido['id']; ?></h3>
                                    <p class="text-gray-400">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                                    <span class="px-4 py-2 rounded-full text-sm font-medium border <?php echo $statusColor; ?>">
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-white">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></div>
                                        <div class="text-sm text-gray-400"><?php echo count($items); ?> item(s)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items -->
                            <div class="space-y-3 mb-6">
                                <?php foreach ($items as $item): ?>
                                    <div class="flex items-center bg-gray-900/50 rounded-xl p-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-box text-2xl text-gray-400"></i>
                                        </div>
                                        <div class="flex-grow">
                                            <h4 class="font-bold text-white"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($item['description'] ?? 'Produto'); ?></p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-white font-semibold">Qtd: <?php echo $item['quantity'] ?? 1; ?></span>
                                                <span class="text-gray-400 mx-2">•</span>
                                                <span class="text-white font-semibold">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Order Details -->
                            <div class="grid md:grid-cols-2 gap-6 pt-6 border-t border-gray-800">
                                <div>
                                    <h5 class="font-semibold text-white mb-2">Método de Pagamento</h5>
                                    <p class="text-gray-400"><?php echo htmlspecialchars($pedido['metodo_pagamento']); ?></p>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-white mb-2">Endereço de Entrega</h5>
                                    <p class="text-gray-400"><?php echo htmlspecialchars($pedido['endereco_entrega']); ?></p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex justify-end mt-6 space-x-4">
                                <?php if ($pedido['status'] === 'pendente'): ?>
                                    <button onclick="cancelOrder(<?php echo $pedido['id']; ?>)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                                        <i class="fas fa-times mr-2"></i>Cancelar
                                    </button>
                                <?php endif; ?>
                                <button onclick="viewOrderDetails(<?php echo $pedido['id']; ?>)" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                                    <i class="fas fa-eye mr-2"></i>Detalhes
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function cancelOrder(orderId) {
    if (confirm('Tem certeza que deseja cancelar este pedido?')) {
        fetch('../api/cancel_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        });
    }
}

function viewOrderDetails(orderId) {
    // Implement order details modal or redirect
    alert('Detalhes do pedido #' + orderId);
}

// Auto-refresh every 30 seconds for real-time updates
setInterval(() => {
    location.reload();
}, 30000);
</script>

<?php require_once '../footer.php'; ?>
