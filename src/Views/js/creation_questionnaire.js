function ajouter_question(parent) {
    const divConteneur = document.createElement("div");
    const liReponse = document.createElement("li");
    const titreQuestion = document.createElement("p");

    titreQuestion.innerText = "Question ~";

    divConteneur.appendChild(titreQuestion, titreQuestion);
    parent.appendChild(divConteneur);
}

function fermer_modal(modal) {
    modal.classList.remove("is-active");
}

document.addEventListener("DOMContentLoaded", async () => {
    // pour ajouter une question
    const boutonAjouterQuestion = document.getElementById("ajouter-question");
    const divVisualiseurQuestions = document.getElementById("visualiseur-questions");

    // pour ouvrire le modal
    const modal = document.getElementById("dialog");
    const boutonFinir = document.getElementById("bouton-finir");
    //const boutonValider = document.getElementById("bouton-valider");
    const formEnregistrer = document.getElementById("form-enregistrer");
    const boutonFermer = document.getElementById("bouton-fermer");
    
    boutonAjouterQuestion.addEventListener("click", () => {
        ajouter_question(divVisualiseurQuestions);
    });

    boutonFinir.addEventListener("click", () => {
        modal.classList.add("is-active");
    });

    boutonFermer.addEventListener("click", () => {
        fermer_modal(modal);
    });

    formEnregistrer.addEventListener("submit", (e) => {
        e.preventDefault();
        // TODO: traiter les données du formulaire ici
        fermer_modal(modal);
        window.location.href = "./?c=home";
    });

    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete') || []).forEach(($close) => {
        $close.addEventListener('click', () => {
            fermer_modal(modal);
        });
    });
});