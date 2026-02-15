<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)) {
    header('Location: carrinho.php');
    exit();
}

$total = array_sum(array_map(fn($item) => $item['preco'] * $item['quantidade'], $cart));

$titulo_pagina = "Checkout - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold mb-8">Finalizar Compra</h1>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Order Summary -->
                <div class="glass-effect rounded-2xl p-6">
                    <h2 class="text-2xl font-bold mb-4">Resumo do Pedido</h2>
                    <?php foreach($cart as $item): ?>
                    <div class="flex justify-between py-2 border-b border-gray-800">
                        <span><?php echo htmlspecialchars($item['nome']); ?> x<?php echo $item['quantidade']; ?></span>
                        <span>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="flex justify-between pt-4 mt-4 border-t border-gray-700 text-xl font-bold">
                        <span>Total:</span>
                        <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="glass-effect rounded-2xl p-6">
                    <h2 class="text-2xl font-bold mb-4">Pagamento</h2>
                    <form id="checkoutForm">
                        <div class="mb-4">
                            <label class="block text-gray-400 mb-2">Método de Pagamento</label>
                            <select name="metodo" required class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                                <option value="pix">PIX</option>
                                <option value="cartao">Cartão de Crédito</option>
                                <option value="dinheiro">Dinheiro</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-400 mb-2">Observações (opcional)</label>
                            <textarea name="observacoes" rows="3" class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-white text-black px-6 py-4 rounded-xl font-bold hover:bg-gray-200 transition-all">
                            <i class="fas fa-check mr-2"></i>Confirmar Pedido
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('../api/finalizar_compra.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            metodo: this.metodo.value,
            observacoes: this.observacoes.value
        })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert('Pedido realizado com sucesso!');
            window.location.href = 'meusPedidos.php';
        } else {
            alert('Erro: ' + (data.message || 'Tente novamente'));
        }
    });
});
</script>

<?php require_once '../footer.php'; ?>
