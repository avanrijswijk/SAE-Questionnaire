import { ajouterQuestionVisualiseurQuestions } from "./afficher/questions.js";
import { ajouterReponseVisualisateurQuestions } from "./afficher/questions.js";
import { ajouterQuestionVisualiseurQuestionnaire } from "./afficher/questionnaire.js";
import { ajouterReponseVisualiseurQuestionnaire } from "./afficher/questionnaire.js"; // ← AJOUT
import { TypeQuestion } from "./typeQuestion.js";

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
            type: q.type,
            obligatoire: q.est_obligatoire == 1,
            _id: idCourant
        };

        // Ajout dans les deux visualiseurs (mode chargement)
        ajouterQuestionVisualiseurQuestions(divQuestions, info, true);
        ajouterQuestionVisualiseurQuestionnaire(divQuestionnaire, info, true);

        // Ajout des réponses si nécessaire
        if (q.type === TypeQuestion.RADIO_BOUTON || q.type === TypeQuestion.CHECK_BOUTON) {
            q.choix.reverse(); // temporaire
            q.choix.forEach((rep, index) => {
                if (rep.texte !== null) {
                    ajouterReponse(
                        idCourant,   // identifiant interne de la question
                        index,       // index de la réponse
                        rep.texte    // texte réel
                    );
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
function ajouterReponse(idQuestion, idReponse, texte) {
    const type = document.querySelector(`div[data-_id="${idQuestion}"]`).dataset.type;

    // Partie gauche
    ajouterReponseVisualisateurQuestions(
        idQuestion,
        texte,
        idReponse
    );
    ajouterReponseVisualiseurQuestionnaire(
        `${idQuestion}-${idReponse}`,
        type,
        texte
    );
};

document.addEventListener("DOMContentLoaded", () => {
    if (window.questionnaire) {
        chargerQuestionnaire(window.questionnaire);

        remplirFormulaireEnregistrement(window.questionnaire);
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