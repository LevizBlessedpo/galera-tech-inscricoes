<?php
require_once dirname(__DIR__) . '/config.php';
auth();
$nome = trim((string)($_GET['nome'] ?? ''));
$escola = (string)($_GET['escola'] ?? '');
$serie = (string)($_GET['serie'] ?? '');
$cidade = trim((string)($_GET['cidade'] ?? ''));
$ano = (string)($_GET['ano'] ?? '');
$sem = (string)($_GET['semestre'] ?? '');
$where = [];
$par = [];
if ($nome !== '') {
    $where[] = 'a.nome LIKE ?';
    $par[] = '%' . $nome . '%';
}
if ($escola !== '') {
    $where[] = 'a.nome_escola=?';
    $par[] = $escola;
}
if ($serie !== '') {
    $where[] = 'a.serie_escolar=?';
    $par[] = $serie;
}
if ($cidade !== '') {
    $where[] = 'a.cidade LIKE ?';
    $par[] = '%' . $cidade . '%';
}
if ($ano !== '' && ctype_digit($ano)) {
    $where[] = 'i.ano=?';
    $par[] = (int)$ano;
}
if (in_array($sem, ['1', '2'], true)) {
    $where[] = 'i.semestre=?';
    $par[] = (int)$sem;
}
$sql = ' FROM inscricoes i JOIN alunos a ON a.id_aluno=i.aluno_id ' . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
$q = db()->prepare('SELECT COUNT(*)' . $sql);
$q->execute($par);
$total = (int)$q->fetchColumn();
$q = db()->prepare('SELECT i.id_inscricao,i.ano,i.semestre,i.data_inscricao,i.situacao,a.nome,a.cidade,a.serie_escolar,a.nome_escola' . $sql . ' ORDER BY i.data_inscricao DESC, i.id_inscricao DESC LIMIT 200');
$q->execute($par);
$rows = $q->fetchAll();
$escolas = db()->query('SELECT DISTINCT nome_escola FROM alunos ORDER BY nome_escola')->fetchAll();
$series = db()->query('SELECT DISTINCT serie_escolar FROM alunos ORDER BY serie_escolar')->fetchAll();
function option(string $v, string $t, string $current): void
{
    echo '<option value="' . e($v) . '"' . ($v === $current ? ' selected' : '') . '>' . e($t) . '</option>';
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Galera Tech — Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@400;500;600;700" rel="stylesheet" />
</head>

<body>
    <header class="cabecalho py-4">
        <div class="container d-flex justify-content-between align-items-center" style="max-width: 1100px;">
            <div>
                <h1 class="h3 mb-1 fw-bold text-white">Painel de Inscrições</h1>
                <div class="text-white-50 small">Galera Tech • Área Administrativa</div>
            </div>
            <a class="btn btn-light btn-sm fw-semibold px-3" href="sair.php">Sair do Sistema</a>
        </div>
    </header>

    <main class="container py-4" style="max-width: 1100px;">

        <!-- CARD DE FILTROS -->
        <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold text-primary mb-3">Filtros de Pesquisa</h2>
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Nome do Candidato</label>
                        <input class="form-control" name="nome" value="<?= e($nome) ?>" placeholder="Pesquisar por nome">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Escola</label>
                        <select class="form-select" name="escola">
                            <?php
                            option('', 'Todas as escolas', $escola);
                            foreach ($escolas as $v) option($v['nome_escola'], $v['nome_escola'], $escola);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Série</label>
                        <select class="form-select" name="serie">
                            <?php
                            option('', 'Todas as séries', $serie);
                            foreach ($series as $v) option($v['serie_escolar'], $v['serie_escolar'], $serie);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Cidade</label>
                        <input name="cidade" class="form-control" value="<?= e($cidade) ?>" placeholder="Cidade de residência">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Ano</label>
                        <select name="ano" class="form-select">
                            <?php
                            option('', 'Todos', $ano);
                            foreach (range(2026, 2030) as $v) option((string)$v, (string)$v, $ano);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Semestre</label>
                        <select name="semestre" class="form-select">
                            <?php
                            option('', 'Todos', $sem);
                            option('1', '1º semestre', $sem);
                            option('2', '2º semestre', $sem);
                            ?>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2 pt-2">
                        <button class="btn btn-primary px-4 fw-semibold">Aplicar filtros</button>
                        <a class="btn btn-outline-secondary px-3" href="painel.php">Limpar filtros</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-light border shadow-sm mb-4 fw-medium text-dark bg-white">
            <strong><?= $total ?></strong> inscrição(ões) encontrada(s)
            <?php if ($total > 200): ?> — exibindo as 200 mais recentes<?php endif; ?>
        </div>

        <!-- TABELA DE RESULTADOS -->
        <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 text-dark">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th class="py-3 ps-3">Aluno</th>
                                <th class="py-3">Escola</th>
                                <th class="py-3">Série</th>
                                <th class="py-3">Cidade</th>
                                <th class="py-3">Período</th>
                                <th class="py-3">Situação</th>
                                <th class="py-3 text-end pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark"><?= e($r['nome']) ?></td>
                                    <td><?= e($r['nome_escola']) ?></td>
                                    <td><?= e($r['serie_escolar']) ?></td>
                                    <td><?= e($r['cidade']) ?></td>
                                    <td><?= e($r['semestre'] . 'º/' . $r['ano']) ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><?= e($r['situacao']) ?></span></td>
                                    <td class="text-end pe-3">
                                        <a class="btn btn-sm btn-outline-primary px-3" href="detalhes.php?id=<?= (int)$r['id_inscricao'] ?>">Detalhes</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$rows): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Nenhuma inscrição encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer>
            Galera Tech • Painel Administrativo
        </footer>
    </main>
</body>

</html>