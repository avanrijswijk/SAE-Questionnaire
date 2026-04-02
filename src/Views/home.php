    <main>
      <script>
        document.title = "Quit - Page d'accueil"
      </script>
      <div class="is-flex is-flex-direction-column mb-6">
        <div class="is-align-items-center is-flex is-flex-direction-column mt-6">
          <h1 class="title is-2 has-text-grey-dark has-text-weight-bold">Que voulez-vous faire ?</h1>
        </div>
        <div class="columns">
          <style>
            a:hover {
              transform: scale(110%);
              transition: all 0.3s;
            }

            a:not(:hover) {
              transform: scale(100%);
              transition: all 0.3s;
            }
          </style>
          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=creation">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/creation_questionnaire.png" alt="icon creation de questionnaire">
            </div>
            <h2 class="title is-3" style="text-align: center;">Créer un nouveau questionnaire</h2>
          </a>

          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=lister">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/repondre_questionnaire.png" alt="icon répondre à un questionnaire">
            </div>
            <h2 class="title is-3" style="text-align: center;">Répondre à un questionnaire</h2>
          </a>

          <a class="column is-flex is-flex-direction-column is-align-items-center p-6" href="./?c=questionnaire&a=resultats">
            <div class="image m-6 p-6" style="background-color: #F5A320; border-radius: 25%;">
              <img src="./src/Views/img/resultat_quetionnaire.png" alt="icon creation de questionnaire">
            </div>
            <h2 class="title is-3" style="text-align: center;">Mes questionnaires</h2>
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