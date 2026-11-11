<?php require_once __DIR__ . "/config.php"; ?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Galera Tech | Inscrição</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@400;500;600;700" rel="stylesheet" />
</head>

<body>

  <!-- HEADER FIXO (STICKY TOP) COM LOGO NA ESQUERDA E TEXTO CENTRALIZADO -->
  <header class="cabecalho sticky-top">
    <div class="container-fluid px-3 px-md-4">
      <div class="row align-items-center">
        <!-- Esquerda: Logo -->
        <div class="col-auto col-md-3 text-start">
          <img src="img/galera_tech_logo.png" alt="Galera Tech" class="cabecalho-logo-img">
        </div>
        <!-- Centro: Título do Formulário alinhado no meio do container -->
        <div class="col text-center me-auto">
          <h1 class="h4 mb-0 cabecalho-titulo">Galera Tech — Formulário de Inscrição</h1>
          <p class="mb-0 small cabecalho-subtitulo">Preencha seus dados para realizar sua inscrição</p>
        </div>
        <!-- Direita: Espaçador para balanço no Desktop -->
        <div class="col-md-3 d-none d-md-block"></div>
      </div>
    </div>
  </header>

  <main class="container py-3">
    <!-- Aviso Institucional -->
    <div class="alert alert-info-custom shadow-sm mb-3" role="note">
      <strong>Sobre o Galera Tech:</strong> capacitação gratuita em tecnologia, com aulas presenciais à tarde, <strong>exclusiva para estudantes do Ensino Médio de escolas públicas</strong>. Antes de se inscrever, leia as condições do programa e confirme sua ciência na seção final.
    </div>

    <!-- Toast de Sucesso Dinâmico -->
    <div id="mensagemSucesso" class="alert alert-success alert-dismissible fade show d-none" role="alert">
      <strong>Inscrição enviada!</strong> Seus dados foram salvos com sucesso no sistema.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="wizard-wrapper">
      <form id="formInscricao" method="post" action="salvar_inscricao.php" novalidate>
        <input type="hidden" name="csrf" value="<?= e(token()) ?>">

        <!-- ETAPA 1 -->
        <section class="card secao step-active">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><span class="numero">1</span>Dados pessoais do estudante</h2>
            <span class="badge bg-light text-primary border fw-semibold">Etapa 1 de 5</span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="nome" class="form-label">Nome completo <span class="obrigatorio">*</span></label>
                <input class="form-control" id="nome" name="nome" autocomplete="name" maxlength="150" required>
                <div class="invalid-feedback">Informe o nome completo.</div>
              </div>
              <div class="col-md-4">
                <label for="nascimento" class="form-label">Data de nascimento <span class="obrigatorio">*</span></label>
                <input type="date" class="form-control" id="nascimento" name="data_nascimento" required>
                <div class="invalid-feedback">Informe uma data de nascimento válida.</div>
              </div>
              <div class="col-md-6">
                <label for="cpf" class="form-label">CPF <span class="obrigatorio">*</span></label>
                <input class="form-control" id="cpf" name="cpf" inputmode="numeric" placeholder="000.000.000-00" maxlength="14" required>
                <div class="invalid-feedback">Informe um CPF válido.</div>
              </div>
              <div class="col-md-6">
                <label for="rg" class="form-label">RG / Documento de identidade <span class="obrigatorio">*</span></label>
                <input class="form-control" id="rg" name="rg" maxlength="30" required>
                <div class="invalid-feedback">Informe o documento de identidade.</div>
              </div>
              <div class="col-md-4">
                <label for="sexo" class="form-label">Sexo <span class="obrigatorio">*</span></label>
                <select class="form-select" id="sexo" name="sexo" required>
                  <option value="">Selecione</option>
                  <option>Masculino</option>
                  <option>Feminino</option>
                  <option>Não desejo informar</option>
                </select>
                <div class="invalid-feedback">Selecione uma opção.</div>
              </div>
              <div class="col-md-4">
                <label for="email" class="form-label">E-mail <span class="obrigatorio">*</span></label>
                <input type="email" class="form-control" id="email" name="email" maxlength="150" autocomplete="email" required>
                <div class="invalid-feedback">Informe um e-mail válido.</div>
              </div>
              <div class="col-md-4">
                <label for="telefone" class="form-label">WhatsApp do estudante (com DDD) <span class="obrigatorio">*</span></label>
                <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(17) 99999-9999" maxlength="15" inputmode="numeric" required>
                <div class="invalid-feedback">Informe um celular com DDD (11 dígitos).</div>
              </div>
            </div>
          </div>
        </section>

        <!-- ETAPA 2 -->
        <section class="card secao">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><span class="numero">2</span>Residência e endereço</h2>
            <span class="badge bg-light text-primary border fw-semibold">Etapa 2 de 5</span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <label for="resideRioPreto" class="form-label">Você reside em São José do Rio Preto? <span class="obrigatorio">*</span></label>
                <select id="resideRioPreto" name="reside_rio_preto" class="form-select" required>
                  <option value="">Selecione</option>
                  <option value="sim">Sim</option>
                  <option value="nao">Não</option>
                </select>
                <div class="invalid-feedback">Informe se reside em São José do Rio Preto.</div>
              </div>
              <div class="col-md-3">
                <label for="cep" class="form-label">CEP <span class="obrigatorio">*</span></label>
                <input class="form-control" id="cep" name="cep" placeholder="00000-000" maxlength="9" inputmode="numeric" required>
                <div class="invalid-feedback">Informe um CEP com 8 dígitos.</div>
              </div>
              <div class="col-md-7">
                <label for="logradouro" class="form-label">Logradouro <span class="obrigatorio">*</span></label>
                <input class="form-control" id="logradouro" name="logradouro" maxlength="150" placeholder="Rua, avenida..." required>
                <div class="invalid-feedback">Informe o logradouro.</div>
              </div>
              <div class="col-md-2">
                <label for="numero" class="form-label">Número <span class="obrigatorio">*</span></label>
                <input class="form-control" id="numero" name="numero" maxlength="15" required>
                <div class="invalid-feedback">Informe o número ou S/N.</div>
              </div>
              <div class="col-md-5">
                <label for="complemento" class="form-label">Complemento</label>
                <input class="form-control" id="complemento" name="complemento" maxlength="100">
              </div>
              <div class="col-md-4">
                <label for="bairro" class="form-label">Bairro <span class="obrigatorio">*</span></label>
                <input class="form-control" id="bairro" name="bairro" maxlength="100" required>
                <div class="invalid-feedback">Informe o bairro.</div>
              </div>
              <div class="col-md-5">
                <label for="cidade" class="form-label">Cidade <span class="obrigatorio">*</span></label>
                <input class="form-control" id="cidade" name="cidade" maxlength="100" required>
                <div class="invalid-feedback">Informe a cidade.</div>
              </div>
              <div class="col-md-3">
                <label for="estado" class="form-label">Estado (UF) <span class="obrigatorio">*</span></label>
                <select class="form-select" id="estado" name="estado" required>
                  <option value="">Selecione</option>
                  <option value="AC">Acre (AC)</option><option value="AL">Alagoas (AL)</option><option value="AP">Amapá (AP)</option><option value="AM">Amazonas (AM)</option><option value="BA">Bahia (BA)</option><option value="CE">Ceará (CE)</option><option value="DF">Distrito Federal (DF)</option><option value="ES">Espírito Santo (ES)</option><option value="GO">Goiás (GO)</option><option value="MA">Maranhão (MA)</option><option value="MT">Mato Grosso (MT)</option><option value="MS">Mato Grosso do Sul (MS)</option><option value="MG">Minas Gerais (MG)</option><option value="PA">Pará (PA)</option><option value="PB">Paraíba (PB)</option><option value="PR">Paraná (PR)</option><option value="PE">Pernambuco (PE)</option><option value="PI">Piauí (PI)</option><option value="RJ">Rio de Janeiro (RJ)</option><option value="RN">Rio Grande do Norte (RN)</option><option value="RS">Rio Grande do Sul (RS)</option><option value="RO">Rondônia (RO)</option><option value="RR">Roraima (RR)</option><option value="SC">Santa Catarina (SC)</option><option value="SP">São Paulo (SP)</option><option value="SE">Sergipe (SE)</option><option value="TO">Tocantins (TO)</option>
                </select>
                <div class="invalid-feedback">Selecione o estado.</div>
              </div>
            </div>
          </div>
        </section>

        <!-- ETAPA 3 -->
        <section class="card secao">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><span class="numero">3</span>Informações escolares</h2>
            <span class="badge bg-light text-primary border fw-semibold">Etapa 3 de 5</span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="escola" class="form-label">Nome completo da escola de origem <span class="obrigatorio">*</span></label>
                <input type="text" class="form-control" id="escola" name="nome_escola" maxlength="150" placeholder="Digite o nome da escola" required>
                <div class="invalid-feedback">Informe o nome da escola.</div>
              </div>
              <div class="col-md-6">
                <label for="serie" class="form-label">Série / ano escolar <span class="obrigatorio">*</span></label>
                <input type="text" class="form-control" id="serie" name="serie_escolar" placeholder="Ex.: 2º ano do Ensino Médio" maxlength="80" required>
                <div class="invalid-feedback">Informe a série ou ano escolar.</div>
              </div>
              <div class="col-md-6">
                <label for="origemDivulgacao" class="form-label">Por onde conheceu o Galera Tech? <span class="obrigatorio">*</span></label>
                <select class="form-select" id="origemDivulgacao" name="origem_divulgacao" required>
                  <option value="">Selecione</option>
                  <option>Instagram / Redes sociais</option>
                  <option>Escola (Diretoria / Professores)</option>
                  <option>Amigos ou familiares</option>
                  <option>Anúncio no rádio ou TV</option>
                  <option>Outros</option>
                </select>
                <div class="invalid-feedback">Selecione como conheceu o projeto.</div>
              </div>
            </div>
            <div class="alert alert-primary mt-3 mb-0" role="note" id="informacaoTransporte">
              <strong>Transporte e deslocamento:</strong> o auxílio-transporte (cartão Circular Santa Luzia), quando disponibilizado pelo programa, é destinado aos estudantes matriculados em escolas do município de São José do Rio Preto. Para residentes de outras cidades, os custos e a logística de deslocamento às atividades presenciais ficam sob responsabilidade do candidato ou responsável.
            </div>
            <p class="small text-secondary mt-3 mb-0">O Galera Tech ocorre à tarde. O ano e o semestre da inscrição serão definidos pelo sistema, não pelo candidato.</p>
          </div>
        </section>

        <!-- ETAPA 4 -->
        <section class="card secao">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><span class="numero">4</span>Dados do responsável</h2>
            <span class="badge bg-light text-primary border fw-semibold">Etapa 4 de 5</span>
          </div>
          <div class="card-body">
            <p class="small text-secondary" id="ajudaResponsavel">Nome, WhatsApp e parentesco são obrigatórios para estudantes menores de 18 anos e opcionais para maiores.</p>
            <div class="row g-3">
              <div class="col-md-7">
                <label for="responsavelNome" class="form-label">Nome completo do responsável <span class="obrigatorio" id="asteriscoResponsavel">*</span></label>
                <input class="form-control responsavel" id="responsavelNome" name="responsavel_nome" maxlength="150" autocomplete="off">
                <div class="invalid-feedback">Informe o nome do responsável.</div>
              </div>
              <div class="col-md-5">
                <label for="parentesco" class="form-label">Grau de parentesco com o estudante <span class="obrigatorio" id="asteriscoParentesco">*</span></label>
                <select class="form-select responsavel" id="parentesco" name="parentesco">
                  <option value="">Selecione o grau de parentesco</option>
                  <option value="Mãe">Mãe</option>
                  <option value="Pai">Pai</option>
                  <option value="Tutor(a) / Responsável legal">Tutor(a) / Responsável legal</option>
                  <option value="Outro">Outro</option>
                </select>
                <div class="invalid-feedback">Selecione o parentesco.</div>
              </div>
              <div class="col-md-5">
                <label for="responsavelTelefone" class="form-label">WhatsApp do responsável (com DDD) <span class="obrigatorio" id="asteriscoTelefoneResponsavel">*</span></label>
                <input type="tel" class="form-control responsavel" id="responsavelTelefone" name="responsavel_telefone" maxlength="15" inputmode="numeric" placeholder="(17) 99999-9999">
                <div class="invalid-feedback">Informe um WhatsApp com DDD (11 dígitos).</div>
              </div>
            </div>
          </div>
        </section>

        <!-- ETAPA 5 -->
        <section class="card secao">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><span class="numero">5</span>Declarações e confirmação</h2>
            <span class="badge bg-light text-primary border fw-semibold">Etapa 5 de 5</span>
          </div>
          <div class="card-body">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="cienciaPrograma" name="ciencia_programa" required>
              <label class="form-check-label" for="cienciaPrograma"><strong>Li e compreendi as condições do Galera Tech:</strong> a capacitação é gratuita, presencial no período da tarde e <strong>exclusiva para estudantes do Ensino Médio de escolas públicas</strong>. <span class="obrigatorio">*</span></label>
              <div class="invalid-feedback">Confirme que leu e compreendeu as condições de participação.</div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="cienciaTransporte" name="ciencia_transporte" required>
              <label class="form-check-label" for="cienciaTransporte"><strong>Li e compreendi as condições de transporte e deslocamento</strong> descritas acima, incluindo as regras do auxílio-transporte e a responsabilidade pelos custos e pela logística de deslocamento quando aplicável. <span class="obrigatorio">*</span></label>
              <div class="invalid-feedback">Confirme a ciência das condições de transporte e deslocamento.</div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="veracidade" name="declarou_veracidade" required>
              <label class="form-check-label" for="veracidade">Declaro que as informações fornecidas são verdadeiras. <span class="obrigatorio">*</span></label>
              <div class="invalid-feedback">É necessário confirmar esta declaração.</div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="privacidade" name="ciencia_privacidade" required>
              <label class="form-check-label" for="privacidade">Declaro que li e estou ciente do <a href="aviso-privacidade.html" target="_blank" rel="noopener noreferrer">Aviso de Privacidade e Tratamento de Dados (abrir documento)</a> referente à inscrição. <span class="obrigatorio">*</span></label>
              <div class="invalid-feedback">É necessário registrar a ciência do aviso de privacidade.</div>
            </div>
            <p class="small text-secondary">* Campos obrigatórios. O aviso de privacidade está disponível no link acima; o texto é uma minuta que deverá ser revisada pela organização antes de receber dados reais.</p>
          </div>
          <div class="wizard-footer-actions">
            <div class="d-flex gap-2 w-100 justify-content-end">
              <button type="reset" class="btn btn-outline-secondary px-3">Limpar</button>
              <button type="submit" class="btn btn-primary px-4 fw-semibold">Enviar inscrição</button>
            </div>
          </div>
        </section>

      </form>
    </div>

    <footer>
      Galera Tech — Programa de estudos em tecnologia • todos os direitos reservados &copy; • Desenvolvido por <a href="https://www.linkedin.com/in/levi-santos-da-cruz-9a998533a/" target="_blank" rel="noopener noreferrer">Levi S. da Cruz</a>
    </footer>
  </main>
  
  <!-- Scripts do formulário e validações -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/validacoes.js"></script>
  <script src="assets/formulario.js"></script>
  <script src="assets/cep.js"></script>
  <script src="assets/localStorage.js"></script>
</body>

</html>