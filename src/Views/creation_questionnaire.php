<main class="is-flex is-flex-direction-row">
    <script src="./src/Views/js/creation_questionnaire.js"></script>
    <div class="is-flex is-flex-direction-column is-justify-content-space-between pb-6" style="background-color: #E9E9E9;width: 30%;">
        <div style="background-color: #F5A320;border-radius: 0 0 100px 0;">
            <button type="button" id="ajouter-question">
                <h3 class="title is-4 m-1 p-2">+ Ajouter une question</h3>
            </button>
        </div>
        <div id="visualiseur-questions" class="pl-2" style="height: 100%;">
            <noscript>
                Partie pour l'affichage des questions, etc
            </noscript>
            
        </div>
        <div id="dialog" class="modal">
            <div class="modal-background"></div>
            <form id="form-enregistrer" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Enregistrer le questionnaire</p>
                    <button type="button" id="bouton-fermer" class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body is-flex is-flex-direction-column is-align-items-center">
                    <div class="field" style="width: 50%;">
                        <label class="label" for="nom-questionnaire">Nom du Questionnaire :</label>
                        <div class="control">
                            <input class="input" name="nom-questionnaire" id="nom-questionnaire" required></input> 
                        </div>     
                    </div>
                    <div class="field" style="width: 50%;">
                        <label class="label" for="liste_participants">Liste participants :</label>
                        <div class="control" style="width: 100%;">
                            <div class="select" style="width: 100%;">
                                <select name="liste_participants" id="liste_participants" style="width: 100%;" required>
                                        <option value="">--Veuillez choisir une option--</option>
                                        <option value="G2A">G2A</option>
                                        <option value="A2">A2</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    <div class="field" style="width: 50%;">
                        <p class="pt-3">Code d'acces au questionnaire : <span><code id="code_acces">XXXXX</code></span></p>  
                    </div>
                </section>
                <footer class="modal-card-foot" style="justify-content: center;">
                    <button type="submit" class="button" id="bouton-valider">
                        <p>Valider</p>
                    </button>
                </footer>
            </form>
        </div>
        <button type="button" id="bouton-finir"> <!--onclick="window.location.href = './?c=home';"-->
            <h3 class="title is-4 mt-3 p-4">FINIR</h3>
        </button>
    </div>
    <div style="background-color: #B5C6E6; width:100%"  class="is-flex is-justify-content-center is-align-items-center">
        <div style="background-color: #dfdfdfff; width:85%; padding: 10px;">
            page
        </div>
    </div>
    
</main>