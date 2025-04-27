<?php
session_start();

// Pegando usuário da sessão
$user = $_SESSION['user'] ?? null;

// Supondo que $matches também venha da sessão ou de uma consulta anterior
$matches = $_SESSION['matches'] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
</head>
<body>
    <h1>Meu Perfil</h1>

    <?php if ($user): ?>
        <? var_dump(value: $user);?>
        <p><strong>Nome:</strong> <?= htmlspecialchars($user['nome']) ?></p>
        <p><strong>Idade:</strong> <?= htmlspecialchars($user['age']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <?php if (!empty($user['photo'])): ?>
            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Foto de Perfil" width="150">
        <?php else: ?>
            <p><em>Sem foto de perfil.</em></p>
        <?php endif; ?>

        <h2>Seus Matches</h2>
        <?php if (!empty($matches)): ?>
            <ul>
                <?php foreach ($matches as $match): ?>
                    <li>
                        <img src="/public/assets/images/<?= htmlspecialchars($match['foto']) ?>" width="50" alt="Foto de <?= htmlspecialchars($match['nome']) ?>">
                        <?= htmlspecialchars($match['nome']) ?> - 
                        <a href="/mensagens?user=<?= urlencode($match['id']) ?>">Conversar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><em>Você ainda não tem matches.</em></p>
        <?php endif; ?>

    <?php else: ?>
        <p>Usuário não encontrade.</p>
    <?php endif; ?>

    <br>
    <a href="home.php">Voltar para Home</a>
</body>
</html>