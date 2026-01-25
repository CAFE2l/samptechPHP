<?php
// carrinho.php - Página do Carrinho de Compras
require_once '../config/database.php';
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calcular totais
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    if (isset($item['price'])) {
        $cart_total += $item['price'];
    }
    $cart_count++;
}

$titulo_pagina = "SampTech - Meu Carrinho";

// Processar remoção de item
if (isset($_GET['remove_item']) && is_numeric($_GET['remove_item'])) {
    $index = intval($_GET['remove_item']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexar array
        header('Location: carrinho.php');
        exit();
    }
}

// Processar limpar carrinho
if (isset($_GET['clear_cart']) && $_GET['clear_cart'] == 'true') {
    $_SESSION['cart'] = [];
    header('Location: carrinho.php');
    exit();
}

// Processar checkout
if (isset($_POST['checkout'])) {
    // Verificar se usuário está logado
    if (!isset($_SESSION['usuario_logado']) || !$_SESSION['usuario_logado']) {
        $_SESSION['redirect_to'] = 'carrinho.php';
        header('Location: login.php');
        exit();
    } else {
        // Redirecionar para página de checkout/agendamento
        header('Location: agendar.php');
        exit();
    }
}

// Verificar se usuário está logado
$usuario_logado = isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$usuario_nome = $usuario_logado ? ($_SESSION['usuario_nome'] ?? '') : '';
$usuario_email = $usuario_logado ? ($_SESSION['usuario_email'] ?? '') : '';

// Calcular totais para exibição
$subtotal = 0;
$desconto = 0;
$total = 0;
$cart_items = $_SESSION['cart'] ?? [];

foreach ($cart_items as $item) {
    if (isset($item['price'])) {
        $subtotal += $item['price'];
    }
}

// Calcular desconto (exemplo: 10% se tiver mais de 2 itens)
if (count($cart_items) >= 2) {
    $desconto = $subtotal * 0.10;
}

$total = $subtotal - $desconto;

// Número de WhatsApp para contato
$whatsapp_number = "5564992800407";
$whatsapp_message = urlencode("Olá! Gostaria de mais informações sobre os serviços no meu carrinho da SampTech.");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --black-primary: #000000;
            --black-secondary: #111111;
            --black-tertiary: #1a1a1a;
            --gray-dark: #262626;
            --gray-medium: #404040;
            --gray-light: #737373;
            --gray-lighter: #a3a3a3;
            --gray-lightest: #d4d4d4;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--black-primary);
            color: var(--white);
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        
        /* Animações */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: slideInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        /* Efeitos de vidro */
        .glass-effect {
            background: rgba(17, 17, 17, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Hover effects */
        .hover-lift:hover {
            transform: translateY(-4px);
            transition: transform 0.3s ease;
        }
        
        .smooth-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Contador de itens no carrinho */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--white);
            color: var(--black-primary);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Garantir padding para o conteúdo principal */
        .main-content {
            padding-top: 80px;
        }
        
        /* Toggle switches */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #ffffff;
        }
        
        .toggle-checkbox:checked + .toggle-label {
            background-color: #ffffff;
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Header Customizado para Carrinho -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/95 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                
                <!-- Logo -->
                <a href="../index.php" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 flex items-center justify-center overflow-hidden smooth-transition group-hover:border-gray-500">
                            <img src="../img/icon.png" alt="SampTech Logo" class="w-full h-full object-cover"/>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight">
                            <span class="text-white">SAMP</span>
                            <span class="text-gray-300">TECH</span>
                        </h1>
                        <p class="text-xs text-gray-400 tracking-wider">INFORMÁTICA</p>
                    </div>
                </a>
                
                <!-- Navegação -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-300 hover:text-white smooth-transition font-medium">
                        Início
                    </a>
                    <a href="servicos.php" class="text-gray-300 hover:text-white smooth-transition font-medium">
                        Serviços
                    </a>
                    <a href="produtos.php" class="text-gray-300 hover:text-white smooth-transition font-medium">
                        Produtos
                    </a>
                </nav>
                
                <!-- Botões de ação -->
                <div class="flex items-center space-x-6">
                    <a href="agendar.php" class="hidden md:flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 smooth-transition">
                        <i class="fas fa-calendar-check"></i>
                        <span>Agendar Serviço</span>
                    </a>
                    
                    <!-- Área do Cliente / Perfil -->
                    <?php if (!$usuario_logado): ?>
                        <a href="login.php" class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border border-gray-700 flex items-center justify-center overflow-hidden">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span class="hidden md:inline font-medium">Login</span>
                        </a>
                    <?php else: ?>
                        <!-- Perfil dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border border-gray-700 flex items-center justify-center overflow-hidden">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="hidden md:inline font-medium"><?php echo htmlspecialchars($usuario_nome); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown do Perfil -->
                            <div class="absolute right-0 top-full mt-2 w-64 bg-gray-900 border border-gray-800 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="p-4 border-b border-gray-800">
                                    <p class="font-semibold text-white"><?php echo htmlspecialchars($usuario_nome); ?></p>
                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($usuario_email); ?></p>
                                </div>
                                <div class="p-2">
                                    <a href="minha-conta.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                        <i class="fas fa-user-circle w-5"></i>
                                        <span>Minha Conta</span>
                                    </a>
                                    <a href="meus-agendamentos.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                        <i class="fas fa-calendar-alt w-5"></i>
                                        <span>Meus Agendamentos</span>
                                    </a>
                                </div>
                                <div class="p-2 border-t border-gray-800">
                                    <a href="../logout.php" class="flex items-center space-x-3 px-3 py-2 text-red-400 hover:text-red-300 hover:bg-gray-800 rounded-lg smooth-transition">
                                        <i class="fas fa-sign-out-alt w-5"></i>
                                        <span>Sair</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Carrinho -->
                    <a href="carrinho.php" class="relative">
                        <i class="fas fa-shopping-cart text-xl text-white smooth-transition"></i>
                        <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Menu Mobile -->
                    <button class="lg:hidden text-gray-300 hover:text-white smooth-transition" id="mobileMenuToggle">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu Mobile Overlay -->
        <div class="lg:hidden fixed inset-0 bg-black z-40 transform translate-x-full smooth-transition" id="mobileMenu">
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center p-6 border-b border-gray-800">
                    <div class="text-2xl font-black">
                        <span class="text-white">SAMP</span>
                        <span class="text-gray-300">TECH</span>
                    </div>
                    <button class="text-gray-300 hover:text-white smooth-transition" id="closeMobileMenu">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <nav class="flex-1 p-6 flex flex-col space-y-8">
                    <a href="../index.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Início</a>
                    <a href="servicos.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Serviços</a>
                    <a href="produtos.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Produtos</a>
                    <a href="carrinho.php" class="text-2xl font-medium text-white smooth-transition">Carrinho</a>
                    
                    <?php if ($usuario_logado): ?>
                        <div class="border-t border-gray-800 pt-4"></div>
                        <a href="minha-conta.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Minha Conta</a>
                        <a href="meus-agendamentos.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Meus Agendamentos</a>
                        <a href="../logout.php" class="text-2xl font-medium text-red-400 hover:text-red-300 smooth-transition">Sair</a>
                    <?php else: ?>
                        <div class="border-t border-gray-800 pt-4"></div>
                        <a href="cadastro.php" class="text-2xl font-medium text-white smooth-transition">Cadastrar</a>
                        <a href="login.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Login</a>
                    <?php endif; ?>
                </nav>
                
                <div class="p-6 border-t border-gray-800">
                    <a href="agendar.php" class="block w-full bg-white text-black py-4 rounded-lg font-semibold hover:bg-gray-200 smooth-transition mb-4 text-center">
                        Agendar Serviço
                    </a>
                    <div class="text-center text-gray-400">
                        <p>(64) 9 9280-0407</p>
                        <p>joaovsampaio.dev@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="min-h-screen bg-black pt-24 pb-12">
            <div class="container mx-auto px-4">
                <!-- Cabeçalho da Página -->
                <div class="mb-8 fade-in-up">
                    <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Meu Carrinho</h1>
                    <div class="flex items-center text-gray-400">
                        <a href="../index.php" class="hover:text-white smooth-transition">Início</a>
                        <i class="fas fa-chevron-right mx-2 text-xs"></i>
                        <a href="produtos.php" class="hover:text-white smooth-transition">Produtos</a>
                        <i class="fas fa-chevron-right mx-2 text-xs"></i>
                        <span class="text-white">Carrinho</span>
                    </div>
                </div>

                <!-- Mensagem de carrinho vazio -->
                <?php if (empty($cart_items)): ?>
                <div class="text-center py-16 fade-in-up">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-900 rounded-full border border-gray-800 mb-6">
                        <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4">Seu carrinho está vazio</h2>
                    <p class="text-gray-400 mb-8 max-w-md mx-auto">
                        Você ainda não adicionou nenhum serviço ao carrinho. Explore nossos produtos e serviços para encontrar o que precisa.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="produtos.php" class="px-8 py-4 bg-white text-black rounded-lg font-bold hover:bg-gray-200 smooth-transition">
                            <i class="fas fa-store mr-2"></i>
                            Ver Produtos
                        </a>
                        <a href="servicos.php" class="px-8 py-4 border-2 border-gray-700 text-white rounded-lg font-medium hover:border-white smooth-transition">
                            <i class="fas fa-laptop-medical mr-2"></i>
                            Ver Serviços
                        </a>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="grid lg:grid-cols-3 gap-8 fade-in-up">
                    <!-- Lista de Itens -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Cabeçalho dos Itens -->
                        <div class="hidden md:flex items-center justify-between p-4 bg-gray-900/50 rounded-lg border border-gray-800">
                            <div class="flex-grow">
                                <span class="text-gray-300 font-medium">Produto/Serviço</span>
                            </div>
                            <div class="w-32 text-center">
                                <span class="text-gray-300 font-medium">Preço</span>
                            </div>
                            <div class="w-24 text-right">
                                <span class="text-gray-300 font-medium">Ação</span>
                            </div>
                        </div>

                        <!-- Itens do Carrinho -->
                        <?php foreach ($cart_items as $index => $item): ?>
                        <div class="glass-effect rounded-xl border border-gray-800 p-4 md:p-6 hover-lift smooth-transition">
                            <div class="flex flex-col md:flex-row md:items-center">
                                <!-- Imagem/Ícone -->
                                <div class="mb-4 md:mb-0 md:mr-6">
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-800 to-black rounded-lg flex items-center justify-center">
                                        <i class="fas <?php echo isset($item['icon']) ? $item['icon'] : 'fa-laptop-medical'; ?> text-2xl text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Informações do Item -->
                                <div class="flex-grow">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                                        <div class="mb-4 md:mb-0 md:mr-4">
                                            <h3 class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? 'Serviço'); ?></h3>
                                            <?php if (!empty($item['description'])): ?>
                                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($item['description']); ?></p>
                                            <?php endif; ?>
                                            
                                            <!-- Detalhes adicionais -->
                                            <?php if (isset($item['details']) && is_array($item['details'])): ?>
                                            <div class="mt-3 space-y-1">
                                                <?php foreach ($item['details'] as $detail): ?>
                                                <div class="flex items-center text-sm text-gray-400">
                                                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                                    <span><?php echo htmlspecialchars($detail); ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Preço e Ações -->
                                        <div class="flex items-center justify-between md:justify-end">
                                            <div class="md:w-32 md:text-center">
                                                <p class="text-xl font-bold text-white">R$ <?php echo number_format($item['price'] ?? 0, 2, ',', '.'); ?></p>
                                            </div>
                                            <div class="md:w-24 md:text-right">
                                                <a href="carrinho.php?remove_item=<?php echo $index; ?>" 
                                                   class="text-gray-400 hover:text-red-400 smooth-transition ml-4 md:ml-0"
                                                   onclick="return confirm('Tem certeza que deseja remover este item?')">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Botões de Ação -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-800">
                            <a href="produtos.php" class="px-6 py-4 border-2 border-gray-700 text-white rounded-lg font-medium hover:border-white smooth-transition text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Continuar Comprando
                            </a>
                            <a href="carrinho.php?clear_cart=true" 
                               class="px-6 py-4 border-2 border-red-900/50 text-red-400 rounded-lg font-medium hover:border-red-400 smooth-transition text-center"
                               onclick="return confirm('Tem certeza que deseja limpar todo o carrinho?')">
                                <i class="fas fa-trash mr-2"></i>
                                Limpar Carrinho
                            </a>
                        </div>
                    </div>

                    <!-- Resumo do Pedido -->
                    <div class="lg:col-span-1">
                        <div class="glass-effect rounded-2xl border border-gray-800 p-6 sticky top-24">
                            <h2 class="text-xl font-bold text-white mb-6">Resumo do Pedido</h2>
                            
                            <!-- Resumo de Valores -->
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Subtotal</span>
                                    <span class="text-white font-medium">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                                </div>
                                
                                <?php if ($desconto > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Desconto (10%)</span>
                                    <span class="text-green-400 font-medium">- R$ <?php echo number_format($desconto, 2, ',', '.'); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex justify-between pt-4 border-t border-gray-800">
                                    <span class="text-lg font-bold text-white">Total</span>
                                    <span class="text-2xl font-black text-white">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                                </div>
                                
                                <?php if ($desconto > 0): ?>
                                <div class="bg-green-900/20 border border-green-800 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-gift text-green-400 mr-3"></i>
                                        <div>
                                            <p class="text-green-300 text-sm font-medium">Desconto Aplicado!</p>
                                            <p class="text-green-400 text-xs">Você ganhou 10% de desconto por ter 2 ou mais itens</p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Botão de Checkout -->
                            <form method="POST" action="" class="mb-6">
                                <button type="submit" name="checkout" 
                                        class="w-full px-6 py-4 bg-gradient-to-r from-white to-gray-300 text-black rounded-lg font-bold hover:from-gray-200 hover:to-gray-100 smooth-transition mb-4 flex items-center justify-center">
                                    <i class="fas fa-calendar-check mr-3"></i>
                                    <?php echo $usuario_logado ? 'Agendar Serviços' : 'Fazer Login para Agendar'; ?>
                                </button>
                            </form>

                            <!-- WhatsApp -->
                            <div class="mb-6">
                                <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $whatsapp_message; ?>" 
                                   target="_blank"
                                   class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg font-bold hover:from-green-700 hover:to-green-800 smooth-transition mb-4 flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-3 text-xl"></i>
                                    Solicitar Orçamento
                                </a>
                                <p class="text-gray-400 text-sm text-center">
                                    Tire dúvidas ou peça um orçamento personalizado
                                </p>
                            </div>

                            <!-- Informações Adicionais -->
                            <div class="space-y-4 text-sm">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt text-gray-400 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-white font-medium">Compra Segura</p>
                                        <p class="text-gray-400">Seus dados estão protegidos</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <i class="fas fa-truck text-gray-400 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-white font-medium">Entrega & Retirada</p>
                                        <p class="text-gray-400">Retire na loja ou agende entrega</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <i class="fas fa-undo-alt text-gray-400 mt=1 mr-3"></i>
                                    <div>
                                        <p class="text-white font-medium">Garantia</p>
                                        <p class="text-gray-400">90 dias para serviços prestados</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cupom de Desconto -->
                        <div class="glass-effect rounded-2xl border border-gray-800 p-6 mt-6">
                            <h3 class="text-lg font-bold text-white mb-4">Cupom de Desconto</h3>
                            <form id="cupomForm" class="space-y-4">
                                <div class="flex">
                                    <input type="text" 
                                           id="cupomCode"
                                           placeholder="Digite o código do cupom" 
                                           class="flex-grow px-4 py-3 bg-gray-900 border border-gray-700 rounded-l-lg text-white placeholder-gray-500 focus:outline-none focus:border-white">
                                    <button type="button" 
                                            id="aplicarCupom"
                                            class="px-6 bg-gray-800 text-white border border-gray-700 border-l-0 rounded-r-lg font-medium hover:bg-gray-700 smooth-transition">
                                        Aplicar
                                    </button>
                                </div>
                                <div id="cupomMessage"></div>
                                <p class="text-gray-400 text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Cupons promocionais podem ser aplicados
                                </p>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Serviços Sugeridos -->
                <div class="mt-16 fade-in-up" style="animation-delay: 0.2s">
                    <h2 class="text-2xl font-bold text-white mb-8">Serviços Recomendados</h2>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Serviço 1 -->
                        <div class="glass-effect rounded-2xl border border-gray-800 p-6 hover-lift smooth-transition">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-black rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-laptop-medical text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Manutenção Preventiva</h3>
                            <p class="text-gray-400 text-sm mb-4">
                                Limpeza, otimização e verificação completa do sistema
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-white">R$ 129,90</p>
                                <button type="button" class="adicionar-carrinho px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 smooth-transition" 
                                        data-nome="Manutenção Preventiva" 
                                        data-preco="129.90" 
                                        data-descricao="Limpeza, otimização e verificação completa do sistema"
                                        data-icon="fa-laptop-medical">
                                    Adicionar
                                </button>
                            </div>
                        </div>

                        <!-- Serviço 2 -->
                        <div class="glass-effect rounded-2xl border border-gray-800 p-6 hover-lift smooth-transition">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-black rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-virus-slash text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Remoção de Vírus</h3>
                            <p class="text-gray-400 text-sm mb-4">
                                Remoção completa de malware, spyware e vírus
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-white">R$ 89,90</p>
                                <button type="button" class="adicionar-carrinho px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 smooth-transition"
                                        data-nome="Remoção de Vírus" 
                                        data-preco="89.90" 
                                        data-descricao="Remoção completa de malware, spyware e vírus"
                                        data-icon="fa-virus-slash">
                                    Adicionar
                                </button>
                            </div>
                        </div>

                        <!-- Serviço 3 -->
                        <div class="glass-effect rounded-2xl border border-gray-800 p-6 hover-lift smooth-transition">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-black rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-hdd text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Backup de Dados</h3>
                            <p class="text-gray-400 text-sm mb-4">
                                Backup completo e seguro de seus arquivos importantes
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-white">R$ 79,90</p>
                                <button type="button" class="adicionar-carrinho px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 smooth-transition"
                                        data-nome="Backup de Dados" 
                                        data-preco="79.90" 
                                        data-descricao="Backup completo e seguro de seus arquivos importantes"
                                        data-icon="fa-hdd">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black pt-16 pb-8 border-t border-gray-800">
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
                        <a href="#" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-gray-800 smooth-transition">
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
                        <li><a href="../index.php" class="text-gray-400 hover:text-white smooth-transition">Início</a></li>
                        <li><a href="servicos.php" class="text-gray-400 hover:text-white smooth-transition">Serviços</a></li>
                        <li><a href="produtos.php" class="text-gray-400 hover:text-white smooth-transition">Produtos</a></li>
                        <li><a href="carrinho.php" class="text-gray-400 hover:text-white smooth-transition">Carrinho</a></li>
                    </ul>
                </div>
                
                <!-- Serviços -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Serviços</h4>
                    <ul class="space-y-3">
                        <li><a href="servicos.php" class="text-gray-400 hover:text-white smooth-transition">Manutenção de Computadores</a></li>
                        <li><a href="servicos.php" class="text-gray-400 hover:text-white smooth-transition">Reparos de Celulares</a></li>
                        <li><a href="servicos.php" class="text-gray-400 hover:text-white smooth-transition">Recuperação de Dados</a></li>
                        <li><a href="servicos.php" class="text-gray-400 hover:text-white smooth-transition">Todos os Serviços</a></li>
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
                        <a href="privacidade.php" class="text-gray-500 text-sm hover:text-gray-400">Política de Privacidade</a>
                        <a href="termos.php" class="text-gray-500 text-sm hover:text-gray-400">Termos de Uso</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos DOM
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        
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
        
        // Adicionar ao carrinho
        const adicionarCarrinhoBtns = document.querySelectorAll('.adicionar-carrinho');
        
        adicionarCarrinhoBtns.forEach(button => {
            button.addEventListener('click', function() {
                const nome = this.getAttribute('data-nome');
                const preco = parseFloat(this.getAttribute('data-preco'));
                const descricao = this.getAttribute('data-descricao');
                const icon = this.getAttribute('data-icon');
                
                // Criar objeto do item
                const item = {
                    name: nome,
                    price: preco,
                    description: descricao,
                    icon: icon
                };
                
                // Adicionar ao carrinho via AJAX
                fetch('adicionar_carrinho.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(item)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensagem de sucesso
                        alert('Item adicionado ao carrinho!');
                        
                        // Atualizar contador do carrinho
                        const cartBadge = document.querySelector('.cart-badge');
                        if (cartBadge) {
                            cartBadge.textContent = data.cart_count;
                        } else {
                            // Criar badge se não existir
                            const cartIcon = document.querySelector('a[href="carrinho.php"]');
                            if (cartIcon) {
                                const badge = document.createElement('span');
                                badge.className = 'cart-badge';
                                badge.textContent = data.cart_count;
                                cartIcon.appendChild(badge);
                            }
                        }
                        
                        // Recarregar a página para mostrar o item
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao adicionar item ao carrinho.');
                });
            });
        });
        
        // Validar cupom
        const aplicarCupomBtn = document.getElementById('aplicarCupom');
        const cupomCodeInput = document.getElementById('cupomCode');
        const cupomMessage = document.getElementById('cupomMessage');
        
        if (aplicarCupomBtn && cupomCodeInput) {
            aplicarCupomBtn.addEventListener('click', function() {
                const cupom = cupomCodeInput.value.trim();
                
                if (!cupom) {
                    showCupomMessage('Por favor, digite um código de cupom.', 'error');
                    return;
                }
                
                // Verificar cupom via API
                fetch('../api/verificar_cupom.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ cupom: cupom })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        showCupomMessage(`Cupom "${cupom}" aplicado com sucesso! Desconto de ${data.desconto}%.`, 'success');
                        aplicarCupomBtn.disabled = true;
                        aplicarCupomBtn.textContent = 'Aplicado';
                    } else {
                        showCupomMessage(data.message || 'Cupom inválido ou expirado.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showCupomMessage('Erro ao verificar cupom. Tente novamente.', 'error');
                });
            });
        }
        
        function showCupomMessage(message, type) {
            if (!cupomMessage) return;
            
            cupomMessage.innerHTML = `
                <div class="mt-3 p-3 rounded-lg ${type === 'success' ? 'bg-green-900/20 border border-green-800' : 'bg-red-900/20 border border-red-800'}">
                    <div class="flex items-center">
                        <i class="fas ${type === 'success' ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400'} mr-3"></i>
                        <span class="${type === 'success' ? 'text-green-300' : 'text-red-300'}">${message}</span>
                    </div>
                </div>
            `;
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
</body>
</html>