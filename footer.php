<?php
    // Check if we're in root directory or subdirectory
    $session_path = file_exists('./config/session.php') ? './config/session.php' : '../config/session.php';
    require_once $session_path;
?>
    </main>
    <style>
.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.whatsapp-float a {
    background: #25D366;
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
    <!-- Footer -->
    <footer class="bg-black pt-16 pb-8 border-t border-gray-800">

        <div class="whatsapp-float">
        <a href="https://wa.me/5564992800407" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    </div>

        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <!-- Logo e Descrição -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-white to-gray-300 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tools text-black text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black">
                                <span class="text-white">SAMP</span>
                                <span class="text-gray-300">TECH</span>
                            </h3>
                            <p class="text-xs text-gray-400">INFORMÁTICA</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-400 mb-6">
                        Assistência técnica especializada com transparência, qualidade e agilidade em Rio Verde.
                    </p>
                    
                    <div class="flex space-x-4">
                        <a href="https://wa.me/5564992800407" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-gray-800 smooth-transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.instagram.com/samptechassistencia" target="_blank" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-gray-800 smooth-transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-gray-800 smooth-transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Links Rápidos -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Links Rápidos</h4>
                    <ul class="space-y-3">
                        <?php 
                        $base_path = file_exists('./index.php') ? './' : '../';
                        ?>
                        <li><a href="<?php echo $base_path; ?>index.php" class="text-gray-400 hover:text-white smooth-transition">Início</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/servicos.php" class="text-gray-400 hover:text-white smooth-transition">Serviços</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/produtos.php" class="text-gray-400 hover:text-white smooth-transition">Produtos</a></li>
                    </ul>
                </div>
                
                <!-- Serviços -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Serviços</h4>
                    <ul class="space-y-3">
                        <li><a href="<?php echo $base_path; ?>pages/servicos.php#manutencao" class="text-gray-400 hover:text-white smooth-transition">Manutenção de Computadores</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/servicos.php#celulares" class="text-gray-400 hover:text-white smooth-transition">Reparos de Celulares</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/servicos.php#upgrades" class="text-gray-400 hover:text-white smooth-transition">Upgrades de Performance</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/servicos.php" class="text-gray-400 hover:text-white smooth-transition">Todos os Serviços</a></li>
                    </ul>
                </div>
                
                <!-- Contato -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Contato</h4>
                    <div class="space-y-3">
                        <p class="text-gray-400">
                            <i class="fas fa-phone-alt mr-2 text-gray-500"></i>
                            (64) 9 9280-0407
                        </p>
                        <p class="text-gray-400">
                            <i class="fas fa-envelope mr-2 text-gray-500"></i>
                            joaovsampaio.dev@gmail.com
                        </p>
                        <p class="text-gray-400">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                            Rio Verde - GO
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Direitos Autorais -->
            <div class="pt-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm mb-4 md:mb-0">
                        &copy; <?php echo date('Y'); ?> SampTech Informática. Todos os direitos reservados.
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?php echo $base_path; ?>pages/privacidade.php" class="text-gray-500 text-sm hover:text-gray-400">Política de Privacidade</a>
                        <a href="<?php echo $base_path; ?>pages/termos.php" class="text-gray-500 text-sm hover:text-gray-400">Termos de Uso</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Carrinho Sidebar -->
    <div id="cartSidebar" class="fixed top-0 right-0 bottom-0 w-full md:w-96 bg-black border-l border-gray-800 z-50 transform translate-x-full smooth-transition overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-white">Seu Carrinho</h2>
                <button class="close-cart text-gray-300 hover:text-white smooth-transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="space-y-6 mb-8" id="cartItems">
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-4xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400">Seu carrinho está vazio</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="flex items-center bg-gray-900 rounded-xl p-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-laptop-medical text-2xl text-gray-400"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-white mb-1"><?php echo htmlspecialchars($item['name'] ?? 'Serviço'); ?></h4>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($item['description'] ?? 'Descrição do serviço'); ?></p>
                            <p class="text-lg font-bold text-white mt-2">R$ <?php echo number_format($item['price'] ?? 0, 2, ',', '.'); ?></p>
                        </div>
                        <a href="?remove_item=<?php echo $index; ?>" class="remove-item text-gray-400 hover:text-red-400 smooth-transition ml-4">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($_SESSION['cart'])): ?>
            <div class="glass-effect rounded-xl p-6 mb-8">
                <div class="flex justify-between mb-4">
                    <span class="text-gray-300">Subtotal</span>
                    <span class="text-white font-bold">R$ <?php echo number_format($cart_total, 2, ',', '.'); ?></span>
                </div>
                <div class="flex justify-between mb-6 pt-4 border-t border-gray-700">
                    <span class="text-white font-bold text-lg">Total</span>
                    <span class="text-white font-bold text-xl">R$ <?php echo number_format($cart_total, 2, ',', '.'); ?></span>
                </div>
                
                <?php if ($usuario_logado): ?>
                    <button onclick="processCart()" class="block w-full bg-white text-black py-4 rounded-lg font-bold hover:bg-gray-200 smooth-transition mb-4 text-center">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Finalizar Pedido
                    </button>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>pages/cadastro.php" class="block w-full bg-white text-black py-4 rounded-lg font-bold hover:bg-gray-200 smooth-transition mb-4 text-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Cadastrar para Agendar
                    </a>
                <?php endif; ?>
                
                <button class="close-cart w-full border-2 border-gray-700 text-white py-4 rounded-lg font-medium hover:border-white smooth-transition">
                    Continuar Comprando
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos DOM
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const cartSidebar = document.getElementById('cartSidebar');
            const cartButton = document.getElementById('cartButton');
            const closeCart = document.querySelector('.close-cart');
            const removeItems = document.querySelectorAll('.remove-item');
            
            // Menu Mobile
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileMenu.classList.remove('translate-x-full');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if (closeMobileMenu) {
                closeMobileMenu.addEventListener('click', () => {
                    mobileMenu.classList.add('translate-x-full');
                    document.body.style.overflow = 'auto';
                });
            }
            
            // Fechar menu ao clicar em link
            if (mobileMenu) {
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.add('translate-x-full');
                        document.body.style.overflow = 'auto';
                    });
                });
            }
            
            // Carrinho
            if (cartButton) {
                cartButton.addEventListener('click', () => {
                    if (cartSidebar) {
                        cartSidebar.classList.remove('translate-x-full');
                        document.body.style.overflow = 'hidden';
                    }
                });
            }
            
            if (closeCart) {
                closeCart.addEventListener('click', () => {
                    if (cartSidebar) {
                        cartSidebar.classList.add('translate-x-full');
                        document.body.style.overflow = 'auto';
                    }
                });
            }
            
            // Remover item do carrinho
            removeItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja remover este item?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Scroll suave para links âncora
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    if (href === '#' || href === '#!' || href.startsWith('#')) {
                        const targetId = href;
                        const targetElement = document.querySelector(targetId);
                        
                        if (targetElement) {
                            e.preventDefault();
                            window.scrollTo({
                                top: targetElement.offsetTop - 80,
                                behavior: 'smooth'
                            });
                            
                            // Fechar menu mobile se aberto
                            if (mobileMenu && !mobileMenu.classList.contains('translate-x-full')) {
                                mobileMenu.classList.add('translate-x-full');
                                document.body.style.overflow = 'auto';
                            }
                        }
                    }
                });
            });
            
            // Animações ao scroll
            const fadeElements = document.querySelectorAll('.fade-in-up');
            
            const checkFade = () => {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.style.opacity = "1";
                        element.style.transform = "translateY(0)";
                    }
                });
            };
            
            // Configurar estado inicial
            if (fadeElements.length > 0) {
                fadeElements.forEach(element => {
                    element.style.opacity = "0";
                    element.style.transform = "translateY(30px)";
                    element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                });
                
                // Verificar ao carregar e ao scroll
                window.addEventListener('scroll', checkFade);
                checkFade();
            }
            
            // Dropdown do perfil
            const profileButton = document.querySelector('.group button');
            if (profileButton) {
                const profileGroup = profileButton.closest('.group');
                const dropdown = profileGroup.querySelector('.absolute');
                
                if (dropdown) {
                    profileGroup.addEventListener('mouseenter', () => {
                        dropdown.classList.remove('opacity-0', 'invisible');
                        dropdown.classList.add('opacity-100', 'visible');
                    });
                    
                    profileGroup.addEventListener('mouseleave', () => {
                        dropdown.classList.add('opacity-0', 'invisible');
                        dropdown.classList.remove('opacity-100', 'visible');
                    });
                }
            }
        });
    </script>
    
    <script>
        // Process cart function
        function processCart() {
            const button = event.target;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processando...';
            button.disabled = true;
            
            // Get cart items from session
            const cartItems = <?php echo json_encode($_SESSION['cart'] ?? []); ?>;
            
            if (cartItems.length === 0) {
                alert('Carrinho vazio!');
                button.innerHTML = originalText;
                button.disabled = false;
                return;
            }
            
            // Prepare items for API
            const items = cartItems.map(item => ({
                name: item.name || 'Produto/Serviço',
                quantity: item.quantity || 1,
                price: item.price || 0,
                type: item.type || 'produto'
            }));
            
            fetch('../api/process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: items,
                    payment_method: 'A definir',
                    address: 'Endereço do usuário'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido criado com sucesso!');
                    
                    // Check if has services or products
                    const hasServices = items.some(item => item.type === 'servico');
                    const hasProducts = items.some(item => item.type === 'produto');
                    
                    if (hasServices && hasProducts) {
                        if (confirm('Você tem produtos e serviços no pedido. Deseja ver os produtos primeiro?')) {
                            window.location.href = 'meusPedidos.php';
                        } else {
                            window.location.href = 'meusServicos.php';
                        }
                    } else if (hasServices) {
                        window.location.href = 'meusServicos.php';
                    } else {
                        window.location.href = 'meusPedidos.php';
                    }
                } else {
                    alert(data.message || 'Erro ao processar pedido');
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao processar pedido');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    </script>
</body>
</html>