<?php
// configuracoes.php - Página de configurações da conta
$titulo_pagina = "SampTech - Configurações";
include '../header.php';

// Verificar se o usuário está logado
if (!$usuario_logado) {
    header('Location: login.php');
    exit();
}

// Processar atualização das configurações
$mensagem = '';
$erro = '';
$sucesso = false;

// Inicializar configurações se não existirem
if (!isset($_SESSION['configuracoes'])) {
    $_SESSION['configuracoes'] = [
        'notificacoes_email' => true,
        'notificacoes_whatsapp' => true,
        'promocoes' => true,
        'newsletter' => false,
        'privacidade_perfil' => 'publico', // publico, amigos, privado
        'idioma' => 'pt-br',
        'tema' => 'escuro' // escuro, claro, auto
    ];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados do formulário
    $notificacoes_email = isset($_POST['notificacoes_email']) ? true : false;
    $notificacoes_whatsapp = isset($_POST['notificacoes_whatsapp']) ? true : false;
    $promocoes = isset($_POST['promocoes']) ? true : false;
    $newsletter = isset($_POST['newsletter']) ? true : false;
    $privacidade_perfil = $_POST['privacidade_perfil'] ?? 'publico';
    $idioma = $_POST['idioma'] ?? 'pt-br';
    $tema = $_POST['tema'] ?? 'escuro';
    
    // Atualizar configurações na sessão
    $_SESSION['configuracoes'] = [
        'notificacoes_email' => $notificacoes_email,
        'notificacoes_whatsapp' => $notificacoes_whatsapp,
        'promocoes' => $promocoes,
        'newsletter' => $newsletter,
        'privacidade_perfil' => $privacidade_perfil,
        'idioma' => $idioma,
        'tema' => $tema
    ];
    
    $mensagem = "Configurações atualizadas com sucesso!";
    $sucesso = true;
}

// Carregar configurações atuais
$config = $_SESSION['configuracoes'];
?>

<!-- Conteúdo da Página -->
<div class="min-h-screen bg-black">
    <div class="container mx-auto px-4 py-12">
        <!-- Cabeçalho da Página -->
        <div class="mb-12 text-center fade-in-up">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 mb-6">
                <i class="fas fa-cog text-2xl text-white"></i>
            </div>
            <h1 class="text-4xl font-black text-white mb-4">Configurações</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Personalize sua experiência e gerencie suas preferências
            </p>
        </div>
        
        <!-- Mensagens de Feedback -->
        <?php if ($mensagem || $erro): ?>
        <div class="max-w-4xl mx-auto mb-8 fade-in-up" style="animation-delay: 0.1s">
            <div class="<?php echo $sucesso ? 'bg-green-900/50 border-green-700' : 'bg-red-900/50 border-red-700'; ?> border rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas <?php echo $sucesso ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400'; ?> text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm <?php echo $sucesso ? 'text-green-300' : 'text-red-300'; ?> font-medium">
                            <?php echo htmlspecialchars($erro ?: $mensagem); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="max-w-4xl mx-auto">
            <!-- Navegação do Perfil -->
            <div class="glass-effect rounded-2xl border border-gray-800 mb-8 overflow-hidden">
                <div class="border-b border-gray-800">
                    <div class="flex overflow-x-auto">
                        <a href="minha-conta.php" class="px-6 py-4 text-gray-400 hover:text-white smooth-transition border-b-2 border-transparent hover:border-gray-600 whitespace-nowrap">
                            <i class="fas fa-user-circle mr-2"></i>
                            Minha Conta
                        </a>
                        <a href="meusServicos.php" class="px-6 py-4 text-gray-400 hover:text-white smooth-transition border-b-2 border-transparent hover:border-gray-600 whitespace-nowrap">
                            <i class="fas fa-laptop-medical mr-2"></i>
                            Meus Serviços
                        </a>
                        <a href="editarPerfil.php" class="px-6 py-4 text-gray-400 hover:text-white smooth-transition border-b-2 border-transparent hover:border-gray-600 whitespace-nowrap">
                            <i class="fas fa-user-edit mr-2"></i>
                            Editar Perfil
                        </a>
                        <a href="configuracoes.php" class="px-6 py-4 text-white smooth-transition border-b-2 border-white font-medium whitespace-nowrap">
                            <i class="fas fa-cog mr-2"></i>
                            Configurações
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Formulário de Configurações -->
            <form method="POST" action="" class="space-y-8">
                <!-- Seção 1: Notificações -->
                <div class="glass-effect rounded-2xl border border-gray-800 overflow-hidden fade-in-up" style="animation-delay: 0.2s">
                    <div class="p-6 border-b border-gray-800">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-bell mr-3 text-gray-400"></i>
                            Notificações
                        </h2>
                        <p class="text-gray-400 text-sm mt-2">
                            Escolha como deseja receber notificações sobre seus serviços
                        </p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Notificações por E-mail -->
                        <div class="flex items-center justify-between">
                            <div class="flex-grow mr-4">
                                <h3 class="text-white font-medium">Notificações por E-mail</h3>
                                <p class="text-gray-400 text-sm">Receba atualizações por e-mail sobre seus agendamentos e serviços</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notificacoes_email" class="sr-only peer" <?php echo $config['notificacoes_email'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white"></div>
                            </label>
                        </div>
                        
                        <!-- Notificações por WhatsApp -->
                        <div class="flex items-center justify-between">
                            <div class="flex-grow mr-4">
                                <h3 class="text-white font-medium">Notificações por WhatsApp</h3>
                                <p class="text-gray-400 text-sm">Receba lembretes e atualizações no seu WhatsApp</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notificacoes_whatsapp" class="sr-only peer" <?php echo $config['notificacoes_whatsapp'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white"></div>
                            </label>
                        </div>
                        
                        <!-- Promoções e Ofertas -->
                        <div class="flex items-center justify-between">
                            <div class="flex-grow mr-4">
                                <h3 class="text-white font-medium">Promoções e Ofertas</h3>
                                <p class="text-gray-400 text-sm">Receba notificações sobre promoções exclusivas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="promocoes" class="sr-only peer" <?php echo $config['promocoes'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white"></div>
                            </label>
                        </div>
                        
                        <!-- Newsletter -->
                        <div class="flex items-center justify-between">
                            <div class="flex-grow mr-4">
                                <h3 class="text-white font-medium">Newsletter Semanal</h3>
                                <p class="text-gray-400 text-sm">Receba dicas e novidades sobre tecnologia semanalmente</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="newsletter" class="sr-only peer" <?php echo $config['newsletter'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Seção 2: Privacidade -->
                <div class="glass-effect rounded-2xl border border-gray-800 overflow-hidden fade-in-up" style="animation-delay: 0.3s">
                    <div class="p-6 border-b border-gray-800">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-shield-alt mr-3 text-gray-400"></i>
                            Privacidade
                        </h2>
                        <p class="text-gray-400 text-sm mt-2">
                            Controle quem pode ver suas informações
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-6">
                            <label for="privacidade_perfil" class="block text-sm font-medium text-gray-300 mb-3">
                                Visibilidade do Perfil
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 bg-gray-900/50 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-800 smooth-transition <?php echo $config['privacidade_perfil'] == 'publico' ? 'border-white bg-gray-800' : ''; ?>">
                                    <input type="radio" name="privacidade_perfil" value="publico" class="mr-3" <?php echo $config['privacidade_perfil'] == 'publico' ? 'checked' : ''; ?>>
                                    <div>
                                        <span class="text-white font-medium">Público</span>
                                        <p class="text-gray-400 text-sm">Qualquer pessoa pode ver seu perfil</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 bg-gray-900/50 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-800 smooth-transition <?php echo $config['privacidade_perfil'] == 'amigos' ? 'border-white bg-gray-800' : ''; ?>">
                                    <input type="radio" name="privacidade_perfil" value="amigos" class="mr-3" <?php echo $config['privacidade_perfil'] == 'amigos' ? 'checked' : ''; ?>>
                                    <div>
                                        <span class="text-white font-medium">Apenas Clientes</span>
                                        <p class="text-gray-400 text-sm">Apenas clientes da SampTech podem ver</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 bg-gray-900/50 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-800 smooth-transition <?php echo $config['privacidade_perfil'] == 'privado' ? 'border-white bg-gray-800' : ''; ?>">
                                    <input type="radio" name="privacidade_perfil" value="privado" class="mr-3" <?php echo $config['privacidade_perfil'] == 'privado' ? 'checked' : ''; ?>>
                                    <div>
                                        <span class="text-white font-medium">Privado</span>
                                        <p class="text-gray-400 text-sm">Apenas você pode ver seu perfil</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Configurações de Privacidade Adicionais -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-900/30 rounded-lg">
                                <div>
                                    <h4 class="text-white font-medium text-sm">Histórico de Serviços</h4>
                                    <p class="text-gray-400 text-xs">Manter histórico de serviços realizados</p>
                                </div>
                                <div class="text-green-400 text-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="ml-1">Ativado</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-900/30 rounded-lg">
                                <div>
                                    <h4 class="text-white font-medium text-sm">Compartilhar dados para análise</h4>
                                    <p class="text-gray-400 text-xs">Dados anônimos para melhorar nossos serviços</p>
                                </div>
                                <div class="text-green-400 text-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="ml-1">Ativado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seção 3: Aparência e Idioma -->
                <div class="glass-effect rounded-2xl border border-gray-800 overflow-hidden fade-in-up" style="animation-delay: 0.4s">
                    <div class="p-6 border-b border-gray-800">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-palette mr-3 text-gray-400"></i>
                            Aparência e Idioma
                        </h2>
                        <p class="text-gray-400 text-sm mt-2">
                            Personalize a aparência do sistema
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Tema -->
                            <div>
                                <label for="tema" class="block text-sm font-medium text-gray-300 mb-3">
                                    Tema
                                </label>
                                <select id="tema" name="tema" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white">
                                    <option value="escuro" <?php echo $config['tema'] == 'escuro' ? 'selected' : ''; ?>>Escuro</option>
                                    <option value="claro" <?php echo $config['tema'] == 'claro' ? 'selected' : ''; ?>>Claro</option>
                                    <option value="auto" <?php echo $config['tema'] == 'auto' ? 'selected' : ''; ?>>Automático (Sistema)</option>
                                </select>
                            </div>
                            
                            <!-- Idioma -->
                            <div>
                                <label for="idioma" class="block text-sm font-medium text-gray-300 mb-3">
                                    Idioma
                                </label>
                                <select id="idioma" name="idioma" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white">
                                    <option value="pt-br" <?php echo $config['idioma'] == 'pt-br' ? 'selected' : ''; ?>>Português (Brasil)</option>
                                    <option value="en" <?php echo $config['idioma'] == 'en' ? 'selected' : ''; ?>>English</option>
                                    <option value="es" <?php echo $config['idioma'] == 'es' ? 'selected' : ''; ?>>Español</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seção 4: Conta -->
                <div class="glass-effect rounded-2xl border border-gray-800 overflow-hidden fade-in-up" style="animation-delay: 0.5s">
                    <div class="p-6 border-b border-gray-800">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user-cog mr-3 text-gray-400"></i>
                            Conta
                        </h2>
                        <p class="text-gray-400 text-sm mt-2">
                            Gerenciamento avançado da conta
                        </p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <a href="javascript:void(0)" class="flex items-center justify-between p-4 bg-gray-900/30 hover:bg-gray-800 rounded-lg smooth-transition">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-file-download text-gray-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-white font-medium">Exportar Dados</h3>
                                    <p class="text-gray-400 text-sm">Baixe todos os seus dados em formato ZIP</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="javascript:void(0)" class="flex items-center justify-between p-4 bg-gray-900/30 hover:bg-gray-800 rounded-lg smooth-transition">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-trash-alt text-gray-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-white font-medium">Excluir Conta</h3>
                                    <p class="text-gray-400 text-sm">Remover permanentemente sua conta e dados</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="flex flex-col md:flex-row gap-4 fade-in-up" style="animation-delay: 0.6s">
                    <a href="minha-conta.php" class="flex-1 px-6 py-4 border-2 border-gray-700 text-white rounded-lg font-medium hover:border-white smooth-transition text-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 px-6 py-4 bg-white text-black rounded-lg font-bold hover:bg-gray-200 smooth-transition">
                        <i class="fas fa-save mr-2"></i>
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Adicional -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview do tema
    const temaSelect = document.getElementById('tema');
    const body = document.body;
    
    if (temaSelect) {
        // Simular mudança de tema (apenas visual para demonstração)
        temaSelect.addEventListener('change', function() {
            const tema = this.value;
            
            // Aqui você pode adicionar lógica para mudar o tema
            // Por enquanto, apenas um alerta de demonstração
            if (tema !== 'escuro') {
                setTimeout(() => {
                    alert('A mudança de tema será aplicada após salvar as configurações e recarregar a página.');
                }, 100);
            }
        });
    }
    
    // Validação de exclusão de conta
    const deleteAccountLink = document.querySelector('a[href*="Excluir Conta"]');
    if (deleteAccountLink) {
        deleteAccountLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita e todos os seus dados serão removidos permanentemente.')) {
                // Redirecionar para página de exclusão de conta
                window.location.href = 'excluir-conta.php';
            }
        });
    }
    
    // Toggle switches customizados
    const toggleSwitches = document.querySelectorAll('input[type="checkbox"].sr-only');
    toggleSwitches.forEach(switchInput => {
        switchInput.addEventListener('change', function() {
            const parent = this.closest('.relative');
            const toggleDiv = parent.querySelector('div');
            
            if (this.checked) {
                toggleDiv.classList.add('peer-checked:bg-white');
                toggleDiv.classList.remove('bg-gray-700');
            } else {
                toggleDiv.classList.remove('peer-checked:bg-white');
                toggleDiv.classList.add('bg-gray-700');
            }
        });
    });
});
</script>

<?php include '../footer.php'; ?>