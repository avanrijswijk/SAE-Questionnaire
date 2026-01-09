import { attribuerModalModifierQuestionAvaecConteneur, attribuerModalModifierQuestionAvecId, TypeModifier } from '../modals/modificationQuestion/modalModifier.js';
import {TypeQuestion} from '../typeQuestion.js';
import { attribuerContexteMenu } from "../contextMenu/contextMenu.js";

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
 * 
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int) 
 * @returns {HTMLDivElement} - un div.div-question.box.div-box comptenant l'intitule de la question
 */
function creerQuestion(info) {
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

    //attribuerModalModifierQuestionAvaecConteneur(_id, TypeModifier.QUESTION); // PB ICI
    attribuerContexteMenu(divConteneur, type);
    return divConteneur;
}

/**
 * 
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int, nombreReponse:int) 
 * @returns {HTMLDivElement || null} - un div.div-reponses ou null si aucun type ne correspond
 */
function creerReponse(info) {
    //const libelle = info["intitule"];
    const _id = info["_id"];
    //const obligatoire = info["obligatoire"];
    const type = info["type"];
    const nombreReponse = info["nombreReponse"];

    // const divReponses = document.createElement("div");
    // divReponses.classList.add("div-reponses");

    let divReponse;

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            divReponse = document.createElement("div");
            divReponse.classList.add("box", "div-box", "div-reponse");
            divReponse.dataset._id = `${_id}-${nombreReponse}`;

            const pReponse = document.createElement("p");
            pReponse.innerText = `Réponse ${nombreReponse+1}`;
            pReponse.classList.add("is-unselectable", "question");
            
            divReponse.appendChild(pReponse);
            //divReponses.appendChild(divReponse);
            break;

        case TypeQuestion.LISTE_DEROULANTE:
            break;

        default:
            break;
    }

    //return divReponses.children.length>1? null : divReponses;
    return divReponse;
}

/**
 * Ajoute une question dans le visualiseur de questions (partie gauche)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int)
 */
function ajouterQuestionVisualiseurQuestions(parent, info) {
    const type = info["type"];
    const _id = info["_id"];

    // conteneur
    const divConteneur = creerQuestion(info);

    // bouton check/radio
    let divReponses;
    if (type == TypeQuestion.CHECK_BOUTON || type == TypeQuestion.RADIO_BOUTON) {
        divReponses = document.createElement("div");
        divReponses.classList.add("div-reponses");
        info["nombreReponse"] = 0;
        const divReponse = creerReponse(info);
        divReponses.appendChild(divReponse);
        divConteneur.appendChild(divReponses);
        affichageReponses(divReponses);
    }

    parent.appendChild(divConteneur);
    if (divReponses) { // si divReponses est initialisé
        const divReponse = divReponses.firstChild; // on prend son premier enfant qui est initialisé comme div.div-reponse
        attribuerModalModifierQuestionAvecId(divReponse.dataset._id, TypeModifier.REPONSE);
    }
    attribuerModalModifierQuestionAvecId(_id, TypeModifier.QUESTION);
}

function ajouterReponseVisualisateurQuestions(id) {
    const divQuestion = document.querySelector(`div[data-_id="${id}"]`)
                                .closest("div.box.div-question.div-box")
                                .firstChild;
    const divReponses = document.querySelector(`div[data-_id="${id}"]`)
                                .closest("div.box.div-question.div-box")
                                .querySelector("div.div-reponses");
    let identifiantReponse = -1;
    
    if (!divReponses) return identifiantReponse;

    const type = divQuestion.dataset.type;

    const info = {
        "intitule" : "",
        "type" : type,
        "obligatoire" : true,
        "_id" : divQuestion.dataset._id
    }

    

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            info["nombreReponse"] = divReponses.childElementCount;
            const divReponse = creerReponse(info);
            if (divReponse){
                console.log("Le div réponse est ajouté")
                divReponses.appendChild(divReponse);
                identifiantReponse = divReponse.dataset._id;
                attribuerModalModifierQuestionAvecId(identifiantReponse, TypeModifier.REPONSE);
            } else {console.log("Le div réponse n'est pas ajouté");}
            break;

        case TypeQuestion.LISTE_DEROULANTE:
            break;
    }
    return identifiantReponse;
}

/**
 * Affiche ou cache le div des réponses pour une question bouton(s) radio/check ou liste déroulante
 * @param {HTMLDivElement} divReponses - le div qui contient les réponses 
 */
function affichageReponses(divReponses) {
    if (divReponses == null) {return;}
    
    const divQuestion = divReponses.parentElement;
    
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
    question.dataset.intitule = libele;
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


/**
 * Donne le nombre de réponse à une question (ne fonctionne pas pour une zone d'entrée)
 * @param {int || string} id - l'identifiant de la question
 * @returns {int} - le nombre de réponse de la question
 */
function donnerNombreReponse(id) {
    const divReponses = donnerQuestionAvecIdVisualiseurQuestions(id).parentElement.querySelector("div.div-reponses");
    if (!divReponses) {
        return 0;
    } else {
        return divReponses.childElementCount;
    }
}

export {
    ajouterQuestionVisualiseurQuestions, 
    ajouterReponseVisualisateurQuestions,
    modifierQuestionVisualiseurQuestions, 
    donnerQuestionAvecIdVisualiseurQuestions, 
    donnerLibelleQuestionAvecIdVisualiseurQuestions,
    donnerNombreReponse
}