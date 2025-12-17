import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';

document.addEventListener("DOMContentLoaded", async () => {
    // ---------- pour ouvrire le modal de validation de questionnaire (MVQ) ----------
    const modalValiderQuestionnaire = document.getElementById("dialog-finir-questionnaire");
    const boutonFinirMVQ = document.getElementById("bouton-finir");
    
    const formMVQ = document.getElementById("form-enregistrer");
    const boutonFermerMVQ = document.getElementById("bouton-fermer");
    
    // ---------- MVQ ----------
    boutonFinirMVQ.addEventListener("click", () => {
        ouvrire_modal(modalValiderQuestionnaire);
    });

    boutonFermerMVQ.addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });

    formMVQ.addEventListener("submit", (e) => {
        e.preventDefault();
        // TODO: traiter les données du formulaire ici
        fermer_modal(modalValiderQuestionnaire);
        window.location.href = "./?c=home";
    });

    if (modalValiderQuestionnaire.getElementsByClassName("modal-background").length != 1) {
        console.error("Il n'y a pas de div.modal-background dans le modal#dialog-finir-questionnaire");
    }

    modalValiderQuestionnaire.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });
});
