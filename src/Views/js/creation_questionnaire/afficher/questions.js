import {TypeQuestion} from '../typeQuestion.js';
import { attribuerContexteMenu } from "../contextMenu/contextMenu.js";
import { notification, TypeNotification } from "../../utils/notification/notification.js";
import { synchroniserOrdreQuestion, synchroniserOrdreReponse } from "./questionnaire.js"

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

div.box.div-question:hover > div.div-box-deplacement,
div.box.div-reponse:hover > div.div-box-deplacement {
    transform: translateX(0px);
} 

div.box.div-question.hover > div.div-box-deplacement,
div.box.div-reponse.hover > div.div-box-deplacement {
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

/////////////////////* PARTIE HOVER */////////////////////

// quand la souris entre
// document.addEventListener("mouseover", (e) => {
//     const target = e.target
//     if (target.classList.contains("div-reponse") || target.classList.contains("div-reponse > p")) {
//         target.classList.add("hover");
//     } else
//     if (target.classList.contains("div-question") || target.classList.contains("div-question > div > p")) {
//         target.classList.add("hover");
//     }
// });


// // quand la souris sort
// document.addEventListener("mouseout", (e) => {
//     const target = e.target;
    
//     if (target.classList.contains("div-box-deplacement > strong.element-for-drag") || target.classList.contains("div-box-deplacement")) {
//         console.log("amama");
//         return;
//     }    
//        if (target.classList.contains("div-reponse") || target.classList.contains("div-reponse > p")) {
//             target.classList.remove("hover");
//         } 
//         if (target.classList.contains("div-question") || target.classList.contains("div-question > div > p")) {
//             target.classList.remove("hover");
//         } 
    
    
// });

/////////////////* PARTIE DRAG AND DROP */////////////////

// est appelée quand la question est déposé apres le dnd
function dropHandler(ev) {
    ev.preventDefault();
    // const data = ev.dataTransfer.getData("text");
    // console.log("_id : "+ data);
    // console.log(data);
    //ev.target.appendChild(document.querySelector(`div[data-_id="$(data)"]`));
}

// est appelée quand la question / reponse est selectionnée (debut du dnd)
function dragstartHandler(ev) {
    if (ev.target.closest) {
        const divBox = ev.target.closest("div.box.div-box[draggable='true']");
        if (divBox) {
            // Si c'est une question
            if (divBox.classList.contains("div-question")) {
                ev.dataTransfer.setData("text", divBox.firstElementChild.dataset._id);
            }
            // Si c'est une réponse
            else if (divBox.classList.contains("div-reponse")) {
                ev.dataTransfer.setData("text", divBox.dataset._id);
            }
        }
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
        window.getSelection().removeAllRanges();

        const divParent = e.target.closest("div.box.div-box"); 
        divParent.setAttribute('draggable', 'true');

        listenerDragEnd(divParent);
    }
});

// quand on pose un element dans un zone où le dnd est possible
document.addEventListener("drop", (e) => {
    e.preventDefault();
    const idDeplace = e.dataTransfer.getData("text");
    const estUneReponse = String(idDeplace).includes("-");

    if (e.target.classList.contains("dnd")) {
        const zoneDnd = e.target; 
        zoneDnd.classList.remove("drag");
        
        const notifErreur = () => {
            notification(TypeNotification.ERREUR, "Une erreur est survenue lors du déplacement.");
        }

        // si c'est une reponse
        if (estUneReponse && zoneDnd.classList.contains("dnd-reponse")) {
            const conteneurReponses = zoneDnd.closest("div.div-reponses");
            const reponse = conteneurReponses.querySelector(`div[data-_id="${idDeplace}"]`);
            
            const indexZoneDnd = Array.prototype.indexOf.call(conteneurReponses.children, zoneDnd);
            const indexReponse = Array.prototype.indexOf.call(conteneurReponses.children, reponse);

            if (!(indexReponse+1 < conteneurReponses.children.length)){notifErreur(); return;} 
            if (zoneDnd.nextElementSibling === reponse) { return ;} 

            const zoneDndReponseAdjacente = conteneurReponses.children[indexReponse+1];

            conteneurReponses.insertBefore(reponse, zoneDnd);
            conteneurReponses.insertBefore(zoneDndReponseAdjacente, reponse);

            const nouvelOrdreIds = [];
            const reponsesApresDeplacement = conteneurReponses.querySelectorAll("div.div-reponse"); 
            reponsesApresDeplacement.forEach(div => {
                nouvelOrdreIds.push(div.dataset._id);
            });

            const idQuestion = String(idDeplace).split("-")[0];
            synchroniserOrdreReponse(idQuestion, nouvelOrdreIds);
            
        // si c'est une question
        } else if (!estUneReponse && !zoneDnd.classList.contains("dnd-reponse")) {
            const parentVQ = document.getElementById("visualiseur-questions");
            const question = parentVQ.querySelector(`div[data-_id="${idDeplace}"]`).parentElement; 
            const indexZoneDnd = Array.prototype.indexOf.call(parentVQ.children, zoneDnd);
            const indexQuestion = Array.prototype.indexOf.call(parentVQ.children, question);

            if (!(indexQuestion+1 < parentVQ.children.length)){notifErreur(); return;} 
            if (!parentVQ.children[indexQuestion+1].classList.contains("dnd")){notifErreur(); return;} 
            if (zoneDnd.nextElementSibling === question || zoneDnd.previousElementSibling === question) { return ;}
            
            const zoneDndQuestion = parentVQ.children[indexQuestion+1];

            if (Array.prototype.indexOf.call(parentVQ.children, zoneDndQuestion) == indexZoneDnd){return;} 

            parentVQ.insertBefore(question, zoneDnd);
            parentVQ.insertBefore(zoneDndQuestion, question);

            const nouvelOrdreIds = [];
            const questionsAGauche = parentVQ.querySelectorAll("div.div-question"); 
            questionsAGauche.forEach(divConteneur => {
                const id = divConteneur.firstElementChild.dataset._id; 
                if(id) nouvelOrdreIds.push(id);
            });

            synchroniserOrdreQuestion(nouvelOrdreIds);
        }
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

/** * @returns {HTMLDivElement} - un div.dnd.dnd-reponse qui est une zone pour le dnd des réponses
*/
function creerZoneDndReponse() {
    const divDnd = document.createElement("div");
    divDnd.ondrop = dropHandler;
    divDnd.ondragover = dragoverHandler;
    divDnd.classList.add("dnd", "dnd-reponse"); // Note l'ajout de dnd-reponse
    // Optionnel : tu peux réduire le padding pour les réponses via CSS si c'est trop espacé
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

    attribuerContexteMenu(divConteneur, type);
    return divConteneur;
}

/**
 * 
 * @param {JSON} info - Les informations sur la question (intitule:str, type:str, obligatoire:bool, _id:int, nombreReponse:int) 
 * @returns {HTMLDivElement || null} - un div.div-reponses ou null si aucun type ne correspond
 */
function creerReponse(info) {
    const divDnd = document.createElement("div");
    const strongDnd = document.createElement("strong");
    const _id = info["_id"];
    const type = info["type"];
    const nombreReponse = info["nombreReponse"];

    const divReponse = document.createElement("div");
    divReponse.classList.add("box", "div-box", "div-reponse", "is-relative"); 

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            divReponse.dataset._id = `${_id}-${nombreReponse}`;
            divReponse.dataset.intitule = `Réponse ${nombreReponse+1}`;

            const pReponse = document.createElement("p");
            pReponse.innerText = `Réponse ${nombreReponse+1}`;
            pReponse.classList.add("is-unselectable", "question");
            
            // dnd
            const divDnd = document.createElement("div");
            divDnd.classList.add("div-box-deplacement");
            const strongDnd = document.createElement("strong");
            strongDnd.classList.add("is-unselectable", "element-for-drag");
            strongDnd.innerText = "⁝⁝";
            divDnd.appendChild(strongDnd);

            divReponse.append(pReponse, divDnd);
            
            divReponse.ondragstart = dragstartHandler;
            divReponse.draggable = false;
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
        
        divReponses.appendChild(creerZoneDndReponse());
        const divReponse = creerReponse(info);
        divReponses.appendChild(divReponse);
        divReponses.appendChild(creerZoneDndReponse());
        
        divConteneur.appendChild(divReponses);
        divConteneur.dataset.replier = "0";
        affichageReponses(divReponses);
    }

    parent.append(divConteneur, creerZoneDnd());
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
        let idMax = -1; // CORRECTION 1 : On commence à -1 pour que le +1 donne 0 s'il n'y a rien
        Array.from(divReponses.children).forEach((divReponse) => {
            // CORRECTION 2 : On filtre pour ignorer les zones DnD qui n'ont pas d'ID
            if (divReponse.classList.contains("div-reponse") && divReponse.dataset._id) {
                const idExtrait = parseInt(String(divReponse.dataset._id).split("-")[1]);
                if (idMax < idExtrait) { 
                    idMax = idExtrait;
                }
            }
        });
        return idMax + 1;
    };
    //console.log(`nb réponse +1 : ${nombreReponse()}`); // debug

    switch (type) {
        case TypeQuestion.CHECK_BOUTON:
        case TypeQuestion.RADIO_BOUTON:
            info["nombreReponse"] = nombreReponse(); 
            const divReponse = creerReponse(info);
            if (divReponse){
                divReponses.appendChild(divReponse);
                divReponses.appendChild(creerZoneDndReponse());
                if (identifiantReponse >= 0) {
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
    
    const divQuestion = divReponses.parentElement; // le parent et divQuestion
    
    divQuestion.addEventListener("click", (event) => {
        if (event.ctrlKey || event.metaKey) {
            // if (divReponses.style.display == "none") {
            //     divReponses.style.display = "";
            //     divQuestion.dataset.replier = "0";
            // } else {
            //     divReponses.style.display = "none";
            //     divQuestion.dataset.replier = "1";
            // }
            divQuestion.dataset.replier = replierReponses(divReponses);
        }
    });
}

/**
 * Repli le div des réponses d'un div question
 * @param {HTMLDivElement} divReponses - le div qui contient les réponses
 * @returns Int - 0 quand ce n'est pas replier ; 1 quand c'est replier
 */
function replierReponses(divReponses) {
    if (divReponses == null) {return;}

    if (divReponses.style.display == "none") {
        divReponses.style.display = "";
        return 0;
    } else {
        divReponses.style.display = "none";
        return 1;
    }
}

/**
 * Supprime une question / reponse
 * @param {int || string} id - l'identifiant de la question / reponse (X ou X-X) 
 */
function supprierQuestionVisualiseurQuestions(id) {
    try {
        const divQuestion = document.querySelector(`div[data-_id="${id}"]`);
        if (!divQuestion) return; // Sécurité au cas où l'élément n'existe plus

        if (String(id).includes("-")) {
            // --- CAS 1 : C'EST UNE RÉPONSE ---
            const parent = divQuestion.parentElement; // Le conteneur div.div-reponses
            
            // 1. Supprimer la zone DnD située juste en dessous de la réponse
            const dndZone = divQuestion.nextElementSibling;
            if (dndZone && dndZone.classList.contains("dnd-reponse")) {
                dndZone.remove();
            }
            
            // 2. Supprimer la réponse elle-même
            divQuestion.remove();

            // 3. Vérifier s'il reste d'autres réponses (on compte les vraies réponses, pas les DnD)
            const nbReponsesRestantes = parent.querySelectorAll("div.div-reponse").length;
            
            if (nbReponsesRestantes === 0) {
                // S'il n'y a plus aucune réponse, on réinitialise proprement
                parent.innerHTML = ""; // On nettoie les éventuelles zones DnD fantômes restantes
                parent.appendChild(creerZoneDndReponse()); // On remet la zone DnD du haut
                
                // On recrée la "Réponse 1" avec l'identifiant 0
                const idQuestion = String(id).split("-")[0];
                ajouterReponseVisualisateurQuestions(idQuestion); 
            }

        } else {
            // --- CAS 2 : C'EST UNE QUESTION ---
            const divParent = divQuestion.closest("div.box.div-question.div-box");
            
            // 1. Supprimer la zone de DnD située juste en dessous de la question
            const dndZone = divParent.nextElementSibling;
            if (dndZone && dndZone.classList.contains("dnd")) {
                dndZone.remove();
            }

            // 2. Supprimer la question elle-même
            divParent.remove();
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
        return divReponses.querySelectorAll("div.div-reponse").length;
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