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

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-white mb-8">Meus Serviços</h1>
        
        <?php if(empty($servicos)): ?>
            <div class="glass-effect rounded-2xl p-8 text-center">
                <i class="fas fa-tools text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-400">Você ainda não tem serviços agendados.</p>
                <a href="agendar.php" class="inline-block mt-4 bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200">
                    Agendar Serviço
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach($servicos as $servico): ?>
                    <div class="glass-effect rounded-2xl p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($servico['servico_nome']); ?></h3>
                                <p class="text-gray-400">Data: <?php echo date('d/m/Y H:i', strtotime($servico['data_agendamento'])); ?></p>
                                <p class="text-gray-400">Status: <?php echo ucfirst($servico['status']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-white">R$ <?php echo number_format($servico['servico_preco'], 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../footer.php'; ?>
