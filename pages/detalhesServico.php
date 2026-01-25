<?php
// pages/detalhesServico.php

require_once '../controllers/AuthController.php';
// verificarAutenticacao();

require_once '../config/database.php';
require_once '../models/Agendamento.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: ../meusServicos.php');
    exit();
}

$agendamentoModel = new Agendamento();
$resultado = $agendamentoModel->buscarPorId($id);

if (!$resultado['success']) {
    header('Location: meusServicos.php?erro=' . urlencode($resultado['message']));
    exit();
}

$agendamento = $resultado['agendamento'];

// Verificar se o usuário é dono do serviço ou admin
if ($agendamento['usuario_id'] != $_SESSION['usuario_id'] && $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: meusServicos.php?erro=Não autorizado');
    exit();
}

$titulo_pagina = "Detalhes do Serviço - SampTech";

// Mapa de cores e estilos por status
$statusConfig = [
    'pendente' => [
        'cor' => 'warning',
        'bgColor' => '#FFA500',
        'icone' => 'fa-clock',
        'texto' => 'Pendente',
        'acoes' => ['cancelar', 'alterar'],
        'borderColor' => '#FFA500'
    ],
    'confirmado' => [
        'cor' => 'blue-500',
        'bgColor' => '#3B82F6',
        'icone' => 'fa-check-circle',
        'texto' => 'Confirmado',
        'acoes' => ['cancelar', 'contato'],
        'borderColor' => '#3B82F6'
    ],
    'em_andamento' => [
        'cor' => 'purple-500',
        'bgColor' => '#A855F7',
        'icone' => 'fa-tools',
        'texto' => 'Em Andamento',
        'acoes' => ['contato'],
        'borderColor' => '#A855F7'
    ],
    'concluido' => [
        'cor' => 'success',
        'bgColor' => '#22C55E',
        'icone' => 'fa-check-double',
        'texto' => 'Concluído',
        'acoes' => ['nota_fiscal', 'avaliar'],
        'borderColor' => '#22C55E'
    ],
    'cancelado' => [
        'cor' => 'danger',
        'bgColor' => '#EF4444',
        'icone' => 'fa-times-circle',
        'texto' => 'Cancelado',
        'acoes' => ['reagendar'],
        'borderColor' => '#EF4444'
    ]
];

$status = $agendamento['status'];
$config = $statusConfig[$status] ?? $statusConfig['pendente'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="sstyle.css">
    
    <style>
        .service-detail-section {
            scroll-margin-top: 100px;
        }
        
        .status-banner {
            border-left-width: 4px;
            border-left-color: <?php echo $config['borderColor']; ?>;
        }
        
        .status-icon-bg {
            background-color: rgba(<?php 
                list($r, $g, $b) = sscanf($config['bgColor'], "#%02x%02x%02x");
                echo "$r, $g, $b";
            ?>, 0.2);
            color: <?php echo $config['bgColor']; ?>;
        }
    </style>
</head>
<body class="bg-black-primary text-white">

<!-- Header -->
<?php 
$pagina_atual = 'detalhesServico.php';
require_once '../header.php'; 
?>

<main class="main-content pt-20">
    <section class="py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Cabeçalho com navegação -->
                <div class="flex items-center justify-between mb-8 animate-fade-in-up">
                    <div>
                        <a href="meusServicos.php" class="inline-flex items-center text-gray-light hover:text-white transition-colors mb-4">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar para Meus Serviços
                        </a>
                        <h1 class="text-3xl md:text-4xl font-black text-white">Detalhes do Serviço</h1>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-sm text-gray-light">Código</div>
                        <div class="text-xl font-bold text-white">#AG<?php echo str_pad($agendamento['id'], 6, '0', STR_PAD_LEFT); ?></div>
                    </div>
                </div>
                
                <!-- Status Banner -->
                <div class="card status-banner p-6 mb-8 animate-fade-in-up animate-delay-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center">
                            <div class="w-16 h-16 status-icon-bg rounded-xl flex items-center justify-center mr-6">
                                <i class="fas <?php echo $config['icone']; ?> text-3xl" style="color: <?php echo $config['bgColor']; ?>;"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white mb-2">Status: <?php echo $config['texto']; ?></h2>
                                <p class="text-gray-light">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    Agendado para: <?php echo date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Ações Rápidas -->
                        <div class="flex flex-wrap gap-3">
                            <a href="https://wa.me/5564992800407?text=Olá! Tenho uma dúvida sobre o serviço #AG<?php echo str_pad($agendamento['id'], 6, '0', STR_PAD_LEFT); ?>" 
                               target="_blank"
                               class="btn btn-success">
                                <i class="fab fa-whatsapp mr-2"></i>
                                WhatsApp
                            </a>
                            
                            <?php if(in_array('cancelar', $config['acoes'])): ?>
                                <button onclick="cancelarServico(<?php echo $agendamento['id']; ?>)" 
                                        class="btn btn-danger">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancelar
                                </button>
                            <?php endif; ?>
                            
                            <?php if(in_array('alterar', $config['acoes'])): ?>
                                <a href="agendar.php?passo=2&servico_id=<?php echo $agendamento['servico_id']; ?>&editar=<?php echo $agendamento['id']; ?>" 
                                   class="btn btn-outline">
                                    <i class="fas fa-edit mr-2"></i>
                                    Alterar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="grid lg:grid-cols-3 gap-8 animate-fade-in-up animate-delay-200">
                    <!-- Informações do Serviço -->
                    <div class="lg:col-span-2">
                        <!-- Resumo do Serviço -->
                        <div class="card p-6 mb-8">
                            <h3 class="text-2xl font-bold text-white mb-6">Informações do Serviço</h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold text-white mb-3">Serviço Contratado</h4>
                                        <div class="flex items-center p-4 bg-gray-dark rounded-xl">
                                            <div class="w-12 h-12 bg-gray-medium rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-laptop-medical text-xl text-white"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-white"><?php echo htmlspecialchars($agendamento['servico_nome']); ?></div>
                                                <div class="text-sm text-gray-light"><?php echo htmlspecialchars($agendamento['servico_descricao'] ?? ''); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-lg font-semibold text-white mb-3">Valor</h4>
                                        <div class="p-4 bg-gray-dark rounded-xl">
                                            <div class="text-3xl font-bold text-white">
                                                R$ <?php echo number_format($agendamento['valor_orcamento'] ?? $agendamento['servico_preco'], 2, ',', '.'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold text-white mb-3">Data e Horário</h4>
                                        <div class="p-4 bg-gray-dark rounded-xl">
                                            <div class="flex items-center text-white">
                                                <i class="far fa-calendar-alt text-gray-light mr-3"></i>
                                                <span class="text-lg"><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></span>
                                            </div>
                                            <div class="flex items-center text-white mt-2">
                                                <i class="far fa-clock text-gray-light mr-3"></i>
                                                <span class="text-lg"><?php echo date('H:i', strtotime($agendamento['data_agendamento'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-lg font-semibold text-white mb-3">Garantia</h4>
                                        <div class="p-4 bg-gray-dark rounded-xl">
                                            <div class="flex items-center text-white">
                                                <i class="fas fa-shield-alt text-gray-light mr-3"></i>
                                                <span class="text-lg"><?php echo htmlspecialchars($agendamento['servico_garantia'] ?? '30 dias'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descrição do Problema -->
                        <div class="card p-6 mb-8">
                            <h3 class="text-2xl font-bold text-white mb-6">Descrição do Problema</h3>
                            <div class="bg-gray-dark rounded-xl p-6">
                                <p class="text-gray-lighter whitespace-pre-line leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($agendamento['descricao_problema'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <?php if(!empty($agendamento['observacoes'])): ?>
                            <div class="card p-6">
                                <h3 class="text-2xl font-bold text-white mb-6">Observações</h3>
                                <div class="bg-gray-dark rounded-xl p-6">
                                    <p class="text-gray-lighter whitespace-pre-line leading-relaxed">
                                        <?php echo nl2br(htmlspecialchars($agendamento['observacoes'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Informações do Cliente -->
                        <div class="card p-6">
                            <h3 class="text-xl font-bold text-white mb-6">Seus Dados</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="text-sm text-gray-light mb-1">Nome</div>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($agendamento['usuario_nome'] ?? $_SESSION['usuario_nome']); ?></div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-light mb-1">E-mail</div>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($agendamento['usuario_email'] ?? $_SESSION['usuario_email']); ?></div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-light mb-1">Telefone</div>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($agendamento['usuario_telefone'] ?? $_SESSION['usuario_telefone']); ?></div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-light mb-1">Endereço</div>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($agendamento['usuario_endereco'] ?? $_SESSION['usuario_endereco'] ?? 'Não informado'); ?></div>
                                </div>
                                
                                <div class="pt-4 border-t border-gray-dark">
                                    <a href="minha-conta.php" class="text-accent hover:text-accent-light text-sm inline-flex items-center">
                                        <i class="fas fa-edit mr-2"></i>
                                        Atualizar meus dados
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ações Adicionais -->
                        <div class="card p-6">
                            <h3 class="text-xl font-bold text-white mb-6">Ações</h3>
                            <div class="space-y-3">
                                <a href="meusServicos.php" class="btn btn-secondary w-full">
                                    <i class="fas fa-list mr-2"></i>
                                    Ver Todos Serviços
                                </a>
                                
                                <?php if($status == 'concluido'): ?>
                                    <button class="btn btn-success w-full">
                                        <i class="fas fa-file-invoice mr-2"></i>
                                        Baixar Nota Fiscal
                                    </button>
                                    <button class="btn btn-warning w-full">
                                        <i class="fas fa-star mr-2"></i>
                                        Avaliar Serviço
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($status == 'cancelado'): ?>
                                    <a href="agendar.php" class="btn btn-accent w-full">
                                        <i class="fas fa-calendar-plus mr-2"></i>
                                        Agendar Novo Serviço
                                    </a>
                                <?php endif; ?>
                                
                                <button onclick="window.print()" class="btn btn-outline w-full">
                                    <i class="fas fa-print mr-2"></i>
                                    Imprimir Detalhes
                                </button>
                            </div>
                        </div>
                        
                        <!-- Suporte Rápido -->
                        <div class="card p-6">
                            <h3 class="text-xl font-bold text-white mb-6">Suporte Rápido</h3>
                            <div class="space-y-3">
                                <a href="https://wa.me/5564992800407" 
                                   target="_blank"
                                   class="btn btn-success w-full">
                                    <i class="fab fa-whatsapp mr-2"></i>
                                    Falar no WhatsApp
                                </a>
                                
                                <a href="tel:+5564992800407" 
                                   class="btn btn-primary w-full">
                                    <i class="fas fa-phone mr-2"></i>
                                    Ligar para Suporte
                                </a>
                                
                                <a href="mailto:suporte@samptech.com" 
                                   class="btn btn-outline w-full">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Enviar E-mail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal de Cancelamento -->
<div id="cancelModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cancelar Serviço</h3>
            <button type="button" class="modal-close" onclick="fecharModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-danger/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-danger"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white"><?php echo htmlspecialchars($agendamento['servico_nome']); ?></h4>
                    <p class="text-gray-light">Tem certeza que deseja cancelar este serviço?</p>
                </div>
            </div>
            
            <p class="text-gray-lighter mb-6">
                Esta ação não pode ser desfeita. O horário será liberado para outros clientes.
                Você poderá agendar um novo serviço quando desejar.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="fecharModal()">
                Manter Serviço
            </button>
            <button type="button" id="confirmCancel" class="btn btn-danger">
                Confirmar Cancelamento
            </button>
        </div>
    </div>
</div>

<script>
    function cancelarServico(id) {
        const modal = document.getElementById('cancelModal');
        modal.classList.add('active');
        
        document.getElementById('confirmCancel').onclick = function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Cancelando...';
            this.disabled = true;
            
            fetch('../api/cancelar_servico.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacao('Serviço cancelado com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = 'meusServicos.php';
                    }, 1500);
                } else {
                    mostrarNotificacao(data.message || 'Erro ao cancelar serviço', 'error');
                    fecharModal();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao processar solicitação', 'error');
                fecharModal();
            });
        };
    }
    
    function fecharModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('active');
        
        const btn = document.getElementById('confirmCancel');
        btn.innerHTML = 'Confirmar Cancelamento';
        btn.disabled = false;
        btn.onclick = null;
    }
    
    // Fechar modal ao clicar fora
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModal();
        }
    });
    
    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModal();
        }
    });
    
    function mostrarNotificacao(mensagem, tipo = 'info') {
        // Criar elemento de notificação
        const toast = document.createElement('div');
        toast.className = `notification-toast alert alert-${tipo} animate-fade-in-right`;
        toast.innerHTML = `
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-message">${mensagem}</div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Remover após 5 segundos
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
</script>

<!-- Footer -->
<?php require_once '../footer.php'; ?>

</body>
</html>