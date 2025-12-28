import { ouvrireModalModifierQuestion } from '../modals/modificationQuestion/modalModifier';

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

div.box.div-question {
    margin-bottom: 0;
    margin-top: 10px;
    padding: 5px 10px;
}
`);
document.adoptedStyleSheets = [...document.adoptedStyleSheets, style];

/**
 * Ajoute une question dans le visualiseur de questions (partie gauche)
 * @param {HTMLElement} parent - Le conteneur parent où sera placé la question
 * @param {string} libele - Le libelé de la question
 * @param {int} _id 
 */
export function ajouterQuestionVisualiseurQuestions(parent, libele, _id) {
    const divConteneur = document.createElement("div");
    const spanFleche = document.createElement("span");
    const spanTitre = document.createElement("span");
    const titreQuestion = document.createElement("p");

    const SVG_NS = "http://www.w3.org/2000/svg";
    const svgFleche = document.createElementNS(SVG_NS, "svg");
    const pathFleche = document.createElementNS(SVG_NS, "path");

    pathFleche.setAttribute("d", "M 160.196 108.442 L 236.061 189.134 C 237.776 190.957 238.697 193.241 238.837 195.559 C 239.262 198.549 238.375 201.697 236.146 204.068 L 160.281 284.76 C 156.386 288.902 149.87 289.102 145.727 285.208 C 141.587 281.314 141.386 274.799 145.28 270.657 L 214.864 196.646 L 145.196 122.545 C 141.301 118.403 141.502 111.888 145.644 107.993 C 149.787 104.099 156.302 104.3 160.196 108.442 Z");
    pathFleche.style.fill = "rgb(216, 216, 216)";
    pathFleche.setAttribute("transform", "matrix(0.9999999999999999, 0, 0, 0.9999999999999999, 0, 0)");
    svgFleche.setAttribute("viewBox", "142.4012 105.1991 96.5389 182.8033");
    svgFleche.setAttribute("width", "10px");
    svgFleche.setAttribute("height", "10px");
    pathFleche.style.stroke = "black"; // Couleur du contour (vous pouvez réutiliser la couleur du fill)
    pathFleche.style.strokeWidth = "5px";
    svgFleche.appendChild(pathFleche);
    spanFleche.appendChild(svgFleche);
    spanFleche.style.display = "none";
    spanFleche.style.marginRight = "5px";
    spanFleche.style.backgroundColor = "var(--bulma-scheme-main)";
    spanFleche.style.position = "absolute";

    divConteneur.classList.add("box", "div-question");
    divConteneur.dataset._id = _id;

    titreQuestion.classList.add("is-unselectable", "question");
    titreQuestion.innerText = libele.toString();
    titreQuestion.title = libele.toString();
    spanTitre.appendChild(titreQuestion);

    // divConteneur.addEventListener('mouseover', () => {
    //     spanFleche.style.display = "";
    // });

    // divConteneur.addEventListener('mouseout', () => {
    //     spanFleche.style.display = "none";
    // });

    //divConteneur.appendChild(titreQuestion);
    divConteneur.append(spanFleche, spanTitre);
    parent.appendChild(divConteneur);

    ouvrireModalModifierQuestion(_id);
}

/**
 * Modifi une question dans le visualisateurs de questions (partie de gauche)
 * @param {int} id - identifiant de la question
 * @param {string} libele - le nouveau libelé
 */
export function modifierQuestionVisualiseurQuestions(id, libele) {
    const question = donnerQuestionAvecIdVisualiseurQuestions(id);
    const balisePQuestion = question.querySelector('p');
    balisePQuestion.innerText = libele;
    balisePQuestion.title = libele;
}


/**
 * retourne le div de la question avec son id
 * (pour une question présent dans le visualisateur de questions (partie de gauche)) 
 * @param {int} id - identifiant de la question
 * @returns {HTMLDivElement} le div de la question
 */
export function donnerQuestionAvecIdVisualiseurQuestions(id) {
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    return divVisualiseurQuestions.querySelector(`[data-_id="${id}"]`);
}

/**
 * donne le libele de la question en fonction de son id 
 * (pour une question présent dans le visualisateur de questions (partie de gauche))
 * @param {int} id - identifiant de la question 
 * @returns {string} le libele
 */
export function donnerLibelleQuestionAvecIdVisualiseurQuestions(id) {
    const divQuestion = donnerQuestionAvecIdVisualiseurQuestions(id);
    const pLibelle = divQuestion.querySelector("p");
    return pLibelle.innerText;
}