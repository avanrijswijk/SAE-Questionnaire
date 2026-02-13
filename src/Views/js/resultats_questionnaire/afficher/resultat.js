console.log("JS chargé !");
/*function telechargerResultats(id) {
    const url = `?c=questionnaire&a=exporter&id=${id}`;
    const a = document.createElement('a');
    a.href = url;
    document.body.appendChild(a);
    a.click();
    a.remove();
}*/

window.addEventListener('load', () => {

    document.querySelectorAll('.ligne-questionnaire').forEach(tr => {
        let timer = null;
        const delay = 250;

        // CLIC SIMPLE sur la ligne OU sur le texte
        tr.addEventListener('click', e => {
            timer = setTimeout(() => {
                alert('clic simple');
            }, delay);
        });

        // DOUBLE CLIC sur le texte du titre uniquement
        const titreTexte = tr.querySelector('.titre-texte');
        if (titreTexte) {
            titreTexte.addEventListener('dblclick', e => {
                e.stopPropagation();
                clearTimeout(timer);
                alert('double clic sur le texte');
            });
        }
    });

});