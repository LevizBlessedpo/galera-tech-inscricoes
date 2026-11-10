let consultaCep = 0;
 
function cpfValido(valor) {
    const cpf = apenasNumeros(valor);
 
    if (!/^\d{11}$/.test(cpf) || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
 
    for (let pos = 9; pos <= 10; pos++) {
        let soma = 0;
 
        for (let i = 0; i < pos; i++) {
            soma += Number(cpf[i]) * (pos + 1 - i);
        }
 
        const digito = (soma * 10) % 11;
 
        if (Number(cpf[pos]) !== (digito === 10 ? 0 : digito)) {
            return false;
        }
    }
 
    return true;
}
 
function idadeEmAnos(data) {
    if (!data) return null;
 
    const partes = data.split('-');
 
    if (partes.length !== 3) return null;
 
    const ano = Number(partes[0]);
    const mes = Number(partes[1]) - 1;
    const dia = Number(partes[2]);
 
    const nascimentoData = new Date(ano, mes, dia);
    const hoje = new Date();
 
    if (
        nascimentoData.getFullYear() !== ano ||
        nascimentoData.getMonth() !== mes ||
        nascimentoData.getDate() !== dia ||
        nascimentoData > hoje
    ) {
        return null;
    }
 
    let idade = hoje.getFullYear() - ano;
 
    if (
        hoje.getMonth() < mes ||
        (hoje.getMonth() === mes && hoje.getDate() < dia)
    ) {
        idade--;
    }
 
    return idade;
}
 
function formatarCPF(valor) {
    let x = apenasNumeros(valor).slice(0, 11);
 
    x = x.replace(/^(\d{3})(\d)/, '$1.$2');
    x = x.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    x = x.replace(/\.(\d{3})(\d)/, '.$1-$2');
 
    return x;
}
 
function formatarTelefone(valor) {
    let x = apenasNumeros(valor).slice(0, 11);
 
    if (x.length <= 2) {
        return x ? '(' + x : '';
    }
 
    return '(' + x.slice(0, 2) + ') ' +
        x.slice(2, 7) +
        (x.length > 7 ? '-' + x.slice(7) : '');
}
 
function atualizarResponsavel() {
    const idade = idadeEmAnos(nascimento.value);
    const obrigatorio = idade !== null && idade < 18;
 
    // Apenas os campos existentes no seu HTML atual
    const camposResponsavel = [
        'responsavelNome',
        'responsavelTelefone',
        'parentesco'
    ];
 
    camposResponsavel.forEach(id => {
        const el = campo(id);
        if (el) el.required = obrigatorio;
    });
 
    if (campo('asteriscoResponsavel')) campo('asteriscoResponsavel').textContent = obrigatorio ? '*' : '';
    if (campo('asteriscoTelefoneResponsavel')) campo('asteriscoTelefoneResponsavel').textContent = obrigatorio ? '*' : '';
    if (campo('asteriscoParentesco')) campo('asteriscoParentesco').textContent = obrigatorio ? '*' : '';
 
    if (campo('ajudaResponsavel')) {
        campo('ajudaResponsavel').textContent = idade === null
            ? 'Informe a data de nascimento para verificar os dados do responsável.'
            : obrigatorio
                ? 'O estudante é menor de 18 anos: informe nome, WhatsApp e parentesco do responsável.'
                : 'O estudante tem 18 anos ou mais: os dados do responsável são opcionais.';
    }
}
 
async function buscarCEP() {
    const cepInput = campo('cep');
    if (!cepInput) return;
   
    const cep = apenasNumeros(cepInput.value);
 
    consultaCep++;
    const consultaAtual = consultaCep;
 
    cepInput.setCustomValidity('');
 
    if (cep.length < 8) {
        campo('logradouro').value = '';
        campo('bairro').value = '';
        campo('cidade').value = '';
        campo('estado').value = '';
        return;
    }
 
    try {
        const resposta = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
 
        if (!resposta.ok) {
            throw new Error('Erro na consulta');
        }
 
        const endereco = await resposta.json();
 
        if (consultaAtual !== consultaCep) return;
 
        if (endereco.erro) {
            campo('logradouro').value = '';
            campo('bairro').value = '';
            campo('cidade').value = '';
            campo('estado').value = '';
            cepInput.setCustomValidity('CEP não encontrado.');
            return;
        }
 
        campo('logradouro').value = endereco.logradouro || '';
        campo('bairro').value = endereco.bairro || '';
        campo('cidade').value = endereco.localidade || '';
        campo('estado').value = endereco.uf || '';
        cepInput.setCustomValidity('');
 
    } catch (erro) {
        if (consultaAtual !== consultaCep) return;
 
        cepInput.setCustomValidity(
            'Não foi possível consultar o CEP. Verifique sua internet.'
        );
    }
}
 
// Eventos do Estudante
if (campo('cpf')) {
    campo('cpf').addEventListener('input', e => {
        e.target.value = formatarCPF(e.target.value);
    });
}
 
if (campo('cep')) {
    campo('cep').addEventListener('input', e => {
        let x = apenasNumeros(e.target.value).slice(0, 8);
        e.target.value = x.replace(/^(\d{5})(\d)/, '$1-$2');
        buscarCEP();
    });
}
 
['telefone', 'responsavelTelefone'].forEach(id => {
    const el = campo(id);
    if (el) {
        el.addEventListener('input', e => {
            e.target.value = formatarTelefone(e.target.value);
        });
    }
});
 
const hoje = new Date();
const ano = hoje.getFullYear();
const mes = String(hoje.getMonth() + 1).padStart(2, '0');
const dia = String(hoje.getDate()).padStart(2, '0');
 
if (nascimento) {
    nascimento.max = ano + '-' + mes + '-' + dia;
    nascimento.addEventListener('change', atualizarResponsavel);
}
 
function validarCamposExtras() {
    const cpf = campo('cpf');
    const cep = campo('cep');
    const telefone = campo('telefone');
    const telResp = campo('responsavelTelefone');
 
    if (cpf) {
        cpf.setCustomValidity(
            cpfValido(cpf.value) ? '' : 'CPF inválido.'
        );
    }
 
    if (cep) {
        cep.setCustomValidity(
            apenasNumeros(cep.value).length === 8
                ? ''
                : 'Informe um CEP com 8 números.'
        );
    }
 
    if (telefone) {
        telefone.setCustomValidity(
            apenasNumeros(telefone.value).length === 11
                ? ''
                : 'Informe um telefone com DDD e 11 números.'
        );
    }
 
    if (telResp) {
        const idade = idadeEmAnos(nascimento.value);
        const menorDeIdade = idade !== null && idade < 18;


        // Se for menor de idade, o telefone do responsável precisa ter 11 dígitos. Se for maior, é opcional mas se preenchido precisa ter 11.
        const numTelResp = apenasNumeros(telResp.value);
        if (menorDeIdade || numTelResp.length > 0) {
            telResp.setCustomValidity(
                numTelResp.length === 11 ? '' : 'Telefone do responsável inválido.'
            );
        } else {
            telResp.setCustomValidity('');
        }
    }
 
    if (nascimento) {
        const idade = idadeEmAnos(nascimento.value);
        nascimento.setCustomValidity(
            idade === null ? 'Data de nascimento inválida.' : ''
        );
    }
}
 
if (form) {
    form.addEventListener('submit', e => {
        sucesso.classList.add('d-none');
 
        atualizarResponsavel();
        validarCamposExtras();
 
        form.classList.add('was-validated');
 
        if (!form.checkValidity()) {
            e.preventDefault();
 
            const primeiro = form.querySelector(':invalid');
 
            if (primeiro) {
                primeiro.focus();
            }
        }
    });
 
    form.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('input', () => {
            el.setCustomValidity('');
            sucesso.classList.add('d-none');
        });
 
        el.addEventListener('change', () => {
            el.setCustomValidity('');
            sucesso.classList.add('d-none');
        });
    });
 
    form.addEventListener('reset', () => {
        consultaCep++;
 
        form.classList.remove('was-validated');
        sucesso.classList.add('d-none');
 
        setTimeout(() => {
            form.querySelectorAll('input, select').forEach(el => {
                el.setCustomValidity('');
            });
 
            atualizarResponsavel();
        }, 0);
    });
}
 
atualizarResponsavel();