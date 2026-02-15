<?php
session_start();
require_once '../config.php';

// Get products from database
try {
    $stmt = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY categoria, nome");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories
    $stmt = $pdo->query("SELECT DISTINCT categoria FROM produtos WHERE ativo = 1 ORDER BY categoria");
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $produtos = [];
    $categorias = [];
}

$titulo_pagina = "Produtos - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 fade-in-up">
            <h1 class="text-5xl font-bold text-white mb-4">
                <span class="bg-gradient-to-r from-gray-200 via-white to-gray-300 bg-clip-text text-transparent">
                    Nossos Produtos
                </span>
            </h1>
            <p class="text-xl text-gray-400">Equipamentos e acessórios de qualidade</p>
        </div>

        <?php if (!empty($categorias)): ?>
        <!-- Category Filter -->
        <div class="mb-8 flex flex-wrap gap-3 justify-center">
            <button class="px-6 py-2 bg-white text-black rounded-xl font-semibold categoria-filter active" data-categoria="todos">
                Todos
            </button>
            <?php foreach ($categorias as $cat): ?>
            <button class="px-6 py-2 glass-effect text-white rounded-xl font-semibold hover:bg-gray-800 categoria-filter" data-categoria="<?php echo strtolower($cat); ?>">
                <?php echo htmlspecialchars($cat); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($produtos)): ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-2xl p-12 text-center">
            <i class="fas fa-box-open text-6xl text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-white mb-4">Em Breve</h2>
            <p class="text-gray-400 mb-6">Estamos preparando nosso catálogo de produtos para você.</p>
            <a href="servicos.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 inline-block">
                Ver Serviços
            </a>
        </div>
        <?php else: ?>
        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($produtos as $produto): ?>
            <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all product-card" data-categoria="<?php echo strtolower($produto['categoria']); ?>">
                <!-- Product Image -->
                <div class="w-full h-48 bg-gray-800 rounded-xl mb-4 flex items-center justify-center overflow-hidden">
                    <?php if (!empty($produto['imagem']) && file_exists('../' . $produto['imagem'])): ?>
                        <img src="../<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-box text-4xl text-gray-600"></i>
                    <?php endif; ?>
                </div>

                <!-- Category Badge -->
                <span class="inline-block px-3 py-1 bg-gra  y-800 text-gray-300 rounded-full text-xs mb-3">
                    <?php echo htmlspecialchars($produto['categoria']); ?>
                </span>

                <!-- Product Name -->
                <h3 class="text-xl font-bold text-white mb-2">
                    <?php echo htmlspecialchars($produto['nome']); ?>
                </h3>

                <!-- Product Description -->
                <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                    <?php echo htmlspecialchars($produto['descricao']); ?>
                </p>

                <!-- Stock Status -->
                <div class="mb-4">
                    <?php if ($produto['estoque'] > 0): ?>
                        <span class="text-green-400 text-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            <?php echo $produto['estoque']; ?> em estoque
                        </span>
                    <?php else: ?>
                        <span class="text-red-400 text-sm">
                            <i class="fas fa-times-circle mr-1"></i>
                            Fora de estoque
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Price and Action -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                    <div class="text-2xl font-bold text-white">
                        R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                    </div>
                    <?php if ($produto['estoque'] > 0): ?>
                        <button onclick="addToCart(<?php echo $produto['id']; ?>, '<?php echo htmlspecialchars($produto['nome']); ?>', <?php echo $produto['preco']; ?>)" 
                                class="bg-white text-black px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition-all">
                            <i class="fas fa-cart-plus mr-1"></i>
                            Adicionar
                        </button>
                    <?php else: ?>
                        <button disabled class="bg-gray-700 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed">
                            Indisponível
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Category Filter
document.querySelectorAll('.categoria-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.categoria-filter').forEach(b => {
            b.classList.remove('active', 'bg-white', 'text-black');
            b.classList.add('glass-effect', 'text-white');
        });
        
        this.classList.add('active', 'bg-white', 'text-black');
        this.classList.remove('glass-effect', 'text-white');
        
        const categoria = this.dataset.categoria;
        document.querySelectorAll('.product-card').forEach(card => {
            if (categoria === 'todos' || card.dataset.categoria === categoria) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Add to Cart
function addToCart(id, nome, preco) {
    fetch('../api/add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({produto_id: id, nome: nome, preco: preco, quantidade: 1})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho!');
            location.reload();
        } else {
            alert('Erro ao adicionar produto.');
        }
    });
}
</script>

<?php require_once '../footer.php'; ?>
