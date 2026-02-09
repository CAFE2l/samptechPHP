<?php
// pages/login.php - Login REAL com banco de dados

// Incluir configuração de sessão
require_once '../config/session.php';

// Incluir configuração e modelos
require_once '../config.php';
require_once '../models/Usuario.php';

// Inicializar array de erros e mensagens
$erro = '';
$sucesso = '';

// Processar formulário de login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar e validar dados
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $lembrar = isset($_POST['lembrar']);

    // Validações básicas
    if(empty($email) || empty($senha)) {
        $erro = "Por favor, preencher email e senha.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, inserir um email válido.";
    } else {
        // Tentar login
        $usuarioModel = new Usuario();
        $resultado = $usuarioModel->login($email, $senha);
        
        if($resultado['success']) {
            $usuario = $resultado['usuario'];
            
            // Get fresh user data from database
            $usuarioModel = new Usuario();
            $freshUserData = $usuarioModel->buscarPorId($usuario['id']);
            
            // Configurar sessão com dados atualizados
            $_SESSION['usuario_id'] = $freshUserData['id'];
            $_SESSION['usuario_nome'] = $freshUserData['nome'];
            $_SESSION['usuario_email'] = $freshUserData['email'];
            $_SESSION['usuario_tipo'] = $freshUserData['tipo'];
            $_SESSION['usuario_telefone'] = $freshUserData['telefone'] ?? '';
            $_SESSION['usuario_endereco'] = $freshUserData['endereco'] ?? '';
            $_SESSION['usuario_bairro'] = $freshUserData['bairro'] ?? '';
            $_SESSION['usuario_cidade'] = $freshUserData['cidade'] ?? '';
            $_SESSION['usuario_estado'] = $freshUserData['estado'] ?? '';
            $_SESSION['usuario_cep'] = $freshUserData['cep'] ?? '';
            $_SESSION['usuario_cpf'] = $freshUserData['cpf'] ?? '';
            $_SESSION['usuario_logado'] = true;
            $_SESSION['login_time'] = time();
            
            // Store user photo if available
            if (!empty($freshUserData['foto_perfil'])) {
                $_SESSION['usuario_foto'] = $freshUserData['foto_perfil'];
            }
            
            // Cookie para "Lembrar-me" (opcional)
            if($lembrar) {
                $token = bin2hex(random_bytes(32));
                $cookie_data = [
                    'usuario_id' => $usuario['id'],
                    'token' => $token
                ];
                setcookie('samptech_auth', json_encode($cookie_data), time() + (86400 * 30), "/", "", false, true);
            }
            
            // Redirecionar
            if(isset($_GET['redirect']) && !empty($_GET['redirect'])) {
                $redirect = urldecode($_GET['redirect']);
                header("Location: ../$redirect");
            } else {
                // Redirecionar baseado no tipo de usuário
                if($usuario['tipo'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: minha-conta.php");
                }
            }
            exit();
        } else {
            $erro = $resultado['message'];
        }
    }
}

// Título da página
$titulo_pagina = "Login - SampTech";
require_once '../header.php';
?>

<!-- Conteúdo principal -->
<main class="main-content pt-24">
    <section class="py-16 md:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-12 fade-in-up">
                    <h1 class="text-4xl md:text-5xl font-black mb-6 text-white">
                        Faça seu <span class="text-gray-300">Login</span>
                    </h1>
                    <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                        Acesse sua conta para gerenciar serviços, orçamentos e muito mais.
                    </p>
                </div>
                
                <!-- Mensagens -->
                <?php if(!empty($erro)): ?>
                    <div class="mb-8 p-6 bg-red-900/30 text-red-400 rounded-2xl border border-red-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Erro no Login</h4>
                                <p><?php echo htmlspecialchars($erro); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
                    <div class="mb-8 p-6 bg-green-900/30 text-green-400 rounded-2xl border border-green-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Cadastro Realizado!</h4>
                                <p>Faça login com suas credenciais para acessar sua conta.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['logout']) && $_GET['logout'] == 'sucesso'): ?>
                    <div class="mb-8 p-6 bg-blue-900/30 text-blue-400 rounded-2xl border border-blue-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-sign-out-alt text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Logout realizado!</h4>
                                <p>Você saiu da sua conta com sucesso.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Card de Login -->
                <div class="glass-effect rounded-2xl p-8 md:p-12 fade-in-up">
                    <form method="POST" action="" class="space-y-8">
                        <!-- Email -->
                        <div>
                            <label class="block text-gray-300 mb-4 font-medium text-lg">
                                E-mail *
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
                                       autocomplete="email"
                                       autofocus>
                            </div>
                        </div>
                        
                        <!-- Senha -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <label class="text-gray-300 font-medium text-lg">
                                    Senha *
                                </label>
                                <a href="recuperar-senha.php" class="text-white hover:text-gray-300 smooth-transition text-sm">
                                    Esqueceu a senha?
                                </a>
                            </div>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" 
                                       name="senha" 
                                       id="senha"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 pl-12 pr-12 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="Digite sua senha"
                                       required
                                       autocomplete="current-password">
                                <button type="button" 
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                                        onclick="togglePassword('senha', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lembrar-me -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="lembrar" 
                                   id="lembrar"
                                   class="w-5 h-5 bg-gray-900 border-gray-700 text-white focus:ring-white rounded">
                            <label for="lembrar" class="ml-3 text-gray-300 cursor-pointer">
                                Lembrar-me
                            </label>
                        </div>
                        
                        <!-- Botão de Login -->
                        <button type="submit" 
                                class="w-full bg-white text-black py-5 px-8 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-xl flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-4"></i>
                            Entrar na Minha Conta
                        </button>
                        
                        <div class="relative text-center my-8">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-gray-900 text-gray-400">ou</span>
                            </div>
                        </div>

                        <!-- Botão Google -->
                        <button type="button" 
                                id="google-login-btn"
                                class="w-full bg-red-600 text-white py-5 px-8 text-lg font-bold hover:bg-red-700 smooth-transition rounded-xl flex items-center justify-center">
                            <i class="fab fa-google mr-4"></i>
                            Continuar com Google
                        </button>
                    </form>

                 

                </div>
                
                <!-- Link para Cadastro -->
                <div class="text-center mt-12 fade-in-up">
                    <p class="text-gray-400 text-lg mb-6">
                        Ainda não tem uma conta?
                    </p>
                    <a href="cadastro.php" 
                       class="inline-flex items-center border-2 border-gray-700 text-white px-8 py-4 text-lg font-bold hover:border-white smooth-transition rounded-xl">
                        <i class="fas fa-user-plus mr-4"></i>
                        Criar Conta Gratuita
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>
<script>
    // Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyC3aNPjWDUdanjUZRB_WOzHIqTeg771Cgc",
        authDomain: "samptech-fc9a4.firebaseapp.com",
        projectId: "samptech-fc9a4",
        storageBucket: "samptech-fc9a4.firebasestorage.app",
        messagingSenderId: "548249646574",
        appId: "1:548249646574:web:2315e21776e1c087efcaff"
    };
    
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    
    // Google login function
    let isLoggingIn = false;
    
    function handleGoogleLogin() {
        if (isLoggingIn) return;
        isLoggingIn = true;
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.setCustomParameters({ prompt: 'select_account' });
        
        firebase.auth().signInWithPopup(provider)
            .then(function(result) {
                const user = result.user;
                
                return fetch('../api/login-google-simple.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        idToken: 'token',
                        user: {
                            uid: user.uid,
                            email: user.email,
                            displayName: user.displayName || user.email
                        }
                    })
                });
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(function(text) {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        window.location.href = 'minha-conta.php';
                    } else {
                        alert('Erro: ' + (data.message || 'Erro desconhecido'));
                    }
                } catch (e) {
                    console.error('JSON parse error:', text);
                    alert('Erro na resposta do servidor');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                if (error.code === 'auth/cancelled-popup-request') {
                    // Ignore cancelled popup
                } else if (error.code !== 'auth/popup-closed-by-user') {
                    alert('Erro no login: ' + error.message);
                }
            })
            .finally(function() {
                isLoggingIn = false;
            });
    }
    
    function togglePassword(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Add event listener when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const googleBtn = document.getElementById('google-login-btn');
        if (googleBtn) {
            googleBtn.addEventListener('click', handleGoogleLogin);
        }
    });
</script>



</body>
</html>