<?php
require_once dirname(__DIR__) . '/config.php';
if (!empty($_SESSION['admin_id'])) {
    header('Location: painel.php');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_token();
    $email = trim((string)($_POST['email'] ?? ''));
    $senha = (string)($_POST['senha'] ?? '');
    $q = db()->prepare('SELECT id_usuario,nome,senha_hash FROM usuarios_admin WHERE email=?');
    $q->execute([$email]);
    $u = $q->fetch();
    if ($u && password_verify($senha, $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $u['id_usuario'];
        $_SESSION['admin_nome'] = $u['nome'];
        header('Location: painel.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso administrativo | Galera Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@400;500;600;700" rel="stylesheet" />
</head>

<body>
    <header class="cabecalho">
        <div class="container" style="max-width: 480px">
            <div class="small text-uppercase fw-bold opacity-75 mb-1">Administração</div>
            <h1 class="h3 fw-bold mb-1">Galera Tech</h1>
            <p class="mb-0 opacity-75 small">Acesso restrito ao painel de controle.</p>
        </div>
    </header>

    <main class="container py-3" style="max-width: 480px">
        <div class="wizard-wrapper">
            <div class="card border-0">
                <div class="card-body p-4">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger shadow-sm mb-3"><?= e($erro) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= e(token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="email">E-mail</label>
                            <input required type="email" id="email" name="email" class="form-control" placeholder="seu.email@exemplo.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="senha">Senha</label>
                            <input required type="password" id="senha" name="senha" class="form-control" placeholder="••••••••">
                        </div>
                        <button class="btn btn-primary w-100 py-2 fw-semibold">Entrar no Painel →</button>
                    </form>
                </div>
            </div>
        </div>
        <footer>
            Projeto Galera Tech • Painel Administrativo
        </footer>
    </main>
</body>

</html>