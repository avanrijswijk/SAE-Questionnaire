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

    function show(modal){ modal.style.display='flex'; modal.inert = false;/*modal.setAttribute('aria-hidden','false');*/ }
    function hide(modal){ modal.style.display='none'; modal.inert = true;/*modal.setAttribute('aria-hidden','true');*/ }

    function isOneChecked(name) {
        const elements = document.querySelectorAll(`input[type='checkbox'][name='${name}']`);
        let s = false;
        elements.forEach(e => {
            if (e.checked) {
                elements.forEach(el=>{el.setCustomValidity("");})
                s = true;
                return true;
            }
        })
        if (!elements) return true;
        if (!s){ 
        const firstElement = elements[0];
        firstElement.setCustomValidity("Veuillez remplir ce champs");
        firstElement.reportValidity();
        return false;
        } else {
            return true;
        }
        
    }

    if(cancelBtn){
        cancelBtn.addEventListener('click', function(e){ e.preventDefault(); show(cancelModal); });
    }

    if(cancelNo){ cancelNo.addEventListener('click', function(){ hide(cancelModal); }); }
    if(cancelYes){ cancelYes.addEventListener('click', function(){ window.location.href='?c=home'; }); }

    if(submitBtn){
        submitBtn.addEventListener('click', (e) => {
            const elements = document.querySelectorAll(`input[type='checkbox']`);
            elements.forEach(el=>{el.setCustomValidity("");})
            if (form.reportValidity()) {
                show(submitModal);
            }
        });
    }

    if(form){
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            hide(submitModal);

            const formData = new FormData(form);

            const f = () => {
                const elements = document.querySelectorAll(`input[type='checkbox']`);

                elements.forEach(e => {
                    if (e.dataset.required) {
                        // console.log(e.name);
                        if (!isOneChecked(e.name)) {
                            return false;
                        }
                    }
                });
                return true;
            }

            if (!f()) return;
            // const data = [];
            

            // const hiddenInput = form.querySelector('input[type=hidden][name=json_reponses]') ?? document.createElement('input');
            // if (!hiddenInput.type) {
            //     hiddenInput.type = 'hidden';
            //     hiddenInput.name = 'json_reponses';
            //     form.appendChild(hiddenInput);
            // }
            
            // hiddenInput.value = JSON.stringify(data);

            console.log("Données brutes :", Object.fromEntries(formData));
            // console.log("Données ordonnées :", data);

            /**
             * sous la forme :
             * id-choix -> réponse
             */
            document.body.focus();
            form.submit();

        });
    }

    if(submitNo){ submitNo.addEventListener('click', function(){ hide(submitModal); }); }
    //if(submitYes){ submitYes.addEventListener('click', function(){ hide(submitModal); if(form) form.submit(); }); }

    // Fermer modal sur clic en dehors de la carte
    [cancelModal].forEach(modal=>{
        modal.addEventListener('click', function(e){ if(e.target===modal) hide(modal); });
    });
});