/**
 * Ferme un modal
 * @param {HTMLDivElement} modal - Un <div> avec la class modal
 */
function fermer_modal(modal) {
    modal.classList.remove("is-active");
}

/**
 * Ouvre un modal
 * @param {HTMLDivElement} modal - Un <div> avec la class modal
 */
function ouvrire_modal(modal) {
    modal.classList.add("is-active");
}


export {
    fermer_modal,
    ouvrire_modal
}