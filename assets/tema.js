// ==========================================================
// CONTROLE DO MODO CLARO / ESCURO
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const btnTema = document.getElementById("btnTemaToggle");
  
  if (!btnTema) return;

  // 1. Aplica o tema salvo ao carregar
  if (localStorage.getItem("tema") === "claro") {
    document.body.classList.add("light-mode");
  }

  // 2. Alterna o tema ao clicar
  btnTema.addEventListener("click", () => {
    document.body.classList.toggle("light-mode");
    const modoAtual = document.body.classList.contains("light-mode") ? "claro" : "escuro";
    localStorage.setItem("tema", modoAtual);
  });
});