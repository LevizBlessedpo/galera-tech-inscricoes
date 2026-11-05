
<?php

// ==========================================================
// 1. IMPORTAÇÃO DO ARQUIVO DE CONFIGURAÇÃO
// ==========================================================

// Importa o arquivo config.php, localizado na pasta anterior.
//
// __DIR__ representa a pasta onde este arquivo está salvo.
//
// dirname(__DIR__) retorna a pasta imediatamente superior.
//
// O operador . concatena (junta) textos.
//
// require_once garante que o arquivo seja incluído
// apenas uma vez durante a execução.
//
// O config.php contém:
// - Conexão com o banco de dados.
// - Configuração das sessões.
// - Funções de segurança.
// - Função para gerar e validar o token CSRF.
require_once dirname(__DIR__) . '/config.php';


// ==========================================================
// 2. VERIFICAÇÃO DE ADMINISTRADOR JÁ AUTENTICADO
// ==========================================================

// Verifica se já existe um administrador autenticado.
//
// $_SESSION armazena informações da sessão do usuário.
//
// admin_id guarda o identificador do administrador.
//
// empty() verifica se uma variável está vazia.
if (!empty($_SESSION['admin_id'])) {

    // Redireciona o administrador para o painel.
    //
    // header() envia um cabeçalho HTTP ao navegador.
    // Location informa a página de destino.
    header('Location: painel.php');

    // Encerra imediatamente a execução do script.
    exit;
}


// ==========================================================
// 3. VARIÁVEL PARA MENSAGENS DE ERRO
// ==========================================================

// Cria uma variável inicialmente vazia.
//
// Ela armazenará uma mensagem caso o login não seja válido.
$erro = '';


// ==========================================================
// 4. VERIFICAÇÃO DO ENVIO DO FORMULÁRIO
// ==========================================================

// $_SERVER contém informações sobre a requisição HTTP.
//
// REQUEST_METHOD informa o método utilizado.
//
// GET: normalmente utilizado para consultar páginas.
// POST: normalmente utilizado para enviar dados.
//
// === compara o valor e o tipo.
//
// O código abaixo será executado somente quando
// o formulário for enviado pelo método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------------------------------------------
    // 4.1. VALIDAÇÃO DO TOKEN CSRF
    // ------------------------------------------------------

    // Executa a função definida no config.php.
    //
    // Verifica se o formulário enviou o token correto.
    //
    // Ajuda a proteger o sistema contra requisições
    // não autorizadas originadas de outros sites.
    verify_token();


    // ------------------------------------------------------
    // 4.2. RECEBIMENTO DO E-MAIL
    // ------------------------------------------------------

    // $_POST contém os dados enviados pelo formulário.
    //
    // $_POST['email'] acessa o campo chamado "email".
    //
    // ?? '' retorna uma string vazia caso o campo
    // não exista ou tenha valor null.
    //
    // (string) converte o valor para texto.
    //
    // trim() remove espaços no início e no final.
    $email = trim((string)($_POST['email'] ?? ''));


    // ------------------------------------------------------
    // 4.3. RECEBIMENTO DA SENHA
    // ------------------------------------------------------

    // Recebe a senha digitada pelo administrador.
    //
    // A senha permanece com seu conteúdo original,
    // sem aplicar trim(), pois espaços podem fazer
    // parte de uma senha válida.
    $senha = (string)($_POST['senha'] ?? '');


    // ======================================================
    // 5. CONSULTA AO BANCO DE DADOS
    // ======================================================

    // db() retorna a conexão PDO com o banco MySQL.
    //
    // prepare() prepara uma instrução SQL.
    //
    // SELECT seleciona dados da tabela.
    //
    // id_usuario: identificador do administrador.
    // nome: nome do administrador.
    // senha_hash: senha armazenada em formato hash.
    //
    // FROM indica a tabela consultada.
    //
    // WHERE filtra o registro pelo e-mail.
    //
    // O símbolo ? representa um parâmetro que será
    // preenchido posteriormente.
    //
    // Esse procedimento ajuda a prevenir SQL Injection.
    $q = db()->prepare(
        'SELECT id_usuario, nome, senha_hash
         FROM usuarios_admin
         WHERE email = ?'
    );


    // Executa a consulta SQL preparada.
    //
    // O array [$email] informa o valor que substituirá
    // o parâmetro ? da consulta.
    $q->execute([$email]);


    // Recupera o primeiro registro encontrado.
    //
    // fetch() retorna os dados do administrador.
    //
    // Como o PDO está configurado com FETCH_ASSOC,
    // o resultado será um array associativo.
    //
    // Exemplo:
    // $u['id_usuario']
    // $u['nome']
    // $u['senha_hash']
    //
    // Se nenhum registro for encontrado, retorna false.
    $u = $q->fetch();


    // ======================================================
    // 6. VERIFICAÇÃO DAS CREDENCIAIS
    // ======================================================

    // Verifica duas condições:
    //
    // 1. $u:
    // verifica se o administrador foi encontrado.
    //
    // 2. password_verify():
    // compara a senha digitada com o hash armazenado
    // no banco de dados.
    //
    // && representa o operador lógico E.
    //
    // As duas condições precisam ser verdadeiras.
    if (
        $u &&
        password_verify($senha, $u['senha_hash'])
    ) {

        // --------------------------------------------------
        // 6.1. RENOVAÇÃO DO IDENTIFICADOR DA SESSÃO
        // --------------------------------------------------

        // Cria um novo identificador de sessão.
        //
        // O parâmetro true solicita a exclusão dos dados
        // associados ao identificador antigo da sessão.
        //
        // Ajuda a prevenir ataques de fixação de sessão.
        session_regenerate_id(true);


        // --------------------------------------------------
        // 6.2. ARMAZENAMENTO DOS DADOS DO ADMINISTRADOR
        // --------------------------------------------------

        // Salva na sessão o ID do administrador autenticado.
        //
        // Esse valor será utilizado para verificar
        // se o administrador pode acessar outras páginas.
        $_SESSION['admin_id'] = $u['id_usuario'];


        // Salva o nome do administrador na sessão.
        //
        // Pode ser utilizado para exibir mensagens como:
        // "Bem-vindo, Administrador".
        $_SESSION['admin_nome'] = $u['nome'];


        // --------------------------------------------------
        // 6.3. REDIRECIONAMENTO PARA O PAINEL
        // --------------------------------------------------

        // Redireciona para a página administrativa.
        header('Location: painel.php');

        // Interrompe a execução após o redirecionamento.
        exit;
    }


    // ======================================================
    // 7. TRATAMENTO DE LOGIN INVÁLIDO
    // ======================================================

    // Se o administrador não foi encontrado
    // ou a senha está incorreta, define uma mensagem.
    //
    // A mensagem é genérica por segurança:
    // não informa se o e-mail existe no sistema.
    $erro = 'E-mail ou senha inválidos.';
}

// Encerra o bloco PHP para iniciar o HTML.
?>

<!-- ===================================================== -->
<!-- 8. ESTRUTURA INICIAL DO DOCUMENTO HTML -->
<!-- ===================================================== -->

<!-- Define que o documento utiliza HTML5. -->
<!doctype html>

<!-- Define o idioma principal da página como português. -->
<html lang="pt-BR">

<head>

    <!-- Define a codificação de caracteres como UTF-8. -->
    <!-- Permite exibir corretamente acentos e símbolos. -->
    <meta charset="utf-8">

    <!-- Configura a visualização em dispositivos móveis. -->
    <!-- width=device-width adapta à largura da tela. -->
    <!-- initial-scale=1 define o zoom inicial. -->
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <!-- Define o título exibido na aba do navegador. -->
    <title>Acesso administrativo</title>

    <!-- Importa o CSS do Bootstrap versão 5.3.3. -->
    <!-- Bootstrap facilita a criação de interfaces -->
    <!-- responsivas e visualmente padronizadas. -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    >

</head>


<!-- ===================================================== -->
<!-- 9. CORPO DA PÁGINA -->
<!-- ===================================================== -->

<!-- bg-light aplica um fundo claro do Bootstrap. -->
<body class="bg-light">

    <!-- main representa o conteúdo principal da página. -->
    <!-- container cria uma área de conteúdo responsiva. -->
    <!-- py-5 adiciona espaçamento vertical. -->
    <!-- max-width limita a largura da área a 480 pixels. -->
    <main
        class="container py-5"
        style="max-width:480px"
    >

        <!-- card cria um componente visual semelhante -->
        <!-- a um cartão ou painel. -->
        <!-- shadow-sm aplica uma sombra discreta. -->
        <!-- border-0 remove a borda padrão. -->
        <div class="card shadow-sm border-0">

            <!-- card-body define a área interna do cartão. -->
            <!-- p-4 adiciona espaçamento interno. -->
            <div class="card-body p-4">

                <!-- h1 define o título principal da página. -->
                <!-- h4 aplica o tamanho visual de um h4. -->
                <h1 class="h4">
                    Galera Tech — Administração
                </h1>

                <!-- Apresenta uma descrição do acesso. -->
                <!-- text-muted aplica uma cor mais discreta. -->
                <p class="text-muted">
                    Acesso restrito ao painel.
                </p>


                <!-- ===================================== -->
                <!-- 10. EXIBIÇÃO DA MENSAGEM DE ERRO -->
                <!-- ===================================== -->

                <?php
                // Verifica se existe uma mensagem de erro.
                //
                // Se $erro não estiver vazio,
                // o HTML da mensagem será exibido.
                if ($erro):
                ?>

                    <!-- alert cria um componente de aviso. -->
                    <!-- alert-danger aplica o estilo de erro. -->
                    <div class="alert alert-danger">

                        <?php
                        // Exibe a mensagem de erro.
                        //
                        // e() é uma função criada no config.php.
                        //
                        // Ela transforma caracteres especiais
                        // em entidades HTML, ajudando a prevenir XSS.
                        echo e($erro);
                        ?>

                    </div>

                <?php
                // Finaliza a condição if.
                endif;
                ?>


                <!-- ===================================== -->
                <!-- 11. FORMULÁRIO DE LOGIN -->
                <!-- ===================================== -->

                <!-- form cria um formulário HTML. -->
                <!-- method="post" envia os dados via POST. -->
                <!-- Sem action, envia à própria página. -->
                <form method="post">


                    <!-- ================================= -->
                    <!-- 11.1. TOKEN CSRF -->
                    <!-- ================================= -->

                    <!-- type="hidden" cria um campo oculto. -->
                    <!-- name="csrf" identifica o dado enviado. -->
                    <!-- token() gera ou recupera o token -->
                    <!-- armazenado na sessão PHP. -->
                    <!-- e() protege a saída no HTML. -->
                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= e(token()) ?>"
                    >


                    <!-- ================================= -->
                    <!-- 11.2. CAMPO DE E-MAIL -->
                    <!-- ================================= -->

                    <!-- label identifica o campo de e-mail. -->
                    <!-- for="email" associa a label ao input. -->
                    <!-- form-label aplica o estilo Bootstrap. -->
                    <label
                        class="form-label"
                        for="email"
                    >
                        E-mail
                    </label>

                    <!-- required exige o preenchimento. -->
                    <!-- type="email" ativa a validação básica -->
                    <!-- de formato de e-mail do navegador. -->
                    <!-- id identifica o campo no HTML. -->
                    <!-- name identifica o dado enviado ao PHP. -->
                    <!-- form-control aplica o estilo Bootstrap. -->
                    <!-- mb-3 adiciona margem inferior. -->
                    <input
                        required
                        type="email"
                        id="email"
                        name="email"
                        class="form-control mb-3"
                    >


                    <!-- ================================= -->
                    <!-- 11.3. CAMPO DE SENHA -->
                    <!-- ================================= -->

                    <!-- Cria a identificação visual da senha. -->
                    <label
                        class="form-label"
                        for="senha"
                    >
                        Senha
                    </label>

                    <!-- type="password" oculta visualmente -->
                    <!-- os caracteres digitados. -->
                    <!-- required torna o campo obrigatório. -->
                    <input
                        required
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control mb-3"
                    >


                    <!-- ================================= -->
                    <!-- 11.4. BOTÃO DE ENTRADA -->
                    <!-- ================================= -->

                    <!-- btn aplica o estilo de botão. -->
                    <!-- btn-primary aplica a cor principal. -->
                    <!-- w-100 faz o botão ocupar 100% -->
                    <!-- da largura disponível. -->
                    <!-- Por padrão, o botão dentro de um -->
                    <!-- formulário funciona como submit. -->
                    <button class="btn btn-primary w-100">
                        Entrar
                    </button>

                <!-- Finaliza o formulário de login. -->
                </form>

            <!-- Finaliza a área interna do cartão. -->
            </div>

        <!-- Finaliza o cartão. -->
        </div>

    <!-- Finaliza o conteúdo principal. -->
    </main>

<!-- Finaliza o corpo da página. -->
</body>

<!-- Finaliza o documento HTML. -->
</html>
 