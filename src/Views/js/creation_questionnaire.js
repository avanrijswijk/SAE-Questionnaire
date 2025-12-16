import {fermer_modal, ouvrire_modal} from './creation_questionnaire/gestion_modal.js';
import {ajouter_question_visualiseur_questionnaire} from './creation_questionnaire/visualiseur_questionnaire.js';
import {ajouter_question_visualiseur_questions} from './creation_questionnaire/visualiseur_questions.js';
import {TypeQuestion} from './creation_questionnaire/type_question.js';

document.addEventListener("DOMContentLoaded", async () => {
    // ---------- pour ouvrir le modal d'ajouter une question (MAQ) ----------
    const boutonAjouterQuestion = document.getElementById("ajouter-question");
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    const modalAjouterQuestion = document.getElementById("dialog-creer-question");
    const boutonFermerMAQ = document.getElementById("bouton-fermerMAQ");
    //const boutonFinirMAQ = document.getElementById("bouton-validerMAQ");
    const formMAQ = document.getElementById("form-ajouter-question");
    const inputRadioChampsLibre = document.getElementById("radio-champs-libre");
    const divSousTypeChamps = document.getElementById("sous-type-champs");

    // ---------- pour ouvrire le modal de validation de questionnaire (MVQ) ----------
    const modalValiderQuestionnaire = document.getElementById("dialog-finir-questionnaire");
    const boutonFinirMVQ = document.getElementById("bouton-finir");
    //const boutonValider = document.getElementById("bouton-validerMVQ");
    const formMVQ = document.getElementById("form-enregistrer");
    const boutonFermerMVQ = document.getElementById("bouton-fermer");
    
    // ---------- ----------
    const divVisualiseurQuestionnaire = document.getElementById("visualiseur-qestionnaire");

    // ---------- MAQ ----------
    boutonAjouterQuestion.addEventListener("click", () => {
        //ajouter_question(divVisualiseurQuestions);
        ouvrire_modal(modalAjouterQuestion);
    });

    boutonFermerMAQ.addEventListener("click", () => {
        fermer_modal(modalAjouterQuestion);
    });

    inputRadioChampsLibre.addEventListener('change', (e) => {
        if (inputRadioChampsLibre.checked) {
            divSousTypeChamps.style.display = "";
        } else {
            divSousTypeChamps.style.display = "none";
        }
    });

    formMAQ.addEventListener("submit", (e) => {
        e.preventDefault();
        fermer_modal(modalAjouterQuestion);

        const formData = new FormData(formMAQ);
        const libeleQuestion = formData.get("libele-question");   // libelé
        // valeur possible de typeQuestion : 
        // champs-libre | radio-box | check-box | select-bar
        let typeQuestion = formData.getAll('type-question')[0]; // type
        // si la question est mise sur obligatoire, alors estObligatoire vaut 'obligatoire'
        // sinon, elle vaut null  
        const estObligatoire = formData.get("question-obligatoire");     
        
        switch (typeQuestion) {
            case "check-box":
                typeQuestion = TypeQuestion.CHECK_BOUTON;
                break;
            case "radio-box":
                typeQuestion = TypeQuestion.RADIO_BOUTON;
                break;
            case "select-bar":
                typeQuestion = TypeQuestion.LISTE_DEROULANTE;
                break;
            case "champs-libre":
                typeQuestion = TypeQuestion.CHAMPS_LONG;
                break;
            default:
                typeQuestion = TypeQuestion.CHAMPS_COURT;
                break;
        };

        console.log("------ Ajout d'une question ------");
        console.log("Typde de question :", libeleQuestion);
        console.log("Typde de question :", typeQuestion);
        console.log("Question obligatoire :", estObligatoire);
        console.log("----------------------------------");

        ajouter_question_visualiseur_questions(divVisualiseurQuestions, libeleQuestion);
        ajouter_question_visualiseur_questionnaire(divVisualiseurQuestionnaire, libeleQuestion, typeQuestion);

        formMAQ.reset();
    });

    // ---------- MVQ ----------
    boutonFinirMVQ.addEventListener("click", () => {
        ouvrire_modal(modalValiderQuestionnaire);
    });

    boutonFermerMVQ.addEventListener("click", () => {
        fermer_modal(modalValiderQuestionnaire);
    });

    formMVQ.addEventListener("submit", (e) => {
        e.preventDefault();
        // TODO: traiter les données du formulaire ici
        fermer_modal(modalValiderQuestionnaire);
        window.location.href = "./?c=home";
    });

    // Pour fermer les modals si on clic à coté
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete') || []).forEach(($close) => {
        $close.addEventListener('click', () => {
            fermer_modal(modalValiderQuestionnaire);
            fermer_modal(modalAjouterQuestion);
        });
    });
});