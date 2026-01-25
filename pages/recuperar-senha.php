<?php
// recuperar-senha.php - Página de Recuperação de Senha SampTech
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verificar se já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: minha-conta.php');
    exit();
}

// Array para mensagens
$mensagem = '';
$erro = '';

// Processar recuperação de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $erro = "Por favor, informe seu e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, informe um e-mail válido.";
    } else {
        // Simulação de envio de e-mail
        // Em um sistema real, você enviaria um e-mail com link de recuperação
        
        // Verificar se o e-mail existe (simulação)
        $usuarios_existentes = ['joao@email.com', 'maria@email.com', 'cliente@samptech.com'];
        
        if (in_array($email, $usuarios_existentes)) {
            $mensagem = "Um e-mail com instruções para redefinir sua senha foi enviado para <strong>$email</strong>. Verifique sua caixa de entrada.";
        } else {
            // Por segurança, não revelamos se o e-mail existe ou não
            $mensagem = "Se o e-mail informado estiver cadastrado em nosso sistema, você receberá instruções para redefinir sua senha.";
        }
    }
}

$titulo_pagina = "Recuperar Senha - SampTech";
?>

<?php include '../header.php'; ?>

<!-- Conteúdo principal -->
<main class="main-content pt-24">
    <section class="py-16 md:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                
                <!-- Cabeçalho da página -->
                <div class="text-center mb-12 fade-in-up">
                    <div class="w-20 h-20 bg-white text-black rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-key text-3xl"></i>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black mb-6 text-white">
                        Recuperar <span class="text-gray-300">Senha</span>
                    </h1>
                    <p class="text-xl text-gray-400">
                        Informe seu e-mail para receber instruções de recuperação.
                    </p>
                </div>
                
                <!-- Mensagens -->
                <?php if ($mensagem): ?>
                    <div class="mb-8 p-6 bg-green-900/30 text-green-400 rounded-2xl border border-green-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <div class="font-bold mb-2">Sucesso!</div>
                                <div><?php echo $mensagem; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="mb-8 p-6 bg-red-900/30 text-red-400 rounded-2xl border border-red-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <span class="font-bold"><?php echo htmlspecialchars($erro); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Card de Recuperação -->
                <div class="glass-effect rounded-2xl p-8 md:p-10 fade-in-up" style="animation-delay: 0.1s;">
                    <form method="POST" action="" class="space-y-8">
                        
                        <!-- Campo E-mail -->
                        <div>
                            <label class="block text-gray-300 mb-4 font-medium text-lg">
                                E-mail Cadastrado *
                            </label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 pl-12 pr-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="seu@email.com"
                                       required
                                       autofocus>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                Enviaremos um link para redefinir sua senha.
                            </p>
                        </div>
                        
                        <!-- Botão de Enviar -->
                        <button type="submit" 
                                class="w-full bg-white text-black py-5 px-8 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-xl flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-4"></i>
                            Enviar Instruções
                        </button>
                    </form>
                </div>
                
                <!-- Links de navegação -->
                <div class="mt-12 space-y-6 text-center fade-in-up" style="animation-delay: 0.2s;">
                    <a href="login.php" 
                       class="inline-flex items-center text-white hover:text-gray-300 smooth-transition text-lg">
                        <i class="fas fa-arrow-left mr-3"></i>
                        Voltar para o Login
                    </a>
                    
                    <div class="border-t border-gray-800 pt-6">
                        <p class="text-gray-400 mb-4">
                            Não tem uma conta ainda?
                        </p>
                        <a href="cadastro.php" 
                           class="inline-flex items-center border-2 border-gray-700 text-white px-6 py-3 text-lg font-bold hover:border-white smooth-transition rounded-xl">
                            <i class="fas fa-user-plus mr-3"></i>
                            Criar Nova Conta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript -->
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

<?php include '../footer.php'; ?>