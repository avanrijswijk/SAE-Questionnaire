document.addEventListener("DOMContentLoaded", async () => {
    const modal = document.getElementById("dialog");
    const boutonCode = document.getElementById("bouton-code");
    //const boutonValider = document.getElementById("bouton-valider");
    const boutonFermer = document.getElementById("bouton-fermer");
    const formEnregistrer = document.getElementById("form-enregistrer");

    function fermer_modal() {
        modal.classList.remove("is-active");
    }

    boutonCode.addEventListener("click", () => {
        modal.classList.add("is-active");
    });

    boutonFermer.addEventListener("click", () => {
        fermer_modal();
    });

    formEnregistrer.addEventListener("submit", (e) => {
        e.preventDefault();
        // TODO: traiter les données du formulaire ici
        fermer_modal();
        window.location.href = "./?c=home";
    });

    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete') || []).forEach(($close) => {
        $close.addEventListener('click', () => {
            fermer_modal();
        });
    });
});