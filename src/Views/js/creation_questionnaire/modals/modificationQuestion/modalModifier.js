import { TypeQuestion } from '../../typeQuestion.js';
import { ouvrire_modal, fermer_modal } from '../gestion_modal.js';
import {
    modifierQuestionVisualiseurQuestions, 
    donnerQuestionAvecIdVisualiseurQuestions, 
    donnerLibelleQuestionAvecIdVisualiseurQuestions,
    donnerInfosQuestionAvecIdVisualiseurQuestions
} from '../../afficher/questions.js';
import {modifierQuestionVisualiseurQuestionnaire} from '../../afficher/questionnaire.js';
import { notification, TypeNotification } from '../../../utils/notification/notification.js';

const NAME_TEXTAREA = "libelle-question-modifier";
const NAME_RADIO_SOUS_TYPE_CHAMP = "sous-type-question-c-modifier";
const NAME_RADIO_SOUS_TYPE_CHOIX_MULTIPLES = "sous-type-question-cm-modifier";
const NAME_CHECK_OBLIGATOIRE = "question-obligatoire-modifier";

const ID_DIV_OBLIGATOIRE = "obligatoire-modifier";
const ID_DIV_OPTIONS_SOUS_TYPE = "sous-type-modifier";
const ID_DIV_OPTIONS_SOUS_TYPE_CHAMP_TEXTE = "sous-type-champ-modifier";
const ID_DIV_OPTIONS_SOUS_TYPE_CHOIX_MULTIPLES = "sous-type-choix-multiples-modifier";

let _id;
let _type;

/**
 * Type de contenue à modifier : Question ou Reponse
 */
const TypeModifier = {
    QUESTION : "question",
    REPONSE : "reponse",
    CONTEXT : "context"
};

/**
 * initialise le modal #dialog-modifier-question
 * @param {HTMLDivElement} modal - le modal modifier
 * @param {int} id - l'identifiant de la question
 * @param {TypeModifier} type 
 */
function init_modal(modal, id, type) {
    const divSousType = modal.querySelector(`div#${ID_DIV_OPTIONS_SOUS_TYPE}`);
    const divOptionsChampTexte = modal.querySelector(`div#${ID_DIV_OPTIONS_SOUS_TYPE_CHAMP_TEXTE}`);
    const divOptionsChoixMultiples = modal.querySelector(`div#${ID_DIV_OPTIONS_SOUS_TYPE_CHOIX_MULTIPLES}`);
    const divObligatoire = modal.querySelector(`div#${ID_DIV_OBLIGATOIRE}`);

    const textarea = modal.querySelector(`[name="${NAME_TEXTAREA}"]`);
    textarea.value = donnerLibelleQuestionAvecIdVisualiseurQuestions(id);

    const infosQuestions = donnerInfosQuestionAvecIdVisualiseurQuestions(id);
    const typeQuestion = infosQuestions.type;

    const pTitreModal = modal.querySelector(`p.modal-card-title`);
    switch (type) {
        case TypeModifier.QUESTION:
            pTitreModal.innerHTML = "Modifier une question";
            break;
        case TypeModifier.REPONSE:
        default :
            pTitreModal.innerHTML = "Modifier une réponse";
            break;
    }

    // par defaut on cache tout
    divSousType.style.display = "none";
    divOptionsChampTexte.style.display = "none";
    divOptionsChoixMultiples.style.display = "none";
    divObligatoire.style.display = "none";

    switch (typeQuestion) {
        case TypeQuestion.CHAMPS_COURT:
        case TypeQuestion.CHAMPS_LONG:
            divSousType.style.display = "";
            divOptionsChampTexte.style.display = "";
            divObligatoire.style.display = "";

            const radiosSousTypeChamp = modal.querySelectorAll(`input[name="${NAME_RADIO_SOUS_TYPE_CHAMP}"]`);
            if (radiosSousTypeChamp && radiosSousTypeChamp.length >= 2) {
                radiosSousTypeChamp[0].checked = typeQuestion == TypeQuestion.CHAMPS_COURT ? true : false; // le premier est champ court
                radiosSousTypeChamp[1].checked = typeQuestion == TypeQuestion.CHAMPS_COURT ? false : true; // le deuxième est champ long
            }
            break;
        case TypeQuestion.RADIO_BOUTON:
        case TypeQuestion.CHECK_BOUTON:
            divSousType.style.display = "";
            divOptionsChoixMultiples.style.display = "";
            divObligatoire.style.display = "";

            const radiosSousTypeChoixMutiples = modal.querySelectorAll(`input[name="${NAME_RADIO_SOUS_TYPE_CHOIX_MULTIPLES}"]`);
            if (radiosSousTypeChoixMutiples && radiosSousTypeChoixMutiples.length >= 2) {
                radiosSousTypeChoixMutiples[0].checked = typeQuestion == TypeQuestion.RADIO_BOUTON ? true : false; // le premier est radio box
                radiosSousTypeChoixMutiples[1].checked = typeQuestion == TypeQuestion.RADIO_BOUTON ? false : true; // le deuxième est check box
            }
            break;
        case TypeQuestion.CONTEXT:
            break;
        default:
            break;
    }

    const estObligatoire = infosQuestions.obligatoire;
    const checkObligatoire = modal.querySelector(`input[name="${NAME_CHECK_OBLIGATOIRE}"]`);
    if (estObligatoire && checkObligatoire) {
        checkObligatoire.checked = String(estObligatoire) == "true" ? true : false;
    }
}

/**
 * Met la premère lettre d'une chaine de caractères en majuscule
 * @param {string} chaine 
 * @returns un chaine de caracteres avec la première lettre en majuscule
 */
function mettreLaPremiereLettreEnMajuscule(chaine) {
    const [first, ...rest] = chaine;
    return (chaine.length>=0) ? (first.toUpperCase() + (rest.join(""))) : "";
}

/**
 * initialise la fermeture et le traitement des données du modal de modification d'une question
 */
function modalModifierQuestion() {
    //
    //const divQuestion = donnerQuestionAvecIdVisualiseurQuestions(id);

    // ---------- pour fermer le modal de modification de question (MMQ) ----------
    const boutonFermer = document.getElementById("bouton-fermerMMQ");
    const boutonAnnuler = document.getElementById("bouton-annulerMMQ");
    
    // ---------- ----------
    const modal = document.getElementById("dialog-modifier-question");
    const form = document.getElementById("form-modifier-question");

    // ---------- ----------
    //const listeRadiosType = document.getElementsByName("type-question");

    // ---------- ----------
    // const divVisualiseurQuestions = document.getElementById("visualiseur-questions");
    // const divVisualiseurQuestionnaire = document.getElementById("visualiseur-questionnaire");

    // ---------- MAQ ----------
    

    boutonFermer.addEventListener("click", () => {
        fermer_modal(modal);
    });

    boutonAnnuler.addEventListener("click", () => {
        fermer_modal(modal);
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        const libelleQuestion = mettreLaPremiereLettreEnMajuscule(formData.get(NAME_TEXTAREA));
        if (libelleQuestion.trim() == "") {
            alert("Le libellé de la question ne doit pas être vide.");
            return
        }

        const typeQuestion = donnerInfosQuestionAvecIdVisualiseurQuestions(_id).type;

        const estObligatoire = formData.get(NAME_CHECK_OBLIGATOIRE) == "obligatoire" ? true : false;  // verif si c'est une reponse / une question
        
        const sousTypeChamptext = formData.getAll(NAME_RADIO_SOUS_TYPE_CHAMP)[0];
        const sousTypeChoixMultiples = formData.getAll(NAME_RADIO_SOUS_TYPE_CHOIX_MULTIPLES)[0];

        let nouveauTypeQuestion = null;
        if (typeQuestion && (typeQuestion == TypeQuestion.CHAMPS_COURT || typeQuestion == TypeQuestion.CHAMPS_LONG)) {
            nouveauTypeQuestion = sousTypeChamptext == "champs-libre-long" ? TypeQuestion.CHAMPS_LONG : TypeQuestion.CHAMPS_COURT;
        } else
        if (typeQuestion && (typeQuestion == TypeQuestion.RADIO_BOUTON || typeQuestion == TypeQuestion.CHECK_BOUTON)) {
            nouveauTypeQuestion = sousTypeChoixMultiples == "radio-box" ? TypeQuestion.RADIO_BOUTON : TypeQuestion.CHECK_BOUTON;
        }
        
        if (_id != null) {
            try {
                modifierQuestionVisualiseurQuestions(_id, libelleQuestion, nouveauTypeQuestion, estObligatoire);
                modifierQuestionVisualiseurQuestionnaire(_id, libelleQuestion, nouveauTypeQuestion, estObligatoire);
            } catch (e) {
                notification(TypeNotification.ERREUR, "Une erreur c'est produite lors de la modification.");
                console.error(e);
            }
            
        }

        // if (_type == TypeModifier.QUESTION) {
        //     // modifier l'obligation de la question

        //     const typeQuestion = donnerTypeQuestionAvecIdVisualiseurQuestions(id);
        //     if (typeQuestion && (typeQuestion == TypeQuestion.CHAMPS_COURT || typeQuestion == TypeQuestion.CHAMPS_LONG)) {
        //         // modifier le type de textfield
        //     }
        // }

        
        

        fermer_modal(modal);
        form.reset();
    });

    modal.getElementsByClassName("modal-background")[0].addEventListener("click", () => {
        fermer_modal(modal);
    });
}

/**
 * ouvre un modal de modification de questions/réponses en fonction de son id
 * @param {int || string} identifiant - son identifiant (id)
 * @param {TypeModifier} type - le type de comptenu qui sera modifié 
 */
function ouvrireModalModifierQuestion(identifiant, type) {
    const form = document.getElementById("form-modifier-question");
    const modal = document.getElementById("dialog-modifier-question");
    _id = identifiant;
    _type = type
    form.reset();
    init_modal(modal, identifiant, type);
    ouvrire_modal(modal);
}

/**
 * attribu un modal de modification de questions/réponses en fonction de son conteneur
 * @param {HTMLDivElement} conteneur - le conteneur de la question
 * @param {TypeModifier} type - le type de comptenu qui sera modifié
 */
function attribuerModalModifierQuestionAvaecConteneur(conteneur, type) {
    const identifiant = conteneur.dataset["_id"];
    if (identifiant) {
        conteneur.addEventListener("dblclick", () => {
            ouvrireModalModifierQuestion(identifiant, type);
        });
    } 
    
}

/**
 * attribu un modal de modification de questions/réponses en fonction de son id
 * @param {*} identifiant - son identifiant (id)
 * @param {TypeModifier} type - le type de comptenu qui sera modifié
 */
function attribuerModalModifierQuestionAvecId(identifiant, type) {
    const divConteneur = donnerQuestionAvecIdVisualiseurQuestions(identifiant); // peut etre un divQuestion ou un divReponse
    if (!divConteneur){console.error("Le conteneur est null pour l'attribution du listener");}
    divConteneur.addEventListener("dblclick", () => {
        ouvrireModalModifierQuestion(identifiant, type);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    modalModifierQuestion();

    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");

    divVisualiseurQuestions.addEventListener("dblclick", (e) => {
            const divReponse = e.target.closest(".div-reponse");
            const divQuestion = e.target.closest(".div-question").firstChild;
            if (divQuestion) {
                const type = divQuestion.dataset.type;
                const id = (divReponse) ? divReponse.dataset._id : divQuestion.dataset._id;
                // console.log(id); //debug

                ouvrireModalModifierQuestion(id, type);
            }
    });
});

export {
    attribuerModalModifierQuestionAvecId,
    attribuerModalModifierQuestionAvaecConteneur,
    ouvrireModalModifierQuestion,
    TypeModifier
}