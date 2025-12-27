import {TypeQuestion} from '../typeQuestion.js';

// Ce fichier est le fichier relié à l'ajout d'une question dans la partie pour la visualitation du questionnaire



/**
 * Ajoute une question dans le visualiseur de questionnaire (partie droite)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {string} libeleQestion - Le libelé de la question
 * @param {TypeQuestion} type - Le type de réponse possible 
 * @param {int} _id 
 */
export function ajouterQuestionVisualiseurQuestionnaire(parent, libeleQestion, type, _id) {
    const divConteneur = document.createElement("div");
    const titreQuestion = document.createElement("h4");
    const divSousConteneur = document.createElement("div");

    let elementReponse;

    divConteneur.classList.add("block");
    divConteneur.dataset._id = _id;
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

/**
 * Modifi une question dans le visualisateur de questionnaire (partie de droite)
 * @param {int} id - identifiant de la question
 * @param {string} libele - le nouveau libelé
 */
export function modifierQuestionVisualiseurQuestionnaire(id, libele) {
    const question = donnerQuestionAvecIdVisualiseurQuestionnaire(id);
    const baliseH4Question = question.querySelector('h4');
    baliseH4Question.innerText = libele;
}


/**
 * retourne le div de la question avec son id
 * (pour une question présent dans le visualisateur de questionnaire (partie de droite)) 
 * @param {int} id - identifiant de la question
 * @returns {HTMLDivElement} le div de la question
 */
export function donnerQuestionAvecIdVisualiseurQuestionnaire(id) {
    const divVisualiseurQuestions = document.getElementById("visualiseur-qestionnaire");
    return divVisualiseurQuestions.querySelector(`[data-_id="${id}"]`);
}