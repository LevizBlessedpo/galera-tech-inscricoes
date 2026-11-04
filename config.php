<?php

// ==========================================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// ==========================================================

// Define o endereço do servidor onde o MySQL está instalado.
// 127.0.0.1 indica que o banco está no próprio computador.
const DB_HOST = '127.0.0.1';

// Define o nome do banco de dados que será utilizado.
const DB_NAME = 'galera_tech';

// Define o usuário responsável pela conexão com o MySQL.
// No Laragon, o usuário padrão geralmente é "root".
const DB_USER = 'root';

// Define a senha de acesso ao banco de dados.
// No Laragon, em instalações locais, pode estar vazia.
// Em produção, utilize credenciais seguras.
const DB_PASS = '';

// Define o ano em que as inscrições serão realizadas.
const ANO_INSCRICAO = 2027;

// Define o semestre das inscrições.
// 1 representa o primeiro semestre.
const SEMESTRE_INSCRICAO = 1;


// ==========================================================
// CONTROLE DE SESSÃO
// ==========================================================

// Verifica se ainda não existe uma sessão PHP ativa.
if (session_status() !== PHP_SESSION_ACTIVE) {

    // Inicia uma nova sessão com configurações de segurança.
    session_start([

        // Impede que o JavaScript acesse o cookie da sessão.
        'cookie_httponly' => true,

        // Restringe o envio do cookie em requisições entre sites.
        'cookie_samesite' => 'Lax',

        // Exige um identificador válido para aceitar a sessão.
        'use_strict_mode' => true

    ]);
}


// ==========================================================
// FUNÇÃO DE CONEXÃO COM O BANCO DE DADOS
// ==========================================================

// Declara a função db(), que retorna uma conexão PDO.
function db(): PDO {

    // Cria uma variável estática para armazenar a conexão.
    // Ela mantém seu valor entre chamadas da função.
    static $pdo = null;

    // Verifica se a conexão ainda não foi criada.
    if ($pdo === null) {

        // Cria uma conexão com o banco de dados usando PDO.
        $pdo = new PDO(

            // Monta a string de conexão com MySQL.
            // Informa servidor, banco de dados e codificação.
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',

            // Informa o usuário do banco de dados.
            DB_USER,

            // Informa a senha do banco de dados.
            DB_PASS,

            // Define configurações adicionais para o PDO.
            [

                // Faz o PDO lançar exceções quando ocorrerem erros.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Retorna consultas como arrays associativos.
                // Exemplo: $aluno['nome'].
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Desativa a emulação de prepared statements.
                // Utiliza consultas preparadas nativas do driver.
                PDO::ATTR_EMULATE_PREPARES => false,

            ]
        );
    }

    // Retorna a conexão existente ou recém-criada.
    return $pdo;
}


// ==========================================================
// FUNÇÃO DE PROTEÇÃO PARA EXIBIÇÃO DE TEXTOS
// ==========================================================

// Recebe um texto e retorna uma versão segura para HTML.
function e(?string $v): string {

    // Converte caracteres especiais em entidades HTML.
    // ENT_QUOTES converte aspas simples e duplas.
    // ENT_SUBSTITUTE substitui sequências UTF-8 inválidas.
    // UTF-8 define a codificação utilizada.
    return htmlspecialchars(
        (string)$v,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// ==========================================================
// FUNÇÃO PARA GERAR TOKEN DE SEGURANÇA CSRF
// ==========================================================

// Declara uma função que retorna um token de segurança.
function token(): string {

    // Verifica se ainda não existe um token na sessão.
    if (empty($_SESSION['csrf'])) {

        // Gera 32 bytes aleatórios com segurança criptográfica.
        // bin2hex converte os bytes para uma string hexadecimal.
        // O resultado possui 64 caracteres.
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    // Retorna o token armazenado na sessão.
    return $_SESSION['csrf'];
}


// ==========================================================
// FUNÇÃO PARA VALIDAR O TOKEN CSRF
// ==========================================================

// Verifica se o formulário enviou um token válido.
function verify_token(): void {

    // Verifica se o token não foi enviado OU se é diferente
    // do token armazenado na sessão.
    if (
        !isset($_POST['csrf']) ||
        !hash_equals(token(), (string)$_POST['csrf'])
    ) {

        // Define o código HTTP 403: acesso proibido.
        http_response_code(403);

        // Encerra a execução e apresenta uma mensagem.
        exit('Sessão inválida. Recarregue a página.');
    }
}


// ==========================================================
// FUNÇÃO DE AUTENTICAÇÃO DO ADMINISTRADOR
// ==========================================================

// Verifica se existe um administrador autenticado.
function auth(): void {

    // Consulta o identificador do administrador na sessão.
    if (empty($_SESSION['admin_id'])) {

        // Redireciona para a página de login.
        header('Location: login.php');

        // Interrompe a execução do código.
        exit;
    }
}


// ==========================================================
// FUNÇÃO PARA MANTER APENAS NÚMEROS
// ==========================================================

// Recebe um texto e retorna somente os dígitos.
function digits(string $v): string {

    // \D representa qualquer caractere que não seja dígito.
    // O sinal + identifica um ou mais caracteres consecutivos.
    // preg_replace substitui esses caracteres por vazio.
    return preg_replace('/\D+/', '', $v);
}


// ==========================================================
// FUNÇÃO PARA VALIDAR CPF
// ==========================================================

// Recebe um CPF e retorna true (válido) ou false (inválido).
function cpf_ok(string $v): bool {

    // Remove pontos, traços, espaços e outros caracteres.
    $v = digits($v);

    // Verifica se o CPF não possui exatamente 11 dígitos
    // OU se todos os dígitos são iguais.
    if (
        !preg_match('/^\d{11}$/', $v) ||
        preg_match('/^(\d)\1{10}$/', $v)
    ) {

        // Se alguma condição for verdadeira, o CPF é inválido.
        return false;
    }

    // Executa duas verificações:
    // p = 9 verifica o primeiro dígito.
    // p = 10 verifica o segundo dígito.
    for ($p = 9; $p <= 10; $p++) {

        // Inicializa a soma utilizada no cálculo.
        $sum = 0;

        // Percorre os dígitos necessários para a validação.
        for ($i = 0; $i < $p; $i++) {

            // Multiplica o dígito pelo peso correspondente
            // e acrescenta o resultado à soma.
            $sum += (int)$v[$i] * ($p + 1 - $i);
        }

        // Calcula o dígito verificador.
        // % retorna o resto da divisão.
        $digit = ($sum * 10) % 11;

        // Compara o dígito calculado com o dígito informado.
        // Se o resultado for 10, considera o dígito como 0.
        if (
            (int)$v[$p] !== ($digit === 10 ? 0 : $digit)
        ) {

            // Se os valores forem diferentes, o CPF é inválido.
            return false;
        }
    }

    // Se todas as verificações passaram, o CPF é válido.
    return true;
}


// ==========================================================
// FUNÇÃO PARA VALIDAR CAMPOS OBRIGATÓRIOS
// ==========================================================

// Recebe:
// $data: dados enviados pelo formulário.
// $key: nome do campo que será validado.
// $max: quantidade máxima de caracteres permitidos.
// $label: nome amigável do campo.
// &$errors: array que armazena mensagens de erro.
//
// Retorna o texto recebido após remover espaços das extremidades.
function required_str(
    array $data,
    string $key,
    int $max,
    string $label,
    array &$errors
): string {

    // Obtém o valor do campo informado.
    // ?? retorna uma string vazia se o campo não existir
    // ou estiver com valor null.
    // trim remove espaços no início e no final.
    $s = trim((string)($data[$key] ?? ''));

    // Verifica se o campo está vazio.
    if ($s === '') {

        // Adiciona uma mensagem ao array de erros.
        $errors[] = "$label é obrigatório(a).";
    }

    // Verifica se a quantidade de caracteres excede o limite.
    // mb_strlen considera caracteres multibyte, como acentos.
    if (mb_strlen($s) > $max) {

        // Adiciona uma mensagem informando o problema.
        $errors[] = "$label ultrapassou o tamanho máximo.";
    }

    // Retorna o conteúdo do campo após o tratamento.
    return $s;
}
