<?php
// Incluir configuração de sessão (agora pelo header.php)
require_once '../config/session.php';


// Incluir configuração e modelos
require_once '../config.php';
require_once '../models/Usuario.php';

$erro = '';
$sucesso = '';
$dados_form = [];
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar dados CORRIGIDO
    $dados_form = [
        'nome' => trim(htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8')),
        'email' => trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL)),
        'senha' => $_POST['senha'] ?? '',
        'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
        'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? ''),
        'cpf' => preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? ''),
        'endereco' => trim(htmlspecialchars($_POST['endereco'] ?? '', ENT_QUOTES, 'UTF-8')),
        'bairro' => trim(htmlspecialchars($_POST['bairro'] ?? '', ENT_QUOTES, 'UTF-8')),
        'cidade' => trim(htmlspecialchars($_POST['cidade'] ?? 'Rio Verde', ENT_QUOTES, 'UTF-8')),
        'estado' => trim(htmlspecialchars($_POST['estado'] ?? 'GO', ENT_QUOTES, 'UTF-8')),
        'cep' => preg_replace('/[^0-9-]/', '', $_POST['cep'] ?? ''),
        'tipo' => 'cliente'
    ];
    
    // Validações
    $erro = '';
    
    if(empty($dados_form['nome']) || empty($dados_form['email']) || empty($dados_form['senha'])) {
        $erro = "Por favor, preencher todos os campos obrigatórios.";
    } elseif(!filter_var($dados_form['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, inserir um email válido.";
    } elseif($dados_form['senha'] !== $dados_form['confirmar_senha']) {
        $erro = "As senhas não coincidem.";
    } elseif(strlen($dados_form['senha']) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    }
    
    // Se não há erro, prosseguir
    if(empty($erro)) {
        try {
            // Verificar se classe existe
            if(!class_exists('Usuario')) {
                throw new Exception("Classe Usuario não encontrada");
            }
            
            // Criar usuário
            $usuarioModel = new Usuario();
            $resultado = $usuarioModel->criar($dados_form);
            
            // Debug: ver o que retorna
            error_log("Resultado criar: " . print_r($resultado, true));
            
            if($resultado['success']) {
                // Cadastro bem sucedido
                $sucesso = "Cadastro realizado com sucesso! Faça login para continuar.";
                
                // Limpar dados do formulário
                $dados_form = [];
                
                // Redirecionar para login após 3 segundos
                header("refresh:3;url=login.php?cadastro=sucesso");
            } else {
                $erro = $resultado['message'] ?? "Erro desconhecido ao criar usuário";
            }
            
        } catch (Exception $e) {
            $erro = "Erro no cadastro: " . $e->getMessage();
            error_log("Erro cadastro: " . $e->getMessage());
        }
    }
}
// Título da página
$titulo_pagina = "Cadastro - SampTech";
require_once '../header.php';
?>

<!-- Conteúdo principal -->
<main class="main-content pt-24">
    <section class="py-16 md:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-12 fade-in-up">
                    <h1 class="text-4xl md:text-5xl font-black mb-6 text-white">
                        Crie sua <span class="text-gray-300">Conta</span>
                    </h1>
                    <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                        É rápido, fácil e gratuito!
                    </p>
                </div>
                
                <!-- Mensagens -->
                <?php if(!empty($erro)): ?>
                    <div class="mb-8 p-6 bg-red-900/30 text-red-400 rounded-2xl border border-red-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Erro no Cadastro</h4>
                                <p><?php echo htmlspecialchars($erro); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($sucesso)): ?>
                    <div class="mb-8 p-6 bg-green-900/30 text-green-400 rounded-2xl border border-green-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Sucesso!</h4>
                                <p><?php echo htmlspecialchars($sucesso); ?></p>
                                <p class="text-sm mt-2">Você será redirecionado para a página de login...</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de Cadastro -->
                <div class="glass-effect rounded-2xl p-8 md:p-12 fade-in-up">
                    <form method="POST" action="" class="space-y-8">
                        <!-- Nome Completo -->
                        <div>
                            <label class="block text-gray-300 mb-4 font-medium text-lg">
                                Nome Completo *
                            </label>
                            <input type="text" 
                                   name="nome" 
                                   value="<?php echo htmlspecialchars($dados_form['nome'] ?? ''); ?>"
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                   placeholder="Seu nome completo"
                                   required>
                        </div>
                        
                        <!-- Email e Telefone -->
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    E-mail *
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($dados_form['email'] ?? ''); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="seu@email.com"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Telefone
                                </label>
                                <input type="tel" 
                                       name="telefone" 
                                       value="<?php echo htmlspecialchars($dados_form['telefone'] ?? ''); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="(64) 9 9999-9999"
                                       oninput="formatarTelefone(this)">
                            </div>
                        </div>
                        
                        <!-- CPF -->
                        <div>
                            <label class="block text-gray-300 mb-4 font-medium text-lg">
                                CPF
                            </label>
                            <input type="text" 
                                   name="cpf" 
                                   value="<?php echo htmlspecialchars($dados_form['cpf'] ?? ''); ?>"
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                   placeholder="000.000.000-00"
                                   oninput="formatarCPF(this)">
                        </div>
                        
                        <!-- Senhas -->
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Senha *
                                </label>
                                <input type="password" 
                                       name="senha" 
                                       id="senha"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="Mínimo 6 caracteres"
                                       required
                                       minlength="6">
                            </div>
                            
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Confirmar Senha *
                                </label>
                                <input type="password" 
                                       name="confirmar_senha" 
                                       id="confirmar_senha"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="Digite novamente"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Endereço -->
                        <div>
                            <label class="block text-gray-300 mb-4 font-medium text-lg">
                                Endereço
                            </label>
                            <input type="text" 
                                   name="endereco" 
                                   value="<?php echo htmlspecialchars($dados_form['endereco'] ?? ''); ?>"
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                   placeholder="Rua, número, complemento">
                        </div>
                        
                        <!-- Bairro, Cidade, Estado, CEP -->
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Bairro
                                </label>
                                <input type="text" 
                                       name="bairro" 
                                       value="<?php echo htmlspecialchars($dados_form['bairro'] ?? ''); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="Seu bairro">
                            </div>
                            
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    CEP
                                </label>
                                <input type="text" 
                                       name="cep" 
                                       value="<?php echo htmlspecialchars($dados_form['cep'] ?? ''); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="00000-000"
                                       oninput="formatarCEP(this)">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Cidade
                                </label>
                                <input type="text" 
                                       name="cidade" 
                                       value="<?php echo htmlspecialchars($dados_form['cidade'] ?? 'Rio Verde'); ?>"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white placeholder-gray-600"
                                       placeholder="Sua cidade">
                            </div>
                            
                            <div>
                                <label class="block text-gray-300 mb-4 font-medium text-lg">
                                    Estado
                                </label>
                                <select name="estado" 
                                        class="w-full bg-gray-900 border border-gray-700 text-white py-4 px-6 rounded-xl focus:outline-none focus:border-white">
                                    <option value="">Selecione</option>
                                    <option value="AC" <?php echo ($dados_form['estado'] ?? '') == 'AC' ? 'selected' : ''; ?>>Acre</option>
                                    <option value="AL" <?php echo ($dados_form['estado'] ?? '') == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                                    <option value="AP" <?php echo ($dados_form['estado'] ?? '') == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                                    <option value="AM" <?php echo ($dados_form['estado'] ?? '') == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                                    <option value="BA" <?php echo ($dados_form['estado'] ?? '') == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                                    <option value="CE" <?php echo ($dados_form['estado'] ?? '') == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                                    <option value="DF" <?php echo ($dados_form['estado'] ?? '') == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                                    <option value="ES" <?php echo ($dados_form['estado'] ?? '') == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                                    <option value="GO" <?php echo ($dados_form['estado'] ?? '') == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                    <option value="MA" <?php echo ($dados_form['estado'] ?? '') == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                                    <option value="MT" <?php echo ($dados_form['estado'] ?? '') == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                                    <option value="MS" <?php echo ($dados_form['estado'] ?? '') == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                    <option value="MG" <?php echo ($dados_form['estado'] ?? '') == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                    <option value="PA" <?php echo ($dados_form['estado'] ?? '') == 'PA' ? 'selected' : ''; ?>>Pará</option>
                                    <option value="PB" <?php echo ($dados_form['estado'] ?? '') == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                                    <option value="PR" <?php echo ($dados_form['estado'] ?? '') == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                                    <option value="PE" <?php echo ($dados_form['estado'] ?? '') == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                                    <option value="PI" <?php echo ($dados_form['estado'] ?? '') == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                                    <option value="RJ" <?php echo ($dados_form['estado'] ?? '') == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                    <option value="RN" <?php echo ($dados_form['estado'] ?? '') == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                    <option value="RS" <?php echo ($dados_form['estado'] ?? '') == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                    <option value="RO" <?php echo ($dados_form['estado'] ?? '') == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                                    <option value="RR" <?php echo ($dados_form['estado'] ?? '') == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                                    <option value="SC" <?php echo ($dados_form['estado'] ?? '') == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                                    <option value="SP" <?php echo ($dados_form['estado'] ?? '') == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                    <option value="SE" <?php echo ($dados_form['estado'] ?? '') == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                                    <option value="TO" <?php echo ($dados_form['estado'] ?? '') == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Termos -->
                        <div class="flex items-start">
                            <input type="checkbox" 
                                   name="termos" 
                                   id="termos"
                                   class="w-5 h-5 mt-1 bg-gray-900 border-gray-700 text-white focus:ring-white rounded"
                                   required>
                            <label for="termos" class="ml-3 text-gray-300 cursor-pointer">
                                Concordo com os 
                                <a href="../termos.php" class="text-white hover:text-gray-300 underline">Termos de Uso</a> 
                                e 
                                <a href="../privacidade.php" class="text-white hover:text-gray-300 underline">Política de Privacidade</a>
                            </label>
                        </div>
                        
                        <!-- Botão de Cadastro -->
                        <button type="submit" 
                                class="w-full bg-white text-black py-5 px-8 text-lg font-bold hover:bg-gray-200 smooth-transition rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-plus mr-4"></i>
                            Criar Minha Conta
                        </button>
                    </form>
                </div>
                
                <!-- Link para Login -->
                <div class="text-center mt-12 fade-in-up">
                    <p class="text-gray-400 text-lg mb-6">
                        Já tem uma conta?
                    </p>
                    <a href="login.php" 
                       class="inline-flex items-center border-2 border-gray-700 text-white px-8 py-4 text-lg font-bold hover:border-white smooth-transition rounded-xl">
                        <i class="fas fa-sign-in-alt mr-4"></i>
                        Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript -->
<script>
    // Formatadores
    function formatarTelefone(input) {
        let valor = input.value.replace(/\D/g, '');
        
        if (valor.length <= 10) {
            valor = valor.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            valor = valor.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        
        input.value = valor;
    }
    
    function formatarCPF(input) {
        let valor = input.value.replace(/\D/g, '');
        
        if (valor.length <= 11) {
            valor = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        }
        
        input.value = valor;
    }
    
    function formatarCEP(input) {
        let valor = input.value.replace(/\D/g, '');
        
        if (valor.length <= 8) {
            valor = valor.replace(/(\d{5})(\d{0,3})/, '$1-$2');
        }
        
        input.value = valor;
    }
    
    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;
        const termos = document.getElementById('termos');
        
        if (senha !== confirmarSenha) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return false;
        }
        
        if (senha.length < 6) {
            e.preventDefault();
            alert('A senha deve ter no mínimo 6 caracteres!');
            return false;
        }
        
        if (!termos.checked) {
            e.preventDefault();
            alert('Você deve aceitar os Termos de Uso e Política de Privacidade!');
            return false;
        }
        
        return true;
    });
</script>

<?php include '../footer.php'; ?>