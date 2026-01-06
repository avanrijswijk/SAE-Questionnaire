/**
 * Ferme un modal
 * @param {HTMLDivElement} modal - Un <div> avec la class modal
 */
export function fermer_modal(modal) {
    modal.classList.remove("is-active");
}

/**
 * Ouvre un modal
 * @param {HTMLDivElement} modal - Un <div> avec la class modal
 */
export function ouvrire_modal(modal) {
    modal.classList.add("is-active");
}