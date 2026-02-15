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
                    <h2 class="text-2xl font-bold mb-6">Pagamento</h2>
                    <form id="checkoutForm">
                        <div class="mb-6">
                            <label class="block text-gray-400 mb-4">Método de Pagamento</label>
                            <div class="grid gap-4">
                                <label class="payment-option cursor-pointer">
                                    <input type="radio" name="metodo" value="pix" required class="hidden">
                                    <div class="glass-effect rounded-xl p-4 border-2 border-gray-700 hover:border-white transition-all">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-qrcode text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-lg">PIX</div>
                                                <div class="text-sm text-gray-400">Pagamento instantâneo</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option cursor-pointer">
                                    <input type="radio" name="metodo" value="cartao" class="hidden">
                                    <div class="glass-effect rounded-xl p-4 border-2 border-gray-700 hover:border-white transition-all">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-credit-card text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-lg">Cartão de Crédito</div>
                                                <div class="text-sm text-gray-400">Parcelamento disponível</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option cursor-pointer">
                                    <input type="radio" name="metodo" value="dinheiro" class="hidden">
                                    <div class="glass-effect rounded-xl p-4 border-2 border-gray-700 hover:border-white transition-all">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-lg">Dinheiro</div>
                                                <div class="text-sm text-gray-400">Pagamento na entrega</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
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

<style>
.payment-option input:checked + div {
    border-color: white;
    background: rgba(255, 255, 255, 0.1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes checkmark {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.success-modal {
    animation: slideDown 0.3s ease-out;
}

.checkmark {
    animation: checkmark 0.5s ease-out 0.3s both;
}
</style>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="success-modal glass-effect rounded-3xl p-8 max-w-md mx-4 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-white text-5xl checkmark"></i>
        </div>
        <h2 class="text-3xl font-bold mb-4">Pedido Confirmado!</h2>
        <p class="text-gray-400 mb-6">Seu pedido foi realizado com sucesso. Você será redirecionado em instantes...</p>
        <div class="flex gap-4">
            <a href="meusPedidos.php" class="flex-1 bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                Ver Pedidos
            </a>
            <a href="produtos.php" class="flex-1 glass-effect px-6 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all">
                Continuar Comprando
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const metodo = document.querySelector('input[name="metodo"]:checked');
    if(!metodo) {
        alert('Selecione um método de pagamento');
        return;
    }
    
    fetch('../api/finalizar_compra.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            metodo: metodo.value,
            observacoes: this.observacoes.value
        })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            setTimeout(() => {
                window.location.href = 'meusPedidos.php';
            }, 3000);
        } else {
            alert('Erro: ' + (data.message || 'Tente novamente'));
        }
    });
});
</script>

<?php require_once '../footer.php'; ?>
