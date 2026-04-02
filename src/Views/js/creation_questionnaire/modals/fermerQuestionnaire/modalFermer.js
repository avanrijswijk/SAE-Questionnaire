import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import { notification, TypeNotification } from '../../../utils/notification/notification.js';
import { TypeQuestion } from '../../typeQuestion.js';

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
function listerQuestions() {
    const divConteneurQuestions = document.getElementById("visualiseur-questions");
    
    const divQuestions = divConteneurQuestions.querySelectorAll("div.div-question");
    const questions = [];

    if (!divConteneurQuestions) {
        return questions;
    }

    divQuestions.forEach((divConteneur, index) => {
        const divQuestion = divConteneur.firstChild;
        const divReponses = divConteneur.querySelector("div.div-reponses");
        
        const data = {
            "intitule" : divQuestion.dataset.intitule,
            "type" : divQuestion.dataset.type,
            "position" : index+1,
            "est_obligatoire" : divQuestion.dataset.obligatoire,    
            "choix" : []
        };

        if (divReponses) {
            divReponses.childNodes.forEach((divReponse) => {
                data["choix"].push(divReponse.dataset.intitule);
            });
        } else {
            data["choix"].push(null);
        }

        questions.push(data);
    });
    return questions
}

document.addEventListener("DOMContentLoaded", async () => {
    // ---------- pour ouvrire le modal de validation de questionnaire (MVQ) ----------
    const modalValiderQuestionnaire = document.getElementById("dialog-finir-questionnaire");
    const boutonFinirMVQ = document.getElementById("bouton-finir");
    
    const formMVQ = document.getElementById("form-enregistrer");
    const boutonFermerMVQ = document.getElementById("bouton-fermer");
    
    const boutonPublier = document.getElementById("bouton-PublierMVQ");
    const boutonBrouillon = document.getElementById("bouton-brouillonMVQ");

    // --- Champs cachés ---
    const inputMode = document.getElementById("mode-enregistrement");

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
        const inputTitre = formMVQ.querySelector("input#nom-questionnaire");
        ouvrire_modal(modalValiderQuestionnaire);
        inputTitre.focus();
        inputTitre.select();
    });

    boutonFermerMVQ.addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });

    formMVQ.addEventListener("submit", (e) => {
       fermer_modal(modalValiderQuestionnaire);
        /*
        intitule : str 
        type : str
        position : int
        est_obligatoire : bool
        choix : [str]
        */
        const listeQuestions = document.createElement('input');
        const jsonQuestions = listerQuestions();
        if (jsonQuestions.length === 0) {
            notification(TypeNotification.ERREUR, "Aucune question n'a été créé.");
            e.preventDefault();
            return;
        } else
        if (jsonQuestions.filter((q) => q["type"].toLowerCase() == TypeQuestion.CONTEXT.toLowerCase()).length === jsonQuestions.length) {
            notification(TypeNotification.ATTENTION, "Vous ne pouvez pas créer un questionnaire avec\nuniquement des contextes.");
            e.preventDefault();
            return;
        }
        
        listeQuestions.type = "hidden";
        listeQuestions.name = "liste-questions";
        listeQuestions.value = JSON.stringify(jsonQuestions);

        // Debug //
        // console.log(jsonQuestions);
        // e.preventDefault();
        // return;

        formMVQ.appendChild(listeQuestions);
    });

    if (modalValiderQuestionnaire.getElementsByClassName("modal-background").length != 1) {
        console.error("Il n'y a pas de div.modal-background dans le modal#dialog-finir-questionnaire");
    }

    modalValiderQuestionnaire.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });


    const lienAcceuil = document.getElementById("img-accueil").parentElement;
    lienAcceuil.addEventListener("click", (e) => {
        e.preventDefault();

        const listeQuestions = listerQuestions();
        if (listeQuestions.length > 0) {
            if (confirm("Etes vous sur de vouloir revenir à l'accueil ?\nSi vous revenez à l'accueil, vous perdrez toute votre progression.")) {
                window.location.href = lienAcceuil.href;
            }
        } else {
            window.location.href = lienAcceuil.href;
        }
    });
});
