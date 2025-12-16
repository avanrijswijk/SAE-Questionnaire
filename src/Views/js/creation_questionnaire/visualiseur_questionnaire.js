import {TypeQuestion} from './type_question.js';
import {ouvrire_modal, fermer_modal} from './gestion_modal.js';
/*
Ce fichier est le fichier relié à l'ajout d'une question dans la partie pour la visualitation du questionnaire
*/


/**
 * Ajoute une question dans le visualiseur de questionnaire (partie droite)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {string} libeleQestion - Le libelé de la question
 * @param {TypeQuestion} type - Le type de réponse possible 
 */
export function ajouter_question_visualiseur_questionnaire(parent, libeleQestion, type) {
    const divConteneur = document.createElement("div");
    const titreQuestion = document.createElement("h4");
    const divSousConteneur = document.createElement("div");

    let reponse;

    divConteneur.classList.add("block");
    titreQuestion.classList.add("title", "is-4", "has-text-weight-semibold");
    titreQuestion.innerText = libeleQestion;
    titreQuestion.style.marginBottom = "10px";
    divSousConteneur.classList.add("ml-3", "field", "control");

    // changer ça dans le future : selection du type de reponse avec la variable type
    reponse = document.createElement("textarea");
    reponse.rows = 4;
    reponse.name = `${libeleQestion}-reponse1`;
    reponse.classList.add("textarea");
    reponse.style.border = "1px solid";
    reponse.disabled = true;
    reponse.style.resize = "none";
    divSousConteneur.appendChild(reponse);

    divConteneur.appendChild(titreQuestion);
    divConteneur.appendChild(divSousConteneur);
    parent.appendChild(divConteneur);
}

document.addEventListener("DOMContentLoaded", async () => {
    
});