    <main>
      <div class="is-flex is-flex-direction-column ">
        <div style="justify-items: center;">
          <h1 class="title is-1 mt-3">Que voulez-vous faire ?</h1>
        </div>
        <div class="columns">
          <style>
            a:hover {
              transform: scale(110%);
              transition: all 1s;
            }

            a:not(:hover) {
              transform: scale(100%);
              transition: all 1s;
            }
          </style>
          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=creation">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/creation_questionnaire.png" alt="icon creation de questionnaire">
            </div>
            <h2 class="title is-2" style="text-align: center;">Créer ou modifier un questionnaire</h2>
          </a>

          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=lister">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/repondre_questionnaire.png" alt="icon répondre à un questionnaire">
            </div>
            <h2 class="title is-2" style="text-align: center;">Répondre à un questionnaire</h2>
          </a>

          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=resultats">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/resultat_quetionnaire.png" alt="icon creation de questionnaire">
            </div>
            <h2 class="title is-2" style="text-align: center;">Voir les résultats d'un questionnaire</h2>
          </a>
        </div>

      </div>
      <div class="notification" style="width:50%; position: absolute; bottom: 0%; left: 2%; display: none;">
        <button class="delete"></button>
        Primar lorem ipsum dolor sit amet, consectetur adipiscing elit lorem ipsum
        dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta
        nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus
        diam, et dictum <a>felis venenatis</a> efficitur.
      </div>
    </main>