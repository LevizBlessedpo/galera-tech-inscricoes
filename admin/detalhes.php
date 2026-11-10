<?php
require_once dirname(__DIR__) . '/config.php';
auth();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Inscrição inválida.');
}
$q = db()->prepare('SELECT i.*,a.*,r.nome AS nome_responsavel,r.parentesco,r.telefone AS telefone_responsavel FROM inscricoes i JOIN alunos a ON a.id_aluno=i.aluno_id LEFT JOIN responsaveis r ON a.responsavel_id=r.id_responsavel WHERE i.id_inscricao=?');
$q->execute([$id]);
$d = $q->fetch();
if (!$d) {
    http_response_code(404);
    exit('Inscrição não encontrada.');
}
$campos = ['id_inscricao' => 'Número da inscrição', 'nome' => 'Estudante', 'data_nascimento' => 'Nascimento', 'cpf' => 'CPF', 'rg' => 'RG', 'sexo' => 'Sexo', 'email' => 'E-mail', 'telefone' => 'WhatsApp', 'reside_rio_preto' => 'Reside em Rio Preto', 'cep' => 'CEP', 'logradouro' => 'Logradouro', 'numero' => 'Número', 'complemento' => 'Complemento', 'bairro' => 'Bairro', 'cidade' => 'Cidade', 'estado' => 'UF', 'nome_escola' => 'Escola pública', 'serie_escolar' => 'Série', 'origem_divulgacao' => 'Como conheceu', 'nome_responsavel' => 'Responsável', 'parentesco' => 'Parentesco', 'telefone_responsavel' => 'WhatsApp responsável', 'ano' => 'Ano', 'semestre' => 'Semestre', 'data_inscricao' => 'Data inscrição', 'situacao' => 'Situação', 'data_aceite' => 'Data declarações'];
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalhes da Inscrição | Galera Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@400;500;600;700" rel="stylesheet" />
</head>

<body>
    <header class="cabecalho">
        <div class="container" style="max-width:850px">
            <h1 class="h3 fw-bold mb-1">Detalhes da Inscrição</h1>
            <p class="mb-0 opacity-75 small">Visualização completa dos dados do candidato</p>
        </div>
    </header>

    <main class="container py-3" style="max-width:850px">
        <div class="mb-3">
            <a href="painel.php" class="btn btn-outline-light btn-sm px-3">← Voltar ao painel</a>
        </div>

        <div class="wizard-wrapper mb-4">
            <div class="card border-0">
                <div class="card-body p-4 p-md-4">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <tbody>
                                <?php foreach ($campos as $k => $label): ?>
                                    <tr>
                                        <th style="width:35%" class="text-secondary"><?= e($label) ?></th>
                                        <td class="fw-medium"><?= e((string)($d[$k] ?? '—')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info shadow-sm">
            <strong>Status das Declarações:</strong> 
            <?= ((int)$d['ciencia_programa'] === 1 && (int)$d['ciencia_transporte'] === 1 && (int)$d['declarou_veracidade'] === 1 && (int)$d['ciencia_privacidade'] === 1) ? 'Todas as confirmações foram aceitas pelo candidato.' : 'Pendências nas declarações.' ?>
        </div>

        <footer>
            Galera Tech • Administração
        </footer>
    </main>
</body>

</html>