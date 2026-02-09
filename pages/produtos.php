<?php
// minha-conta.php - Modern User Dashboard
require_once '../config/session.php';
require_once __DIR__ . '/../config.php';  // ✅ Funciona sempre
require_once '../models/Usuario.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data from database
$usuarioModel = new Usuario();
$usuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);

if (!$usuario) {
    header('Location: login.php');
    exit;
}

// Get real data from database
require_once '../models/Pedido.php';
require_once '../models/Servico.php';

$pedidoModel = new Pedido();
$servicoModel = new Servico();

$pedidos = $pedidoModel->buscarPorUsuario($_SESSION['usuario_id']);
$servicos = $servicoModel->buscarPorUsuario($_SESSION['usuario_id']);

// Calculate real statistics
$total_pedidos = count($pedidos);
$total_servicos = count($servicos);
$servicos_ativos = count(array_filter($servicos, fn($s) => in_array($s['status'], ['agendado', 'em_andamento'])));
$pedidos_processando = count(array_filter($pedidos, fn($p) => $p['status'] === 'processando'));
$pedidos_entregues = count(array_filter($pedidos, fn($p) => in_array($p['status'], ['concluido', 'entregue'])));
$valor_total_pedidos = array_sum(array_column($pedidos, 'total'));
$valor_total_servicos = array_sum(array_column($servicos, 'preco'));
$valor_total_gasto = $valor_total_pedidos + $valor_total_servicos;

// Get recent activities (combine orders and services)
$recentActivities = [];
foreach ($pedidos as $pedido) {
    $recentActivities[] = [
        'type' => 'pedido',
        'title' => 'Pedido #' . $pedido['id'],
        'status' => ucfirst($pedido['status']),
        'date' => $pedido['data_pedido'],
        'value' => $pedido['total']
    ];
}
foreach ($servicos as $servico) {
    $recentActivities[] = [
        'type' => 'servico',
        'title' => $servico['tipo_servico'],
        'status' => ucfirst(str_replace('_', ' ', $servico['status'])),
        'date' => $servico['data_agendamento'],
        'value' => $servico['preco']
    ];
}

// Sort by date (most recent first)
usort($recentActivities, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Take only the 5 most recent
$recentActivities = array_slice($recentActivities, 0, 5);

// Assign variables for template
$usuario_nome = $usuario['nome'] ?? 'Usuário';
$usuario_email = $usuario['email'] ?? '';
$usuario_telefone = $usuario['telefone'] ?? 'Não informado';
$usuario_cpf = $usuario['cpf'] ?? 'Não informado';
$usuario_endereco = $usuario['endereco'] ?? 'Não informado';
$usuario_bairro = $usuario['bairro'] ?? '';
$usuario_cep = $usuario['cep'] ?? '';
$usuario_data_cadastro = $usuario['data_cadastro'] ?? date('Y-m-d');
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$titulo_pagina = "Minha Conta - SampTech";
require_once '../header.php';
?>

<!-- Dashboard da Conta -->
<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        

        
        <div class="text-center mb-16 fade-in-up">
            <h1 class="text-6xl font-black mb-6">
                <span class="bg-gradient-to-r from-gray-200 via-white to-gray-300 bg-clip-text text-transparent">
                    Bem-vindo de volta
                </span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Gerencie seus serviços, pedidos e configurações em um só lugar
            </p>
        </div>
        
        <!-- Profile Section -->
        <div class="max-w-7xl mx-auto mb-16">
            <div class="glass-effect rounded-3xl p-8 animate-slide-up delay-100">
                <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-8 lg:space-y-0 lg:space-x-12">
                    
                    <!-- Profile Photo -->
                    <div class="relative">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center text-4xl font-bold text-white shadow-2xl">
                            <?php if (!empty($usuario['foto_perfil']) && file_exists('../' . $usuario['foto_perfil'])): ?>
                                <img src="../<?php echo $usuario['foto_perfil']; ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                            <?php else: ?>
                                <?php echo strtoupper(substr($usuario_nome, 0, 2)); ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Change Photo Button -->
                        <button id="change_photo_btn" class="absolute -bottom-2 -right-2 w-10 h-10 bg-white text-black rounded-full border-4 border-black flex items-center justify-center hover:bg-gray-200 transition-all">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                        
                        <!-- Hidden File Input -->
                        <input type="file" id="foto_perfil" accept="image/png,image/jpeg,image/jpg" class="hidden">
                        
                        <!-- Upload Button (hidden initially) -->
                        <button id="upload_photo_btn" class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-all" style="display: none;">
                            <i class="fas fa-upload mr-1"></i>Salvar
                        </button>
                        
                        <!-- Status Indicator -->
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-gray-600 rounded-full border-4 border-black flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="flex-1 text-center lg:text-left">
                        <h2 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($usuario_nome); ?></h2>
                        <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($usuario_email); ?></p>
                        
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start mb-6">
                            <span class="glass-effect px-4 py-2 rounded-full text-sm font-medium">
                                <i class="fas fa-user text-gray-400 mr-2"></i>Cliente
                            </span>
                            <span class="glass-effect px-4 py-2 rounded-full text-sm font-medium">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>Membro desde <?php echo date('Y', strtotime($usuario_data_cadastro)); ?>
                            </span>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                            <a href="editarPerfil.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                                <i class="fas fa-edit mr-2"></i>Editar Perfil
                            </a>
                            <a href="agendar.php" class="glass-effect px-6 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all">
                                <i class="fas fa-plus mr-2"></i>Novo Serviço
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4 lg:gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold bg-gradient-to-r from-gray-200 to-white bg-clip-text text-transparent"><?php echo $total_servicos; ?></div>
                            <div class="text-sm text-gray-400">Serviços</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold bg-gradient-to-r from-gray-200 to-white bg-clip-text text-transparent"><?php echo $total_pedidos; ?></div>
                            <div class="text-sm text-gray-400">Pedidos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            
            <!-- Services Card -->
            <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tools text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold"><?php echo $servicos_ativos; ?></span>
                </div>
                <h3 class="font-semibold mb-2">Serviços Ativos</h3>
                <p class="text-gray-400 text-sm mb-4"><?php echo $servicos_ativos; ?> em andamento</p>
                <a href="meusServicos.php" class="text-gray-300 hover:text-white text-sm font-medium">
                    Ver todos →
                </a>
            </div>
            
            <!-- Orders Card -->
            <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold"><?php echo $total_pedidos; ?></span>
                </div>
                <h3 class="font-semibold mb-2">Pedidos Recentes</h3>
                <p class="text-gray-400 text-sm mb-4"><?php echo $pedidos_entregues; ?> entregues, <?php echo $pedidos_processando; ?> processando</p>
                <a href="meusPedidos.php" class="text-gray-300 hover:text-white text-sm font-medium">
                    Ver todos →
                </a>
            </div>
            
            <!-- Wallet Card -->
            <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold">R$ <?php echo number_format($valor_total_gasto / 1000, 1); ?>k</span>
                </div>
                <h3 class="font-semibold mb-2">Total Gasto</h3>
                <p class="text-gray-400 text-sm mb-4">Total: R$ <?php echo number_format($valor_total_gasto, 2, ',', '.'); ?></p>
                <a href="#" class="text-gray-300 hover:text-white text-sm font-medium">
                    Ver detalhes →
                </a>
            </div>
            
            <!-- Support Card -->
            <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-white text-xl"></i>
                    </div>
                    <span class="w-3 h-3 bg-gray-500 rounded-full animate-pulse"></span>
                </div>
                <h3 class="font-semibold mb-2">Suporte 24/7</h3>
                <p class="text-gray-400 text-sm mb-4">Estamos online agora</p>
                <a href="https://wa.me/5564992800407" class="text-gray-300 hover:text-white text-sm font-medium">
                    Falar conosco →
                </a>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="max-w-7xl mx-auto">
            <div class="glass-effect rounded-3xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold">Atividades Recentes</h2>
                    <div class="flex space-x-2">
                        <button class="glass-effect px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition-all">
                            Todos
                        </button>
                        <button class="glass-effect px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition-all">
                            Serviços
                        </button>
                        <button class="glass-effect px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition-all">
                            Produtos
                        </button>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $index => $activity): ?>
                        <div class="bg-gray-900/50 rounded-2xl p-6 hover:bg-gray-800/50 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl flex items-center justify-center">
                                        <i class="fas <?php echo $activity['type'] === 'service' ? 'fa-tools' : 'fa-box'; ?> text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold"><?php echo htmlspecialchars($activity['title']); ?></h3>
                                        <p class="text-gray-400 text-sm"><?php echo date('d/m/Y', strtotime($activity['date'])); ?></p>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="flex items-center space-x-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php 
                                            echo $activity['status'] === 'Concluído' ? 'bg-gray-600/20 text-gray-300' : 
                                                ($activity['status'] === 'Em andamento' ? 'bg-gray-500/20 text-gray-400' : 'bg-gray-700/20 text-gray-300');
                                        ?>">
                                            <?php echo $activity['status']; ?>
                                        </span>
                                        <span class="font-bold text-lg">R$ <?php echo number_format($activity['value'], 2, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-8">
                    <a href="#" class="bg-white text-black px-8 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all inline-block">
                        Ver Todas as Atividades
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="fixed bottom-8 right-8 z-50">
            <div class="flex flex-col space-y-4">
                <a href="agendar.php" class="w-14 h-14 bg-white text-black rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform group">
                    <i class="fas fa-plus text-xl group-hover:rotate-90 transition-transform"></i>
                </a>
                <a href="https://wa.me/5564992800407" class="w-14 h-14 bg-gray-600 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform">
                    <i class="fab fa-whatsapp text-white text-xl"></i>
                </a>
            </div>
        </div>
        
        <!-- Dashboard da Conta -->
        <div class="grid lg:grid-cols-4 gap-8">
            
            <!-- Sidebar de Navegação -->
            <div class="lg:col-span-1 fade-in-up" style="animation-delay: 0.1s;">
                <div class="glass-effect rounded-2xl p-6">
                    <!-- Avatar e Info -->
                    <div class="text-center mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border-2 border-gray-700 flex items-center justify-center mx-auto mb-4 relative">
                            <?php if (!empty($usuario['foto_perfil']) && file_exists('../' . $usuario['foto_perfil'])): ?>
                                <img src="../<?php echo $usuario['foto_perfil']; ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-3xl text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($usuario_nome); ?></h3>
                        <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($usuario_email); ?></p>
                    </div>
                    
                    <!-- Menu da Conta -->
                    <nav class="space-y-2">
                        <a href="minha-conta.php" 
                           class="flex items-center space-x-3 px-4 py-3 bg-gray-800 text-white rounded-xl">
                            <i class="fas fa-user-circle w-5"></i>
                            <span>Visão Geral</span>
                        </a>
                        <a href="meusPedidos.php" 
                           class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl smooth-transition">
                            <i class="fas fa-shopping-bag w-5"></i>
                            <span>Meus Pedidos</span>
                        </a>
                        <a href="meusServicos.php" 
                           class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl smooth-transition">
                            <i class="fas fa-laptop-medical w-5"></i>
                            <span>Meus Serviços</span>
                        </a>
                        <a href="configuracoes.php" 
                           class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl smooth-transition">
                            <i class="fas fa-cog w-5"></i>
                            <span>Configurações</span>
                        </a>
                        <a href="../logout.php" 
                           class="flex items-center space-x-3 px-4 py-3 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-xl smooth-transition mt-4">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Sair</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Conteúdo Principal -->
            <div class="lg:col-span-3">
                
                <!-- Cards de Estatísticas -->
                <div class="grid md:grid-cols-2 gap-6 mb-8 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-effect rounded-2xl p-6 text-center">
                        <div class="w-16 h-16 bg-white text-black rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-white mb-2"><?php echo $cart_count; ?></div>
                        <div class="text-gray-400">Itens no Carrinho</div>
                    </div>
                    
                    <div class="glass-effect rounded-2xl p-6 text-center">
                        <div class="w-16 h-16 bg-white text-black rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tools text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-white mb-2"><?php echo $total_servicos; ?></div>
                        <div class="text-gray-400">Serviços Realizados</div>
                    </div>
                </div>
                
                <!-- Informações da Conta -->
                <div class="glass-effect rounded-2xl p-8 mb-8 fade-in-up" style="animation-delay: 0.3s;">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-user-circle mr-3"></i>
                        Informações da Conta
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-400 mb-2">Nome Completo</label>
                            <div class="bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl">
                                <?php echo htmlspecialchars($usuario_nome); ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-400 mb-2">E-mail</label>
                            <div class="bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl">
                                <?php echo htmlspecialchars($usuario_email); ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-400 mb-2">Telefone</label>
                            <div class="bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl">
                                <?php echo htmlspecialchars($usuario_telefone); ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-400 mb-2">CPF</label>
                            <div class="bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl">
                                <?php echo htmlspecialchars($usuario_cpf); ?>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-400 mb-2">Endereço</label>
                            <div class="bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl">
                                <?php echo htmlspecialchars($usuario_endereco); ?>, <?php echo htmlspecialchars($usuario_bairro); ?> - CEP: <?php echo htmlspecialchars($usuario_cep); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-gray-800">
                        <a href="editarPerfil.php" 
                           class="inline-flex items-center bg-white text-black px-6 py-3 font-bold hover:bg-gray-200 smooth-transition rounded-xl">
                            <i class="fas fa-edit mr-3"></i>
                            Editar Perfil
                        </a>
                    </div>
                </div>
                
                <!-- Últimos Pedidos -->
                <div class="glass-effect rounded-2xl p-8 fade-in-up" style="animation-delay: 0.4s;">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Atividade Recente
                    </h2>
                    
                    <div class="space-y-4">
                        <?php if (empty($recentActivities)): ?>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-history text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-400">Nenhuma atividade recente encontrada</p>
                                <a href="produtos.php" class="inline-block mt-4 bg-white text-black px-6 py-2 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                                    Fazer Primeiro Pedido
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-900/50 rounded-xl">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center">
                                            <i class="fas <?php echo $activity['type'] === 'servico' ? 'fa-tools' : 'fa-shopping-bag'; ?> text-gray-400"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white"><?php echo htmlspecialchars($activity['title']); ?></h4>
                                            <p class="text-gray-400 text-sm">Status: <?php echo htmlspecialchars($activity['status']); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-white font-bold">R$ <?php echo number_format($activity['value'], 2, ',', '.'); ?></div>
                                        <div class="text-gray-400 text-sm"><?php echo date('d/m/Y', strtotime($activity['date'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-8 text-center">
                        <a href="meusPedidos.php" 
                           class="inline-flex items-center border-2 border-gray-700 text-white px-6 py-3 font-bold hover:border-white smooth-transition rounded-xl">
                            <i class="fas fa-list mr-3"></i>
                            Ver Todos os Pedidos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script src="../js/profile-photo.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        
        window.addEventListener('scroll', checkFade);
        checkFade();
    });
</script>

<?php require_once '../footer.php'; ?>
