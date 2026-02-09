<?php
// index.php - Página inicial da SampTech
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verificar se usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $usuario_logado ? $_SESSION['usuario_nome'] : '';
$usuario_email = $usuario_logado ? $_SESSION['usuario_email'] : '';

// Get user photo from database if logged in
$usuario_foto = '';
if ($usuario_logado && isset($_SESSION['usuario_id'])) {
    try {
        require_once __DIR__ . '/config.php';
        require_once __DIR__ . '/models/usuario.php';
        $usuarioModel = new Usuario();
        $userData = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
        if ($userData && !empty($userData['foto_perfil'])) {
            $usuario_foto = $userData['foto_perfil'];
        }
    } catch (Exception $e) {
        // Silently fail if there's an error
    }
}

// Calcular total do carrinho
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'];
}

// Definir título da página
$titulo_pagina = "SampTech - Assistência Técnica Profissional";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
    
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
        
        /* LED Border Animation */
        .led-border {
            position: relative;
            border: 2px solid transparent;
            background: linear-gradient(var(--black-secondary), var(--black-secondary)) padding-box,
                        conic-gradient(from 0deg, #ffffff, transparent, #ffffff, transparent, #ffffff) border-box;
            animation: ledBorder 4s linear infinite;
        }
        
        @keyframes ledBorder {
            0% { 
                background: linear-gradient(var(--black-secondary), var(--black-secondary)) padding-box,
                            conic-gradient(from 0deg, #ffffff, transparent, #ffffff, transparent, #ffffff) border-box;
                filter: drop-shadow(0 0 8px rgba(255,255,255,0.4));
            }
            100% { 
                background: linear-gradient(var(--black-secondary), var(--black-secondary)) padding-box,
                            conic-gradient(from 360deg, #ffffff, transparent, #ffffff, transparent, #ffffff) border-box;
                filter: drop-shadow(0 0 8px rgba(255,255,255,0.4));
            }
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
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 flex items-center justify-center overflow-hidden smooth-transition group-hover:border-gray-500">
                        <img src="./img/icon.png" alt="SampTech Logo" class="w-full h-full object-cover"/>
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
            
            <!-- Navegação Desktop - SEM login/cadastro aqui -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="#home" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                    Início
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="pages/servicos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                    Serviços
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="./pages/produtos.php" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                    Produtos
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#contact" class="text-gray-300 hover:text-white smooth-transition font-medium relative group">
                    Contato
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transitionall duration-300"></span>
                </a>
            </nav>  
            
            <!-- Botões de ação - APENAS botão para área do cliente -->
            <div class="flex items-center space-x-6">
                <a href="./pages/agendar.php class="hidden md:flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 smooth-transition">
                    <i class="fas fa-calendar-check"></i>
                    <span>Agendar Serviço</span>
                </a>
                
                <!-- Se NÃO estiver logado, mostra botão para Área do Cliente -->
                <?php if (!$usuario_logado): ?>
                    <a href="./pages/cadastro.php" class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                        <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border border-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <span class="hidden md:inline font-medium">Criar Conta</span>
                    </a>
                <?php else: ?>
                    <!-- Se ESTIVER logado, mostra perfil dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-300 hover:text-white smooth-transition">
                            <div class="w-10 h-10 rounded-full border-2 border-white profile-glow flex items-center justify-center overflow-hidden">
                                <?php if (!empty($usuario_foto) && file_exists($usuario_foto)): ?>
                                    <img src="<?php echo $usuario_foto; ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($usuario_nome); ?>&background=1f2937&color=ffffff&size=40" alt="Profile" class="w-full h-full rounded-full object-cover">
                                <?php endif; ?>
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
                                <a href="./pages/minha-conta.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                    <i class="fas fa-user-circle w-5"></i>
                                    <span>Minha Conta</span>
                                </a>
                                <a href="./pages/meusServicos.php" class="flex items-center space-x-3 px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg smooth-transition">
                                    <i class="fas fa-laptop-medical w-5"></i>
                                    <span>Meus Serviços</span>
                                </a>
                            </div>
                            <div class="p-2 border-t border-gray-800">
                                <a href="logout.php" class="flex items-center space-x-3 px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg smooth-transition">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Sair</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
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
                <a href="#home" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Início</a>
                <a href="#services" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Serviços</a>
                <a href="./pages/produtos.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Produtos</a>
                <a href="#contact" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Contato</a>
                
                <?php if ($usuario_logado): ?>
                    <a href="./pages/minha-conta.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Minha Conta</a>
                    <a href="logout.php" class="text-2xl font-medium text-red-400 hover:text-red-300 smooth-transition">Sair</a>
                <?php else: ?>
                    <a href="cadastro.php" class="text-2xl font-medium text-white smooth-transition">Cadastrar</a>
                    <a href="login.php" class="text-2xl font-medium text-gray-300 hover:text-white smooth-transition">Login</a>
                <?php endif; ?>
            </nav>
            
            <div class="p-6 border-t border-gray-800">
                <a href="./pages/contato.php" class="block w-full bg-white text-black py-4 rounded-lg font-semibold hover:bg-gray-200 smooth-transition mb-4 text-center">
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

<!-- Hero Section -->
<section id="home" class="relative pt-20 pb-20 md:pt-28 md:pb-28 overflow-hidden">
    <!-- Background com imagem -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
             alt="Tecnologia e manutenção" 
             class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-black/90 to-black"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Conteúdo -->
            <div class="fade-in-up">
                <span class="inline-flex items-center px-4 py-2 bg-white text-black rounded-full text-sm font-bold mb-6">
                    <i class="fas fa-bolt mr-2"></i>
                    SERVIÇOS TÉCNICOS ESPECIALIZADOS
                </span>
                
                <h1 class="text-5xl md:text-7xl font-black leading-tight mb-6">
                    <span class="text-white">Assistência</span>
                    <span class="block text-gray-300 mt-2">Técnica</span>
                    <span class="block text-white mt-2">de Confiança</span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-10 max-w-2xl">
                    Resolvemos seus problemas tecnológicos com <span class="text-white font-semibold">agilidade, transparência e qualidade</span>. Da manutenção de computadores ao reparo de celulares, estamos aqui para tornar a tecnologia sua aliada.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#services" class="inline-flex items-center justify-center bg-white text-black px-8 py-4 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-lg">
                        <i class="fas fa-laptop-code mr-3"></i>
                        Ver Serviços
                    </a>
                    
                    <?php if (!$usuario_logado): ?>
                        <a href="./pages/cadastro.php" class="inline-flex items-center justify-center border-2 border-gray-600 text-white px-8 py-4 text-lg font-semibold hover:border-white smooth-transition rounded-lg">
                            <i class="fas fa-user-plus mr-3"></i>
                            Criar Conta Gratuita
                        </a>
                    <?php else: ?>
                        <a href="./pages/minha-conta.php" class="inline-flex items-center justify-center border-2 border-gray-600 text-white px-8 py-4 text-lg font-semibold hover:border-white smooth-transition rounded-lg">
                            <i class="fas fa-user-circle mr-3"></i>
                            Minha Conta
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Destaques -->
                <div class="grid grid-cols-3 gap-6 mt-12">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">100%</div>
                        <div class="text-gray-400 text-sm">Transparência</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">24h</div>
                        <div class="text-gray-400 text-sm">Resposta Rápida</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">30d</div>
                        <div class="text-gray-400 text-sm">Garantia</div>
                    </div>
                </div>
            </div>
            
            <!-- Imagem Hero -->
            <div class="relative fade-in-up" style="animation-delay: 0.2s;">
                <div class="relative rounded-2xl overflow-hidden border border-gray-800">
                    <img src="./img/loja.png" 
                         alt="SampTech Assistência Técnica" 
                         class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    
                    <!-- Chamada para ação sobre a imagem -->
                    <div class="absolute bottom-6 left-6 right-6">
                        <div class="glass-effect rounded-xl p-6">
                            <h3 class="text-xl font-bold text-white mb-2">Precisa de ajuda?</h3>
                            <p class="text-gray-300 mb-4">Entre em contato agora mesmo!</p>
                            <a href="#contact" class="inline-flex items-center bg-white text-black px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 smooth-transition">
                                <i class="fas fa-phone-alt mr-2"></i>
                                Falar com Especialista
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Elementos flutuantes -->
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-gradient-to-br from-white to-gray-300 rounded-full flex items-center justify-center float-animation opacity-10">
                    <i class="fas fa-cogs text-4xl text-black"></i>
                </div>
                
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-gradient-to-br from-gray-800 to-black rounded-full flex items-center justify-center float-animation opacity-20">
                    <i class="fas fa-microchip text-5xl text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Serviços -->
<section id="services" class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6 text-white">
                Nossos <span class="text-gray-300">Serviços</span>
            </h2>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                Oferecemos soluções completas para todos os seus problemas tecnológicos
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Serviço 1 -->
            <div class="glass-effect rounded-2xl p-8 hover-lift smooth-transition service-card fade-in-up">
                <div class="w-16 h-16 bg-gradient-to-br from-white to-gray-300 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-desktop text-2xl text-black"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Manutenção de Computadores</h3>
                <p class="text-gray-300 mb-6">
                    Formatação, limpeza, otimização e reparo completo de desktops e notebooks.
                </p>
                <a href="#contact" class="inline-flex items-center text-white font-semibold">
                    Solicitar Orçamento
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <!-- Serviço 2 -->
            <div class="glass-effect rounded-2xl p-8 hover-lift smooth-transition service-card fade-in-up" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-gradient-to-br from-white to-gray-300 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-mobile-alt text-2xl text-black"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Reparos de Celulares</h3>
                <p class="text-gray-300 mb-6">
                    Troca de telas, baterias, conectores e reparos em todos os modelos.
                </p>
                <a href="#contact" class="inline-flex items-center text-white font-semibold">
                    Solicitar Orçamento
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <!-- Serviço 3 -->
            <div class="glass-effect rounded-2xl p-8 hover-lift smooth-transition service-card fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-gradient-to-br from-white to-gray-300 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-arrow-up text-2xl text-black"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Upgrades de Performance</h3>
                <p class="text-gray-300 mb-6">
                    Instalação de SSDs, aumento de memória RAM e melhorias de hardware.
                </p>
                <a href="#contact" class="inline-flex items-center text-white font-semibold">
                    Solicitar Orçamento
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Botão para ver todos os serviços -->
        <div class="text-center mt-12 fade-in-up" style="animation-delay: 0.3s;">
            <a href="pages/servicos.php" class="inline-flex items-center border-2 border-gray-700 text-white px-8 py-4 text-lg font-semibold hover:border-white smooth-transition rounded-lg">
                <i class="fas fa-list mr-3"></i>
                Ver Todos os Serviços
            </a>
        </div>
    </div>
</section>

<!-- Seção de Processo -->
<section id="process" class="py-20 bg-gradient-to-b from-black to-gray-900">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6 text-white">
                Como <span class="text-gray-300">Funciona</span>
            </h2>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                Um processo simples e transparente para resolver seus problemas
            </p>
        </div>
        
        <div class="max-w-6xl mx-auto">
            <!-- Etapa 1 -->
            <div class="process-step fade-in-up">
                <div class="glass-effect rounded-xl p-8 hover-lift smooth-transition led-border">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">1. Contato Inicial</h3>
                            <p class="text-gray-300 mb-4">
                                Você entra em contato via WhatsApp, telefone ou preenche nosso formulário online.
                            </p>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Resposta em até 15 minutos</span>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fimg.freepik.com%2Fpremium-photo%2Fview-professional-handshake-business-people_23-2150917018.jpg&f=1&nofb=1&ipt=da709a5da67efccb6b09d099b711b442f85b9b85a6f85df915e0cb22378eb142" alt="Contato Inicial" class="w-64 h-48 object-cover rounded-lg border-2 border-gray-700">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Etapa 2 -->
            <div class="process-step fade-in-up">
                <div class="glass-effect rounded-xl p-8 hover-lift smooth-transition led-border">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">2. Diagnóstico e Orçamento</h3>
                            <p class="text-gray-300 mb-4">
                                Analisamos o problema e apresentamos um orçamento detalhado e transparente.
                            </p>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                <span>Orçamento 100% transparente</span>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fhypescience.com%2Fwp-content%2Fuploads%2F2013%2F02%2Fdiagnose.jpg&f=1&nofb=1&ipt=521e4ac4800e2ffabe918556195504937d2dbb57324a0b777164404de0b27a0f" alt="Diagnóstico" class="w-64 h-48 object-cover rounded-lg border-2 border-gray-700">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Etapa 3 -->
            <div class="process-step fade-in-up">
                <div class="glass-effect rounded-xl p-8 hover-lift smooth-transition led-border">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">3. Execução do Serviço</h3>
                            <p class="text-gray-300 mb-4">
                                Com sua aprovação, executamos o serviço com as melhores práticas do mercado.
                            </p>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-user-check mr-2"></i>
                                <span>Execução com aprovação prévia</span>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fwww.idealprintcartuchos.com.br%2Fimages%2Fconserto-de-computadores.png&f=1&nofb=1&ipt=9dbca3c40b581689be351f0a39a1b86ae3b89ff03bab1b8cf8168ac7be24d948" alt="Execução do Serviço" class="w-64 h-48 object-cover rounded-lg border-2 border-gray-700">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Etapa 4 -->
            <div class="process-step fade-in-up">
                <div class="glass-effect rounded-xl p-8 hover-lift smooth-transition led-border">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">4. Entrega e Garantia</h3>
                            <p class="text-gray-300 mb-4">
                                Entregamos seu equipamento funcionando perfeitamente com garantia de 30 dias.
                            </p>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>30 dias de garantia</span>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fmercadoeconsumo.com.br%2Fwp-content%2Fuploads%2F2017%2F11%2Fbigstock-Delivery-Concept-Smiling-Hap-180717058.jpg&f=1&nofb=1&ipt=6cdcfc53402d021e7c495d5fc6da7d8f474ff1f1ebecb50086fb41a8ea20d8a0" alt="Entrega e Garantia" class="w-64 h-48 object-cover rounded-lg border-2 border-gray-700">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção CTA - Call to Action -->
<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="glass-effect rounded-2xl p-12 text-center fade-in-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6 text-white">
                Pronto para resolver seus problemas?
            </h2>
            <p class="text-xl text-gray-300 mb-10 max-w-3xl mx-auto">
                Cadastre-se gratuitamente para agendar serviços, acompanhar orçamentos e receber ofertas especiais.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if (!$usuario_logado): ?>
                    <a href="../pages/cadastro.php" class="inline-flex items-center justify-center bg-white text-black px-8 py-4 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-lg">
                        <i class="fas fa-user-plus mr-3"></i>
                        Criar Minha Conta
                    </a>
                    <a href="#contact" class="inline-flex items-center justify-center border-2 border-gray-600 text-white px-8 py-4 text-lg font-semibold hover:border-white smooth-transition rounded-lg">
                        <i class="fas fa-phone-alt mr-3"></i>
                        Falar com Especialista
                    </a>
                <?php else: ?>
                    <a href="./pages/agendar.php" class="inline-flex items-center justify-center bg-white text-black px-8 py-4 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-lg">
                        <i class="fas fa-laptop-medical mr-3"></i>
                        Agendar Novo Serviço
                    </a>
                    <a href="./pages/servicos.php" class="inline-flex items-center justify-center border-2 border-gray-600 text-white px-8 py-4 text-lg font-semibold hover:border-white smooth-transition rounded-lg">
                        <i class="fas fa-list mr-3"></i>
                        Ver Todos os Serviços
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Contador de clientes atendidos -->
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">+5</div>
                        <div class="text-gray-400">Clientes Atendidos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">+1</div>
                        <div class="text-gray-400">Serviços Realizados</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">98%</div>
                        <div class="text-gray-400">Satisfação</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">24h</div>
                        <div class="text-gray-400">Suporte</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Contato -->
<section id="contact" class="py-20 bg-gradient-to-b from-gray-900 to-black">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6 text-white">
                Entre em <span class="text-gray-300">Contato</span>
            </h2>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                Estamos prontos para atender suas necessidades
            </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Informações de Contato -->
            <div class="fade-in-up">
                <div class="glass-effect rounded-2xl p-8">
                    <h3 class="text-2xl font-bold mb-6 text-white">Informações de Contato</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-black"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">Telefone/WhatsApp</h4>
                                <p class="text-gray-300">(64) 9 9280-0407</p>
                                <a href="https://wa.me/5564992800407" class="text-sm text-green-400 hover:text-green-300 smooth-transition">
                                    <i class="fab fa-whatsapp mr-1"></i> Chamar no WhatsApp
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-black"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">E-mail</h4>
                                <p class="text-gray-300">joaovsampaio.dev@gmail.com</p>
                                <a href="mailto:joaovsampaio.dev@gmail.com" class="text-sm text-white hover:text-gray-300 smooth-transition">
                                    <i class="fas fa-paper-plane mr-1"></i> Enviar E-mail
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-black"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white mb-1">Localização</h4>
                                <p class="text-gray-300">Rua 9 Quadra 2 Lote 19</p>
                                <p class="text-gray-300">Conjunto Morada do Sol</p>
                                <p class="text-gray-400 text-sm">Rio Verde - GO</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Horário de funcionamento -->
                    <div class="mt-8 pt-8 border-t border-gray-800">
                        <h4 class="font-bold text-white mb-4">Horário de Funcionamento</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-300">Segunda - Sexta</p>
                                <p class="text-gray-400">8:00 - 18:00</p>
                            </div>
                            <div>
                                <p class="text-gray-300">Sábado</p>
                                <p class="text-gray-400">8:00 - 12:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulário de Contato -->
            <div class="fade-in-up" style="animation-delay: 0.2s;">
                <div class="glass-effect rounded-2xl p-8">
                    <h3 class="text-2xl font-bold mb-6 text-white">Solicitar Orçamento</h3>
                    
                    <form class="space-y-6">
                        <div>
                            <label class="block text-gray-300 mb-2">Nome Completo *</label>
                            <input type="text" 
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-lg focus:outline-none focus:border-white" 
                                   placeholder="Seu nome"
                                   required>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-300 mb-2">Telefone *</label>
                                <input type="tel" 
                                       id="telefone"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-lg focus:outline-none focus:border-white" 
                                       placeholder="(64) 9 9999-9999"
                                       maxlength="15"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-gray-300 mb-2">E-mail *</label>
                                <input type="email" 
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-lg focus:outline-none focus:border-white" 
                                       placeholder="seu@email.com"
                                       required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 mb-2">Tipo de Equipamento *</label>
                            <select class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-lg focus:outline-none focus:border-white">
                                <option value="">Selecione o equipamento</option>
                                <option value="Notebook">Notebook</option>
                                <option value="Computador Desktop">Computador Desktop</option>
                                <option value="Celular/Smartphone">Celular/Smartphone</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 mb-2">Descrição do Problema *</label>
                            <textarea class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-lg focus:outline-none focus:border-white h-32" 
                                      placeholder="Descreva o problema que está enfrentando..."
                                      required></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-white text-black py-4 rounded-lg font-bold hover:bg-gray-200 smooth-transition flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Enviar Solicitação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<?php require_once './footer.php' ?>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Phone number formatting
        const telefoneInput = document.getElementById('telefone');
        if (telefoneInput) {
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value.length <= 11) {
                    if (value.length <= 2) {
                        value = value.replace(/(\d{0,2})/, '($1');
                    } else if (value.length <= 6) {
                        value = value.replace(/(\d{2})(\d{0,4})/, '($1) $2');
                    } else if (value.length <= 10) {
                        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                    }
                }
                
                e.target.value = value;
            });
        }
        
        // Elementos DOM
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        
        // Menu Mobile
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
        
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            document.body.style.overflow = 'auto';
        });
        
        // Fechar menu ao clicar em link
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('translate-x-full');
                document.body.style.overflow = 'auto';
            });
        });
        
        // Carrinho
        cartButton.addEventListener('click', () => {
            cartSidebar.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
        
        closeCart.addEventListener('click', () => {
            cartSidebar.classList.add('translate-x-full');
            document.body.style.overflow = 'auto';
        });
        
        // Scroll suave para links âncora
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') return;
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Fechar menu mobile se aberto
                    if (!mobileMenu.classList.contains('translate-x-full')) {
                        mobileMenu.classList.add('translate-x-full');
                        document.body.style.overflow = 'auto';
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
        fadeElements.forEach(element => {
            element.style.opacity = "0";
            element.style.transform = "translateY(30px)";
            element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        });
        
        // Verificar ao carregar e ao scroll
        window.addEventListener('scroll', checkFade);
        checkFade();
    });
</script>

</body>
</html>
