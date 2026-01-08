import { ouvrireModalModifierQuestion, TypeModifier } from '../modals/modificationQuestion/modalModifier';
import {TypeQuestion} from '../typeQuestion.js';

const style = new CSSStyleSheet();
style.replaceSync(`
p.question {
    display: -webkit-box; 
    -webkit-line-clamp: 1; 
    -webkit-box-orient: vertical; 
    overflow: hidden; 
    text-overflow: ellipsis;
    white-space: normal;
}

div.box.div-box {
    margin-bottom: 0;
    margin-top: 10px;
    padding: 5px 10px;
}

div.box.div-question:hover {
    border-left: 4px #90D5FF solid;
    transition: border-left 0.2s ease-out;
}

div.box.div-question:not(:hover) {
    border-left: 0px #90D5FF solid;
    transition: border-left 0.2s ease-out;
}

div.div-reponses {
}

div.div-reponse {
    background-color: #eaeaea;
}
`);

document.adoptedStyleSheets = [...document.adoptedStyleSheets, style];

/**
 * Ajoute une question dans le visualiseur de questions (partie gauche)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int)
 */
function ajouterQuestionVisualiseurQuestions(parent, info) {
    const libelle = info["intitule"];
    const _id = info["_id"];
    const obligatoire = info["obligatoire"];
    const type = info["type"];

    // conteneur
    const divConteneur = document.createElement("div");
    const divQuestion = document.createElement("div");
    const titreQuestion = document.createElement("p");

    divConteneur.classList.add("box", "div-question", "div-box");
    divQuestion.dataset._id = _id;
    divQuestion.dataset.intitule = libelle;
    divQuestion.dataset.type = type;
    divQuestion.dataset.obligatoire = obligatoire;

    titreQuestion.classList.add("is-unselectable", "question");
    titreQuestion.innerText = libelle.toString();
    titreQuestion.title = libelle.toString();

    divQuestion.appendChild(titreQuestion);
    divConteneur.appendChild(divQuestion);
    
    // variables réponses
    let divReponses;
    let divReponse;
    let pReponse;

    // bouton check/radio
    if (type == TypeQuestion.CHECK_BOUTON || type == TypeQuestion.RADIO_BOUTON) {
        divReponses = document.createElement("div");
        divReponses.classList.add("div-reponses");

        divReponse = document.createElement("div");
        divReponse.classList.add("box", "div-box", "div-reponse");
        divReponse.dataset._id = `${_id}-${divReponses.children.length}`;

        pReponse = document.createElement("p");
        pReponse.innerText = "Réponse 1";
        pReponse.classList.add("is-unselectable");
        
        divReponse.appendChild(pReponse);
        divReponses.appendChild(divReponse);
        divConteneur.appendChild(divReponses);

        affichageReponses(divQuestion, divReponses);
    }

    //
    parent.appendChild(divConteneur);

    ouvrireModalModifierQuestion(_id, TypeModifier.QUESTION);
    if (divReponse) {
        console.log("hello");
        ouvrireModalModifierQuestion(divReponse.dataset._id, TypeModifier.REPONSE);
    }
}

/**
 * Affiche ou cache le div des réponses pour une question bouton(s) radio/check ou liste déroulante
 * @param {HTMLDivElement} divQuestion - me div qui contient la question
 * @param {HTMLDivElement} divReponses - le div qui contient les réponses 
 */
function affichageReponses(divQuestion, divReponses) {
    divQuestion.addEventListener("click", (event) => {
        if (event.ctrlKey || event.metaKey) {
            if (divReponses.style.display == "none") {
                divReponses.style.display = "";
            } else {
                divReponses.style.display = "none";
            }
        }
    });
}

/**
 * Modifi une question dans le visualisateurs de questions (partie de gauche)
 * @param {int} id - identifiant de la question
 * @param {string} libele - le nouveau libelé
 */
function modifierQuestionVisualiseurQuestions(id, libele) {
    const question = donnerQuestionAvecIdVisualiseurQuestions(id);
    const balisePQuestion = question.querySelector('p');
    balisePQuestion.innerText = libele;
    balisePQuestion.title = libele;
}


/**
 * retourne le div de la question avec son id
 * (pour une question présent dans le visualisateur de questions (partie de gauche)) 
 * @param {int || string} id - identifiant de la question
 * @returns {HTMLDivElement} le div de la question
 */
function donnerQuestionAvecIdVisualiseurQuestions(id) {
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    return divVisualiseurQuestions.querySelector(`[data-_id="${id}"]`);
}

/**
 * donne le libele de la question en fonction de son id 
 * (pour une question présent dans le visualisateur de questions (partie de gauche))
 * @param {int} id - identifiant de la question 
 * @returns {string} le libele
 */
function donnerLibelleQuestionAvecIdVisualiseurQuestions(id) {
    const divQuestion = donnerQuestionAvecIdVisualiseurQuestions(id);
    const pLibelle = divQuestion.querySelector("p");
    return pLibelle.innerText;
}


function donnerNombreReponse(id) {
    const divReponses = donnerQuestionAvecIdVisualiseurQuestions(id).parentElement.querySelector("div.div-reponses");
    if (divReponses) {
        return 0;
    } else {
        return divReponses.childElementCount;
    }
}

export {
    ajouterQuestionVisualiseurQuestions, 
    modifierQuestionVisualiseurQuestions, 
    donnerQuestionAvecIdVisualiseurQuestions, 
    donnerLibelleQuestionAvecIdVisualiseurQuestions,
    donnerNombreReponse
}