<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

$titulo_pagina = "Carrinho - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold mb-8">Meu Carrinho</h1>

            <?php if(empty($cart)): ?>
            <div class="glass-effect rounded-2xl p-12 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-600 mb-4"></i>
                <h2 class="text-2xl font-bold mb-4">Carrinho Vazio</h2>
                <p class="text-gray-400 mb-6">Adicione produtos para continuar</p>
                <a href="produtos.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 inline-block">
                    Ver Produtos
                </a>
            </div>
            <?php else: ?>
            <div class="glass-effect rounded-2xl p-8 mb-6">
                <?php foreach($cart as $item): 
                    $subtotal = $item['preco'] * $item['quantidade'];
                    $total += $subtotal;
                ?>
                <div class="flex items-center justify-between py-4 border-b border-gray-800">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg"><?php echo htmlspecialchars($item['nome']); ?></h3>
                        <p class="text-gray-400">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?> x <?php echo $item['quantidade']; ?></p>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xl mb-2">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></div>
                        <button onclick="removeItem(<?php echo $item['produto_id']; ?>)" class="text-red-400 hover:text-red-300 text-sm">
                            <i class="fas fa-trash mr-1"></i>Remover
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-700">
                    <div class="text-2xl font-bold">Total:</div>
                    <div class="text-3xl font-bold">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="produtos.php" class="flex-1 text-center glass-effect px-6 py-4 rounded-xl font-semibold hover:bg-gray-800 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Continuar Comprando
                </a>
                <a href="checkout.php" class="flex-1 text-center bg-white text-black px-6 py-4 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                    Finalizar Compra<i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function removeItem(id) {
    fetch('../api/remove_from_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({produto_id: id})
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) location.reload();
    });
}
</script>

<?php require_once '../footer.php'; ?>
