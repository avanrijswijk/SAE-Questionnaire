import { TypeNotification, notification } from "../../utils/notification/notification.js";

function telechargerResultats() {
    alert("Telechargement fictif");
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("code.code").forEach((element) => {
        element.addEventListener("click", () => {
            const code = element.innerText;
            navigator.clipboard.writeText(code)
                .then(() => {
                    notification(TypeNotification.SUCCES, "Code copié avec succes.");
                })
                .catch(() => {
                    notification(TypeNotification.ERREUR, "Le code n'a pas pu être copié.");
                });
        });
    });
});

