import { ajouterQuestionVisualiseurQuestions } from "./afficher/questions.js";
import { ajouterReponseVisualisateurQuestions } from "./afficher/questions.js";
import { ajouterQuestionVisualiseurQuestionnaire } from "./afficher/questionnaire.js";
import { ajouterReponseVisualiseurQuestionnaire } from "./afficher/questionnaire.js"; // ← AJOUT
import { TypeQuestion } from "./typeQuestion.js";

/**
 * Convertit les types venant du back vers les types internes
 */
function convertirType(type) {
    switch (type) {
        case "textfield":
            return TypeQuestion.CHAMPS_COURT;
        case "radio":
            return TypeQuestion.RADIO_BOUTON;
        case "check":
            return TypeQuestion.CHECK_BOUTON;
        default:
            console.warn("Type inconnu :", type);
            return TypeQuestion.CHAMPS_COURT;
    }
}

/**
 * Charge un questionnaire complet dans l’éditeur
 */
function chargerQuestionnaire(questionnaire) {
    console.log("INIT – questionnaire reçu :", questionnaire);

    if (!questionnaire || !questionnaire.questions) {
        console.log("Aucune question à charger.");
        return;
    }

    const divQuestions = document.getElementById("visualiseur-questions");
    const divQuestionnaire = document.getElementById("visualiseur-qestionnaire");

    let idCourant = 0;

    questionnaire.questions.forEach(q => {

        const info = {
            intitule: q.intitule,
            type: convertirType(q.type),
            obligatoire: q.est_obligatoire == 1,
            _id: idCourant
        };

        // Ajout dans les deux visualiseurs (mode chargement)
        ajouterQuestionVisualiseurQuestions(divQuestions, info, true);
        ajouterQuestionVisualiseurQuestionnaire(divQuestionnaire, info, true);

        // Ajout des réponses si nécessaire
        if (q.type === "radio" || q.type === "check") {
            q.choix.forEach((rep, index) => {
                if (rep.texte !== null) {

                    const evt = new CustomEvent("ajouter-reponse", {
                        detail: {
                            idQuestion: idCourant,   // identifiant interne de la question
                            idReponse: index,        // index de la réponse
                            texte: rep.texte         // texte réel
                        }
                    });

                    document.dispatchEvent(evt);
                }
            });
        }

        idCourant++;
    });

    console.log("Questionnaire chargé :", questionnaire);
}

/**
 * Listener global pour ajouter une réponse avec son vrai texte
 */
document.addEventListener("ajouter-reponse", (e) => {
    const { idQuestion, idReponse, texte } = e.detail;

    // Partie gauche
    ajouterReponseVisualisateurQuestions(
        idQuestion,
        texte,
        idReponse
    );

    // Partie droite
    const type = document.querySelector(`div[data-_id="${idQuestion}"]`).dataset.type;
    const evt2 = new CustomEvent("ajouter-reponse-questionnaire", {
        detail: {
            idReponse: `${idQuestion}-${idReponse}`,
            type,
            texte
        }
    });
    document.dispatchEvent(evt2);
});

/**
 * Listener pour la partie droite
 */
document.addEventListener("ajouter-reponse-questionnaire", (e) => {
    const { idReponse, type, texte } = e.detail;
    ajouterReponseVisualiseurQuestionnaire(idReponse, type, texte);
});

document.addEventListener("DOMContentLoaded", () => {
    try {
        chargerQuestionnaire(window.questionnaire);

        // Remplir le formulaire d’enregistrement
        remplirFormulaireEnregistrement(window.questionnaire);

    } catch (e) {
        console.error("Erreur lors du chargement du questionnaire :", e);
    }
});

function remplirFormulaireEnregistrement(questionnaire) {
    if (!questionnaire) return;

    // Nom du questionnaire
    const inputNom = document.getElementById("nom-questionnaire");
    if (inputNom) inputNom.value = questionnaire.titre ?? "";

    // Date d'expiration
    const inputDate = document.getElementById("date-expriration");
    if (inputDate && questionnaire.date_expiration) {
        inputDate.value = questionnaire.date_expiration.split(" ")[0];
    }

    // Groupes autorisés (JSON string → objet)
    let groupes = null;
    try {
        groupes = JSON.parse(questionnaire.groupes_autorises);
    } catch (e) {
        console.warn("Impossible de parser groupes_autorises :", questionnaire.groupes_autorises);
    }

    if (groupes && groupes.groupes_requis) {
        const selectCibles = document.getElementById("mes-cibles");
        const valeurs = groupes.groupes_requis.map(String);

        Array.from(selectCibles.options).forEach(opt => {
            opt.selected = valeurs.includes(opt.value);
        });
    }
}