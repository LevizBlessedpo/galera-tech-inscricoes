// Esse script transforma o formulário de inscrição em um wizard de múltiplas etapas, com validação de campos e navegação entre seções.
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formInscricao");
  if (!form) return;

  const sections = form.querySelectorAll(".secao");
  if (sections.length === 0) return;

  const stepTitles = [
    "1. Dados Pessoais",
    "2. Residência",
    "3. Escola",
    "4. Responsável",
    "5. Termos",
  ];

  // Cria o container do wrapper e o cabeçalho de abas no topo do form
  const wrapper = document.createElement("div");
  wrapper.className = "wizard-wrapper";
  form.parentNode.insertBefore(wrapper, form);
  wrapper.appendChild(form);

  const headerTabs = document.createElement("div");
  headerTabs.className = "wizard-header-tabs";

  let currentStep = 0;
  // Controla qual foi a maior etapa alcançada/liberada pelo usuário
  let maxReachedStep = 0;

  stepTitles.forEach((title, index) => {
    const tab = document.createElement("button");
    tab.type = "button";
    tab.className = `wizard-tab ${index === 0 ? "active" : ""}`;
    tab.innerHTML = `${title}<div class="tab-indicator"></div>`;

    tab.addEventListener("click", () => {
      // O usuário só pode clicar se a aba for anterior/atual ou se já tiver liberado aquela etapa
      if (index <= maxReachedStep) {
        goToStep(index);
      } else {
        // Feedback visual opcional ou alerta sutil de que precisa preencher a etapa atual
        shakeTab(tab);
      }
    });
    headerTabs.appendChild(tab);
  });

  wrapper.insertBefore(headerTabs, form);

  function updateTabsUI() {
    const tabs = headerTabs.querySelectorAll(".wizard-tab");
    tabs.forEach((tab, idx) => {
      tab.classList.toggle("active", idx === currentStep);

      // Estiliza visualmente as abas bloqueadas vs liberadas
      if (idx > maxReachedStep) {
        tab.style.opacity = "0.45";
        tab.style.cursor = "not-allowed";
      } else {
        tab.style.opacity = "1";
        tab.style.cursor = "pointer";
      }
    });
  }

  function showStep(stepIndex) {
    sections.forEach((sec, idx) => {
      sec.classList.remove("step-active");
      if (idx === stepIndex) {
        sec.classList.add("step-active");
      }
    });

    currentStep = stepIndex;
    updateTabsUI();
    window.scrollTo({ top: wrapper.offsetTop - 40, behavior: "smooth" });
  }

  function goToStep(stepIndex) {
    if (stepIndex >= 0 && stepIndex < sections.length) {
      showStep(stepIndex);
    }
  }

  function shakeTab(tabElement) {
    tabElement.style.transform = "translateX(4px)";
    setTimeout(() => (tabElement.style.transform = "translateX(-4px)"), 100);
    setTimeout(() => (tabElement.style.transform = "translateX(0)"), 200);
  }

  // Injeta botões de "Voltar" e "Continuar" em cada seção
  sections.forEach((section, index) => {
    const actionsDiv = section.querySelector(".acoes-form");
    const footerActions = document.createElement("div");
    footerActions.className = "wizard-footer-actions";

    // Botão Voltar
    if (index > 0) {
      const btnPrev = document.createElement("button");
      btnPrev.type = "button";
      btnPrev.className = "btn-wizard-prev";
      btnPrev.textContent = "← Voltar";
      btnPrev.addEventListener("click", () => goToStep(index - 1));
      footerActions.appendChild(btnPrev);
    } else {
      footerActions.appendChild(document.createElement("div"));
    }

    // Botão Continuar ou Submeter
    if (index < sections.length - 1) {
      const btnNext = document.createElement("button");
      btnNext.type = "button";
      btnNext.className = "btn-wizard-next";
      btnNext.textContent = "Continuar →";
      btnNext.addEventListener("click", () => {
        // Valida os campos da seção atual
        const inputs = section.querySelectorAll("input, select, textarea");
        let valid = true;
        let firstInvalid = null;

        inputs.forEach((input) => {
          // Ignora campos opcionais vazios (como complemento ou responsável se maior de idade)
          if (!input.checkValidity()) {
            input.classList.add("is-invalid");
            valid = false;
            if (!firstInvalid) firstInvalid = input;
          } else {
            input.classList.remove("is-invalid");
          }
        });

        if (valid) {
          // Libera o acesso à próxima etapa se passar na validação
          if (index + 1 > maxReachedStep) {
            maxReachedStep = index + 1;
          }
          goToStep(index + 1);
        } else {
          if (firstInvalid) firstInvalid.focus();
        }
      });
      footerActions.appendChild(btnNext);
    } else {
      // Na última etapa, insere os botões originais (Enviar / Limpar)
      if (actionsDiv) {
        footerActions.appendChild(actionsDiv);
      }
    }

    section.appendChild(footerActions);
  });

  // Inicializa na primeira etapa
  showStep(0);
});
