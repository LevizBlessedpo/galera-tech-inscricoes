<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
verify_token();
$d = $_POST;
$errors = [];
$nome = required_str($d, 'nome', 150, 'Nome', $errors);
$nascimento = required_str($d, 'data_nascimento', 10, 'Nascimento', $errors);
$dt = DateTimeImmutable::createFromFormat('!Y-m-d', $nascimento);
if (!$dt || $dt->format('Y-m-d') !== $nascimento || $dt > new DateTimeImmutable('today')) $errors[] = 'Nascimento inválido.';
$idade = $dt && $dt <= new DateTimeImmutable('today') ? $dt->diff(new DateTimeImmutable('today'))->y : null;
$cpf = digits((string)($d['cpf'] ?? ''));
if (!cpf_ok($cpf)) $errors[] = 'CPF inválido.';
$rg = required_str($d, 'rg', 30, 'RG', $errors);
$sexo = required_str($d, 'sexo', 30, 'Sexo', $errors);
if (!in_array($sexo, ['Masculino', 'Feminino', 'Não desejo informar'], true)) $errors[] = 'Sexo inválido.';
$email = required_str($d, 'email', 150, 'E-mail', $errors);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
$telefone = digits((string)($d['telefone'] ?? ''));
if (strlen($telefone) !== 11) $errors[] = 'WhatsApp do aluno deve ter 11 dígitos.';
$reside = required_str($d, 'reside_rio_preto', 3, 'Residência', $errors);
if (!in_array($reside, ['sim', 'nao'], true)) $errors[] = 'Residência inválida.';
$cep = digits((string)($d['cep'] ?? ''));
if (strlen($cep) !== 8) $errors[] = 'CEP inválido.';
$logradouro = required_str($d, 'logradouro', 150, 'Logradouro', $errors);
$numero = required_str($d, 'numero', 15, 'Número', $errors);
$complemento = trim((string)($d['complemento'] ?? ''));
if (mb_strlen($complemento) > 100) $errors[] = 'Complemento longo demais.';
$bairro = required_str($d, 'bairro', 100, 'Bairro', $errors);
$cidade = required_str($d, 'cidade', 100, 'Cidade', $errors);
$estado = required_str($d, 'estado', 2, 'Estado', $errors);
$ufs = explode(' ', 'AC AL AP AM BA CE DF ES GO MA MT MS MG PA PB PR PE PI RJ RN RS RO RR SC SP SE TO');
if (!in_array($estado, $ufs, true)) $errors[] = 'Estado inválido.';
$escola = required_str($d, 'nome_escola', 150, 'Escola', $errors);
$serie = required_str($d, 'serie_escolar', 80, 'Série', $errors);
$origem = required_str($d, 'origem_divulgacao', 100, 'Origem da divulgação', $errors);
$opcoes = ['Instagram / Redes sociais', 'Escola (Diretoria / Professores)', 'Amigos ou familiares', 'Anúncio no rádio ou TV', 'Outros'];
if (!in_array($origem, $opcoes, true)) $errors[] = 'Origem da divulgação inválida.';
$respn = trim((string)($d['responsavel_nome'] ?? ''));
$parentesco = trim((string)($d['parentesco'] ?? ''));
$respt = digits((string)($d['responsavel_telefone'] ?? ''));
$temResp = $respn !== '' || $parentesco !== '' || $respt !== '';
if (($idade !== null && $idade < 18) || $temResp) {
    if ($respn === '' || mb_strlen($respn) > 150) $errors[] = 'Informe o nome do responsável.';
    if (!in_array($parentesco, ['Mãe', 'Pai', 'Tutor(a) / Responsável legal', 'Outro'], true)) $errors[] = 'Informe o parentesco.';
    if (strlen($respt) !== 11) $errors[] = 'Informe WhatsApp válido do responsável.';
}
foreach (['ciencia_programa', 'ciencia_transporte', 'declarou_veracidade', 'ciencia_privacidade'] as $key) if (!isset($d[$key]) || $d[$key] !== 'on') $errors[] = 'Confirmação obrigatória: ' . $key;
function page_error(array $messages, int $status): void
{
    http_response_code($status);
    echo '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><title>Inscrição</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><main class="container my-5"><h1>Não foi possível concluir</h1><div class="alert alert-warning"><ul>';
    foreach ($messages as $m) echo '<li>' . e($m) . '</li>';
    echo '</ul></div><a href="index.php" class="btn btn-primary">Voltar ao formulário</a></main></html>';
    exit;
}
if ($errors) page_error($errors, 422);
try {
    $pdo = db();
    $pdo->beginTransaction();
    $q = $pdo->prepare('SELECT id_aluno FROM alunos WHERE cpf=? FOR UPDATE');
    $q->execute([$cpf]);
    $id = $q->fetchColumn();
    if ($id) {
        $q = $pdo->prepare('SELECT COUNT(*) FROM inscricoes WHERE aluno_id=? AND ano=? AND semestre=?');
        $q->execute([$id, ANO_INSCRICAO, SEMESTRE_INSCRICAO]);
        if ($q->fetchColumn()) throw new RuntimeException('Já existe inscrição para este CPF neste semestre.');
        throw new RuntimeException('CPF já cadastrado em outro período. Procure a administração para atualizar o cadastro.');
    }
    $respId = null;
    if ($temResp) {
        $q = $pdo->prepare('INSERT INTO responsaveis(nome,parentesco,telefone) VALUES(?,?,?)');
        $q->execute([$respn, $parentesco, $respt]);
        $respId = (int)$pdo->lastInsertId();
    }
    $q = $pdo->prepare('INSERT INTO alunos(nome,data_nascimento,cpf,rg,sexo,email,telefone,reside_rio_preto,cep,logradouro,numero,complemento,bairro,cidade,estado,nome_escola,serie_escolar,origem_divulgacao,responsavel_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $q->execute([$nome, $nascimento, $cpf, $rg, $sexo, $email, $telefone, $reside, $cep, $logradouro, $numero, $complemento ?: null, $bairro, $cidade, $estado, $escola, $serie, $origem, $respId]);
    $id = (int)$pdo->lastInsertId();
    $q = $pdo->prepare('INSERT INTO inscricoes(aluno_id,ano,semestre,ciencia_programa,ciencia_transporte,declarou_veracidade,ciencia_privacidade) VALUES(?,?,?,?,?,?,?)');
    $q->execute([$id, ANO_INSCRICAO, SEMESTRE_INSCRICAO, 1, 1, 1, 1]);
    $pdo->commit();
    unset($_SESSION['csrf']);
    header('Location: sucesso.php');
    exit;
} catch (Throwable $ex) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    $msg = $ex instanceof RuntimeException ? $ex->getMessage() : 'Erro ao salvar inscrição. Confira o banco e a configuração do sistema.';
    page_error([$msg], 400);
}
