import { TypeQuestion } from "../typeQuestion.js";
import { ouvrireModalModifierQuestion, TypeModifier } from "../modals/modificationQuestion/modalModifier.js";
import { ajouterReponseVisualisateurQuestions } from "../afficher/questions.js";
import { ajouterReponseVisualiseurQuestionnaire } from "../afficher/questionnaire.js";

const ID = "context-menu";

const style = new CSSStyleSheet();
style.replaceSync(`

#${ID} {
    display: none;
    position: absolute;
    padding: 5px;
    background-color: #d4d4d4;
    border: 1px solid;
    border-radius: 10px;
    z-index: 100000;
    height: auto;
}

#${ID}.afficher {
    display: initial;
}

#${ID} > ul.menu {
}

#${ID} > ul.menu > li.menu-item {
    margin: 3px 0;
    padding : 2px 5px;
    border-radius: 5px;     
}

#${ID} > ul.menu > li.menu-item:hover{
    background-color: #c2c2c2ff;
}

#${ID} > ul.menu > li.menu-separator {
    height: 1px;
    background-color: grey;
}
`);
document.adoptedStyleSheets = [...document.adoptedStyleSheets, style];

let identifiant;

/**
 * Identifie la question est retroune l'identifiant du parent (X ou X-X)
 * @param {HTMLElement} element - un element HTML 
 * @return {int} - l'identifiant du parent
 */
function identifierElement(element) {
    const classes = element.classList.value;
    const dataset = element.dataset;
    let parent;

    if (element.tagName == 'P' && classes == "is-unselectable question") {
        parent = element.parentElement;
    } else if (element.tagName == 'DIV' && dataset["_id"]) {
        parent = element;
    } else if (element.tagName == 'DIV' && classes == "box div-question div-box") {
        parent = element.firstChild;
    }

    return parent.dataset["_id"];
}

function fonc(element) {
    const id = 0;
    document.querySelector(`div[data-_id="${id}"]`).closest("div.box.div-question.div-box").querySelector("div.div-reponses");
}

// de https://github.com/NouvelleTechno/Right-Click-Menu

/**
 * Affiche un menu lors du clic droit
 * @param {HTMLDivElement} divQuestion - le div contenant une question 
 * @param {TypeQuestion} type - le type de la question
 */
function attribuerContexteMenu(divQuestion, type) {
    divQuestion.addEventListener("contextmenu", (event) => {
        // On a ouvert le menu
        // On empêche le "vrai" menu d'apparaître
        event.preventDefault();
        
        const elementSelectionne = event.target;
        identifiant = identifierElement(elementSelectionne);
        console.log("----------------------------");
        console.log(elementSelectionne);
        console.log(elementSelectionne.parentElement);
        console.log(identifiant);
        console.log("----------------------------");

        const menuItemAjouterReponse = document.getElementById("menu-item-ajouter-reponse");
        switch (type) {
            case TypeQuestion.CHAMPS_COURT:
            case TypeQuestion.CHAMPS_LONG:
            default:
                menuItemAjouterReponse.style.display = "none";
                break;

            case TypeQuestion.CHECK_BOUTON:
            case TypeQuestion.RADIO_BOUTON:
            case TypeQuestion.LISTE_DEROULANTE:
                menuItemAjouterReponse.style.display = "";
                break;
        }

        // On récupère le menu
        let menu = document.querySelector("#context-menu");

        // On met ou retire la classe active
        menu.classList.toggle("afficher");

        // On ouvre le menu là où se trouve la souris
        // On récupère les coordonnées de la souris
        let posX = event.clientX;
        let posY = event.clientY;

        // On calcule la position du menu pour éviter qu'il dépasse
        // Position la plus à droite "largeur fenêtre - largeur menu - 25"
        let maxX = window.innerWidth - menu.clientWidth - 25;

        // Position la plus basse "hauteur fenêtre - hauteur menu - 25"
        let maxY = window.innerHeight - menu.clientHeight - 25;

        // On vérifie si on dépasse
        if(posX > maxX){
            posX = maxX;
        }
        if(posY > maxY){
            posY = maxY;
        }

        // On positionne le menu
        menu.style.top = posY + "px";
        menu.style.left = posX + "px";
    });
}

// On écoute le clic pour retirer le menu
document.addEventListener("click", () => {
    // On va chercher le menu et on lui retire la classe "active"
    document.querySelector("#context-menu").classList.remove("afficher");
});

document.addEventListener("DOMContentLoaded", () => {
    const menuItemAfficher = document.getElementById("menu-item-afficher");
    const menuItemAjouterReponse = document.getElementById("menu-item-ajouter-reponse");
    const menuItemModifier = document.getElementById("menu-item-modifier");
    const menuItemSupprimer = document.getElementById("menu-item-supprimer");

    menuItemAfficher.addEventListener("click", () => {
        alert("affiche où se situe la question");
    });

    menuItemAjouterReponse.addEventListener("click", () => {
        const idReponse = ajouterReponseVisualisateurQuestions(identifiant);
        if (idReponse < 0) {
            // afficher une erreur
            console.error("Erreur dans l'ajout d'une nouvelle reponse");
        } else {
            // definir type
            const type = document.querySelector(`div[data-_id="${String(idReponse).split("-")[0]}"]`).dataset.type;
            ajouterReponseVisualiseurQuestionnaire(idReponse, type);
        }
    });

    menuItemModifier.addEventListener("click", () => {
        const typeQuestion = String(identifiant).includes("-") ? TypeModifier.REPONSE : TypeModifier.QUESTION;
        ouvrireModalModifierQuestion(identifiant, typeQuestion);
    });

    menuItemSupprimer.addEventListener("click", () => {
        alert("supprimer une question / reponse");
    });
});

export {
    attribuerContexteMenu
}