function telechargerResultats(id) {
    const url = `?c=questionnaire&a=exporter&id=${id}`;
    const a = document.createElement('a');
    a.href = url;
    a.download = ''; // facultatif, le serveur gère ça
    document.body.appendChild(a);
    a.click();
    a.remove();
}