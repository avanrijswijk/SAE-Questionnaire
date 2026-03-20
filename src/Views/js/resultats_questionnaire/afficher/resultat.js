console.log("JS chargé !"); //debug
/*function telechargerResultats(id) {
    const url = `?c=questionnaire&a=exporter&id=${id}`;
    const a = document.createElement('a');
    a.href = url;
    document.body.appendChild(a);
    a.click();
    a.remove();
}*/

// fonction qui passe en POST id et newText | permet l'enregistrement du nouveau titre dans la base de données
function changerTitre(id, newText) {
    return fetch(`?c=questionnaire&a=changertitre`, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${encodeURIComponent(id)}&titre=${encodeURIComponent(newText)}`
    })
    .then(res => res.text());
}

//fonction qui permet de renommer un questionnaire sur la page
function renommer(span) {
    const oldText = span.textContent;
    const id = span.closest('.ligne-questionnaire').dataset.id;

    // récupère le titre dans une slection de texte
    const input = document.createElement('input');
    input.type = 'text';
    input.value = oldText;
    input.className = 'input is-small';
    input.style.width = "90%";

    span.replaceWith(input);
    input.focus();

    // sauvegarde du nouveau titre
    const save = () => {
        const newText = input.value;
        changerTitre(id, newText)
            .then(response => {
                // Recrée le titre(non modifiable) avec le nouveau texte
                const newSpan = document.createElement('span');
                newSpan.className = 'titre-texte';
                newSpan.textContent = newText;

                input.replaceWith(newSpan);

                newSpan.addEventListener('dblclick', e => {
                    e.stopPropagation();
                    renommer(newSpan);
                });
            })
            .catch(err => {
                console.error("Erreur lors de la mise à jour :", err);
            });
    };
    

    // touche 'Enter' pour valider ou quitter la zone de texte
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') save();
    });

    input.addEventListener('blur', save);
}

window.addEventListener('load', () => {

    let annulerRedirection = false;

    document.querySelectorAll('.ligne-questionnaire').forEach(tr => {
        let timer = null;
        const delay = 250;

        tr.addEventListener('click', e => {
            timer = setTimeout(() => {

                if (!annulerRedirection) {
                    window.location.href = `?c=questionnaire&a=detail&id=${tr.dataset.id}`;
                }

                annulerRedirection = false;
            }, delay);
        });

        const titreTexte = tr.querySelector('.titre-texte');
        if (titreTexte) {
            titreTexte.addEventListener('dblclick', e => {
                e.stopPropagation();
                clearTimeout(timer);

                annulerRedirection = true;
                renommer(titreTexte);
            });
        }
    });

});