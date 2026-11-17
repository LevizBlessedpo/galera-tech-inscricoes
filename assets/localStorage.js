document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formInscricao");
    if (!form) return;

    const STORAGE_KEY = "galera_tech_inscricao_draft";
    const STEP_KEY = "galera_tech_inscricao_step";

    // 1. Recuperar dados salvos nos campos (rascunho)
    const savedData = localStorage.getItem(STORAGE_KEY);
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            for (const key in data) {
                const input = form.elements[key];
                if (input) {
                    if (input.type === "checkbox") {
                        input.checked = data[key];
                    } else {
                        input.value = data[key];
                    }
                }
            }
            // Atualiza a regra de maior/menor de idade caso a data tenha sido restaurada
            if (typeof atualizarResponsavel === "function") {
                atualizarResponsavel();
            }
        } catch (e) {
            console.error("Erro ao carregar rascunho:", e);
        }
    }

    // 2. Salvar automaticamente no LocalStorage sempre que o usuário digitar ou marcar algo
    const salvarRascunho = () => {
        const formData = new FormData(form);
        const dataObj = {};
        formData.forEach((value, key) => {
            if (key !== "csrf") dataObj[key] = value;
        });
        // Garante que checkboxes desmarcados também sejam salvos corretamente
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            dataObj[cb.name] = cb.checked;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataObj));
    };

    form.addEventListener("input", salvarRascunho);
    form.addEventListener("change", salvarRascunho);

    // 3. Botão de Limpar (Reseta campos, limpa cache e recarrega na Etapa 1)
    const btnReset = form.querySelector('button[type="reset"]');
    if (btnReset) {
        btnReset.addEventListener("click", function (e) {
            e.preventDefault();
            form.reset();
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(STEP_KEY);
            window.location.reload();
        });
    }

    // 4. Limpar o cache ao enviar a inscrição com sucesso
    form.addEventListener("submit", function () {
        if (form.checkValidity()) {
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(STEP_KEY);
        }
    });
});