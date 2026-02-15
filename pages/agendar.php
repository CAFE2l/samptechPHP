<?php
// pages/agendar.php - Página de Agendamento
session_start();

// Verificar se o usuário está logado
if(!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_logado'])) {
    header('Location: login.php?redirect=agendar.php');
    exit();
}

// Incluir modelos
require_once '../config.php';
require_once '../models/Servico.php';
require_once '../models/Agendamento.php';

// Inicializar variáveis
$erro = '';
$sucesso = '';
$passo = isset($_GET['passo']) ? intval($_GET['passo']) : 1;
$servico_id = isset($_GET['servico_id']) ? intval($_GET['servico_id']) : 0;
$data_agendamento = isset($_GET['data']) ? $_GET['data'] : '';
$horario_selecionado = isset($_GET['horario']) ? $_GET['horario'] : '';

// Instanciar modelos
$servicoModel = new Servico();
$agendamentoModel = new Agendamento();

// Buscar serviços disponíveis
$resultadoServicos = $servicoModel->listarAtivos();
$servicos = $resultadoServicos['success'] ? $resultadoServicos['servicos'] : [];

// Buscar categorias
$resultadoCategorias = $servicoModel->listarCategorias();
$categorias = $resultadoCategorias['success'] ? $resultadoCategorias['categorias'] : [];

// Buscar serviço específico se selecionado
$servico_selecionado = null;
if ($servico_id > 0) {
    $servico_selecionado = $servicoModel->buscarPorId($servico_id);
}

// Buscar horários disponíveis
$horarios_disponiveis = [];
if ($data_agendamento) {
    $resultadoHorarios = $agendamentoModel->buscarHorariosDisponiveis($data_agendamento);
    if ($resultadoHorarios['success']) {
        $horarios_disponiveis = $resultadoHorarios['horarios'];
    }
}

// Processar agendamento (passo 3)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $passo == 3) {
    // Validar dados
    $dados = [
        'usuario_id' => $_SESSION['usuario_id'],
        'servico_id' => intval($_POST['servico_id']),
        'data_agendamento' => $_POST['data_agendamento'],
        'descricao_problema' => trim($_POST['descricao_problema']),
        'valor_orcamento' => floatval($_POST['valor_total']),
        'observacoes' => trim($_POST['observacoes'])
    ];
    
    // Verificar promoção
    $desconto_aplicado = 0;
    if (!empty($_POST['codigo_promocional'])) {
        $resultadoPromo = $agendamentoModel->verificarPromocao($_POST['codigo_promocional']);
        if ($resultadoPromo['success']) {
            $promocao = $resultadoPromo['promocao'];
            if ($promocao['desconto_percentual'] > 0) {
                $desconto_aplicado = ($dados['valor_orcamento'] * $promocao['desconto_percentual']) / 100;
                $dados['valor_orcamento'] -= $desconto_aplicado;
                $dados['observacoes'] .= "\nPromoção aplicada: " . $promocao['codigo'] . 
                                       " - Desconto: " . $promocao['desconto_percentual'] . "%";
            }
        }
    }
    
    // Criar agendamento
    $resultado = $agendamentoModel->criar($dados);
    
    if ($resultado['success']) {
        $sucesso = $resultado['message'] . " Seu código de agendamento é: AG#" . str_pad($resultado['id'], 6, '0', STR_PAD_LEFT);
        
        // Limpar seleções
        $servico_id = 0;
        $data_agendamento = '';
        $horario_selecionado = '';
        $passo = 1;
        
        // Não redirecionar imediatamente para mostrar mensagem de sucesso
    } else {
        $erro = $resultado['message'];
    }
}

$titulo_pagina = "Agendar Serviço - SampTech";
require_once '../header.php';
?>

<!-- Conteúdo principal -->
<main class="main-content pt-24">
    <section class="py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-12 fade-in-up">
                    <h1 class="text-4xl md:text-5xl font-black mb-6 text-white">
                        Agendar <span class="text-gray-300">Serviço</span>
                    </h1>
                    <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                        Escolha o serviço, selecione o horário e agende sua visita técnica.
                    </p>
                </div>
                
                <!-- Mensagens -->
                <?php if(!empty($erro)): ?>
                    <div class="mb-8 p-6 bg-red-900/30 text-red-400 rounded-2xl border border-red-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Erro no Agendamento</h4>
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
                                <h4 class="text-xl font-bold">Agendamento Confirmado!</h4>
                                <p><?php echo htmlspecialchars($sucesso); ?></p>
                                <div class="mt-4">
                                    <a href="minha-conta.php" class="btn-primary mr-4">
                                        <i class="fas fa-user-circle mr-2"></i>
                                        Ver Meus Agendamentos
                                    </a>
                                    <a href="agendar.php" class="btn-secondary">
                                        <i class="fas fa-plus mr-2"></i>
                                        Novo Agendamento
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Progresso do agendamento -->
                <div class="mb-12 fade-in-up">
                    <div class="flex justify-between items-center mb-8">
                        <?php for($i = 1; $i <= 3; $i++): ?>
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2
                                    <?php echo $i == $passo ? 'bg-white text-black' : 
                                           ($i < $passo ? 'bg-green-500 text-white' : 'bg-gray-800 text-gray-400'); ?>">
                                    <span class="font-bold"><?php echo $i; ?></span>
                                </div>
                                <span class="text-sm font-medium
                                    <?php echo $i == $passo ? 'text-white' : 
                                           ($i < $passo ? 'text-green-400' : 'text-gray-500'); ?>">
                                    <?php echo $i == 1 ? 'Escolher Serviço' : 
                                           ($i == 2 ? 'Selecionar Horário' : 'Confirmar Agendamento'); ?>
                                </span>
                            </div>
                            <?php if($i < 3): ?>
                                <div class="flex-1 h-1 mx-4 
                                    <?php echo $i < $passo ? 'bg-green-500' : 'bg-gray-800'; ?>">
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Conteúdo do passo atual -->
                <div class="fade-in-up">
                    <?php if($passo == 1): ?>
                        <!-- Passo 1: Escolher Serviço -->
                        <div class="glass-effect rounded-2xl p-8">
                            <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
                                <i class="fas fa-laptop-medical mr-3 text-accent"></i>
                                Escolha o Serviço Desejado
                            </h2>
                            
                            <!-- Filtro por categoria -->
                            <div class="mb-8">
                                <div class="flex flex-wrap gap-2 mb-6">
                                    <button class="px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-all categoria-btn active" 
                                            data-categoria="todos">
                                        Todos os Serviços
                                    </button>
                                    <?php foreach($categorias as $categoria): ?>
                                        <button class="px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-all categoria-btn" 
                                                data-categoria="<?php echo htmlspecialchars($categoria); ?>">
                                            <?php echo htmlspecialchars($categoria); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Grid de Serviços -->
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="servicos-container">
                                <?php foreach($servicos as $servico): ?>
                                    <div class="service-card categoria-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $servico['categoria']))); ?>">
                                        <div class="bg-gray-900/50 rounded-xl p-6 h-full flex flex-col hover-lift smooth-transition">
                                            <div class="flex items-start justify-between mb-4">
                                                <div>
                                                    <h3 class="text-xl font-bold text-white mb-2">
                                                        <?php echo htmlspecialchars($servico['nome']); ?>
                                                    </h3>
                                                    <span class="inline-block px-3 py-1 bg-gray-800 text-gray-300 rounded-full text-sm">
                                                        <?php echo htmlspecialchars($servico['categoria']); ?>
                                                    </span>
                                                </div>
                                                <div class="w-12 h-12 bg-gradient-to-br from-white to-gray-300 rounded-lg flex items-center justify-center">
                                                    <?php if($servico['categoria'] == 'Computadores'): ?>
                                                        <i class="fas fa-desktop text-black text-xl"></i>
                                                    <?php elseif($servico['categoria'] == 'Notebooks'): ?>
                                                        <i class="fas fa-laptop text-black text-xl"></i>
                                                    <?php elseif($servico['categoria'] == 'Celulares'): ?>
                                                        <i class="fas fa-mobile-alt text-black text-xl"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-tools text-black text-xl"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <p class="text-gray-400 mb-4 flex-grow">
                                                <?php echo htmlspecialchars($servico['descricao']); ?>
                                            </p>
                                            
                                            <div class="mb-4">
                                                <div class="flex items-center text-gray-400 mb-1">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <span class="text-sm"><?php echo htmlspecialchars($servico['tempo_estimado'] ?? 'A definir'); ?></span>
                                                </div>
                                                <div class="flex items-center text-gray-400">
                                                    <i class="fas fa-shield-alt mr-2"></i>
                                                    <span class="text-sm">Garantia: <?php echo htmlspecialchars($servico['garantia']); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-800">
                                                <div>
                                                    <span class="text-2xl font-bold text-white">
                                                        R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                                                    </span>
                                                </div>
                                                <a href="agendar.php?passo=2&servico_id=<?php echo $servico['id']; ?>" 
                                                   class="btn-primary px-6 py-2">
                                                    <i class="fas fa-calendar-plus mr-2"></i>
                                                    Agendar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if(empty($servicos)): ?>
                                <div class="text-center py-12">
                                    <i class="fas fa-tools text-4xl text-gray-600 mb-4"></i>
                                    <p class="text-gray-400">Nenhum serviço disponível no momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php elseif($passo == 2): ?>
                        <!-- Passo 2: Selecionar Horário -->
                        <div class="glass-effect rounded-2xl p-8">
                            <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
                                <i class="fas fa-calendar-alt mr-3 text-accent"></i>
                                Escolha Data e Horário
                            </h2>
                            
                            <?php if($servico_selecionado): ?>
                                <!-- Resumo do Serviço -->
                                <div class="mb-8 p-6 bg-gray-900/50 rounded-xl">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-white mb-2">
                                                <?php echo htmlspecialchars($servico_selecionado['nome']); ?>
                                            </h3>
                                            <p class="text-gray-400 mb-3">
                                                <?php echo htmlspecialchars($servico_selecionado['descricao']); ?>
                                            </p>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center text-gray-300">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <span><?php echo htmlspecialchars($servico_selecionado['tempo_estimado'] ?? 'A definir'); ?></span>
                                                </div>
                                                <div class="flex items-center text-gray-300">
                                                    <i class="fas fa-shield-alt mr-2"></i>
                                                    <span>Garantia: <?php echo htmlspecialchars($servico_selecionado['garantia']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-white mb-2">
                                                R$ <?php echo number_format($servico_selecionado['preco'], 2, ',', '.'); ?>
                                            </div>
                                            <a href="agendar.php?passo=1" class="text-accent hover:text-accent-light text-sm">
                                                <i class="fas fa-exchange-alt mr-1"></i>
                                                Trocar Serviço
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Seletor de Data -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-bold text-white mb-4">Selecione uma Data:</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-7 gap-3" id="calendario">
                                        <?php
                                        // Gerar próximos 15 dias
                                        for($i = 0; $i < 15; $i++):
                                            $data = date('Y-m-d', strtotime("+$i days"));
                                            $dia_semana = date('w', strtotime($data));
                                            $dia_numero = date('d', strtotime($data));
                                            $mes_ano = date('m/Y', strtotime($data));
                                            $hoje = date('Y-m-d');
                                            
                                            // Verificar se é dia útil (segunda a sexta)
                                            $is_dia_util = ($dia_semana >= 1 && $dia_semana <= 5);
                                            $is_hoje = ($data == $hoje);
                                            $is_selecionado = ($data == $data_agendamento);
                                        ?>
                                            <button type="button" 
                                                    class="p-3 rounded-xl text-center transition-all data-btn 
                                                           <?php echo $is_dia_util ? 'bg-gray-900 hover:bg-gray-800' : 'bg-gray-800 opacity-50 cursor-not-allowed'; ?>
                                                           <?php echo $is_selecionado ? '!bg-accent !text-black' : 'text-white'; ?>"
                                                    data-data="<?php echo $data; ?>"
                                                    <?php echo !$is_dia_util ? 'disabled' : ''; ?>>
                                                <div class="text-sm text-gray-400 mb-1">
                                                    <?php echo date('D', strtotime($data)); ?>
                                                </div>
                                                <div class="text-2xl font-bold mb-1">
                                                    <?php echo $dia_numero; ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <?php echo $mes_ano; ?>
                                                </div>
                                                <?php if($is_hoje): ?>
                                                    <div class="mt-1 text-xs text-accent font-medium">
                                                        Hoje
                                                    </div>
                                                <?php endif; ?>
                                            </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <!-- Horários Disponíveis -->
                                <div id="horarios-container">
                                    <?php if($data_agendamento): ?>
                                        <h3 class="text-lg font-bold text-white mb-4">
                                            Horários disponíveis para <?php echo date('d/m/Y', strtotime($data_agendamento)); ?>:
                                        </h3>
                                        
                                        <?php if(!empty($horarios_disponiveis)): ?>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <?php foreach($horarios_disponiveis as $horario): ?>
                                                    <button type="button" 
                                                            class="p-4 bg-gray-900 rounded-xl text-center hover:bg-gray-800 transition-all horario-btn
                                                                   <?php echo ($horario_selecionado == $horario['hora']) ? '!bg-accent !text-black' : 'text-white'; ?>"
                                                            data-horario="<?php echo $horario['hora']; ?>">
                                                        <div class="text-lg font-bold">
                                                            <?php echo date('H:i', strtotime($horario['hora'])); ?>
                                                        </div>
                                                        <div class="text-sm text-gray-400 mt-1">
                                                            Disponível
                                                        </div>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-8 bg-gray-900/50 rounded-xl">
                                                <i class="fas fa-calendar-times text-4xl text-gray-600 mb-4"></i>
                                                <p class="text-gray-400">Nenhum horário disponível para esta data.</p>
                                                <p class="text-gray-500 text-sm mt-2">Por favor, selecione outra data.</p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Botão Continuar -->
                                        <?php if($horario_selecionado): ?>
                                            <div class="mt-8 pt-8 border-t border-gray-800">
                                                <a href="agendar.php?passo=3&servico_id=<?php echo $servico_id; ?>&data=<?php echo $data_agendamento; ?>&horario=<?php echo $horario_selecionado; ?>" 
                                                   class="block w-full py-5 text-xl font-bold text-center bg-gradient-to-r from-white to-gray-200 text-black rounded-2xl hover:from-gray-100 hover:to-white transition-all duration-300 shadow-lg animate-pulse">
                                                    <i class="fas fa-arrow-right mr-3 text-xl"></i>
                                                    Continuar para Confirmação
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                    <?php else: ?>
                                        <div class="text-center py-12 bg-gray-900/50 rounded-xl">
                                            <i class="fas fa-calendar-alt text-4xl text-gray-600 mb-4"></i>
                                            <p class="text-gray-400">Selecione uma data para ver os horários disponíveis.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
                                    <p class="text-gray-400 mb-4">Serviço não encontrado.</p>
                                    <a href="agendar.php?passo=1" class="btn-primary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Voltar para Escolha de Serviços
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php elseif($passo == 3): ?>
                        <!-- Passo 3: Confirmar Agendamento -->
                        <?php 
                        // Verificar se todos os dados estão presentes
                        if(!$servico_selecionado || !$data_agendamento || !$horario_selecionado) {
                            header('Location: agendar.php?passo=1');
                            exit();
                        }
                        
                        $data_hora_completa = $data_agendamento . ' ' . $horario_selecionado;
                        $valor_total = $servico_selecionado['preco'];
                        ?>
                        
                        <div class="glass-effect rounded-2xl p-8">
                            <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
                                <i class="fas fa-check-circle mr-3 text-accent"></i>
                                Confirmar Agendamento
                            </h2>
                            
                            <form method="POST" action="" class="space-y-8">
                                <!-- Resumo do Agendamento -->
                                <div class="bg-gray-900/50 rounded-xl p-6">
                                    <h3 class="text-xl font-bold text-white mb-6">Resumo do Agendamento</h3>
                                    
                                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h4 class="text-lg font-semibold text-white mb-3">Informações do Serviço</h4>
                                            <div class="space-y-3">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Serviço:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($servico_selecionado['nome']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Data:</span>
                                                    <span class="text-white font-medium"><?php echo date('d/m/Y', strtotime($data_agendamento)); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Horário:</span>
                                                    <span class="text-white font-medium"><?php echo date('H:i', strtotime($horario_selecionado)); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Duração:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($servico_selecionado['tempo_estimado'] ?? 'A definir'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-lg font-semibold text-white mb-3">Informações do Cliente</h4>
                                            <div class="space-y-3">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Nome:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Telefone:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['usuario_telefone']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">E-mail:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Endereço:</span>
                                                    <span class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['usuario_endereco']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Descrição do Problema -->
                                    <div class="mb-6">
                                        <label class="block text-gray-300 mb-3 font-medium">
                                            Descreva o problema detalhadamente *
                                        </label>
                                        <textarea name="descricao_problema" 
                                                  class="w-full bg-gray-800 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white h-32"
                                                  placeholder="Descreva em detalhes o problema que está enfrentando..."
                                                  required></textarea>
                                    </div>
                                    
                                    <!-- Observações -->
                                    <div>
                                        <label class="block text-gray-300 mb-3 font-medium">
                                            Observações Adicionais
                                        </label>
                                        <textarea name="observacoes" 
                                                  class="w-full bg-gray-800 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white h-24"
                                                  placeholder="Alguma observação adicional que considere importante..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Cupom de Desconto -->
                                <div class="bg-gray-900/50 rounded-xl p-6">
                                    <h3 class="text-xl font-bold text-white mb-4">Cupom de Desconto</h3>
                                    <div class="flex gap-4">
                                        <input type="text" 
                                               name="codigo_promocional" 
                                               id="codigo_promocional"
                                               class="flex-grow bg-gray-800 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white"
                                               placeholder="Digite seu cupom de desconto">
                                        <button type="button" 
                                                id="aplicar-cupom"
                                                class="btn-secondary px-6">
                                            Aplicar
                                        </button>
                                    </div>
                                    <div id="cupom-mensagem" class="mt-3 text-sm hidden"></div>
                                </div>
                                
                                <!-- Resumo Financeiro -->
                                <div class="bg-gray-900/50 rounded-xl p-6">
                                    <h3 class="text-xl font-bold text-white mb-6">Resumo Financeiro</h3>
                                    
                                    <div class="space-y-4">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Valor do Serviço:</span>
                                            <span class="text-white">R$ <?php echo number_format($servico_selecionado['preco'], 2, ',', '.'); ?></span>
                                        </div>
                                        
                                        <div id="desconto-container" class="hidden">
                                            <div class="flex justify-between text-green-400">
                                                <span>Desconto:</span>
                                                <span id="valor-desconto">- R$ 0,00</span>
                                            </div>
                                        </div>
                                        
                                        <div class="pt-4 border-t border-gray-800">
                                            <div class="flex justify-between">
                                                <span class="text-xl font-bold text-white">Total:</span>
                                                <span id="valor-total" class="text-2xl font-bold text-white">
                                                    R$ <?php echo number_format($valor_total, 2, ',', '.'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Campos ocultos -->
                                <input type="hidden" name="servico_id" value="<?php echo $servico_id; ?>">
                                <input type="hidden" name="data_agendamento" value="<?php echo $data_hora_completa; ?>">
                                <input type="hidden" name="valor_total" id="valor_total_input" value="<?php echo $valor_total; ?>">
                                
                                <!-- Termos e Condições -->
                                <div class="flex items-start">
                                    <input type="checkbox" 
                                           name="termos" 
                                           id="termos"
                                           class="w-5 h-5 mt-1 bg-gray-900 border-gray-700 text-white focus:ring-white rounded"
                                           required>
                                    <label for="termos" class="ml-3 text-gray-300 cursor-pointer">
                                        Concordo com os 
                                        <a href="../termos.php" class="text-white hover:text-gray-300 underline">Termos de Serviço</a>, 
                                        <a href="../politica.php" class="text-white hover:text-gray-300 underline">Política de Agendamento</a> 
                                        e confirmo que li e compreendi as informações acima.
                                    </label>
                                </div>
                                
                                <!-- Botões -->
                                <div class="flex flex-col md:flex-row gap-4">
                                    <a href="agendar.php?passo=2&servico_id=<?php echo $servico_id; ?>&data=<?php echo $data_agendamento; ?>" 
                                       class="btn-secondary flex-1 text-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Voltar
                                    </a>
                                    <button type="submit" 
                                            class="btn-accent flex-1 text-center py-4 text-lg">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Confirmar Agendamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Dúvidas Frequentes -->
                <div class="mt-12 glass-effect rounded-2xl p-8 fade-in-up">
                    <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-question-circle mr-3 text-accent"></i>
                        Dúvidas Frequentes
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="border-b border-gray-800 pb-4">
                            <h4 class="text-lg font-semibold text-white mb-2">Posso reagendar meu serviço?</h4>
                            <p class="text-gray-400">Sim, você pode reagendar seu serviço com até 24 horas de antecedência através da sua conta ou entrando em contato conosco.</p>
                        </div>
                        
                        <div class="border-b border-gray-800 pb-4">
                            <h4 class="text-lg font-semibold text-white mb-2">Qual a política de cancelamento?</h4>
                            <p class="text-gray-400">Cancelamentos podem ser feitos com até 24 horas de antecedência sem custo. Após esse prazo, pode ser cobrada uma taxa de 20% do valor do serviço.</p>
                        </div>
                        
                        <div class="border-b border-gray-800 pb-4">
                            <h4 class="text-lg font-semibold text-white mb-2">Como funciona a garantia?</h4>
                            <p class="text-gray-400">Todos os nossos serviços possuem garantia conforme especificado em cada serviço. A garantia cobre defeitos de mão de obra e peças originais.</p>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-2">Preciso estar presente durante o serviço?</h4>
                            <p class="text-gray-400">Sim, é necessário que um responsável esteja presente no local para autorizar o início do serviço e receber o técnico.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if($passo == 1): ?>
        // Filtro por categoria
        const categoriaBtns = document.querySelectorAll('.categoria-btn');
        const serviceCards = document.querySelectorAll('.service-card');
        
        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remover classe active de todos
                categoriaBtns.forEach(b => b.classList.remove('active'));
                // Adicionar active no botão clicado
                this.classList.add('active');
                
                const categoria = this.getAttribute('data-categoria');
                
                // Mostrar/esconder serviços
                serviceCards.forEach(card => {
                    if (categoria === 'todos' || card.classList.contains('categoria-' + categoria.toLowerCase().replace(' ', '-'))) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        <?php endif; ?>
        
        <?php if($passo == 2): ?>
        // Selecionar data
        const dataBtns = document.querySelectorAll('.data-btn');
        
        dataBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    const data = this.getAttribute('data-data');
                    window.location.href = `agendar.php?passo=2&servico_id=<?php echo $servico_id; ?>&data=${data}`;
                }
            });
        });
        
        // Selecionar horário
        const horarioBtns = document.querySelectorAll('.horario-btn');
        
        horarioBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const horario = this.getAttribute('data-horario');
                window.location.href = `agendar.php?passo=2&servico_id=<?php echo $servico_id; ?>&data=<?php echo $data_agendamento; ?>&horario=${horario}`;
            });
        });
        <?php endif; ?>
        
        <?php if($passo == 3): ?>
        // Aplicar cupom de desconto
        const aplicarCupomBtn = document.getElementById('aplicar-cupom');
        const codigoPromocional = document.getElementById('codigo_promocional');
        const cupomMensagem = document.getElementById('cupom-mensagem');
        const descontoContainer = document.getElementById('desconto-container');
        const valorDesconto = document.getElementById('valor-desconto');
        const valorTotal = document.getElementById('valor-total');
        const valorTotalInput = document.getElementById('valor_total_input');
        
        let descontoAtual = 0;
        const valorServico = <?php echo $servico_selecionado['preco']; ?>;
        
        aplicarCupomBtn.addEventListener('click', function() {
            const codigo = codigoPromocional.value.trim();
            
            if (!codigo) {
                mostrarMensagemCupom('Digite um código promocional.', 'error');
                return;
            }
            
            // Simular verificação de cupom (em produção seria via AJAX)
            fetch('../api/verificar_cupom.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ codigo: codigo })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const desconto = data.promocao.desconto_percentual;
                    descontoAtual = (valorServico * desconto) / 100;
                    const valorComDesconto = valorServico - descontoAtual;
                    
                    // Atualizar interface
                    valorDesconto.textContent = `- R$ ${descontoAtual.toFixed(2).replace('.', ',')}`;
                    valorTotal.textContent = `R$ ${valorComDesconto.toFixed(2).replace('.', ',')}`;
                    valorTotalInput.value = valorComDesconto;
                    
                    descontoContainer.classList.remove('hidden');
                    mostrarMensagemCupom(`Cupom aplicado! ${desconto}% de desconto.`, 'success');
                } else {
                    descontoAtual = 0;
                    descontoContainer.classList.add('hidden');
                    valorTotal.textContent = `R$ ${valorServico.toFixed(2).replace('.', ',')}`;
                    valorTotalInput.value = valorServico;
                    mostrarMensagemCupom(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagemCupom('Erro ao verificar cupom. Tente novamente.', 'error');
            });
        });
        
        function mostrarMensagemCupom(mensagem, tipo) {
            cupomMensagem.textContent = mensagem;
            cupomMensagem.className = 'mt-3 text-sm';
            
            if (tipo === 'success') {
                cupomMensagem.classList.add('text-green-400');
            } else {
                cupomMensagem.classList.add('text-red-400');
            }
            
            cupomMensagem.classList.remove('hidden');
            
            // Esconder mensagem após 5 segundos
            setTimeout(() => {
                cupomMensagem.classList.add('hidden');
            }, 5000);
        }
        
        // Validação do formulário
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const descricao = this.querySelector('textarea[name="descricao_problema"]').value;
            const termos = this.querySelector('input[name="termos"]');
            
            if (!descricao.trim()) {
                e.preventDefault();
                alert('Por favor, descreva o problema detalhadamente.');
                return false;
            }
            
            if (!termos.checked) {
                e.preventDefault();
                alert('Você deve aceitar os Termos de Serviço para continuar.');
                return false;
            }
            
            return true;
        });
        <?php endif; ?>
        
        // Animações
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