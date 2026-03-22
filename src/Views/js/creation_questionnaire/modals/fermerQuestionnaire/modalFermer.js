import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import { notification, TypeNotification } from '../../notification/notification.js';
/**
 * liste les questions :
 * [{
 * intitule : str 
 * type : str
 * position : int
 * est_obligatoire : bool
 * reponses : list[str]
 * }]
 * @returns {Array} un fichier JSON contenant les informations
 */
export function listerQuestions() {
    const divQuestions = document.getElementById("visualiseur-questions");
    const questions = [];

    if (!divQuestions) {
        return questions;
    }

    for (let index = 0; index < divQuestions.childElementCount; index++) {
        const divConteneur = divQuestions.children[index];
        const divQuestion = divConteneur.firstElementChild;
        const divReponses = divConteneur.querySelector("div.div-reponses");

        const data = {
            "intitule" : divQuestion.dataset.intitule,
            "type" : divQuestion.dataset.type,
            "position" : index,
            "est_obligatoire" : divQuestion.dataset.obligatoire,
            "choix" : []
        };

        if (divReponses) {
            Array.from(divReponses.children).forEach((divReponse) => {
                data["choix"].push(divReponse.dataset.intitule);
            });
        } else {
            data["choix"].push(null);
        }

        questions.push(data);
    }
    return questions
}

document.addEventListener("DOMContentLoaded", async () => {
    // ---------- pour ouvrire le modal de validation de questionnaire (MVQ) ----------
    const modalValiderQuestionnaire = document.getElementById("dialog-finir-questionnaire");
    const boutonFinirMVQ = document.getElementById("bouton-finir");
    
    const formMVQ = document.getElementById("form-enregistrer");
    const boutonFermerMVQ = document.getElementById("bouton-fermer");

    // --- Récupération des boutons ---
    const boutonPublier = document.getElementById("bouton-PublierMVQ");
    const boutonBrouillon = document.getElementById("bouton-brouillonMVQ");

    // --- Champs cachés ---
    const inputMode = document.getElementById("mode-enregistrement");
    const inputId = document.getElementById("id-questionnaire");

    // --- Bouton Publier ---
    boutonPublier.addEventListener("click", () => {
        inputMode.value = "publier";
        formMVQ.requestSubmit();
    });

    // --- Bouton Brouillon ---
    boutonBrouillon.addEventListener("click", () => {
        inputMode.value = "brouillon";
        formMVQ.requestSubmit();
    });
    
    // ---------- MVQ ----------
    boutonFinirMVQ.addEventListener("click", () => {
        ouvrire_modal(modalValiderQuestionnaire);
    });

    boutonFermerMVQ.addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });

    formMVQ.addEventListener("submit", (e) => {
        //e.preventDefault();
        
        fermer_modal(modalValiderQuestionnaire);
        // window.location.href = "./?c=home";

        /*
        intitule : str 
        type : str
        position : int
        est_obligatoire : bool
        choix : [str]
        */
        const listeQuestions = document.createElement('input');
        const jsonQuestions = listerQuestions();
        console.log(jsonQuestions);
        if (jsonQuestions.length === 0) {
            notification(TypeNotification.ERREUR, "Aucune question n'a été créé.");
            e.preventDefault();
        }
        listeQuestions.type = "hidden";
        listeQuestions.name = "liste-questions";
        listeQuestions.value = JSON.stringify(jsonQuestions);

        // e.preventDefault();
        // return;
        const old = formMVQ.querySelector('input[name="liste-questions"]');
        if (old) old.remove();
        formMVQ.appendChild(listeQuestions);
    });

    if (modalValiderQuestionnaire.getElementsByClassName("modal-background").length != 1) {
        console.error("Il n'y a pas de div.modal-background dans le modal#dialog-finir-questionnaire");
    }

    modalValiderQuestionnaire.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });
});
