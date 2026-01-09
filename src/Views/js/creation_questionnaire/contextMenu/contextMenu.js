import { TypeQuestion } from "../typeQuestion";

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
        
        //console.log(event.currentTarget);
        console.log(event.target);

        const menuItemAjouterReponse = document.getElementById("ajouter-reponse");
        switch (type) {
            case TypeQuestion.CHAMPS_COURT:
            case TypeQuestion.CHAMPS_LONG:
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

export {
    attribuerContexteMenu
}