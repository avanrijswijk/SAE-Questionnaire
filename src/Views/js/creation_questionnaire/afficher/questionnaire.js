import {TypeQuestion} from '../typeQuestion.js';
import { donnerNombreReponse } from "./questions.js";

// Ce fichier est le fichier relié à l'ajout d'une question dans la partie pour la visualitation du questionnaire



/**
 * Ajoute une question dans le visualiseur de questionnaire (partie droite)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int)
 */
export function ajouterQuestionVisualiseurQuestionnaire(parent, info) {
    const libelleQestion = info['intitule'];
    const type = info['type'];
    const _id = info['_id'];
    const estObligatoire = info["obligatoire"];

    const divConteneur = document.createElement("div");
    const divLibelle = document.createElement("div");
    const titreQuestion = document.createElement("h4");
    const spanObligatoire = document.createElement('span');
    const divSousConteneur = document.createElement("div");

    let elementReponse;

    divConteneur.classList.add("block");
    divConteneur.dataset._id = _id;
    divLibelle.classList.add("is-flex", "is-flex-direction-row");
    titreQuestion.classList.add("title", "is-4", "has-text-weight-semibold");
    titreQuestion.innerText = libelleQestion;
    titreQuestion.style.marginBottom = "10px";
    divSousConteneur.classList.add("ml-3", "field", "control");

    spanObligatoire.style.color = "red";
    spanObligatoire.innerText = "*";
    spanObligatoire.classList.add("is-size-5", "ml-1");
    spanObligatoire.title = "obligatoire";

    // changer ça dans le future : selection du type de reponse avec la variable type
    switch (type) {
        case TypeQuestion.CHAMPS_LONG :
        case TypeQuestion.CHAMPS_COURT :
            elementReponse = document.createElement("textarea");
            elementReponse.rows = type == TypeQuestion.CHAMPS_COURT ? 1 : 4;
            // elementReponse.name = `${libelleQestion}-reponse1`;
            elementReponse.classList.add("textarea");
            elementReponse.style.border = "1px solid";
            elementReponse.disabled = true;
            elementReponse.style.resize = "none";
            break;
        
        case TypeQuestion.RADIO_BOUTON :
        case TypeQuestion.CHECK_BOUTON :
            elementReponse = document.createElement("div");
            elementReponse.classList.add(type == TypeQuestion.RADIO_BOUTON ? "radios" : "checkboxs");
            
            const input = document.createElement("input");
            input.type = type == TypeQuestion.RADIO_BOUTON ? "radio" : "checkbox";
            input.disabled = true;

            const span = document.createElement("span");
            span.innerText = " Réponse 1";
            span.dataset._id = `${_id}-${donnerNombreReponse(_id)}`;
            span.classList.add("libelle-reponse");

            const spanEspace = document.createElement("span").innerText=" ";

            const label = document.createElement("label");
            label.classList.add(type == TypeQuestion.RADIO_BOUTON ? "radio" : "checkbox");
            label.append(input, spanEspace, span);
            
            elementReponse.appendChild(label);
            break;
    }

    if (!estObligatoire) {
        spanObligatoire.style.display = "none";
    }
    
    if (elementReponse != null) {
        divSousConteneur.appendChild(elementReponse);
    }

    //titreQuestion.appendChild(spanObligatoire);
    divLibelle.append(titreQuestion, spanObligatoire);
    divConteneur.append(divLibelle, divSousConteneur);
    //divConteneur.appendChild(divSousConteneur);
    parent.appendChild(divConteneur);
}

document.addEventListener("DOMContentLoaded", async () => {
    
});

/**
 * Modifi une question dans le visualisateur de questionnaire (partie de droite)
 * @param {int || string} id - identifiant de la question
 * @param {string} libelle - le nouveau libelé
 */
export function modifierQuestionVisualiseurQuestionnaire(id, libelle) {
    const identifiant = String(id);
    const question = donnerQuestionAvecIdVisualiseurQuestionnaire(identifiant.includes("-") ? identifiant.split("-")[0] : identifiant);
    if (identifiant.includes("-")) {
        const reponse = question.querySelector(`span.libelle-reponse[data-_id="${id}"`);
        reponse.textContent = libelle;
    } else {
        const baliseH4Question = question.querySelector('h4');
        baliseH4Question.innerText = libelle; 
    }
    
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