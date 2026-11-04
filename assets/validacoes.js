
'use strict';

// ==========================================================
// 1. SELEÇÃO DOS ELEMENTOS HTML
// ==========================================================

// Localiza no HTML o formulário com id="formInscricao".
// O formulário será utilizado para controlar o envio.
const form = document.getElementById('formInscricao');

// Localiza o campo de data de nascimento.
// A data será utilizada para calcular a idade do estudante.
const nascimento = document.getElementById('nascimento');

// Localiza o elemento responsável por exibir a mensagem
// de sucesso após uma operação.
const sucesso = document.getElementById('mensagemSucesso');


// ==========================================================
// 2. FUNÇÕES AUXILIARES
// ==========================================================

// Cria uma função que recebe um valor e mantém apenas números.
// A sintaxe => representa uma arrow function.
//
// A expressão regular /\D/g identifica todos os caracteres
// que não são dígitos.
//
// replace substitui esses caracteres por uma string vazia.
//
// Exemplo:
// apenasNumeros('(17) 99999-8888')
// Resultado: '17999998888'
const apenasNumeros = valor => valor.replace(/\D/g, '');


// Cria uma função auxiliar para localizar elementos pelo ID.
// Evita repetir document.getElementById() em todo o código.
//
// Exemplo:
// campo('cpf') equivale a document.getElementById('cpf').
const campo = id => document.getElementById(id);


// ==========================================================
// 3. FUNÇÃO PARA VALIDAR CPF
// ==========================================================

// Declara uma função que recebe um CPF e retorna:
// true = CPF matematicamente válido.
// false = CPF inválido.
function cpfValido(valor) {

    // Remove pontos, traços e outros caracteres.
    // Exemplo: 123.456.789-09 vira 12345678909.
    const cpf = apenasNumeros(valor);

    // Verifica duas condições:
    //
    // 1. Se o CPF NÃO possui exatamente 11 números.
    // 2. Se todos os números são iguais.
    //
    // /^\d{11}$/ exige exatamente 11 dígitos.
    // /^(\d)\1{10}$/ identifica 11 dígitos iguais.
    //
    // O operador || significa OU.
    if (
        !/^\d{11}$/.test(cpf) ||
        /^(\d)\1{10}$/.test(cpf)
    ) {

        // Retorna falso se alguma condição for inválida.
        return false;
    }

    // Inicia a validação dos dois dígitos verificadores.
    //
    // pos = 9: verifica o primeiro dígito.
    // pos = 10: verifica o segundo dígito.
    for (let pos = 9; pos <= 10; pos++) {

        // Cria uma variável para acumular os resultados.
        let soma = 0;

        // Percorre os dígitos utilizados no cálculo.
        for (let i = 0; i < pos; i++) {

            // Number converte o caractere para número.
            //
            // Multiplica cada dígito pelo peso correspondente.
            // Adiciona o resultado à variável soma.
            soma += Number(cpf[i]) * (pos + 1 - i);
        }

        // Calcula o dígito verificador.
        //
        // O operador % calcula o resto da divisão.
        const digito = (soma * 10) % 11;

        // Compara o dígito calculado com o informado.
        //
        // O operador !== significa estritamente diferente.
        //
        // A expressão (digito === 10 ? 0 : digito)
        // é um operador ternário:
        // Se o dígito for 10, utiliza 0.
        // Caso contrário, utiliza o próprio dígito.
        if (
            Number(cpf[pos]) !==
            (digito === 10 ? 0 : digito)
        ) {

            // Se forem diferentes, o CPF é inválido.
            return false;
        }
    }

    // Se passou por todas as verificações,
    // o CPF é matematicamente válido.
    return true;
}


// ==========================================================
// 4. FUNÇÃO PARA CALCULAR A IDADE
// ==========================================================

// Recebe uma data de nascimento.
// Retorna a idade em anos completos.
// Retorna null quando a data é inválida ou futura.
function idadeEmAnos(data) {

    // Verifica se nenhuma data foi informada.
    if (!data) return null;

    // Obtém a data e a hora atuais do computador.
    const hoje = new Date();

    // Converte o texto informado para um objeto Date.
    //
    // Acrescenta T12:00:00 para utilizar meio-dia
    // como horário local na interpretação da data.
    const n = new Date(data + 'T12:00:00');

    // getTime retorna o instante da data em milissegundos.
    //
    // Number.isNaN verifica se o resultado é NaN.
    //
    // n > hoje verifica se a data está no futuro.
    if (
        Number.isNaN(n.getTime()) ||
        n > hoje
    ) {

        // Retorna null se a data for inválida ou futura.
        return null;
    }

    // Calcula a diferença entre o ano atual
    // e o ano de nascimento.
    let idade = hoje.getFullYear() - n.getFullYear();

    // Verifica se o aniversário ainda não aconteceu neste ano.
    //
    // getMonth retorna o mês, começando em 0 para janeiro.
    // getDate retorna o dia do mês.
    //
    // Se o mês atual é anterior ao mês do aniversário,
    // ou se o mês é igual e o dia ainda não chegou,
    // devemos diminuir um ano da idade.
    if (
        hoje.getMonth() < n.getMonth() ||
        (
            hoje.getMonth() === n.getMonth() &&
            hoje.getDate() < n.getDate()
        )
    ) {

        // Subtrai um ano porque o aniversário ainda não ocorreu.
        idade--;
    }

    // Retorna a idade calculada.
    return idade;
}


// ==========================================================
// 5. FUNÇÃO PARA FORMATAR TELEFONE
// ==========================================================

// Recebe um telefone e aplica uma máscara visual.
//
// Exemplo de celular:
// 17999998888 -> (17) 99999-8888
//
// Exemplo de telefone com 10 dígitos:
// 1732224444 -> (17) 3222-4444
function formatarTelefone(v) {

    // Remove todos os caracteres que não são números.
    //
    // slice(0, 11) limita o conteúdo a 11 dígitos.
    const x = apenasNumeros(v).slice(0, 11);

    // Verifica se o usuário digitou até dois números.
    if (x.length <= 2) {

        // Se houver algum número, acrescenta "(".
        // Caso contrário, retorna uma string vazia.
        return x ? '(' + x : '';
    }

    // Monta a formatação do telefone.
    //
    // x.slice(0, 2) representa o DDD.
    //
    // Para 11 dígitos:
    // utiliza cinco números antes do hífen.
    //
    // Para 10 dígitos:
    // utiliza quatro números antes do hífen.
    //
    // Os operadores ternários escolhem a máscara
    // conforme a quantidade de dígitos.
    return (
        '(' + x.slice(0, 2) + ') ' +

        (
            x.length > 10
                ? x.slice(2, 7)
                : x.slice(2, 6)
        ) +

        (
            x.length > 10
                ? (
                    x.length > 7
                        ? '-' + x.slice(7)
                        : ''
                  )
                : (
                    x.length > 6
                        ? '-' + x.slice(6)
                        : ''
                  )
        )
    );
}


// ==========================================================
// 6. OBRIGATORIEDADE DOS DADOS DO RESPONSÁVEL
// ==========================================================

// Atualiza os campos do responsável com base na idade.
//
// Menores de 18 anos:
// nome, telefone e parentesco são obrigatórios.
//
// Maiores de 18 anos:
// os mesmos campos são opcionais.
function atualizarResponsavel() {

    // Calcula a idade usando a data de nascimento.
    const idade = idadeEmAnos(nascimento.value);

    // Define se o responsável deve ser obrigatório.
    //
    // !== null verifica se a idade foi calculada.
    // < 18 verifica se o estudante é menor de idade.
    //
    // && significa E.
    const obrigatorio = idade !== null && idade < 18;

    // Seleciona todos os elementos HTML
    // que possuem a classe "responsavel".
    document.querySelectorAll('.responsavel')

        // Percorre cada elemento encontrado.
        .forEach(el => {

            // Define a propriedade required.
            //
            // true: preenchimento obrigatório.
            // false: preenchimento opcional.
            el.required = obrigatorio;
        });

    // Atualiza o asterisco do campo nome do responsável.
    // Exibe * quando o preenchimento for obrigatório.
    campo('asteriscoResponsavel').textContent =
        obrigatorio ? '*' : '';

    // Atualiza o asterisco do WhatsApp do responsável.
    campo('asteriscoTelefoneResponsavel').textContent =
        obrigatorio ? '*' : '';

    // Atualiza o asterisco do grau de parentesco.
    campo('asteriscoParentesco').textContent =
        obrigatorio ? '*' : '';

    // Atualiza a mensagem de orientação exibida
    // próxima dos campos do responsável.
    campo('ajudaResponsavel').textContent =

        // Se ainda não for possível calcular a idade:
        idade === null

            ? 'Informe a data de nascimento para verificar ' +
              'se os dados do responsável são obrigatórios.'

            // Caso a idade seja menor que 18:
            : obrigatorio

                ? 'O estudante é menor de 18 anos: informe ' +
                  'nome, WhatsApp e tipo de parentesco ' +
                  'do responsável.'

                // Caso tenha 18 anos ou mais:
                : 'O estudante tem 18 anos ou mais: ' +
                  'nome, WhatsApp e parentesco do ' +
                  'responsável são opcionais.';
}


// ==========================================================
// 7. MÁSCARA DE ENTRADA DO CPF
// ==========================================================

// IMPORTANTE:
// Máscaras alteram a aparência do texto digitado.
// Não substituem a validação realizada no servidor PHP.

// Seleciona o campo CPF e adiciona um evento "input".
// O evento é executado quando o conteúdo do campo muda.
campo('cpf').addEventListener('input', e => {

    // e representa o objeto do evento.
    // e.target representa o campo que recebeu a entrada.
    //
    // Remove caracteres não numéricos.
    // Limita o CPF a 11 dígitos.
    let x = apenasNumeros(e.target.value).slice(0, 11);

    // Aplica a máscara progressivamente.
    e.target.value = x

        // Insere o primeiro ponto após três dígitos.
        .replace(/^(\d{3})(\d)/, '$1.$2')

        // Insere o segundo ponto após mais três dígitos.
        .replace(
            /^(\d{3})\.(\d{3})(\d)/,
            '$1.$2.$3'
        )

        // Insere o hífen antes dos dois últimos dígitos.
        .replace(/\.(\d{3})(\d)/, '.$1-$2');
});


// ==========================================================
// 8. MÁSCARA DE ENTRADA DO CEP
// ==========================================================

// Adiciona um evento de digitação ao campo CEP.
campo('cep').addEventListener('input', e => {

    // Remove caracteres não numéricos.
    // Limita o CEP a oito dígitos.
    let x = apenasNumeros(e.target.value).slice(0, 8);

    // Insere um hífen depois dos cinco primeiros números.
    //
    // Exemplo:
    // 15085000 -> 15085-000
    e.target.value = x.replace(
        /^(\d{5})(\d)/,
        '$1-$2'
    );
});


// ==========================================================
// 9. MÁSCARA DOS TELEFONES
// ==========================================================

// Cria um array com os IDs dos dois campos de telefone.
['telefone', 'responsavelTelefone']

    // Percorre cada ID do array.
    .forEach(id =>

        // Localiza o campo correspondente.
        campo(id).addEventListener(

            // Executa a função quando houver digitação.
            'input',

            // Recebe o evento do campo.
            e =>

                // Atualiza o valor usando a função
                // de formatação de telefone.
                e.target.value = formatarTelefone(
                    e.target.value
                )
        )
    );


// ==========================================================
// 10. CONFIGURAÇÃO DA DATA DE NASCIMENTO
// ==========================================================

// Cria a data máxima permitida no campo nascimento.
//
// new Date() obtém a data e hora atuais.
//
// toISOString() converte a data para uma string ISO,
// utilizando UTC.
//
// slice(0, 10) mantém apenas AAAA-MM-DD.
//
// Exemplo: 2026-11-03.
nascimento.max = new Date().toISOString().slice(0, 10);


// Adiciona um evento ao campo de nascimento.
//
// Quando a data é alterada, a função será executada
// para atualizar a obrigatoriedade do responsável.
nascimento.addEventListener(
    'change',
    atualizarResponsavel
);


// ==========================================================
// 11. FUNÇÃO PARA VALIDAR CAMPOS ESPECÍFICOS
// ==========================================================

// Realiza validações adicionais antes de enviar o formulário.
function validarCamposExtras() {

    // Localiza o campo CPF.
    const cpf = campo('cpf');

    // Localiza o campo CEP.
    const cep = campo('cep');

    // Localiza o telefone do estudante.
    const telefone = campo('telefone');

    // Localiza o telefone do responsável.
    const telResp = campo('responsavelTelefone');


    // ------------------------------------------------------
    // VALIDAÇÃO DO CPF
    // ------------------------------------------------------

    // Executa a função cpfValido().
    //
    // Se o CPF for válido:
    // define uma mensagem vazia.
    //
    // Caso contrário:
    // define a mensagem "CPF inválido".
    //
    // setCustomValidity permite definir erros personalizados.
    cpf.setCustomValidity(
        cpfValido(cpf.value) ? '' : 'CPF inválido'
    );


    // ------------------------------------------------------
    // VALIDAÇÃO DO CEP
    // ------------------------------------------------------

    // Remove a máscara e verifica se existem oito dígitos.
    cep.setCustomValidity(
        apenasNumeros(cep.value).length === 8
            ? ''
            : 'CEP inválido'
    );


    // ------------------------------------------------------
    // VALIDAÇÃO DO TELEFONE DO ESTUDANTE
    // ------------------------------------------------------

    // Verifica se o telefone possui exatamente 11 dígitos.
    telefone.setCustomValidity(
        apenasNumeros(telefone.value).length === 11
            ? ''
            : 'Telefone inválido'
    );


    // ------------------------------------------------------
    // VALIDAÇÃO DO TELEFONE DO RESPONSÁVEL
    // ------------------------------------------------------

    // O telefone do responsável pode ser opcional,
    // dependendo da idade do estudante.
    //
    // !telResp.required:
    // verifica se o campo NÃO é obrigatório.
    //
    // !telResp.value.trim():
    // verifica se o campo está vazio.
    //
    // Se for opcional e estiver vazio, será válido.
    //
    // Se estiver preenchido, deverá possuir 11 dígitos.
    telResp.setCustomValidity(
        (
            !telResp.required &&
            !telResp.value.trim()
        ) ||
        apenasNumeros(telResp.value).length === 11

            ? ''
            : 'Telefone inválido'
    );


    // ------------------------------------------------------
    // VALIDAÇÃO DA DATA DE NASCIMENTO
    // ------------------------------------------------------

    // Calcula a idade informada.
    const idade = idadeEmAnos(nascimento.value);

    // Se não for possível calcular a idade,
    // apresenta a mensagem "Data inválida".
    nascimento.setCustomValidity(
        idade === null ? 'Data inválida' : ''
    );
}


// ==========================================================
// 12. VALIDAÇÃO NO ENVIO DO FORMULÁRIO
// ==========================================================

// Adiciona um evento ao enviar o formulário.
form.addEventListener('submit', e => {

    // Impede temporariamente o envio automático.
    //
    // Assim, o JavaScript pode validar os campos primeiro.
    e.preventDefault();

    // Oculta a mensagem de sucesso.
    //
    // classList.add adiciona uma classe CSS.
    // d-none é uma classe do Bootstrap que oculta elementos.
    sucesso.classList.add('d-none');

    // Atualiza quais campos do responsável são obrigatórios.
    atualizarResponsavel();

    // Executa as validações personalizadas.
    validarCamposExtras();

    // Adiciona a classe do Bootstrap para exibir
    // o estado de validação dos campos.
    form.classList.add('was-validated');

    // Verifica se o formulário possui algum campo inválido.
    //
    // checkValidity retorna true quando o formulário
    // atende às regras de validação HTML.
    if (!form.checkValidity()) {

        // Localiza o primeiro elemento inválido.
        //
        // :invalid é um seletor CSS de validação.
        const primeiro = form.querySelector(':invalid');

        // Coloca o foco no primeiro campo inválido.
        //
        // ?. é o operador de encadeamento opcional.
        // Evita erro caso nenhum elemento seja encontrado.
        primeiro?.focus();

        // Interrompe a função.
        // O formulário não será enviado.
        return;
    }

    // Se todas as validações passaram,
    // envia o formulário ao servidor.
    //
    // Os dados serão processados pelo PHP.
    form.submit();
});


// ==========================================================
// 13. LIMPEZA DOS ERROS DURANTE A DIGITAÇÃO
// ==========================================================

// Seleciona os elementos input e select do formulário.
form.querySelectorAll('input,select')

    // Percorre todos os campos encontrados.
    .forEach(el =>

        // Adiciona um evento de alteração de conteúdo.
        el.addEventListener('input', () => {

            // Limpa a mensagem personalizada de erro
            // anteriormente definida para o campo.
            el.setCustomValidity('');

            // Oculta a mensagem de sucesso
            // quando algum dado é alterado.
            sucesso.classList.add('d-none');
        })
    );


// ==========================================================
// 14. LIMPEZA DO FORMULÁRIO
// ==========================================================

// Adiciona um evento ao botão de redefinição do formulário.
form.addEventListener('reset', () => {

    // Remove a classe de validação visual do Bootstrap.
    form.classList.remove('was-validated');

    // Oculta a mensagem de sucesso.
    sucesso.classList.add('d-none');

    // Agenda a atualização dos campos do responsável.
    //
    // setTimeout(..., 0) permite que a redefinição
    // dos valores do formulário seja processada antes
    // de atualizar a obrigatoriedade dos campos.
    setTimeout(atualizarResponsavel, 0);
});


// ==========================================================
// 15. INICIALIZAÇÃO DO FORMULÁRIO
// ==========================================================

// Executa a função assim que este script é processado.
//
// Isso garante que os campos do responsável
// sejam configurados conforme a data já preenchida,
// caso exista.
atualizarResponsavel();