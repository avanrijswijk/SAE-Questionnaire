import {TypeQuestion} from '../typeQuestion.js';
import { donnerNombreReponse } from "./questions.js";

// Ce fichier est le fichier relié à l'ajout d'une question dans la partie pour la visualitation du questionnaire

function ajouterReponseVisualiseurQuestionnaire(idReponse, type, texte = null) {
    const idQuestion = String(idReponse).split("-")[0];

    let _type;
    switch (type) {
        case TypeQuestion.CHECK_BOUTON: _type = "checkboxs"; break;
        case TypeQuestion.RADIO_BOUTON: _type = "radios"; break;
    }

    const divReponses = document
        .querySelector(`div.block[data-_id="${idQuestion}"]`)
        .querySelector(`div.${_type ?? "rien"}`);

    if (divReponses) {
        const index = parseInt(String(idReponse).split("-")[1]);
        const divReponse = creerReponse(idReponse, type, index, texte);
        if (divReponse) divReponses.appendChild(divReponse);
    }
}

function creerReponse(id, type, nombreReponse = 0, texte = null) {
    let elementReponse;

    switch (type) {

        case TypeQuestion.CHAMPS_LONG:
        case TypeQuestion.CHAMPS_COURT:
            elementReponse = document.createElement("textarea");
            elementReponse.rows = type == TypeQuestion.CHAMPS_COURT ? 1 : 4;
            elementReponse.classList.add("textarea");
            elementReponse.style.border = "1px solid";
            elementReponse.disabled = true;
            elementReponse.style.resize = "none";
            break;

        case TypeQuestion.RADIO_BOUTON:
        case TypeQuestion.CHECK_BOUTON:
            const input = document.createElement("input");
            input.type = type == TypeQuestion.RADIO_BOUTON ? "radio" : "checkbox";
            input.disabled = true;

            const span = document.createElement("span");
            span.innerText = texte ?? `Réponse ${nombreReponse + 1}`;
            span.dataset._id = `${id}`;
            span.dataset.type = type;
            span.classList.add("libelle-reponse");

            const label = document.createElement("label");
            label.classList.add(type == TypeQuestion.RADIO_BOUTON ? "radio" : "checkbox");
            label.append(input, document.createTextNode(" "), span);

            elementReponse = label;
            break;
    }

    return elementReponse;
}

function ajouterQuestionVisualiseurQuestionnaire(parent, info, chargement = false) {
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

    spanObligatoire.innerText = "*";
    spanObligatoire.style.color = "red";

    divSousConteneur.classList.add("ml-3", "field", "control");

    if (type == TypeQuestion.RADIO_BOUTON || type == TypeQuestion.CHECK_BOUTON) {
        elementReponse = document.createElement("div");
        elementReponse.classList.add(type == TypeQuestion.RADIO_BOUTON ? "radios" : "checkboxs", "is-flex", "is-flex-direction-column");

        if (!chargement) {
            elementReponse.appendChild(creerReponse(`${_id}-0`, type, 0));
        }

    } else if (type != TypeQuestion.CONTEXT) {
        elementReponse = creerReponse(`${_id}-0`, type, 0);
    }

    if (!estObligatoire) spanObligatoire.style.display = "none";

    if (elementReponse) divSousConteneur.appendChild(elementReponse);

    divLibelle.append(titreQuestion, spanObligatoire);
    divConteneur.append(divLibelle, divSousConteneur);
    parent.appendChild(divConteneur);
}

function supprimerQuestionVisualiseurQuestionnaire(id) {
    
    // console.warn("Faire la modification de la suppression d'une question/réponse dans le visualisateur de questionnaire");
    if (String(id).includes("-")) {
        const spanReponse = document.querySelector(`span[data-_id="${id}"]`);
        
        if (!spanReponse) return;
        const labelReponse = spanReponse.closest("label");
        if (!labelReponse) return;
        const type = spanReponse.dataset.type;
        const parent = labelReponse.parentElement;
        labelReponse.remove();

        if (parent.childElementCount <= 0) {
            console.log(parent.childElementCount);
            ajouterReponseVisualiseurQuestionnaire(id, type);
            modifierQuestionVisualiseurQuestionnaire(id, "Réponse 1");
        }
    } else{
        const divQuestion = document.querySelector(`div.block[data-_id="${id}"]`);
        divQuestion.remove();
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    
});

/**
 * Modifi une question dans le visualisateur de questionnaire (partie de droite)
 * @param {int || string} id - identifiant de la question
 * @param {string} libelle - le nouveau libelé
 */
function modifierQuestionVisualiseurQuestionnaire(id, libelle) {
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
function donnerQuestionAvecIdVisualiseurQuestionnaire(id) {
    const divVisualiseurQuestions = document.getElementById("visualiseur-qestionnaire");
    return divVisualiseurQuestions.querySelector(`[data-_id="${id}"]`);
}

export {
    donnerQuestionAvecIdVisualiseurQuestionnaire,
    modifierQuestionVisualiseurQuestionnaire,
    ajouterQuestionVisualiseurQuestionnaire,
    ajouterReponseVisualiseurQuestionnaire,
    supprimerQuestionVisualiseurQuestionnaire
}