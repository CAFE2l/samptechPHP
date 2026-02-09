<?php
session_start();
require_once '../config.php';

// Get services from database
try {
    $stmt = $pdo->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
    $servicos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $servicos_db = [];
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Adicionar item ao carrinho
if (isset($_POST['add_to_cart'])) {
    $service = [
        'id' => $_POST['service_id'],
        'name' => $_POST['service_name'],
        'price' => $_POST['service_price'],
        'description' => $_POST['service_description'],
        'category' => $_POST['service_category']
    ];
    $_SESSION['cart'][] = $service;
    $cart_message = "Serviço adicionado ao carrinho!";
}

// Calcular total do carrinho
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'];
}

// Serviços da SampTech (do plano de negócios)
$services = [
    'manutencao' => [
        'name' => 'Manutenção de Computadores',
        'description' => 'Serviços completos de manutenção preventiva e corretiva para seu computador ou notebook.',
        'price' => 120.00,
        'features' => [
            'Diagnóstico completo de hardware e software',
            'Formatação e reinstalação de sistema',
            'Otimização e limpeza de sistema',
            'Remoção de vírus e malwares',
            'Manutenção preventiva (limpeza física)'
        ],
        'icon' => 'fas fa-desktop'
    ],
    'reparos' => [
        'name' => 'Reparos de Hardware',
        'description' => 'Conserto e substituição de componentes danificados com peças de qualidade.',
        'price' => 80.00,
        'features' => [
            'Troca de telas, teclados e baterias',
            'Reparo de conectores de carga',
            'Substituição de componentes danificados',
            'Conserto de placas-mãe',
            'Reparo de fontes de alimentação'
        ],
        'icon' => 'fas fa-tools'
    ],
    'upgrades' => [
        'name' => 'Upgrades de Performance',
        'description' => 'Melhore o desempenho do seu equipamento com upgrades inteligentes.',
        'price' => 150.00,
        'features' => [
            'Substituição de HD por SSD',
            'Aumento de memória RAM',
            'Instalação de placas de vídeo',
            'Atualização de processador',
            'Instalação de coolers'
        ],
        'icon' => 'fas fa-arrow-up'
    ],
    'montagem' => [
        'name' => 'Montagem de PCs Personalizados',
        'description' => 'PCs montados sob medida para suas necessidades específicas.',
        'price' => 200.00,
        'features' => [
            'Consultoria para escolha de componentes',
            'Montagem completa do PC',
            'Instalação e configuração do sistema',
            'Testes de performance',
            'Garantia na montagem'
        ],
        'icon' => 'fas fa-computer'
    ],
    'celulares' => [
        'name' => 'Reparos de Celulares',
        'description' => 'Assistência técnica especializada para dispositivos móveis.',
        'price' => 100.00,
        'features' => [
            'Troca de telas e películas',
            'Substituição de baterias',
            'Reparo de conectores',
            'Conserto de câmeras',
            'Desbloqueio e formatação'
        ],
        'icon' => 'fas fa-mobile-alt'
    ],
    'limpeza' => [
        'name' => 'Limpeza Profissional',
        'description' => 'Limpeza completa interna e externa para melhorar performance.',
        'price' => 70.00,
        'features' => [
            'Desmontagem completa do equipamento',
            'Limpeza interna de componentes',
            'Limpeza de ventoinhas',
            'Troca de pasta térmica',
            'Organização de cabos interna'
        ],
        'icon' => 'fas fa-broom'
    ],
    'backup' => [
        'name' => 'Backup e Recuperação',
        'description' => 'Proteja seus dados importantes com nossos serviços de backup.',
        'price' => 90.00,
        'features' => [
            'Backup completo de dados',
            'Recuperação de arquivos deletados',
            'Migração para novo equipamento',
            'Configuração de backup automático',
            'Armazenamento seguro temporário'
        ],
        'icon' => 'fas fa-hdd'
    ],
    'consultoria' => [
        'name' => 'Consultoria Técnica',
        'description' => 'Orientações especializadas para compras e configurações.',
        'price' => 50.00,
        'features' => [
            'Análise de necessidades técnicas',
            'Recomendações de compra',
            'Configuração de redes',
            'Otimização para trabalho/estudo',
            'Solução de problemas complexos'
        ],
        'icon' => 'fas fa-user-tie'
    ]
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - SampTech Assistência Técnica</title>
    <link rel="shortcut icon" href="../../public/assets/images/favicon.png" type="image/x-icon">
    
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
        
        /* Hover effects para cards de serviço */
        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--gray-dark);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            border-color: var(--gray-light);
        }
        
        /* Botão de adicionar ao carrinho */
        .add-to-cart-btn {
            position: relative;
            overflow: hidden;
        }
        
        .add-to-cart-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        .add-to-cart-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(40, 40);
                opacity: 0;
            }
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
        
        /* Filtro de categorias */
        .category-filter-btn {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .category-filter-btn.active {
            background: var(--white);
            color: var(--black-primary);
            border-color: var(--white);
        }
        
        .category-filter-btn:not(.active):hover {
            border-color: var(--gray-light);
        }
        
        /* Cards com bordas elegantes */
        .elegant-border {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .elegant-border::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 12px;
            padding: 1px;
            background: linear-gradient(45deg, var(--gray-medium), var(--white), var(--gray-medium));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        
        /* Progresso da barra de filtro */
        .price-range {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 5px;
            background: var(--gray-dark);
            outline: none;
        }
        
        .price-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--white);
            cursor: pointer;
        }
        
        /* Modal de detalhes do serviço */
        .service-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .service-modal.active {
            display: flex;
            opacity: 1;
        }
        
        .modal-content {
            background: var(--black-secondary);
            border-radius: 16px;
            padding: 40px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(30px);
            transition: transform 0.3s ease;
        }
        
        .service-modal.active .modal-content {
            transform: translateY(0);
        }
        
        /* Tooltips */
        .tooltip {
            position: relative;
        }
        
        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gray-dark);
            color: var(--white);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-8px);
        }
    </style>
</head>
<body class="bg-black text-white overflow-x-hidden">
    
    <!-- Cabeçalho / Navegação -->
   <?php include '../header.php'?>

    <!-- Hero Section Serviços -->
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-black via-gray-900 to-black opacity-90"></div>
            <img src="../img/wallpaper.png" 
                 alt="Serviços técnicos" 
                 class="w-full h-full object-cover opacity-30">
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center fade-in-up">
                <span class="inline-flex items-center px-4 py-2 bg-white text-black rounded-full text-sm font-bold mb-6">
                    <i class="fas fa-laptop-code mr-2"></i>
                    PORTFÓLIO COMPLETO DE SERVIÇOS
                </span>
                
                <h1 class="text-5xl md:text-7xl font-black leading-tight mb-6">
                    <span class="text-white">Serviços</span>
                    <span class="block text-gray-300 mt-2">Técnicos</span>
                    <span class="block text-white mt-2">Especializados</span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-10 max-w-3xl mx-auto">
                    Conheça todos os serviços que oferecemos com <span class="text-white font-semibold">transparência, qualidade e agilidade</span>. Cada serviço é executado por profissionais qualificados com as melhores práticas do mercado.
                </p>
                
                <!-- Filtro de categorias -->
                <div class="flex flex-wrap justify-center gap-3 mb-12 fade-in-up" style="animation-delay: 0.2s;">
                    <button class="category-filter-btn active px-6 py-2 bg-white text-black rounded-lg font-medium" data-filter="all">
                        Todos os Serviços
                    </button>
                    <button class="category-filter-btn px-6 py-2 bg-gray-900 text-gray-300 rounded-lg font-medium border border-gray-800" data-filter="manutencao">
                        Manutenção
                    </button>
                    <button class="category-filter-btn px-6 py-2 bg-gray-900 text-gray-300 rounded-lg font-medium border border-gray-800" data-filter="reparos">
                        Reparos
                    </button>
                    <button class="category-filter-btn px-6 py-2 bg-gray-900 text-gray-300 rounded-lg font-medium border border-gray-800" data-filter="upgrades">
                        Upgrades
                    </button>
                    <button class="category-filter-btn px-6 py-2 bg-gray-900 text-gray-300 rounded-lg font-medium border border-gray-800" data-filter="celulares">
                        Celulares
                    </button>
                </div>
                
                <!-- Contador de serviços -->
                <div class="flex justify-center space-x-12 text-center fade-in-up" style="animation-delay: 0.3s;">
                    <div>
                        <div class="text-4xl font-black text-white mb-2"><?php echo count($servicos_db); ?></div>
                        <div class="text-gray-400">Serviços Disponíveis</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-white mb-2">30</div>
                        <div class="text-gray-400">Dias de Garantia</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-white mb-2">24h</div>
                        <div class="text-gray-400">Resposta Rápida</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid de Serviços -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-4">
            <!-- Mensagem de carrinho -->
            <?php if (isset($cart_message)): ?>
            <div id="cartMessage" class="max-w-4xl mx-auto mb-8 p-4 bg-green-900/30 text-green-400 rounded-lg border border-green-800 flex items-center justify-between fade-in-up">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span><?php echo $cart_message; ?></span>
                </div>
                <button onclick="document.getElementById('cartMessage').remove()" class="text-green-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Grid de serviços -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                $delay = 0.1;
                foreach ($servicos_db as $servico): 
                    $icon_map = [
                        'Manutenção' => 'fas fa-desktop',
                        'Hardware' => 'fas fa-tools',
                        'Software' => 'fas fa-laptop-code',
                        'Reparo' => 'fas fa-wrench',
                        'Montagem' => 'fas fa-computer'
                    ];
                    $icon = $icon_map[$servico['categoria']] ?? 'fas fa-tools';
                ?>
                <div class="service-card bg-gradient-to-b from-gray-900 to-black rounded-2xl p-6 fade-in-up" 
                     data-category="<?php echo strtolower($servico['categoria']); ?>"
                     style="animation-delay: <?php echo $delay; ?>s">
                    <div class="flex flex-col h-full">
                        <!-- Ícone e título -->
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mr-4">
                                <i class="<?php echo $icon; ?> text-2xl text-gray-300"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($servico['nome']); ?></h3>
                                <div class="text-2xl font-black text-white">
                                    R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descrição -->
                        <p class="text-gray-300 mb-6 flex-grow">
                            <?php echo htmlspecialchars($servico['descricao']); ?>
                        </p>
                        
                        <!-- Info adicional -->
                        <div class="mb-6 space-y-2">
                            <div class="flex items-center text-gray-400 text-sm">
                                <i class="fas fa-clock mr-2"></i>
                                <span><?php echo htmlspecialchars($servico['tempo_estimado']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-400 text-sm">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>Garantia: <?php echo htmlspecialchars($servico['garantia']); ?></span>
                            </div>
                        </div>
                        
                        <!-- Botão -->
                        <div class="mt-auto">
                            <a href="agendar.php?servico_id=<?php echo $servico['id']; ?>" 
                               class="add-to-cart-btn w-full bg-white text-black py-3 rounded-lg font-bold hover:bg-gray-200 smooth-transition flex items-center justify-center">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Agendar Serviço
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                $delay += 0.1;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

  

    <!-- Seção de FAQ -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12">
                <!-- FAQ -->
                <div class="fade-in-up">
                    <h2 class="text-4xl font-black mb-8 text-white">
                        Perguntas <span class="text-gray-300">Frequentes</span>
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="glass-effect rounded-xl p-6">
                            <button class="w-full text-left flex justify-between items-center faq-toggle">
                                <span class="font-semibold text-white">Quanto tempo leva um serviço?</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div class="faq-content mt-4 hidden">
                                <p class="text-gray-300">
                                    Depende do serviço. Manutenções básicas levam 1-2 horas, formatações 2-3 horas, e reparos com peças podem levar 1-2 dias úteis dependendo da disponibilidade das peças.
                                </p>
                            </div>
                        </div>
                        
                        <div class="glass-effect rounded-xl p-6">
                            <button class="w-full text-left flex justify-between items-center faq-toggle">
                                <span class="font-semibold text-white">Vocês dão garantia?</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div class="faq-content mt-4 hidden">
                                <p class="text-gray-300">
                                    Sim! Todos os serviços têm 30 dias de garantia.
                                </p>
                            </div>
                        </div>
                        
                        <div class="glass-effect rounded-xl p-6">
                            <button class="w-full text-left flex justify-between items-center faq-toggle">
                                <span class="font-semibold text-white">Fazem orçamento sem compromisso?</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div class="faq-content mt-4 hidden">
                                <p class="text-gray-300">
                                    Sim! O diagnóstico inicial é gratuito. Após análise, apresentamos orçamento detalhado para sua aprovação antes de iniciar qualquer serviço.
                                </p>
                            </div>
                        </div>
                        
                        <div class="glass-effect rounded-xl p-6">
                            <button class="w-full text-left flex justify-between items-center faq-toggle">
                                <span class="font-semibold text-white">Atendem em domicílio?</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div class="faq-content mt-4 hidden">
                                <p class="text-gray-300">
                                    Atualmente atendemos apenas em nossa sede no Morada do Sol, Rio Verde. O atendimento localizado nos permite utilizar equipamentos especializados para melhor qualidade.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chamada para ação -->
                <div class="fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-effect rounded-2xl p-8 h-full">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 bg-gradient-to-br from-white to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-headset text-3xl text-black"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-4">Precisa de ajuda?</h3>
                            <p class="text-gray-300 mb-8">
                                Entre em contato para tirar dúvidas ou solicitar um orçamento personalizado.
                            </p>
                        </div>
                        
                        <div class="space-y-6">
                            <a href="https://wa.me/5564992800407" class="flex items-center justify-center bg-green-600 text-white py-4 rounded-lg font-bold hover:bg-green-700 smooth-transition" target="_blank">
                                <i class="fab fa-whatsapp mr-3 text-xl"></i>
                                Chamar no WhatsApp
                            </a>
                            
                            <a href="index.php#contact" class="flex items-center justify-center border-2 border-gray-700 text-white py-4 rounded-lg font-bold hover:border-white smooth-transition">
                                <i class="fas fa-envelope mr-3"></i>
                                Enviar E-mail
                            </a>
                            
                            <div class="text-center pt-6 border-t border-gray-800">
                                <p class="text-gray-400 mb-2">
                                    <i class="fas fa-clock mr-2"></i>
                                    Atendimento: Seg-Sex 8h-18h
                                </p>
                                <p class="text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Rua 9 Qd 2 Lt 19, Morada do Sol
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Detalhes do Serviço -->
    <div id="serviceModal" class="service-modal">
        <div class="container mx-auto px-4 flex items-center justify-center">
            <div class="modal-content">
                <div class="flex justify-between items-start mb-8">
                    <h2 id="modalTitle" class="text-3xl font-bold text-white"></h2>
                    <button class="close-modal text-gray-300 hover:text-white">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <div id="modalIcon" class="w-20 h-20 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mb-6">
                            <!-- Ícone será inserido via JS -->
                        </div>
                        <p id="modalDescription" class="text-gray-300 text-lg mb-6"></p>
                        <div class="text-3xl font-black text-white mb-6">
                            R$ <span id="modalPrice"></span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold text-white mb-4">O que está incluído:</h3>
                        <ul id="modalFeatures" class="space-y-3">
                            <!-- Lista de features será inserida via JS -->
                        </ul>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <form method="POST" action="" class="flex-grow">
                        <input type="hidden" id="modalServiceId" name="service_id">
                        <input type="hidden" id="modalServiceName" name="service_name">
                        <input type="hidden" id="modalServicePrice" name="service_price">
                        <input type="hidden" id="modalServiceDesc" name="service_description">
                        <input type="hidden" id="modalServiceCat" name="service_category">
                        
                        <button type="submit" 
                                name="add_to_cart"
                                class="w-full bg-white text-black py-4 rounded-lg font-bold hover:bg-gray-200 smooth-transition flex items-center justify-center">
                            <i class="fas fa-cart-plus mr-3"></i>
                            Adicionar ao Carrinho
                        </button>
                    </form>
                    
                    <a href="index.php#contact" class="flex items-center justify-center border-2 border-gray-700 text-white px-8 py-4 rounded-lg font-bold hover:border-white smooth-transition">
                        <i class="fas fa-calendar-check mr-3"></i>
                        Agendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Carrinho Sidebar (Mesmo do index.php) -->
    <div id="cartSidebar" class="fixed top-0 right-0 bottom-0 w-full md:w-96 bg-black border-l border-gray-800 z-50 transform translate-x-full smooth-transition overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-white">Seu Carrinho</h2>
                <button class="close-cart text-gray-300 hover:text-white smooth-transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="space-y-6 mb-8" id="cartItems">
                <!-- Itens do carrinho serão carregados aqui -->
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
                            <h4 class="font-bold text-white mb-1"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="text-lg font-bold text-white mt-2">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></p>
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
                
                <a href="index.php#contact" class="block w-full bg-white text-black py-4 rounded-lg font-bold hover:bg-gray-200 smooth-transition mb-4 text-center">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Agendar Serviços
                </a>
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
            const serviceModal = document.getElementById('serviceModal');
            const closeModal = document.querySelector('.close-modal');
            
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
            
            // Filtro de categorias
            document.querySelectorAll('.category-filter-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Remover classe active de todos os botões
                    document.querySelectorAll('.category-filter-btn').forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('bg-white', 'text-black');
                        btn.classList.add('bg-gray-900', 'text-gray-300', 'border', 'border-gray-800');
                    });
                    
                    // Adicionar classe active ao botão clicado
                    this.classList.add('active');
                    this.classList.add('bg-white', 'text-black');
                    this.classList.remove('bg-gray-900', 'text-gray-300', 'border', 'border-gray-800');
                    
                    // Filtrar serviços
                    const filter = this.getAttribute('data-filter');
                    const serviceCards = document.querySelectorAll('.service-card');
                    
                    serviceCards.forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-category') === filter) {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 10);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
            
            // Modal de detalhes do serviço
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const serviceKey = this.getAttribute('data-service');
                    const serviceData = <?php echo json_encode($services); ?>;
                    const service = serviceData[serviceKey];
                    
                    // Preencher modal
                    document.getElementById('modalTitle').textContent = service.name;
                    document.getElementById('modalDescription').textContent = service.description;
                    document.getElementById('modalPrice').textContent = service.price.toFixed(2).replace('.', ',');
                    
                    // Ícone
                    const iconContainer = document.getElementById('modalIcon');
                    iconContainer.innerHTML = `<i class="${service.icon} text-2xl text-gray-300"></i>`;
                    
                    // Features
                    const featuresList = document.getElementById('modalFeatures');
                    featuresList.innerHTML = '';
                    service.features.forEach(feature => {
                        const li = document.createElement('li');
                        li.className = 'flex items-start';
                        li.innerHTML = `
                            <i class="fas fa-check text-green-400 mr-2 mt-1"></i>
                            <span class="text-gray-300">${feature}</span>
                        `;
                        featuresList.appendChild(li);
                    });
                    
                    // Form inputs
                    document.getElementById('modalServiceId').value = serviceKey;
                    document.getElementById('modalServiceName').value = service.name;
                    document.getElementById('modalServicePrice').value = service.price;
                    document.getElementById('modalServiceDesc').value = service.description;
                    document.getElementById('modalServiceCat').value = serviceKey;
                    
                    // Mostrar modal
                    serviceModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            });
            
            // Fechar modal
            closeModal.addEventListener('click', () => {
                serviceModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
            
            // Fechar modal ao clicar fora
            serviceModal.addEventListener('click', (e) => {
                if (e.target === serviceModal) {
                    serviceModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
            
            // FAQ toggle
            document.querySelectorAll('.faq-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('i');
                    
                    content.classList.toggle('hidden');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
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
            
            // Feedback visual ao adicionar ao carrinho
            document.querySelectorAll('button[name="add_to_cart"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Se o formulário tiver validação, não fazemos nada aqui
                    if (this.closest('form').checkValidity()) {
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        this.classList.add('bg-green-500', 'hover:bg-green-600');
                        
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                            this.classList.remove('bg-green-500', 'hover:bg-green-600');
                        }, 1500);
                    }
                });
            });
            
            // Fechar mensagem de carrinho automaticamente
            const cartMessage = document.getElementById('cartMessage');
            if (cartMessage) {
                setTimeout(() => {
                    cartMessage.style.opacity = '0';
                    setTimeout(() => {
                        cartMessage.remove();
                    }, 300);
                }, 5000);
            }
        });


    </script>

    <?php include '../footer.php'?>
</body>
</html>