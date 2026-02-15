<?php
session_start();
require_once '../config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$produto) {
    header('Location: produtos.php');
    exit();
}

// Get reviews
$stmt = $pdo->prepare("
    SELECT a.*, u.nome as usuario_nome 
    FROM avaliacoes_produtos a 
    JOIN usuarios u ON a.usuario_id = u.id 
    WHERE a.produto_id = ? 
    ORDER BY a.data_avaliacao DESC
");
$stmt->execute([$id]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$avg = $pdo->prepare("SELECT AVG(avaliacao) as media, COUNT(*) as total FROM avaliacoes_produtos WHERE produto_id = ?");
$avg->execute([$id]);
$rating = $avg->fetch(PDO::FETCH_ASSOC);

$titulo_pagina = $produto['nome'] . " - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Product Details -->
            <div class="glass-effect rounded-2xl p-8 mb-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Media -->
                    <div class="bg-gray-900 rounded-xl overflow-hidden">
                        <?php if($produto['imagem']): 
                            $ext = pathinfo($produto['imagem'], PATHINFO_EXTENSION);
                            if(in_array($ext, ['mp4', 'webm'])): ?>
                            <video controls class="w-full">
                                <source src="../<?php echo $produto['imagem']; ?>" type="video/<?php echo $ext; ?>">
                            </video>
                        <?php else: ?>
                            <img src="../<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="w-full">
                        <?php endif; else: ?>
                            <div class="h-96 flex items-center justify-center">
                                <i class="fas fa-box text-6xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Info -->
                    <div>
                        <span class="px-3 py-1 bg-gray-800 text-gray-300 rounded-full text-sm"><?php echo htmlspecialchars($produto['categoria']); ?></span>
                        <h1 class="text-4xl font-bold mt-4 mb-4"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-2 mb-4">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= round($rating['media']) ? 'text-yellow-400' : 'text-gray-600'; ?>"></i>
                            <?php endfor; ?>
                            <span class="text-gray-400"><?php echo number_format($rating['media'], 1); ?> (<?php echo $rating['total']; ?> avaliações)</span>
                        </div>

                        <p class="text-gray-400 mb-6"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                        
                        <div class="text-4xl font-bold mb-6">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                        
                        <div class="mb-6">
                            <?php if($produto['estoque'] > 0): ?>
                                <span class="text-green-400"><i class="fas fa-check-circle mr-1"></i><?php echo $produto['estoque']; ?> em estoque</span>
                            <?php else: ?>
                                <span class="text-red-400"><i class="fas fa-times-circle mr-1"></i>Fora de estoque</span>
                            <?php endif; ?>
                        </div>

                        <?php if($produto['estoque'] > 0): ?>
                        <button onclick="addToCart(<?php echo $produto['id']; ?>)" class="w-full bg-white text-black px-6 py-4 rounded-xl font-bold hover:bg-gray-200 transition-all">
                            <i class="fas fa-cart-plus mr-2"></i>Adicionar ao Carrinho
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="glass-effect rounded-2xl p-8">
                <h2 class="text-3xl font-bold mb-6">Avaliações dos Clientes</h2>

                <!-- Add Review Form -->
                <?php if(isset($_SESSION['usuario_id'])): ?>
                <form id="reviewForm" class="bg-gray-900 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-bold mb-4">Deixe sua avaliação</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2">Sua nota</label>
                        <div class="flex gap-2">
                            <?php for($i=1; $i<=5; $i++): ?>
                            <button type="button" onclick="setRating(<?php echo $i; ?>)" class="rating-star text-3xl text-gray-600 hover:text-yellow-400 transition-all">
                                <i class="fas fa-star"></i>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2">Comentário</label>
                        <textarea id="comentario" rows="4" class="w-full bg-gray-800 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2">Foto/Vídeo (opcional)</label>
                        <input type="file" id="midia" accept="image/*,video/*" class="w-full bg-gray-800 border border-gray-700 text-white py-3 px-4 rounded-xl">
                    </div>

                    <button type="submit" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Avaliação
                    </button>
                </form>
                <?php else: ?>
                <div class="bg-gray-900 rounded-xl p-6 mb-8 text-center">
                    <p class="text-gray-400 mb-4">Faça login para avaliar este produto</p>
                    <a href="login.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 inline-block">Login</a>
                </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <div class="space-y-6">
                    <?php foreach($avaliacoes as $av): ?>
                    <div class="bg-gray-900 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="font-bold"><?php echo htmlspecialchars($av['usuario_nome']); ?></div>
                                <div class="flex gap-1 mt-1">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $av['avaliacao'] ? 'text-yellow-400' : 'text-gray-600'; ?> text-sm"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <span class="text-gray-500 text-sm"><?php echo date('d/m/Y', strtotime($av['data_avaliacao'])); ?></span>
                        </div>
                        
                        <?php if($av['comentario']): ?>
                        <p class="text-gray-300 mb-4"><?php echo nl2br(htmlspecialchars($av['comentario'])); ?></p>
                        <?php endif; ?>

                        <?php if($av['midia']): 
                            $ext = pathinfo($av['midia'], PATHINFO_EXTENSION);
                            if(in_array($ext, ['mp4', 'webm'])): ?>
                            <video controls class="w-full max-w-md rounded-lg">
                                <source src="../<?php echo $av['midia']; ?>" type="video/<?php echo $ext; ?>">
                            </video>
                        <?php else: ?>
                            <img src="../<?php echo $av['midia']; ?>" alt="Review" class="w-full max-w-md rounded-lg">
                        <?php endif; endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let selectedRating = 0;

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('rating').value = rating;
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        star.classList.toggle('text-yellow-400', index < rating);
        star.classList.toggle('text-gray-600', index >= rating);
    });
}

document.getElementById('reviewForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if(!selectedRating) {
        alert('Por favor, selecione uma nota');
        return;
    }

    const formData = new FormData();
    formData.append('produto_id', <?php echo $id; ?>);
    formData.append('avaliacao', selectedRating);
    formData.append('comentario', document.getElementById('comentario').value);
    
    const midia = document.getElementById('midia').files[0];
    if(midia) formData.append('midia', midia);

    const response = await fetch('../api/add_review.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();
    if(data.success) {
        location.reload();
    } else {
        alert('Erro ao enviar avaliação');
    }
});

function addToCart(id) {
    fetch('../api/add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({produto_id: id, quantidade: 1})
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert('Produto adicionado ao carrinho!');
            location.reload();
        }
    });
}
</script>

<?php require_once '../footer.php'; ?>
