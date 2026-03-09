import {TypeQuestion} from '../typeQuestion.js';
import { attribuerContexteMenu } from "../contextMenu/contextMenu.js";
import { notification, TypeNotification } from "../../utils/notification/notification.js";
import { synchroniserOrdreQuestion } from "./questionnaire.js"

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
    /*margin-top: 10px;*/
    padding: 5px 10px;
    overflow: hidden;
}


/*div.box.div-question:hover {
    border-left: 4px #90D5FF solid;
    transition: border-left 0.2s ease-out;
}

div.box.div-question:not(:hover) {
    border-left: 0px #90D5FF solid;
    transition: border-left 0.2s ease-out;
    user-select: none;
    -webkit-user-select: none; /* Pour Safari */
    -webkit-touch-callout: none; /* Pour mobile */
}*/

div.div-reponses {
}

div.div-reponse {
    background-color: #eaeaea;
}

div.div-box-deplacement {
    transform: translateX(20px);
    transition: transform .2s ease-out;
    position: absolute;
    right: 8px;
    top: 6px;
    background: inherit;
    cursor: grab;
}

div.box.div-question:hover div.div-box-deplacement {
    transform: translateX(0px);
}

div.dnd {
    padding: 5px;
    transition: padding .2s ease-out;
}

div.dnd.drag {
    padding: 6px;
    background-color: #90D5FF;
    border-radius: 15px;
    opacity: 0.8;
    margin: 0 10px;
}
`);

document.adoptedStyleSheets = [...document.adoptedStyleSheets, style];

/////////////////* PARTIE DRAG AND DROP */////////////////

// est appelée quand la question est déposé apres le dnd
function dropHandler(ev) {
    ev.preventDefault();
    // const data = ev.dataTransfer.getData("text");
    // console.log("_id : "+ data);
    // console.log(data);
    //ev.target.appendChild(document.querySelector(`div[data-_id="$(data)"]`));
}

// est appelée quand la question est aggriper pour le dnd
function dragstartHandler(ev) {
    if (ev.target.closest) {
        const element = ev.target.closest("div.box.div-question.div-box").firstElementChild;
        ev.dataTransfer.setData("text", element.dataset._id);
    }
    
}

//
function dragoverHandler(ev) {
    ev.preventDefault();
}

// fonction pour enlever le fait qu'une question ne soit plus dnd
const listenerDragEnd = (div) => {
    div.addEventListener('dragend', (e) => {
        div.setAttribute('draggable', 'false');
        div.removeEventListener("dragend", listenerDragEnd);
    })
};

// quand on passe au dessus d'un element où le dnd est possible
document.addEventListener("dragover", (e) => {
    e.preventDefault();
    if (e.target.classList.contains("dnd")) {
        e.target.classList.add("drag");
    }
});

// quand on sort d'une zone où le dnd est possible
document.addEventListener("dragleave", (e) => {
    e.preventDefault();
    if (e.target.classList.contains("dnd")) {
        e.target.classList.remove("drag");
    }
});

 // quand on clic dans la zone où le deplacement de la question est
document.addEventListener('mousedown', (e) => {
    if (e.target.classList.contains("element-for-drag")) {
        window.getSelection().removeAllRanges(); // enlève la section pour ne garder que la zone à grab
        const divParent = e.target.closest("div.box.div-question.div-box");
        divParent.setAttribute('draggable', 'true');
        listenerDragEnd(divParent);
    }
});

// quand on pose un element dans un zone où le dnd est possible
document.addEventListener("drop", (e) => {
    e.preventDefault();
    const parentVQ = document.getElementById("visualiseur-questions");
    if (e.target.classList.contains("dnd")) {
        const notifErreur = () => {
            notification(TypeNotification.ERREUR, "Une erreur est survenue lors du déplacement de la question.");
        }

        const zoneDnd = e.target; 
        zoneDnd.classList.remove("drag");

        const indexZoneDnd = Array.prototype.indexOf.call(parentVQ.children, zoneDnd);
        const question = parentVQ.querySelector(`div[data-_id="${e.dataTransfer.getData("text")}"]`).parentElement; 
        const indexQuestion = Array.prototype.indexOf.call(parentVQ.children, question);

        if (!(indexQuestion+1 < parentVQ.children.length)){notifErreur(); return;} 
        if (!parentVQ.children[indexQuestion+1].classList.contains("dnd")){notifErreur(); return;} 
        if (zoneDnd.nextElementSibling === question) { return ;} 
        
        const zoneDndQuestion = parentVQ.children[indexQuestion+1];

        if (Array.prototype.indexOf.call(parentVQ.children, zoneDndQuestion) == indexZoneDnd){return;} 

        // 1. On déplace les éléments dans le DOM de GAUCHE de manière standard
        parentVQ.insertBefore(question, zoneDnd);
        parentVQ.insertBefore(zoneDndQuestion, question);

        // 2. On lit le NOUVEL ORDRE généré à gauche
        const nouvelOrdreIds = [];
        // On récupère toutes les questions de la partie gauche
        const questionsAGauche = parentVQ.querySelectorAll("div.div-question"); 
        
        questionsAGauche.forEach(divConteneur => {
            // Dans votre structure, le data-_id est sur le premier enfant (divQuestion)
            const id = divConteneur.firstElementChild.dataset._id; 
            if(id) nouvelOrdreIds.push(id);
        });

        // 3. On synchronise la partie DROITE avec cet ordre parfait
        synchroniserOrdreQuestion(nouvelOrdreIds);
    }
});

// quand on relache le clic dans la zone où le deplacement de la question est
document.addEventListener('mouseup', (e) => {
    if (e.target.classList.contains("element-for-drag")) {
        const divParent = e.target.closest("div.box.div-question.div-box");
        divParent.setAttribute('draggable', 'false');
    }
    
});

/** 
 * @returns {HTMLDivElement} - un div.dnd qui est une zone pour le dnd 
*/
function creerZoneDnd() {
    const divDnd = document.createElement("div");
    divDnd.ondrop = dropHandler;
    divDnd.ondragover = dragoverHandler;
    divDnd.classList.add("dnd");
    return divDnd;
}

document.addEventListener("DOMContentLoaded", () => {
    const parentVQ = document.getElementById("visualiseur-questions");
    parentVQ.appendChild(creerZoneDnd());
});


/////////////////* PARTIE CREATION QUESTION */////////////////

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
    const divDnd = document.createElement("div");
    const strongDnd = document.createElement("strong");

    divConteneur.ondragstart = dragstartHandler;
    divConteneur.draggable = false;

    divConteneur.classList.add("box", "div-question", "div-box", "is-relative");
    divQuestion.dataset._id = _id;
    divQuestion.dataset.intitule = libelle;
    divQuestion.dataset.type = type;
    divQuestion.dataset.obligatoire = obligatoire;

    titreQuestion.classList.add("is-unselectable", "question");
    titreQuestion.innerText = libelle.toString();
    titreQuestion.title = libelle.toString();

    divQuestion.appendChild(titreQuestion);

    divDnd.classList.add("div-box-deplacement");
    strongDnd.classList.add("is-unselectable", "element-for-drag");
    strongDnd.innerText = "⁝⁝";
    divDnd.appendChild(strongDnd);

    divConteneur.append(divQuestion, divDnd);

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
    const _id = info["_id"];
    const type = info["type"];
    const nombreReponse = info["nombreReponse"];

    const divReponse = document.createElement("div");
    divReponse.classList.add("box", "div-box", "div-reponse");

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            divReponse.dataset._id = `${_id}-${nombreReponse}`;
            divReponse.dataset.intitule = `Réponse ${nombreReponse+1}`;

            const pReponse = document.createElement("p");
            pReponse.innerText = `Réponse ${nombreReponse+1}`;
            pReponse.classList.add("is-unselectable", "question");
            
            divReponse.appendChild(pReponse);
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
    //const _id = info["_id"];

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
    parent.appendChild(creerZoneDnd());
}

/**
 * Ajout une réponse à une question dans le visualisateur de questions
 * @param {int || string} id - l'identifiant de la question (X)
 * @param {number} [idReponse=-1] - l'identifiant de la réponse (X) s'il en faut une spécifique. id auto sinon
 * @returns {int} - l'identifiant de la réponse. -1 si un probleme est survenu
 */
function ajouterReponseVisualisateurQuestions(id, idReponse=-1) {
    const divQuestion = document.querySelector(`div[data-_id="${id}"]`)
                                .closest("div.box.div-question.div-box")
                                .firstChild;
    const divReponses = document.querySelector(`div[data-_id="${id}"]`)
                                .closest("div.box.div-question.div-box")
                                .querySelector("div.div-reponses");

    let identifiantReponse = idReponse;
    
    if (!divReponses) return identifiantReponse;

    const type = divQuestion.dataset.type;

    const info = {
        "intitule" : "",
        "type" : type,
        "obligatoire" : true,
        "_id" : divQuestion.dataset._id
    }

    const nombreReponse = function() {
        let idMax = 0;
        Array.from(divReponses.children).forEach((divReponse) => {
            const id = parseInt(String(divReponse.dataset._id).split("-")[1]);
            if (idMax < id) { 
                idMax = id;
            }
        });
        return idMax+1;
    };
    console.log(`nb réponse +1 : ${nombreReponse()}`);

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            info["nombreReponse"] = nombreReponse(); //divReponses.childElementCount;
            const divReponse = creerReponse(info);
            if (divReponse){
                divReponses.appendChild(divReponse);
                if (identifiantReponse > 0) {
                    divReponse.dataset._id = `${id}-${identifiantReponse}`;
                } else {
                    identifiantReponse = divReponse.dataset._id;
                }
                
                //attribuerModalModifierQuestionAvecId(identifiantReponse, TypeModifier.REPONSE);
            }
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
 * Supprime une question / reponse
 * @param {int || string} id - l'identifiant de la question / reponse (X ou X-X) 
 */
function supprierQuestionVisualiseurQuestions(id) {
    try {
        const divQuestion = document.querySelector(`div[data-_id="${id}"]`);
        if (String(id).includes("-")) {
            const parent = divQuestion.parentElement;
            divQuestion.remove();
            if (parent.childElementCount <= 0) {
                ajouterReponseVisualisateurQuestions(String(id).split("-")[0], String(id).split("-")[1]);
                modifierQuestionVisualiseurQuestions(id, "Réponse 1");
            }
        } else {
            divQuestion.closest("div.box.div-question.div-box").remove();
        }
    } catch (e) {
        console.error(e);
        notification(TypeNotification.ERREUR, `Une erreur est survenue lors de la suppression d'une ${String(id).includes("-") ? "réponse." : "question."}`);
    }
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
    supprierQuestionVisualiseurQuestions, 
    donnerQuestionAvecIdVisualiseurQuestions, 
    donnerLibelleQuestionAvecIdVisualiseurQuestions,
    donnerNombreReponse
}