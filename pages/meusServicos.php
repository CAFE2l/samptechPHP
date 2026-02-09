<?php
session_start();

if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config.php';
require_once '../models/Agendamento.php';

$agendamentoModel = new Agendamento();
$resultado = $agendamentoModel->listarPorUsuario($_SESSION['usuario_id']);
$servicos = $resultado['agendamentos'] ?? [];

$titulo_pagina = "Meus Serviços - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black min-h-screen">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Meus Serviços</h1>
            <p class="text-gray-400">Acompanhe o status dos seus agendamentos</p>
        </div>

        <?php if(empty($servicos)): ?>
            <!-- Empty State -->
            <div class="glass-effect rounded-2xl p-12 text-center">
                <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tools text-3xl text-gray-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Nenhum serviço agendado</h2>
                <p class="text-gray-400 mb-6">Você ainda não tem serviços agendados.</p>
                <a href="agendar.php" class="inline-block bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Agendar Serviço
                </a>
            </div>
        <?php else: ?>
            <!-- Services List -->
            <div class="space-y-4">
                <?php foreach($servicos as $servico): 
                    $status_colors = [
                        'pendente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                        'confirmado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        'em_andamento' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                        'concluido' => 'bg-green-500/20 text-green-400 border-green-500/30',
                        'cancelado' => 'bg-red-500/20 text-red-400 border-red-500/30'
                    ];
                    $status_class = $status_colors[$servico['status']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    $can_cancel = in_array($servico['status'], ['pendente', 'confirmado']);
                ?>
                    <div class="glass-effect rounded-2xl p-6 hover:bg-gray-900 transition-all">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            
                            <!-- Service Info -->
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-wrench text-xl text-gray-300"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-white mb-1">
                                            <?php echo htmlspecialchars($servico['servico_nome']); ?>
                                        </h3>
                                        <div class="flex flex-wrap gap-3 text-sm text-gray-400 mb-2">
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?php echo date('d/m/Y', strtotime($servico['data_agendamento'])); ?>
                                            </span>
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo date('H:i', strtotime($servico['data_agendamento'])); ?>
                                            </span>
                                        </div>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border <?php echo $status_class; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $servico['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Price & Actions -->
                            <div class="flex flex-col items-end gap-3">
                                <div class="text-2xl font-bold text-white">
                                    R$ <?php echo number_format($servico['servico_preco'], 2, ',', '.'); ?>
                                </div>
                                <div class="flex gap-2">
                                    <?php if($can_cancel): ?>
                                        <button onclick="cancelService(<?php echo $servico['id']; ?>)" 
                                                class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg font-semibold hover:bg-red-500/30 transition-all border border-red-500/30">
                                            <i class="fas fa-times mr-1"></i>
                                            Cancelar
                                        </button>
                                    <?php endif; ?>
                                    <a href="https://wa.me/5564992800407?text=Olá, gostaria de informações sobre o agendamento #<?php echo $servico['id']; ?>" 
                                       target="_blank"
                                       class="px-4 py-2 glass-effect text-white rounded-lg font-semibold hover:bg-gray-700 transition-all">
                                        <i class="fab fa-whatsapp mr-1"></i>
                                        Contato
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-effect rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold text-white mb-1">
                        <?php echo count(array_filter($servicos, fn($s) => in_array($s['status'], ['pendente', 'confirmado']))); ?>
                    </div>
                    <div class="text-sm text-gray-400">Agendados</div>
                </div>
                <div class="glass-effect rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold text-white mb-1">
                        <?php echo count(array_filter($servicos, fn($s) => $s['status'] === 'em_andamento')); ?>
                    </div>
                    <div class="text-sm text-gray-400">Em Andamento</div>
                </div>
                <div class="glass-effect rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold text-white mb-1">
                        <?php echo count(array_filter($servicos, fn($s) => $s['status'] === 'concluido')); ?>
                    </div>
                    <div class="text-sm text-gray-400">Concluídos</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function cancelService(id) {
    if (!confirm('Tem certeza que deseja cancelar este serviço?')) return;
    
    fetch('../api/cancelar_servico.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({agendamento_id: id})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Serviço cancelado com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível cancelar'));
        }
    })
    .catch(() => alert('Erro ao cancelar serviço'));
}
</script>

<?php require_once '../footer.php'; ?>
