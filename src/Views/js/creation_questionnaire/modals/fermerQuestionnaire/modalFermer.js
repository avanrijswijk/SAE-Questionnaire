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
function listerQuestions() {
    const divQuestions = document.getElementById("visualiseur-questions");
    const questions = [];

    if (!divQuestions) {
        return questions;
    }

    for (let index = 0; index < divQuestions.childElementCount; index++) {
        const divConteneur = divQuestions.children[index];
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
                data["reponses"].push(divReponse.dataset.intitule);
            });
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
        //console.log(listerQuestions());
        const listeQuestions = document.createElement('input');
        const jsonQuestions = listerQuestions();
        if (jsonQuestions.length === 0) {
            notification(TypeNotification.ERREUR, "Aucune question n'a été créé.");
            e.preventDefault();
        }
        listeQuestions.type = "hidden";
        listeQuestions.name = "liste-questions";
        listeQuestions.value = JSON.stringify(jsonQuestions);

        console.log(jsonQuestions);
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
});
