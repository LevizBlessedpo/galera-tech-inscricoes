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
        } catch (e) {
            console.error("Erro ao carregar rascunho:", e);
        }
    }

    // 2. Sempre inicia na Etapa 1 ao carregar/atualizar a página (evita pular para a última ou travar)
    localStorage.removeItem(STEP_KEY);

    const tabs = document.querySelectorAll(".wizard-tab");
    const secoes = document.querySelectorAll(".secao");

    if (tabs.length > 0 && secoes.length > 0) {
        tabs.forEach((t, i) => t.classList.toggle("active", i === 0));
        secoes.forEach((s, i) => s.classList.toggle("step-active", i === 0));
    }

    // 3. Salvar automaticamente no LocalStorage sempre que o utilizador digitar algo
    form.addEventListener("input", function () {
        const formData = new FormData(form);
        const dataObj = {};
        formData.forEach((value, key) => {
            if (key !== "csrf") dataObj[key] = value;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataObj));
    });

    // 4. Botão de Limpar (Reseta campos, limpa cache e volta para a Etapa 1 com tudo limpo)
    const btnReset = form.querySelector('button[type="reset"]');
    if (btnReset) {
        btnReset.addEventListener("click", function (e) {
            e.preventDefault();
            form.reset();
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(STEP_KEY);
            
            if (tabs.length > 0 && secoes.length > 0) {
                tabs.forEach((t, i) => t.classList.toggle("active", i === 0));
                secoes.forEach((s, i) => s.classList.toggle("step-active", i === 0));
            }
        });
    }

    // 5. Limpar o cache ao enviar a inscrição com sucesso
    form.addEventListener("submit", function () {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STEP_KEY);
    });
});