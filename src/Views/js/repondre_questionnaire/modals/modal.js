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

    const cancelModal = document.getElementById('cancelModal');
    const cancelNo = document.getElementById('cancelModalNo');
    const cancelYes = document.getElementById('cancelModalYes');

    const submitModal = document.getElementById('submitModal');
    const submitNo = document.getElementById('submitModalNo');
    const submitYes = document.getElementById('submitModalYes');

    function show(modal){ modal.style.display='flex'; modal.setAttribute('aria-hidden','false'); }
    function hide(modal){ modal.style.display='none'; modal.setAttribute('aria-hidden','true'); }

    if(cancelBtn){
        cancelBtn.addEventListener('click', function(e){ e.preventDefault(); show(cancelModal); });
    }

    if(cancelNo){ cancelNo.addEventListener('click', function(){ hide(cancelModal); }); }
    if(cancelYes){ cancelYes.addEventListener('click', function(){ window.location.href='?c=home'; }); }

    if(submitBtn){
        submitBtn.addEventListener('click', (e) => {
            if (form.reportValidity()) {
                show(submitModal);
            }
        });
    }

    if(form){
        form.addEventListener('submit', (e) => {
            //e.preventDefault();

            hide(submitModal);

            // const formData = new FormData(form);

            // const data = [];

            // formData.forEach((valeur, cle) => {
            //     // if (cle === "id_questionnaire") {
            //     //     data.push({
            //     //         "type" : cle,
            //     //         "id_choix" : valeur,
            //     //         "reponse" : valeur
            //     //     });
            //     // } else {
            //     //     data.push({
            //     //         "type" : cle.split("-")[0],
            //     //         "id_choix" : cle.split("-")[1] ?? cle.split("-")[0],
            //     //         "reponse" : valeur
            //     //     });
            //     // }
            //     entree.name = String(entree.name).split("-")[1];
            // });

            // const hiddenInput = form.querySelector('input[type=hidden][name=json_reponses]') ?? document.createElement('input');
            // if (!hiddenInput.type) {
            //     hiddenInput.type = 'hidden';
            //     hiddenInput.name = 'json_reponses';
            //     form.appendChild(hiddenInput);
            // }
            
            // hiddenInput.value = JSON.stringify(data);

            // console.log("Données brutes :", Object.fromEntries(formData));
            // console.log("Données ordonnées :", data);

            form.querySelectorAll('input, select, textarea').forEach(entree => {
                // if (entree.name && entree.name.includes('-')) entree.name = String(entree.name).split("-")[1];
            });
            /**
             * sous la forme :
             * id-choix -> réponse
             */

        });
    }

    if(submitNo){ submitNo.addEventListener('click', function(){ hide(submitModal); }); }
    //if(submitYes){ submitYes.addEventListener('click', function(){ hide(submitModal); if(form) form.submit(); }); }

    // Fermer modal sur clic en dehors de la carte
    [cancelModal].forEach(modal=>{
        modal.addEventListener('click', function(e){ if(e.target===modal) hide(modal); });
    });
});