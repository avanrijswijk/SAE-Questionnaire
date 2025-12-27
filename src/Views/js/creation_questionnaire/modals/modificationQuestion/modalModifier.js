import { TypeQuestion } from '../../typeQuestion.js';
import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import {modifierQuestionVisualiseurQuestions, donnerQuestionAvecIdVisualiseurQuestions, donnerLibelleQuestionAvecIdVisualiseurQuestions} from '../../afficher/questions.js';
import {modifierQuestionVisualiseurQuestionnaire} from '../../afficher/questionnaire.js';

const NAME_TEXTAREA = "libelle-question";

/**
 * initialise le modal #dialog-modifier-question
 * @param {HTMLDivElement} modal - le modal modifier
 * @param {int} id - l'identifiant de la question
 */
function init_modal(modal, id) {
    const textarea = modal.querySelector(`[name="${NAME_TEXTAREA}"]`);
    textarea.value = donnerLibelleQuestionAvecIdVisualiseurQuestions(id);
}

/**
 * Met la premère lettre d'une chaine de caractères en majuscule
 * @param {string} chaine 
 * @returns un chaine de caracteres avec la première lettre en majuscule
 */
function mettreLaPremiereLettreEnMajuscule(chaine) {
    const [first, ...rest] = chaine;
    return (first.toUpperCase() + (rest.join("")));
}

/**
 * initialise l'ouverture, la fermeture et le traitement des données du modal de modification d'une question
 * @param {int} id - identifiant de la question 
 */
export function modalModifierQuestion(id) {
    //
    const divQuestion = donnerQuestionAvecIdVisualiseurQuestions(id);

    // ---------- pour fermer le modal de modification de question (MMQ) ----------
    const boutonFermer = document.getElementById("bouton-fermerMMQ");
    const boutonAnnuler = document.getElementById("bouton-annulerMMQ");
    
    // ---------- ----------
    const modal = document.getElementById("dialog-modifier-question");
    const form = document.getElementById("form-modifier-question");

    // ---------- ----------
    //const listeRadiosType = document.getElementsByName("type-question");

    // ---------- ----------
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    const divVisualiseurQuestionnaire = document.getElementById("visualiseur-qestionnaire");

    // ---------- MAQ ----------
    divQuestion.addEventListener("dblclick", () => {
        form.reset();
        init_modal(modal, id);
        ouvrire_modal(modal);
    });

    boutonFermer.addEventListener("click", () => {
        fermer_modal(modal);
    });

    boutonAnnuler.addEventListener("click", () => {
        fermer_modal(modal);
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const libelleQuestion = mettreLaPremiereLettreEnMajuscule(formData.get(NAME_TEXTAREA).trimStart());   // libelé
        if (libelleQuestion.trim() == "") {
            alert("Le libelé de la question ne doit pas être vide.")
            return
        }
         
        //const estObligatoire = formData.get("question-obligatoire") == "obligatoire" ? true : false;     
 
        console.log("------ Modifier d'une question ------");
        console.log("Libellé de question :", libelleQuestion);
        console.log("----------------------------------");

        modifierQuestionVisualiseurQuestions(id, libelleQuestion);
        modifierQuestionVisualiseurQuestionnaire(id, libelleQuestion);

        fermer_modal(modal);
        form.reset();
    });

    modal.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modal);
    });
}