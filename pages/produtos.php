<?php
session_start();

$titulo_pagina = "Produtos - SampTech";
require_once '../header.php';
?>

<section class="py-20 bg-black">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-white mb-4">Nossos Produtos</h1>
            <p class="text-xl text-gray-400">Equipamentos e acessórios de qualidade</p>
        </div>
        
        <div class="glass-effect rounded-2xl p-8 text-center">
            <i class="fas fa-box-open text-6xl text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-white mb-4">Em Breve</h2>
            <p class="text-gray-400 mb-6">Estamos preparando nosso catálogo de produtos para você.</p>
            <a href="servicos.php" class="bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 inline-block">
                Ver Serviços
            </a>
        </div>
    </div>
</section>

<?php require_once '../footer.php'; ?>
