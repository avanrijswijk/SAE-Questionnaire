import { TypeQuestion } from '../../typeQuestion.js';
import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import {ajouterQuestionVisualiseurQuestions} from '../../afficher/questions.js';
import {ajouterQuestionVisualiseurQuestionnaire} from '../../afficher/questionnaire.js';

/**
 * Met la premère lettre d'une chaine de caractères en majuscule
 * @param {string} chaine 
 * @returns un chaine de caracteres avec la première lettre en majuscule
 */
function mettreLaPremiereLettreEnMajuscule(chaine) {
    const [first, ...rest] = chaine;
    return (first.toUpperCase() + (rest.join("")));
}

document.addEventListener("DOMContentLoaded", async () => {
    //
    let _id = 0;

    // ---------- pour ouvrir le modal d'ajouter une question (MAQ) ----------
    const boutonAjouterQuestion = document.getElementById("ajouter-question");
    const boutonFermerMAQ = document.getElementById("bouton-fermerMAQ");
    
    // ---------- ----------
    const modalAjouterQuestion = document.getElementById("dialog-creer-question");
    const formMAQ = document.getElementById("form-ajouter-question");

    // ---------- ----------
    const listeRadiosType = document.getElementsByName("type-question");
    const radioTypeChampsLibre = document.getElementById("radio-champs-libre");
    //const radioTypeRadioBox = document.getElementById("radio-radio-box");
    //const radioTypeCheckBox = document.getElementById("radio-check-box");
    //const radioTypeSelectBar = document.getElementById("radio-select-bar");
    // ---------- ----------
    const divRadioSousType = document.getElementById("radio-sous-type");

    
    // ---------- ----------
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    const divVisualiseurQuestionnaire = document.getElementById("visualiseur-qestionnaire");

    // ---------- MAQ ----------
    boutonAjouterQuestion.addEventListener("click", () => {
        formMAQ.reset();
        ouvrire_modal(modalAjouterQuestion);
    });

    boutonFermerMAQ.addEventListener("click", () => {
        fermer_modal(modalAjouterQuestion);
    });

    listeRadiosType.forEach( (radio) => {
        radio.addEventListener("change", (e) => {
            if (radio == radioTypeChampsLibre) {
                divRadioSousType.style.display = "";
            } else {
                divRadioSousType.style.display = "none";
            }
        });
    });

    formMAQ.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(formMAQ);
        const libelleQuestion = mettreLaPremiereLettreEnMajuscule(formData.get("libelle-question"));   // libelé
        if (libelleQuestion.trim() == "") {
            alert("Le libellé de la question ne doit pas être vide.")
            return
        }
        // valeur possible de typeQuestionChoisi : 
        // champs-libre | radio-box | check-box | select-bar
        const typeQuestionChoisi = formData.getAll('type-question')[0]; // type
        let typeQuestion;
        // valeur possible de sousTypeQuestionChoisi
        // champs-libre-court | champs-libre-long
        const sousTypeQuestionChoisi = formData.getAll("sous-type-question")[0];
        // si la question est mise sur obligatoire, alors estObligatoire vaut 'obligatoire'
        // sinon, elle vaut null  
        const estObligatoire = formData.get("question-obligatoire") == "obligatoire" ? true : false;     
        
        switch (typeQuestionChoisi) {
            case "check-box":
                typeQuestion = TypeQuestion.CHECK_BOUTON;
                break;
            case "radio-box":
                typeQuestion = TypeQuestion.RADIO_BOUTON;
                break;
            case "select-bar":
                typeQuestion = TypeQuestion.LISTE_DEROULANTE;
                break;
            default: // "champs-libre"
                if (sousTypeQuestionChoisi == "champs-libre-long") {
                    typeQuestion = TypeQuestion.CHAMPS_LONG;
                } else {
                    typeQuestion = TypeQuestion.CHAMPS_COURT;
                }
                break;
        };

        const informations = {
            "intitule" : libelleQuestion,
            "type" : typeQuestion,
            "obligatoire" : estObligatoire,
            "_id" : _id
        };

        console.log("------ Ajout d'une question ------");
        console.log("Libellé de question :", libelleQuestion);
        console.log("Type de question :", typeQuestion);
        console.log("Question obligatoire :", estObligatoire);
        console.log("----------------------------------");

        ajouterQuestionVisualiseurQuestions(divVisualiseurQuestions, informations);
        ajouterQuestionVisualiseurQuestionnaire(divVisualiseurQuestionnaire, libelleQuestion, typeQuestion, _id);
        _id++;

        fermer_modal(modalAjouterQuestion);
        formMAQ.reset();
    });

    if (modalAjouterQuestion.getElementsByClassName("modal-background").length != 1) {
        console.error("Il n'y a pas de div.modal-background dans le modal#dialog-finir-questionnaire");
    }

    modalAjouterQuestion.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modalAjouterQuestion);
    });
});
