document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formInscricao");
    if (!form) return;

    const STORAGE_KEY = "galera_tech_inscricao_draft";
    const STEP_KEY = "galera_tech_inscricao_step";

    // 1. Recuperar dados salvos nos campos
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

    // 2. Destrancar todas as abas do Wizard para navegação livre
    const tabs = document.querySelectorAll(".wizard-tab");
    const secoes = document.querySelectorAll(".secao");

    tabs.forEach(tab => {
        tab.removeAttribute("disabled");
        tab.style.pointerEvents = "auto";
        tab.style.opacity = "1";
    });

    // 3. Restaurar exatamente a etapa (step) em que o utilizador estava antes de abrir a privacidade
    const savedStep = localStorage.getItem(STEP_KEY);
    if (savedStep !== null && tabs[savedStep] && secoes[savedStep]) {
        const targetIndex = parseInt(savedStep, 10);
        
        tabs.forEach((t, i) => t.classList.toggle("active", i === targetIndex));
        secoes.forEach((s, i) => s.classList.toggle("step-active", i === targetIndex));
    }

    // 4. Guardar a etapa atual sempre que o utilizador clicar numa aba
    tabs.forEach((tab, index) => {
        tab.addEventListener("click", function () {
            localStorage.setItem(STEP_KEY, index);
        });
    });

    // Guardar etapa ao clicar nos botões de avançar/voltar
    document.querySelectorAll(".btn-proximo, .btn-wizard-next, .btn-voltar, .btn-wizard-prev").forEach(btn => {
        btn.addEventListener("click", function () {
            setTimeout(() => {
                tabs.forEach((tab, index) => {
                    if (tab.classList.contains("active")) {
                        localStorage.setItem(STEP_KEY, index);
                    }
                });
            }, 100);
        });
    });

    // 5. Salvar automaticamente no LocalStorage sempre que o utilizador digitar algo
    form.addEventListener("input", function () {
        const formData = new FormData(form);
        const dataObj = {};
        formData.forEach((value, key) => {
            if (key !== "csrf") dataObj[key] = value;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataObj));
    });

    // 6. Botão de Limpar (Reseta campos, limpa cache e volta para a Etapa 1)
    const btnReset = form.querySelector('button[type="reset"]');
    if (btnReset) {
        btnReset.addEventListener("click", function (e) {
            e.preventDefault();
            form.reset();
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(STEP_KEY);
            
            tabs.forEach((t, i) => t.classList.toggle("active", i === 0));
            secoes.forEach((s, i) => s.classList.toggle("step-active", i === 0));
        });
    }

    // 7. Limpar o cache ao enviar a inscrição com sucesso
    form.addEventListener("submit", function () {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STEP_KEY);
    });
});