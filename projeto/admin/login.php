<?php
require_once dirname(__DIR__).'/config.php';
if(!empty($_SESSION['admin_id'])){header('Location: painel.php');exit;}
$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_token();
 $email=trim((string)($_POST['email']??''));$senha=(string)($_POST['senha']??'');
 $q=db()->prepare('SELECT id_usuario,nome,senha_hash FROM usuarios_admin WHERE email=?');$q->execute([$email]);$u=$q->fetch();
 if($u&&password_verify($senha,$u['senha_hash'])){session_regenerate_id(true);$_SESSION['admin_id']=$u['id_usuario'];$_SESSION['admin_nome']=$u['nome'];header('Location: painel.php');exit;}
 $erro='E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acesso administrativo</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    </head>
        <body class="bg-light">
            <main class="container py-5" style="max-width:480px">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                    <h1 class="h4">Galera Tech — Administração</h1>
                    <p class="text-muted">Acesso restrito ao painel.</p>
                    
                    <?php if($erro):?><div class="alert alert-danger"><?=e($erro)?></div><?php endif;?><form method="post">
                        <input type="hidden" name="csrf" value="<?=e(token())?>">
                        <label class="form-label" for="email">E-mail</label>
                        <input required type="email" id="email" name="email" class="form-control mb-3">
                        <label class="form-label" for="senha">Senha</label><input required type="password" id="senha" name="senha" class="form-control mb-3">
                        <button class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </main>
    </body>
    </html>