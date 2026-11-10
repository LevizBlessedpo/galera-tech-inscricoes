<?php
require_once dirname(__DIR__) . '/config.php';
if ((int)db()->query('SELECT COUNT(*) FROM usuarios_admin')->fetchColumn() > 0) {
    http_response_code(403);
    exit('Administrador já existe. Remova este instalador.');
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_token();
    $nome = trim((string)($_POST['nome'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $senha = (string)($_POST['senha'] ?? '');
    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 12) {
        $erro = 'Informe nome, e-mail válido e senha com ao menos 12 caracteres.';
    } else {
        try {
            $q = db()->prepare('INSERT INTO usuarios_admin(nome,email,senha_hash) VALUES(?,?,?)');
            $q->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            echo '<div style="font-family:sans-serif; text-align:center; margin-top:50px;"><h2>Administrador criado com sucesso!</h2><p>EXCLUA o arquivo <code>admin/criar_admin.php</code> por segurança.</p><a href="login.php">Ir para a tela de login</a></div>';
            exit;
        } catch (Throwable $e) {
            $erro = 'Não foi possível criar administrador.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Administrador | Galera Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@400;500;600;700" rel="stylesheet" />
</head>
<body>
    <header class="cabecalho">
        <div class="container" style="max-width:500px">
            <h1 class="h3 fw-bold mb-1">Criar Administrador</h1>
            <p class="mb-0 opacity-75 small">Instalação e configuração inicial local</p>
        </div>
    </header>
    <main class="container py-3" style="max-width:500px">
        <div class="alert alert-warning shadow-sm">
            <strong>Atenção:</strong> Remova este arquivo do servidor após cadastrar o primeiro administrador.
        </div>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= e($erro) ?></div>
        <?php endif; ?>
        <div class="wizard-wrapper">
            <div class="card border-0">
                <div class="card-body p-4">
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= e(token()) ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input class="form-control" name="nome" required placeholder="Seu nome completo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input class="form-control" type="email" name="email" required placeholder="seu.email@exemplo.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Senha forte (12+ caracteres)</label>
                            <input class="form-control" type="password" name="senha" minlength="12" required placeholder="••••••••••••">
                        </div>
                        <button class="btn btn-primary w-100 fw-semibold">Criar Administrador</button>
                    </form>
                </div>
            </div>
        </div>
        <footer>Galera Tech • Instalação Local</footer>
    </main>
</body>
</html>