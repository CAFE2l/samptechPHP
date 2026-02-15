<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imagem = '';
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'uploads/produtos/' . uniqid() . '.' . $ext;
        if(!is_dir('../uploads/produtos')) mkdir('../uploads/produtos', 0777, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../' . $imagem);
    }
    
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, categoria, estoque, imagem, ativo) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['preco'],
        $_POST['categoria'],
        $_POST['estoque'],
        $imagem
    ]);
    header('Location: produtos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - SampTech Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-effect {
            background: rgba(17, 17, 17, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-black text-white">

<div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 glass-effect border-r border-gray-800">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SampTech</h1>
            <p class="text-xs text-gray-500 mt-1">Admin Dashboard</p>
        </div>
        <nav class="p-4">
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="agendamentos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-calendar"></i>
                <span>Agendamentos</span>
            </a>
            <a href="servicos.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-tools"></i>
                <span>Serviços</span>
            </a>
            <a href="produtos.php" class="flex items-center space-x-3 px-4 py-3 bg-white text-black rounded-xl mb-2 font-semibold">
                <i class="fas fa-box"></i>
                <span>Produtos</span>
            </a>
            <a href="usuarios.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl mb-2 transition-all">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
            <div class="border-t border-gray-800 my-4"></div>
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:bg-gray-800 rounded-xl transition-all">
                <i class="fas fa-home"></i>
                <span>Voltar ao Site</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">Adicionar Produto</h2>
                    <p class="text-gray-400">Cadastre um novo produto na loja</p>
                </div>
                <a href="produtos.php" class="glass-effect px-6 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>

            <!-- Form -->
            <div class="max-w-3xl">
                <form method="POST" enctype="multipart/form-data" class="glass-effect rounded-2xl p-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-400 mb-2">Imagem/Vídeo do Produto</label>
                            <input type="file" name="imagem" accept="image/*,video/*"
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                            <p class="text-gray-500 text-sm mt-2">Formatos: JPG, PNG, MP4, WEBM</p>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-2">Nome do Produto</label>
                            <input type="text" name="nome" required
                                   class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-2">Descrição</label>
                            <textarea name="descricao" rows="4" required
                                      class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white"></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-400 mb-2">Preço (R$)</label>
                                <input type="number" name="preco" step="0.01" required
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-2">Categoria</label>
                                <input type="text" name="categoria" required placeholder="Ex: Hardware, Periféricos"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-2">Estoque</label>
                                <input type="number" name="estoque" required min="0"
                                       class="w-full bg-gray-900 border border-gray-700 text-white py-3 px-4 rounded-xl focus:outline-none focus:border-white">
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6">
                            <button type="submit" class="flex-1 bg-white text-black px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                                <i class="fas fa-save mr-2"></i>Adicionar Produto
                            </button>
                            <a href="produtos.php" class="flex-1 text-center glass-effect px-6 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
