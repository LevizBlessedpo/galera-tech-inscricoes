let consultaCep = 0;

async function buscarCEP() {
    const cepInput = document.getElementById('cep');
    if (!cepInput) return;

    const cep = cepInput.value.replace(/\D/g, '');
    consultaCep++;
    const consultaAtual = consultaCep;

    cepInput.setCustomValidity('');

    if (cep.length < 8) {
        document.getElementById('logradouro').value = '';
        document.getElementById('bairro').value = '';
        document.getElementById('cidade').value = '';
        document.getElementById('estado').value = '';
        return;
    }

    try {
        const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        if (!resposta.ok) throw new Error('Erro na consulta');

        const endereco = await resposta.json();
        if (consultaAtual !== consultaCep) return;

        if (endereco.erro) {
            document.getElementById('logradouro').value = '';
            document.getElementById('bairro').value = '';
            document.getElementById('cidade').value = '';
            document.getElementById('estado').value = '';
            cepInput.setCustomValidity('CEP não encontrado.');
            return;
        }

        document.getElementById('logradouro').value = endereco.logradouro || '';
        document.getElementById('bairro').value = endereco.bairro || '';
        document.getElementById('cidade').value = endereco.localidade || '';
        document.getElementById('estado').value = endereco.uf || '';
        cepInput.setCustomValidity('');

    } catch (erro) {
        if (consultaAtual !== consultaCep) return;
        cepInput.setCustomValidity('Não foi possível consultar o CEP.');
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', () => {
            buscarCEP();
        });
    }
});