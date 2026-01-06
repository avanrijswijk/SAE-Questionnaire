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

div.box.div-question:hover {
    border-left: 4px #90D5FF solid;
    transition: border-left 0.2s ease-out;
}

div.box.div-question:not(:hover) {
    border-left: 0px #90D5FF solid;
    transition: border-left 0.2s ease-out;
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

    const divConteneur = document.createElement("div");
    const spanTitre = document.createElement("span");
    const titreQuestion = document.createElement("p");

    divConteneur.classList.add("box", "div-question");
    divConteneur.dataset._id = _id;
    divConteneur.dataset.intitule = libelle;
    divConteneur.dataset.type = type;
    divConteneur.dataset.obligatoire = obligatoire;

    titreQuestion.classList.add("is-unselectable", "question");
    titreQuestion.innerText = libelle.toString();
    titreQuestion.title = libelle.toString();
    spanTitre.appendChild(titreQuestion);

    // divConteneur.addEventListener('mouseover', () => {
    //     spanFleche.style.display = "";
    // });

    // divConteneur.addEventListener('mouseout', () => {
    //     spanFleche.style.display = "none";
    // });

    //divConteneur.appendChild(titreQuestion);
    divConteneur.append(spanTitre);
    parent.appendChild(divConteneur);

    ouvrireModalModifierQuestion(_id);
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
 * @param {int} id - identifiant de la question
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

export {
    ajouterQuestionVisualiseurQuestions, 
    modifierQuestionVisualiseurQuestions, 
    donnerQuestionAvecIdVisualiseurQuestions, 
    donnerLibelleQuestionAvecIdVisualiseurQuestions
}