<?php
@session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Você precisa estar logado!'];
    header("Location: /login");
    exit;
}

$userId = $_SESSION['usuario_id'];

// Dados do afiliado
$stmt = $pdo->prepare("SELECT codigo_indicacao FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$codigo = $stmt->fetchColumn();

// Link personalizado
$link = "https://".$_SERVER['HTTP_HOST']."/cadastro?ref=".$codigo;

// Indicados diretos (nível 1)
$stmt = $pdo->prepare("SELECT id, nome, email, created_at FROM usuarios WHERE indicacao = ?");
$stmt->execute([$codigo]);
$indicados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total comissões recebidas
$stmt = $pdo->prepare("SELECT SUM(valor) as total FROM transacoes_afiliados WHERE afiliado_id = ?");
$stmt->execute([$userId]);
$totalComissao = $stmt->fetchColumn() ?? 0.00;

// Histórico de comissões por depósito
$stmt = $pdo->prepare("
    SELECT ta.*, u.nome as usuario_nome
    FROM transacoes_afiliados ta
    JOIN usuarios u ON u.id = ta.usuario_id
    WHERE ta.afiliado_id = ?
    ORDER BY ta.created_at DESC
    LIMIT 50
");
$stmt->execute([$userId]);
$comissoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Afiliado</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white font-sans">

<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">Painel do Afiliado</h1>

    <div class="mb-6 p-4 bg-gray-800 rounded-lg">
        <p class="mb-2 font-semibold">Seu link de indicação:</p>
        <div class="flex items-center space-x-2">
            <input type="text" id="linkAfiliado" value="<?= $link ?>" readonly class="w-full p-2 text-black rounded">
            <button onclick="copiarLink()" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded text-white">Copiar</button>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="p-4 bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-400">Indicados diretos</p>
            <p class="text-2xl font-bold"><?= count($indicados) ?></p>
        </div>
        <div class="p-4 bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-400">Comissões totais</p>
            <p class="text-2xl font-bold">R$ <?= number_format($totalComissao, 2, ',', '.') ?></p>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-2">Seus indicados (nível 1)</h2>
    <table class="w-full text-left mb-8">
        <thead>
            <tr class="bg-gray-700 text-sm">
                <th class="p-2">Nome</th>
                <th class="p-2">Email</th>
                <th class="p-2">Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indicados as $i): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-800">
                    <td class="p-2"><?= htmlspecialchars($i['nome']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($i['email']) ?></td>
                    <td class="p-2"><?= date('d/m/Y', strtotime($i['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="text-xl font-semibold mb-2">Histórico de Comissões</h2>
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-700 text-sm">
                <th class="p-2">Usuário</th>
                <th class="p-2">Valor</th>
                <th class="p-2">Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comissoes as $c): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-800">
                    <td class="p-2"><?= htmlspecialchars($c['usuario_nome']) ?></td>
                    <td class="p-2">R$ <?= number_format($c['valor'], 2, ',', '.') ?></td>
                    <td class="p-2"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function copiarLink() {
        const input = document.getElementById("linkAfiliado");
        input.select();
        document.execCommand("copy");
        alert("Link copiado!");
    }
</script>
</body>
</html>
