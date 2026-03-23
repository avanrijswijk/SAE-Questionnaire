import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import { notification, TypeNotification } from '../../../utils/notification/notification.js';

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

    divQuestions.forEach((divQuestion, index) => {
        divQuestion = divQuestion.firstChild;
        const divReponses = divQuestion.querySelector("div.div-reponses");

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
        if (jsonQuestions.length === 0) {
            notification(TypeNotification.ERREUR, "Aucune question n'a été créé.");
            e.preventDefault();
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
            if (confirm("Etes vous sur de vouloir revenir à l'acceuil ?\nSi vous revenez à l'acceuil, vous predrez votre progression.")) {
                window.location.href = lienAcceuil.href;
            }
        } else {
            window.location.href = lienAcceuil.href;
        }
    });
});
