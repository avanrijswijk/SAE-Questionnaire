let notificationConteneur;

document.addEventListener("DOMContentLoaded", () => {
    notificationConteneur = document.getElementById("notifications");
});

const TypeNotification = {
  ERREUR : "is-danger",
  SUCCES : "is-success",
  INFO : "is-info",
  ATTENTION : "is-warning"
};

/**
 * 
 * @param {TypeNotification} type - Le type de message
 * @param {string} message - Le message qui sera afficher à l'utilisateur 
 */
function notification(type, message) {
    const notifConteneur = document.createElement("div");
    const boutonFermer = document.createElement("button");
    const pMessage = document.createElement("p");

    // const id = "notification-" + notifications.childElementCount;

    notifConteneur.classList.add("notification", type);
    boutonFermer.classList.add("delete");
    pMessage.classList.add("is-unselectable");
    pMessage.innerText = message;
    
    // notifConteneur.id = id;

    if (notificationConteneur) {
        notifConteneur.append(boutonFermer, pMessage);
        notificationConteneur.appendChild(notifConteneur);

        boutonFermer.addEventListener("click", () => {
            notificationConteneur.removeChild(notifConteneur);
        });
    }
}

export {TypeNotification, notification}

// notification(TypeNotification.INFO, "message INFO");
// notification(TypeNotification.ERREUR, "message ERREUR");
// notification(TypeNotification.SUCCES, "message SUCCES");
// notification(TypeNotification.ATTENTION, "message ATTENTION");  