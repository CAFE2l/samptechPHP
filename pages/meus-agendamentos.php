<?php
// pages/meus-agendamentos.php

require_once '../includes/auth_check.php';
verificarAutenticacao();

require_once '../config.php';
require_once '../models/Agendamento.php';

$agendamentoModel = new Agendamento();
$resultado = $agendamentoModel->listarPorUsuario($_SESSION['usuario_id']);

if ($resultado['success']) {
    $agendamentos = $resultado['agendamentos'];
} else {
    $erro = $resultado['message'] ?? 'Erro ao carregar agendamentos';
    $agendamentos = [];
}

$titulo_pagina = "Meus Agendamentos - SampTech";
require_once '../includes/header.php';
?>

<main class="main-content pt-24">
    <section class="py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-12 fade-in-up">
                    <h1 class="text-4xl md:text-5xl font-black mb-6 text-white">
                        Meus <span class="text-gray-300">Agendamentos</span>
                    </h1>
                    <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                        Gerencie e acompanhe todos os seus serviços
                    </p>
                </div>
                
                <?php if(!empty($erro)): ?>
                    <div class="mb-8 p-6 bg-red-900/30 text-red-400 rounded-2xl border border-red-800 fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <div>
                                <h4 class="text-xl font-bold">Erro</h4>
                                <p><?php echo htmlspecialchars($erro); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Botão Novo Agendamento -->
                <div class="mb-8 fade-in-up">
                    <a href="agendar.php" class="inline-flex items-center px-6 py-4 bg-white text-black font-semibold rounded-xl hover:bg-gray-100 smooth-transition">
                        <i class="fas fa-plus mr-3"></i>
                        Novo Agendamento
                    </a>
                </div>
                
                <!-- Lista de Agendamentos -->
                <div class="space-y-6 fade-in-up">
                    <?php if(empty($agendamentos)): ?>
                        <div class="text-center py-16 glass-effect rounded-2xl">
                            <i class="fas fa-calendar-times text-5xl text-gray-600 mb-6"></i>
                            <h3 class="text-2xl font-bold text-white mb-3">Nenhum agendamento encontrado</h3>
                            <p class="text-gray-400 mb-8">Você ainda não possui agendamentos.</p>
                            <a href="agendar.php" class="inline-flex items-center px-8 py-4 bg-white text-black font-semibold rounded-xl hover:bg-gray-100 smooth-transition">
                                <i class="fas fa-calendar-plus mr-3"></i>
                                Fazer Primeiro Agendamento
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach($agendamentos as $agendamento): 
                            // Cores para cada status
                            $statusClasses = [
                                'pendente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                'confirmado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                'em_andamento' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                'concluido' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                'cancelado' => 'bg-red-500/20 text-red-400 border-red-500/30'
                            ];
                            $statusClass = $statusClasses[$agendamento['status']] ?? 'bg-gray-800 text-gray-400';
                        ?>
                            <div class="glass-effect rounded-2xl p-6 smooth-transition hover:border-gray-700 hover-lift">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                                    <div class="flex items-start">
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                            <i class="fas fa-laptop-medical text-2xl text-gray-300"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-white mb-2">
                                                <?php echo htmlspecialchars($agendamento['servico_nome']); ?>
                                            </h3>
                                            <p class="text-gray-400 mb-1">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                <?php echo htmlspecialchars($agendamento['data_formatada']); ?>
                                            </p>
                                            <p class="text-gray-400">
                                                <i class="fas fa-tag mr-2"></i>
                                                R$ <?php echo number_format($agendamento['valor_orcamento'] ?? $agendamento['servico_preco'], 2, ',', '.'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col md:items-end gap-3">
                                        <span class="px-4 py-2 rounded-full text-sm font-medium border <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($agendamento['status_formatado']); ?>
                                        </span>
                                        
                                        <?php if($agendamento['status'] == 'pendente'): ?>
                                            <div class="flex gap-2">
                                                <button onclick="cancelarAgendamento(<?php echo $agendamento['id']; ?>)" 
                                                        class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 smooth-transition text-sm">
                                                    <i class="fas fa-times mr-2"></i>
                                                    Cancelar
                                                </button>
                                                <a href="agendar.php?passo=2&servico_id=<?php echo $agendamento['servico_id']; ?>" 
                                                   class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 smooth-transition text-sm">
                                                    <i class="fas fa-edit mr-2"></i>
                                                    Alterar
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Detalhes do Agendamento -->
                                <div class="grid md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <h4 class="text-lg font-semibold text-white mb-3">Descrição do Problema</h4>
                                        <p class="text-gray-300 bg-gray-900/50 rounded-lg p-4">
                                            <?php echo nl2br(htmlspecialchars($agendamento['descricao_problema'])); ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-lg font-semibold text-white mb-3">Observações</h4>
                                        <p class="text-gray-300 bg-gray-900/50 rounded-lg p-4">
                                            <?php echo nl2br(htmlspecialchars($agendamento['observacoes'] ?: 'Nenhuma observação adicional.')); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Botões de Ação -->
                                <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-800">
                                    <a href="agendamento.php?id=<?php echo $agendamento['id']; ?>" 
                                       class="px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 smooth-transition text-sm">
                                        <i class="fas fa-eye mr-2"></i>
                                        Ver Detalhes
                                    </a>
                                    
                                    <?php if($agendamento['status'] == 'concluido'): ?>
                                        <button class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg hover:bg-green-500/30 smooth-transition text-sm">
                                            <i class="fas fa-file-invoice mr-2"></i>
                                            Baixar Nota Fiscal
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Dicas e Suporte -->
                <div class="mt-12 glass-effect rounded-2xl p-8 fade-in-up">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-3">Precisa de ajuda?</h3>
                            <p class="text-gray-400">Nosso suporte está disponível para te ajudar com qualquer dúvida.</p>
                        </div>
                        <div class="flex gap-4">
                            <a href="https://wa.me/5564992800407" 
                               target="_blank"
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 smooth-transition">
                                <i class="fab fa-whatsapp mr-3"></i>
                                WhatsApp
                            </a>
                            <a href="tel:+5564992800407" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 smooth-transition">
                                <i class="fas fa-phone mr-3"></i>
                                Ligar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal de Cancelamento -->
<div id="cancelModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80">
    <div class="glass-effect rounded-2xl max-w-md w-full p-6">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">Cancelar Agendamento</h3>
                <p class="text-gray-400">Tem certeza que deseja cancelar?</p>
            </div>
        </div>
        
        <p class="text-gray-300 mb-6">Esta ação não pode ser desfeita. O horário será liberado para outros clientes.</p>
        
        <div class="flex justify-end gap-3">
            <button onclick="fecharModal()" 
                    class="px-6 py-3 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 smooth-transition">
                Não Cancelar
            </button>
            <button id="confirmCancel" 
                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 smooth-transition">
                Sim, Cancelar
            </button>
        </div>
    </div>
</div>

<script>
    let agendamentoParaCancelar = null;
    
    function cancelarAgendamento(id) {
        agendamentoParaCancelar = id;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    
    function fecharModal() {
        agendamentoParaCancelar = null;
        document.getElementById('cancelModal').classList.add('hidden');
    }
    
    document.getElementById('confirmCancel').addEventListener('click', function() {
        if (!agendamentoParaCancelar) return;
        
        fetch('../api/cancelar_agendamento.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: agendamentoParaCancelar
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erro ao cancelar agendamento');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        })
        .finally(() => {
            fecharModal();
        });
    });
    
    // Fechar modal ao clicar fora
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModal();
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
