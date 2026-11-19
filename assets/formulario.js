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

  const wrapper = document.createElement("div");
  wrapper.className = "wizard-wrapper";
  form.parentNode.insertBefore(wrapper, form);
  wrapper.appendChild(form);

  const headerTabs = document.createElement("div");
  headerTabs.className = "wizard-header-tabs";

  const STEP_KEY = "galera_tech_inscricao_step";
  const MAX_STEP_KEY = "galera_tech_inscricao_max_step";

  const savedStep = parseInt(localStorage.getItem(STEP_KEY) || "0", 10);
  const savedMaxStep = parseInt(localStorage.getItem(MAX_STEP_KEY) || "0", 10);

  let currentStep =
    savedStep >= 0 && savedStep < sections.length ? savedStep : 0;
  let maxReachedStep = Math.max(
    currentStep,
    savedMaxStep >= 0 && savedMaxStep < sections.length ? savedMaxStep : 0
  );

  function saveMaxStep() {
    localStorage.setItem(MAX_STEP_KEY, String(maxReachedStep));
  }

  stepTitles.forEach((title, index) => {
    const tab = document.createElement("button");
    tab.type = "button";
    tab.className = `wizard-tab ${index === currentStep ? "active" : ""}`;
    tab.innerHTML = `${title}<div class="tab-indicator"></div>`;

    tab.addEventListener("click", () => {
      if (index <= maxReachedStep) {
        goToStep(index);
      } else {
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
    if (currentStep > maxReachedStep) {
      maxReachedStep = currentStep;
      saveMaxStep();
    }

    localStorage.setItem(STEP_KEY, String(currentStep));
    updateTabsUI();

    const percentual = Math.round(((currentStep + 1) / sections.length) * 100);

    const progressBar = document.getElementById("globalProgressBar");
    if (progressBar) {
      progressBar.style.width = `${percentual}%`;
    }

    const progressText = document.getElementById("progressText");
    if (progressText) {
      progressText.textContent = `Progresso da Inscrição: ${percentual}%`;
    }

    const progressStepText = document.getElementById("progressStepText");
    if (progressStepText) {
      progressStepText.textContent = `Etapa ${currentStep + 1} de ${sections.length}`;
    }

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

  sections.forEach((section, index) => {
    const actionsDiv = section.querySelector(".acoes-form");
    const footerActions = document.createElement("div");
    footerActions.className = "wizard-footer-actions";

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

    if (index < sections.length - 1) {
      const btnNext = document.createElement("button");
      btnNext.type = "button";
      btnNext.className = "btn-wizard-next";
      btnNext.textContent = "Continuar →";
      btnNext.addEventListener("click", () => {
        const inputs = section.querySelectorAll("input, select, textarea");
        let valid = true;
        let firstInvalid = null;

        inputs.forEach((input) => {
          if (!input.checkValidity()) {
            input.classList.add("is-invalid");
            valid = false;
            if (!firstInvalid) firstInvalid = input;
          } else {
            input.classList.remove("is-invalid");
          }
        });

        if (valid) {
          if (index + 1 > maxReachedStep) {
            maxReachedStep = index + 1;
            saveMaxStep();
          }
          goToStep(index + 1);
        } else {
          if (firstInvalid) firstInvalid.focus();
        }
      });
      footerActions.appendChild(btnNext);
    } else {
      if (actionsDiv) {
        footerActions.appendChild(actionsDiv);
      }
    }

    section.appendChild(footerActions);
  });

  showStep(currentStep);
});