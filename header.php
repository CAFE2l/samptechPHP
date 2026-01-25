<?php
require_once '../config/session.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verificar se usuário está logado (versão sem banco de dados)
$usuario_logado = isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$usuario_nome = $usuario_logado ? $_SESSION['usuario_nome'] : '';
$usuario_email = $usuario_logado ? $_SESSION['usuario_email'] : '';

// Get user photo from database if logged in
$usuario_foto = '';
if ($usuario_logado && isset($_SESSION['usuario_id'])) {
    // First try to get from session
    if (isset($_SESSION['usuario_foto']) && !empty($_SESSION['usuario_foto'])) {
        $usuario_foto = $_SESSION['usuario_foto'];
    } else {
        // Fallback to database
        try {
            require_once __DIR__ . '/config/database.php';
            require_once __DIR__ . '/models/usuario.php';
            $usuarioModel = new Usuario();
            $userData = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
            if ($userData && !empty($userData['foto_perfil'])) {
                $usuario_foto = $userData['foto_perfil'];
                $_SESSION['usuario_foto'] = $usuario_foto; // Store in session for next time
            }
        } catch (Exception $e) {
            // Silently fail if there's an error
        }
    }
}

// Calcular total do carrinho
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'];
    $cart_count++;
}


// Definir título da página se não estiver definido
if (!isset($titulo_pagina)) {
    $titulo_pagina = "SampTech - Assistência Técnica Profissional";
}

// Determinar página atual para menu ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="../img/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Efeitos de vidro */
        .glass-effect {
            background: rgba(17, 17, 17, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--black-secondary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-medium);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-light);
        }
        
        /* Transições suaves */
        .smooth-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Hover effects */
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
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
        
        /* Process steps */
        .process-step {
            position: relative;
            padding-left: 40px;
            margin-bottom: 60px;
        }
        
        .process-step:last-child {
            margin-bottom: 0;
        }
        
        .process-step::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 32px;
            height: 32px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--black-primary);
            font-weight: 900;
            font-size: 14px;
        }
        
        .process-step:nth-child(1)::before {
            content: '1';
        }
        
        .process-step:nth-child(2)::before {
            content: '2';
        }
        
        .process-step:nth-child(3)::before {
            content: '3';
        }
        
        .process-step:nth-child(4)::before {
            content: '4';
        }
        
        .process-step::after {
            content: '';
            position: absolute;
            left: 15px;
            top: 32px;
            bottom: -60px;
            width: 2px;
            background: var(--gray-medium);
        }
        
        .process-step:last-child::after {
            display: none;
        }
        
        /* Garantir padding para o conteúdo principal */
        .main-content {
            padding-top: 80px;
        }
/* Efeitos para produtos */
.product-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    border-color: rgba(255, 255, 255, 0.2);
}

/* Modal */
#productModal {
    backdrop-filter: blur(10px);
}

/* Animações de entrada */
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

/* Hover effects para botões de produto */
.bg-white:hover {
    transform: translateY(-2px);
}

/* Estrelas de avaliação */
.fa-star {
    font-size: 0.9em;
}

/* Badges */
.bg-red-500, .bg-green-500, .bg-yellow-500 {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

/* Profile LED border animation */
@keyframes profileGlow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.3), 0 0 10px rgba(255, 255, 255, 0.2), 0 0 15px rgba(255, 255, 255, 0.1);
    }
    50% {
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.6), 0 0 20px rgba(255, 255, 255, 0.4), 0 0 30px rgba(255, 255, 255, 0.2);
    }
}

.profile-glow {
    animation: profileGlow 2s ease-in-out infinite;
}

    </style>
</head>
<body class="bg-black text-white">

<!-- Header Fixo -->
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
            
            <!-- Navegação Desktop -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="../index.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group <?php echo ($pagina_atual == 'index.php') ? 'text-white' : ''; ?>">
                    Início
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300 <?php echo ($pagina_atual == 'index.php') ? 'w-full' : ''; ?>"></span>
                </a>
                <a href="servicos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group <?php echo ($pagina_atual == 'servicos.php') ? 'text-white' : ''; ?>">
                    Serviços
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300 <?php echo ($pagina_atual == 'servicos.php') ? 'w-full' : ''; ?>"></span>
                </a>
                <?php if ($pagina_atual == 'index.php'): ?>
                    <a href="produtos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                        Produtos
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </a>
                   
                <?php else: ?>
                   <a href="produtos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                        Produtos
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </a>
                 
                <?php endif; ?>
            </nav>
            
            <!-- Botões de ação -->
            <div class="flex items-center space-x-6">
                <?php if ($pagina_atual == 'index.php'): ?>
                    <a href="../pages/agendar.php" class="hidden md:flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 smooth-transition">
                        <i class="fas fa-calendar-check"></i>
                        <span>Agendar Serviço</span>
                    </a>
                <?php else: ?>
                    <a href="../pages/agendar.php" class="hidden md:flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 smooth-transition">
                        <i class="fas fa-calendar-check"></i>
                        <span>Agendar Serviço</span>
                    </a>
                <?php endif; ?>
                
                <!-- Área do Cliente / Perfil -->
                <?php if (!$usuario_logado): ?>
                    <a href="cadastro.php" class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                        <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border border-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <span class="hidden md:inline font-medium">Área do Cliente</span>
                    </a>
                <?php else: ?>
                    <!-- Perfil dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                            <div class="w-10 h-10 rounded-full border-2 border-white profile-glow flex items-center justify-center overflow-hidden">
                                <?php 
                                // Debug: Check if photo exists and path
                                if (!empty($usuario_foto)) {
                                    // Try different path combinations
                                    $photo_paths = [
                                        $usuario_foto,
                                        '../' . $usuario_foto,
                                        './' . $usuario_foto
                                    ];
                                    
                                    $photo_found = false;
                                    foreach ($photo_paths as $path) {
                                        if (file_exists($path)) {
                                            echo '<img src="' . $path . '" alt="Profile" class="w-full h-full rounded-full object-cover">';
                                            $photo_found = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$photo_found) {
                                        echo '<img src="https://ui-avatars.com/api/?name=' . urlencode($usuario_nome) . '&background=1f2937&color=ffffff&size=40" alt="Profile" class="w-full h-full rounded-full object-cover">';
                                    }
                                } else {
                                    echo '<img src="https://ui-avatars.com/api/?name=' . urlencode($usuario_nome) . '&background=1f2937&color=ffffff&size=40" alt="Profile" class="w-full h-full rounded-full object-cover">';
                                }
                                ?>
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
                                <a href="meusPedidos.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                    <i class="fas fa-shopping-bag w-5"></i>
                                    <span>Meus Pedidos</span>
                                </a>
                                <a href="meusServicos.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                    <i class="fas fa-laptop-medical w-5"></i>
                                    <span>Meus Serviços</span>
                                </a>
                            </div>
                            <div class="p-2 border-t border-gray-800">
                               <a href="../logout.php" class="dropdown-link dropdown-link-danger">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Sair</span>
                            </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Carrinho -->
                <button class="relative group" id="cartButton">
                    <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border border-gray-700 flex items-center justify-center hover:border-white transition-all duration-300">
                        <i class="fas fa-shopping-cart text-lg text-gray-300 group-hover:text-white transition-colors"></i>
                    </div>
                    <?php if ($cart_count > 0): ?>
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-white text-black rounded-full text-xs font-bold flex items-center justify-center animate-pulse"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </button>
                
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
                <a href="index.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition <?php echo ($pagina_atual == 'index.php') ? 'text-white' : ''; ?>">Início</a>
                <a href="servicos.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition <?php echo ($pagina_atual == 'servicos.php') ? 'text-white' : ''; ?>">Serviços</a>
                <?php if ($pagina_atual == 'index.php'): ?>
                     <a href="produtos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                        Produtos
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </a>
                   
                <?php else: ?>
                    <a href="index.php#process" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Processo</a>
                  
                <?php endif; ?>
                
                <?php if ($usuario_logado): ?>
                    <div class="border-t border-gray-800 pt-4"></div>
                    <a href="minha-conta.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Minha Conta</a>
                    <a href="../logout.php" class="text-2xl font-medium text-red-400 hover:text-red-300 smooth-transition">Sair</a>
                <?php else: ?>
                    <div class="border-t border-gray-800 pt-4"></div>
                    <a href="cadastro.php" class="text-2xl font-medium text-white smooth-transition">Cadastrar</a>
                    <a href="login.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Login</a>
                <?php endif; ?>
            </nav>
            
            <div class="p-6 border-t border-gray-800">
                <?php if ($pagina_atual == 'index.php'): ?>
                    <a href="./agendar.php" class="block w-full bg-white text-black py-4 rounded-lg font-semibold hover:bg-gray-200 smooth-transition mb-4 text-center">
                        Agendar Serviço
                    </a>
                <?php else: ?>
                    <a href="./agendar.php" class="block w-full bg-white text-black py-4 rounded-lg font-semibold hover:bg-gray-200 smooth-transition mb-4 text-center">
                        Agendar Serviço
                    </a>
                <?php endif; ?>
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