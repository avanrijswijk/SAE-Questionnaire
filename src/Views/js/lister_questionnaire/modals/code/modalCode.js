
const fermerModal = (modal) => {
    modal.classList.remove("is-active");
};

const ouvrirModal = (modal) => {
    modal.classList.add("is-active");
};

document.addEventListener("DOMContentLoaded", async () => {
    // ---------- pour ouvrire le modal de validation de questionnaire (MVQ) ----------
    const modalCode = document.getElementById("dialog-code");
    const form = document.getElementById("form-enregistrer");
    const boutonJAiUnCode = document.getElementById("bouton-code");
    const inputCode = form.querySelector("input");
    
    const boutonFermer = document.getElementById("bouton-fermer");

    inputCode.addEventListener('input', function() {
    // On remplace tout ce qui n'est PAS une lettre ou un espace par vide
    this.value = String(this.value.replace(/[^a-zA-Z\s]|\s/g, '')).toUpperCase();
});

    boutonJAiUnCode.addEventListener("click", () => {
        ouvrirModal(modalCode);
    });

    boutonFermer.addEventListener("click", () => {
        fermerModal(modalCode);
    });

    if (modalCode.getElementsByClassName("modal-background").length != 1) {
        console.error("Il n'y a pas de div.modal-background dans le modal#dialog-finir-questionnaire");
    }

    modalCode.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermerModal(modalCode);
    });
});