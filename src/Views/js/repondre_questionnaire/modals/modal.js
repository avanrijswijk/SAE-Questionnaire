const style = new CSSStyleSheet();
style.replaceSync(`
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:1000;
}

.modal-card{
    background:#fff;
    padding:20px;
    border-radius:6px;
    max-width:520px;
    width:90%;
    box-shadow:0 8px 24px rgba(0,0,0,.2);
}

.modal-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:16px;
}

.modal-title{
    font-weight:700;
    margin-bottom:8px;
}
`);

document.adoptedStyleSheets = [...document.adoptedStyleSheets, style];

document.addEventListener("DOMContentLoaded", () => {

    const cancelBtn = document.getElementById('cancelBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.querySelector('form');
    const brouillon = submitBtn.dataset.brouillon;

    const cancelModal = document.getElementById('cancelModal');
    const cancelNo = document.getElementById('cancelModalNo');
    const cancelYes = document.getElementById('cancelModalYes');

    const submitModal = document.getElementById('submitModal');
    const submitNo = document.getElementById('submitModalNo');
    const submitYes = document.getElementById('submitModalYes');

    function show(modal){ modal.style.display='flex'; modal.inert = false; }
    function hide(modal){ modal.style.display='none'; modal.inert = true; }

    /* ---------------------------------------------------
       RADIOS → choix-ID = texte
    --------------------------------------------------- */
    document.querySelectorAll(".radio-choice").forEach(radio => {
        radio.addEventListener("change", () => {
            const qid = radio.name.replace("question-", "");

            // Supprime les anciens hidden de cette question
            document.querySelectorAll(`.radio-hidden-${qid}`).forEach(e => e.remove());

            const idChoix = radio.dataset.idchoix;
            const texte = radio.dataset.texte;

            radio.insertAdjacentHTML("afterend",
                `<input type="hidden"
                        class="radio-hidden-${qid}"
                        name="choix-${idChoix}"
                        value="${texte}">`
            );
        });
    });

    /* ---------------------------------------------------
       CHECKBOXES → choix-ID = texte
    --------------------------------------------------- */
    document.querySelectorAll(".check-choice").forEach(box => {
        box.addEventListener("change", () => {
            const idChoix = box.dataset.idchoix;
            const texte = box.dataset.texte;

            if (box.checked) {
                box.insertAdjacentHTML("afterend",
                    `<input type="hidden"
                            class="check-hidden-${idChoix}"
                            name="choix-${idChoix}"
                            value="${texte}">`
                );
            } else {
                document.querySelector(`.check-hidden-${idChoix}`)?.remove();
            }
        });
    });

    /* ---------------------------------------------------
       VALIDATION CHECKBOX OBLIGATOIRE
    --------------------------------------------------- */
    function isOneChecked(name) {
        const elements = document.querySelectorAll(`input[type='checkbox'][name='${name}']`);
        let checked = false;

        for (const el of elements) {
            if (el.checked) {
                checked = true;
                break;
            }
        }

        if (!checked) {
            const first = elements[0];
            first.setCustomValidity("Veuillez sélectionner au moins une réponse");
            first.reportValidity();
            return false;
        }

        elements.forEach(el => el.setCustomValidity(""));
        return true;
    }

    function validateRequiredCheckboxes() {
        const requiredCheckboxes = document.querySelectorAll("input[type='checkbox'][data-required='required']");
        const treatedNames = new Set();

        for (const checkbox of requiredCheckboxes) {
            const name = checkbox.name;

            if (!treatedNames.has(name)) {
                treatedNames.add(name);

                if (!isOneChecked(name)) {
                    return false;
                }
            }
        }
        return true;
    }

    /* ---------------------------------------------------
       BOUTON SOUMETTRE
    --------------------------------------------------- */
    if(submitBtn){
        submitBtn.addEventListener('click', () => {

            // Nettoyage erreurs HTML5
            document.querySelectorAll("input[type='checkbox']")
                .forEach(el => el.setCustomValidity(""));

            // Validation HTML5
            if (!form.reportValidity()) return;

            // Validation checkbox obligatoire
            if (!validateRequiredCheckboxes()) return;

            // Tout est bon → ouvrir le modal
            show(submitModal);
        });
    }

    /* ---------------------------------------------------
       VALIDATION FINALE (ENVOYER)
    --------------------------------------------------- */
    if(form){
        form.addEventListener('submit', (e) => {

            // Si le modal n'est PAS encore affiché → on bloque l'envoi et on l'affiche
            if (submitModal.style.display !== 'flex') {
                e.preventDefault();

                document.querySelectorAll("input[type='checkbox']")
                    .forEach(el => el.setCustomValidity(""));

                if (!form.reportValidity()) return;
                if (!validateRequiredCheckboxes()) return;

                show(submitModal);
                return;
            }

            // Si le modal est affiché → l'utilisateur a cliqué sur "Envoyer"
            // → on désactive les inputs visibles pour ne garder que les hidden
            document.querySelectorAll(".radio-choice, .check-choice").forEach(el => {
                el.disabled = true;
            });

            hide(submitModal);
            // On laisse le submit se faire naturellement
        });
    }

    /* ---------------------------------------------------
       MODAL ANNULER
    --------------------------------------------------- */
    if(cancelBtn){
        cancelBtn.addEventListener('click', function(e){
            e.preventDefault();
            show(cancelModal);
        });
    }
    if(cancelNo){ cancelNo.addEventListener('click', function(){ hide(cancelModal); }); }
    if(cancelYes){ cancelYes.addEventListener('click', function(){ window.location.href='?c=home'; }); }

    if(submitNo){ submitNo.addEventListener('click', function(){ hide(submitModal); }); }
    //if(submitYes){ submitYes.addEventListener('click', function(){ hide(submitModal); if(form) form.submit(); }); }

    // Fermer modal sur clic en dehors de la carte
    [cancelModal].forEach(modal=>{
        modal.addEventListener('click', function(e){ if(e.target===modal) hide(modal); });
    });

    if ((submitYes && brouillon === '0')){
        submitYes.addEventListener('click', function(e){
            e.preventDefault();
            alert("Ce questionnaire est en brouillon et ne peut pas être soumis.");
        });
    }
    
});