import {TypeQuestion} from '../typeQuestion.js';

// Ce fichier est le fichier relié à l'ajout d'une question dans la partie pour la visualitation du questionnaire



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

    let elementReponse;

    divConteneur.classList.add("block");
    titreQuestion.classList.add("title", "is-4", "has-text-weight-semibold");
    titreQuestion.innerText = libeleQestion;
    titreQuestion.style.marginBottom = "10px";
    divSousConteneur.classList.add("ml-3", "field", "control");

    // changer ça dans le future : selection du type de reponse avec la variable type
    switch (type) {
        case TypeQuestion.CHAMPS_LONG :
        case TypeQuestion.CHAMPS_COURT :
            elementReponse = document.createElement("textarea");
            elementReponse.rows = type == TypeQuestion.CHAMPS_COURT ? 1 : 4;
            elementReponse.name = `${libeleQestion}-reponse1`;
            elementReponse.classList.add("textarea");
            elementReponse.style.border = "1px solid";
            elementReponse.disabled = true;
            elementReponse.style.resize = "none";
            break;
    }
    
    divSousConteneur.appendChild(elementReponse);

    divConteneur.appendChild(titreQuestion);
    divConteneur.appendChild(divSousConteneur);
    parent.appendChild(divConteneur);
}

document.addEventListener("DOMContentLoaded", async () => {
    
});