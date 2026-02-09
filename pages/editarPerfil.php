<?php
// editarPerfil.php - Página para editar perfil do usuário
$titulo_pagina = "SampTech - Editar Perfil";
include '../header.php';

// Verificar se o usuário está logado
if (!$usuario_logado) {
    header('Location: login.php');
    exit();
}

// Processar atualização do perfil
$mensagem = '';
$erro = '';
$sucesso = false;

// Handle profile photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    require_once '../config.php';
    require_once '../models/Usuario.php';
    
    $uploadDir = '../uploads/profiles/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $fileName = $_SESSION['usuario_id'] . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
            $foto_path = 'uploads/profiles/' . $fileName;
            
            $usuarioModel = new Usuario();
            if ($usuarioModel->atualizarFotoPerfil($_SESSION['usuario_id'], $foto_path)) {
                $_SESSION['usuario_foto'] = $foto_path;
                $mensagem = "Foto de perfil atualizada com sucesso!";
                $sucesso = true;
            } else {
                $erro = "Erro ao salvar foto no banco de dados.";
            }
        } else {
            $erro = "Erro ao fazer upload da foto.";
        }
    } else {
        $erro = "Apenas arquivos JPG, JPEG e PNG são permitidos.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['nome'] ?? '');
    $novo_email = trim($_POST['email'] ?? '');
    $novo_telefone = trim($_POST['telefone'] ?? '');
    $novo_cpf = trim($_POST['cpf'] ?? '');
    $novo_endereco = trim($_POST['endereco'] ?? '');
    $novo_bairro = trim($_POST['bairro'] ?? '');
    $nova_cidade = trim($_POST['cidade'] ?? '');
    $novo_estado = trim($_POST['estado'] ?? '');
    $novo_cep = trim($_POST['cep'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Validações básicas
    if (empty($novo_nome) || empty($novo_email)) {
        $erro = "Nome e e-mail são obrigatórios.";
    } elseif (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, insira um e-mail válido.";
    } else {
        // Atualizar dados na sessão
        $_SESSION['usuario_nome'] = $novo_nome;
        $_SESSION['usuario_email'] = $novo_email;
        $_SESSION['usuario_telefone'] = $novo_telefone;
        $_SESSION['usuario_cpf'] = $novo_cpf;
        $_SESSION['usuario_endereco'] = $novo_endereco;
        $_SESSION['usuario_bairro'] = $novo_bairro;
        $_SESSION['usuario_cidade'] = $nova_cidade;
        $_SESSION['usuario_estado'] = $novo_estado;
        $_SESSION['usuario_cep'] = $novo_cep;
        
        // Atualizar senha se fornecida
        if (!empty($nova_senha)) {
            if (empty($senha_atual)) {
                $erro = "Por favor, digite sua senha atual.";
            } elseif ($nova_senha !== $confirmar_senha) {
                $erro = "A nova senha e a confirmação não coincidem.";
            } elseif (strlen($nova_senha) < 6) {
                $erro = "A nova senha deve ter pelo menos 6 caracteres.";
            } else {
                // Verify current password with database
                require_once '../config.php';
                require_once '../models/Usuario.php';
                
                $usuarioModel = new Usuario();
                $usuario = $usuarioModel->buscarPorEmail($_SESSION['usuario_email']);
                
                if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
                    // Update password in database
                    if ($usuarioModel->atualizarSenha($_SESSION['usuario_id'], $nova_senha)) {
                        $mensagem = "Perfil e senha atualizados com sucesso!";
                        $sucesso = true;
                    } else {
                        $erro = "Erro ao atualizar senha. Tente novamente.";
                    }
                } else {
                    $erro = "Senha atual incorreta.";
                }
            }
        } else {
            $mensagem = "Perfil atualizado com sucesso!";
            $sucesso = true;
        }
        
        // Atualizar variáveis para exibir na página
        $usuario_nome = $novo_nome;
        $usuario_email = $novo_email;
    }
}
?>

<!-- Conteúdo da Página -->
<div class="min-h-screen bg-black">
    <div class="container mx-auto px-4 py-12">
        <!-- Cabeçalho da Página -->
        <div class="mb-12 text-center fade-in-up">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 mb-6">
                <i class="fas fa-user-edit text-2xl text-white"></i>
            </div>
            <h1 class="text-4xl font-black text-white mb-4">Editar Perfil</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Atualize suas informações pessoais e configure suas preferências
            </p>
        </div>
        
        <!-- Mensagens de Feedback -->
        <?php if ($mensagem || $erro): ?>
        <div class="max-w-2xl mx-auto mb-8 fade-in-up" style="animation-delay: 0.1s">
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
        
        <!-- Formulário de Edição -->
        <div class="max-w-2xl mx-auto fade-in-up" style="animation-delay: 0.2s">
            <div class="glass-effect rounded-2xl border border-gray-800 overflow-hidden">
                <!-- Navegação do Perfil -->
                <div class="border-b border-gray-800">
                    <div class="flex overflow-x-auto">
                        <a href="minha-conta.php" class="px-6 py-4 text-gray-400 hover:text-white smooth-transition border-b-2 border-transparent hover:border-gray-600">
                            <i class="fas fa-user-circle mr-2"></i>
                            Minha Conta
                        </a>
                        <a href="meusServicos.php" class="px-6 py-4 text-gray-400 hover:text-white smooth-transition border-b-2 border-transparent hover:border-gray-600">
                            <i class="fas fa-laptop-medical mr-2"></i>
                            Meus Serviços
                        </a>
                        <a href="editarPerfil.php" class="px-6 py-4 text-white smooth-transition border-b-2 border-white font-medium">
                            <i class="fas fa-user-edit mr-2"></i>
                            Editar Perfil
                        </a>
                    </div>
                </div>
                
                <!-- Formulário -->
                <form method="POST" action="" enctype="multipart/form-data" class="p-8">
                    <!-- Profile Photo Section -->
                    <div class="mb-10">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-camera mr-3 text-gray-400"></i>
                            Foto de Perfil
                        </h2>
                        
                        <div class="flex items-center space-x-6">
                            <!-- Current Photo -->
                            <div class="relative">
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center text-2xl font-bold text-white shadow-lg">
                                    <?php 
                                    require_once '../config.php';
                                    require_once '../models/Usuario.php';
                                    $usuarioModel = new Usuario();
                                    $userData = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
                                    if ($userData && !empty($userData['foto_perfil']) && file_exists('../' . $userData['foto_perfil'])): 
                                    ?>
                                        <img src="../<?php echo $userData['foto_perfil']; ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($usuario_nome, 0, 2)); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Upload Button -->
                            <div class="flex-1">
                                <label for="profile_photo" class="block text-sm font-medium text-gray-300 mb-2">
                                    Alterar Foto
                                </label>
                                <input
                                    type="file"
                                    id="profile_photo"
                                    name="profile_photo"
                                    accept="image/*"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white file:text-black hover:file:bg-gray-200"
                                >
                                <p class="text-xs text-gray-500 mt-2">JPG, JPEG ou PNG. Máximo 5MB.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Informações Básicas -->
                    <div class="mb-10">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-id-card mr-3 text-gray-400"></i>
                            Informações Pessoais
                        </h2>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-300 mb-2">
                                    Nome Completo *
                                </label>
                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    value="<?php echo htmlspecialchars($usuario_nome); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="Seu nome completo"
                                    required
                                >
                            </div>
                            
                            <!-- E-mail -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                                    E-mail *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($usuario_email); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="seu@email.com"
                                    required
                                >
                            </div>
                            
                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-300 mb-2">
                                    Telefone
                                </label>
                                <input
                                    type="tel"
                                    id="telefone"
                                    name="telefone"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_telefone'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="(64) 99999-9999"
                                    maxlength="15"
                                >
                            </div>
                            
                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-300 mb-2">
                                    CPF
                                </label>
                                <input
                                    type="text"
                                    id="cpf"
                                    name="cpf"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_cpf'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="000.000.000-00"
                                    maxlength="14"
                                >
                            </div>
                            
                            <!-- Endereço -->
                            <div>
                                <label for="endereco" class="block text-sm font-medium text-gray-300 mb-2">
                                    Endereço
                                </label>
                                <input
                                    type="text"
                                    id="endereco"
                                    name="endereco"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_endereco'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="Rua, número"
                                >
                            </div>
                            
                            <!-- Bairro -->
                            <div>
                                <label for="bairro" class="block text-sm font-medium text-gray-300 mb-2">
                                    Bairro
                                </label>
                                <input
                                    type="text"
                                    id="bairro"
                                    name="bairro"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_bairro'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="Nome do bairro"
                                >
                            </div>
                            
                            <!-- Cidade -->
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-gray-300 mb-2">
                                    Cidade
                                </label>
                                <input
                                    type="text"
                                    id="cidade"
                                    name="cidade"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_cidade'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="Nome da cidade"
                                >
                            </div>
                            
                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-300 mb-2">
                                    Estado
                                </label>
                                <select
                                    id="estado"
                                    name="estado"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                >
                                    <option value="">Selecione o estado</option>
                                    <option value="GO" <?php echo ($_SESSION['usuario_estado'] ?? '') === 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                    <option value="SP" <?php echo ($_SESSION['usuario_estado'] ?? '') === 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                    <option value="RJ" <?php echo ($_SESSION['usuario_estado'] ?? '') === 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                    <option value="MG" <?php echo ($_SESSION['usuario_estado'] ?? '') === 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                    <!-- Add more states as needed -->
                                </select>
                            </div>
                            
                            <!-- CEP -->
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-300 mb-2">
                                    CEP
                                </label>
                                <input
                                    type="text"
                                    id="cep"
                                    name="cep"
                                    value="<?php echo htmlspecialchars($_SESSION['usuario_cep'] ?? ''); ?>"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white"
                                    placeholder="00000-000"
                                    maxlength="9"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alteração de Senha -->
                    <div class="mb-10">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-lock mr-3 text-gray-400"></i>
                            Alterar Senha
                        </h2>
                        <p class="text-gray-400 mb-6 text-sm">
                            Preencha apenas se deseja alterar sua senha atual.
                        </p>
                        
                        <div class="space-y-6">
                            <!-- Senha Atual -->
                            <div>
                                <label for="senha_atual" class="block text-sm font-medium text-gray-300 mb-2">
                                    Senha Atual
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="senha_atual"
                                        name="senha_atual"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white pr-10"
                                        placeholder="Digite sua senha atual"
                                    >
                                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white toggle-password" data-target="senha_atual">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Nova Senha -->
                                <div>
                                    <label for="nova_senha" class="block text-sm font-medium text-gray-300 mb-2">
                                        Nova Senha
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="nova_senha"
                                            name="nova_senha"
                                            class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white pr-10"
                                            placeholder="Mínimo 6 caracteres"
                                        >
                                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white toggle-password" data-target="nova_senha">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Deixe em branco para manter a senha atual</p>
                                </div>
                                
                                <!-- Confirmar Nova Senha -->
                                <div>
                                    <label for="confirmar_senha" class="block text-sm font-medium text-gray-300 mb-2">
                                        Confirmar Nova Senha
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="confirmar_senha"
                                            name="confirmar_senha"
                                            class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white focus:ring-1 focus:ring-white pr-10"
                                            placeholder="Repita a nova senha"
                                        >
                                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white toggle-password" data-target="confirmar_senha">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="flex flex-col md:flex-row gap-4 pt-6 border-t border-gray-800">
                        <a href="minha-conta.php" class="flex-1 px-6 py-4 border-2 border-gray-700 text-white rounded-lg font-medium hover:border-white smooth-transition text-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="flex-1 px-6 py-4 bg-white text-black rounded-lg font-bold hover:bg-gray-200 smooth-transition">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Alterações
                        </button>
                        <button type="submit" name="upload_photo" class="px-6 py-4 bg-gray-700 text-white rounded-lg font-medium hover:bg-gray-600 smooth-transition">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Informações Importantes -->
        <div class="max-w-2xl mx-auto mt-8 fade-in-up" style="animation-delay: 0.3s">
            <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white mb-2">Informações Importantes</h3>
                        <ul class="text-gray-400 space-y-2 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mr-2 mt-1 text-gray-600"></i>
                                <span>Seus dados são armazenados de forma segura em nossa plataforma</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mr-2 mt-1 text-gray-600"></i>
                                <span>Ao alterar seu e-mail, você receberá uma notificação de confirmação</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mr-2 mt-1 text-gray-600"></i>
                                <span>Recomendamos usar senhas fortes com letras, números e símbolos</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Adicional -->
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
    
    // CPF formatting
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            
            e.target.value = value;
        });
    }
    
    // CEP formatting
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            
            e.target.value = value;
        });
    }
    
    // Toggle para mostrar/esconder senha
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Validação do formulário
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (novaSenha || confirmarSenha) {
                if (novaSenha.length < 6 && novaSenha.length > 0) {
                    e.preventDefault();
                    alert('A nova senha deve ter pelo menos 6 caracteres.');
                    return false;
                }
                
                if (novaSenha !== confirmarSenha) {
                    e.preventDefault();
                    alert('A nova senha e a confirmação não coincidem.');
                    return false;
                }
            }
            
            return true;
        });
    }
});
</script>

<?php include '../footer.php'; ?>
