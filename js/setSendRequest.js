/* Nollet Antoine Groupe 3 */

/* Connexion */
function sendLogin(ev){
  ev.preventDefault();
  let url = 'services/login.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(beforeConnecte, errorLogin);
}

/* Recuperation des donnes de compte */
function beforeConnecte(personne) {
  let url = 'services/getUser.php?userId='+personne;
  fetchFromJson(url)
  .then(processAnswer)
  .then(etatConnecte, errorLogin);
}

/* Deconnexion */
function sendLogout(ev){ // gestionnaire de l'évènement click sur le bouton logout
  ev.preventDefault();
  let url = 'services/logout.php';
  fetchFromJson(url)
  .then(processAnswer)
  .then(etatDeconnecte, errorLogin);
}

/* Inscription */
function sendCreateUser(ev){
  ev.preventDefault();
  let url = 'services/createUser.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(inscris, errorCreate);
}

/* Envoie d'un nouvel Avatar pour l'utilisateur */
function sendUpdateAvatar(ev){
  ev.preventDefault();
  let url = 'services/uploadAvatar.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(updateAvatar, errorBasic);
  window.alert("Avatar modifié");
}

/* continuité de l'output valide de sendUpdateAvatar, modifie l'avatar en haut à gauche du site */
function updateSmallAvatar() {
    let changeAvatar = function(blob) {
      if (blob.type.startsWith('image/')){ // le mimetype est celui d'une image
        let img2 = document.getElementById('avatar2');
        img2.src = URL.createObjectURL(blob);
        img2.hidden=false;
      }
    };
  fetchBlob('services/getAvatar.php?userId='+currentUser.login)
    .then(changeAvatar);
}

/* continuité de l'output valide de sendUpdateAvatar, modifie l'avatar du profil de l'utilisateur */
function updateLargeAvatar() {
    let changeAvatar = function(blob) {
      if (blob.type.startsWith('image/')){ // le mimetype est celui d'une image
        let img = document.getElementById('avatar');
        img.src = URL.createObjectURL(blob);
      }
    };
  fetchBlob('services/getAvatar.php?userId='+currentUser.login+'&size=large')
    .then(changeAvatar);
}

/* continuité de l'output valide de sendUpdateAvatar, modifie l'avatar dans les messages */
function updateMessagesAvatar() {
    let changeAvatar = function(blob) {
      if (blob.type.startsWith('image/')){ // le mimetype est celui d'une image
        let messages = document.getElementsByClassName("message");
        for(let message of messages) {
          let user = message.getElementsByTagName("div")[0].getElementsByTagName("span")[0].getElementsByTagName("em")[0].innerText.substring(2);
          if(user == currentUser.login) {
            let imgText = message.getElementsByTagName("img")[0];
            imgText.src = URL.createObjectURL(blob);
          }
        }
      }
    };
  fetchBlob('services/getAvatar.php?userId='+currentUser.login)
    .then(changeAvatar);
}

/* Recupère tous les messages */
function TousMessages(){
  let url = 'services/findMessages.php';
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayMessages, displayErrorMessages);
}

/* Recupère les messages filtrés par l'abonnement */
function filtreMessages(){
  let url = 'services/findFollowedMessages.php';
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayMessages, displayErrorMessages);
}

/* Post de message */
function post(ev){
  ev.preventDefault();
  let url = 'services/postMessage.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(poster, nonPoster);
}

/* requete de beforeMessages, ajoute les messages avant le n ième dans le fil des messages, ici le fil des messages est non filtré */
function addPublic(n) {
  let url = 'services/findMessages.php?count=10&before='+n;
  fetchFromJson(url)
  .then(processAnswer)
  .then(addMessages, plusdemessageAvant);
}

/* requete de beforeMessages, ajoute les messages avant le n ième dans le fil des messages, ici le fil des messages est filtré */
function addFiltre(n){
  let url = 'services/findFollowedMessages.php?count=10&before='+n;
  fetchFromJson(url)
  .then(processAnswer)
  .then(addMessages, plusdemessageAvant);
}

/* affichage d'un profil */
function profileNotHidden() {
  let login = this.getElementsByTagName("em")[0].innerText.substring(2);
  let url = 'services/getProfile.php?userId='+login;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfile, errorBasic);
}

/* affichage du profil de l'utilisateur connecte */
function myProfileNotHidden() {
  let url = 'services/getProfile.php?userId='+currentUser.login;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfile, errorBasic);
  updateAvatar();
}

/* suit un utilisateur */
function follow() {
  let user = this.parentNode.getElementsByTagName("em")[0].innerText.substring(1);
  let url = 'services/follow.php?target='+user;
  fetchFromJson(url)
  .then(processAnswer)
  .then(switch1, errorBasic);
}

/* ne suit plus un utilisateur */
function unfollow() {
  let user = this.parentNode.getElementsByTagName("em")[0].innerText.substring(1);
  let url = 'services/unfollow.php?target='+user;
  fetchFromJson(url)
  .then(processAnswer)
  .then(switch2, errorBasic);
}

/* affiche les messages de l'utilisateur dont le profil est affiché */
function profileMessages(user){
  let url = 'services/findMessages.php?count=10&author='+user;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfileMessages, errorNone);
}

/* fonction lancé par oneGuyBeforeMessages, ajoute les messages avant le n ième dans le fil de message de profil de l'utilisateur user */
function sendProfileMessages(n,user) {
  let url = 'services/findMessages.php?count=5&before='+n+'&author='+user;
  fetchFromJson(url)
  .then(processAnswer)
  .then(addProfileMessages, plusdemessageAvant);
}

/* change le profil de l'utilisateur connecte */
function sendSetProfile(ev) {
  ev.preventDefault();
  let url = 'services/setProfile.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(changePseudo, errorBasic);
}

/* change l'espace de recherche */
function sendResearch(ev) {
  ev.preventDefault();
  let url = 'services/findUsers.php';
  let args = new FormData(this);
  fetchFromJson(url,{method:'POST',body:args,credentials:'same-origin'})
  .then(processAnswer)
  .then(displayResearch, errorResearch);
}

/* affichage de profil en cliquant sur un bloc dans l'espace de recherche */
function profileNotHidden2() {
  let url = 'services/getProfile.php?userId='+this.id;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfile, errorBasic);
}

/* affichage de la liste des abonnés */
function sendListeAbonnee() {
  let url = 'services/getFollowers.php';
  fetchFromJson(url,{method:'POST',credentials:'same-origin'})
  .then(processAnswer)
  .then(displayListeAbonnee, errorListe1);
}

/* affichage de la liste des abonnements */
function sendListeAbonnement() {
  let url = 'services/getSubscriptions.php';
  fetchFromJson(url,{method:'POST',credentials:'same-origin'})
  .then(processAnswer)
  .then(displayListeAbonnement, errorListe2);
}

/* affichage du profil par le click dans la liste abonnes et abonnements */
function profileNotHidden3() {
  let url = 'services/getProfile.php?userId='+this.id.substring(4);
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfile, errorBasic);
}

/* suit l'utilisateur via l'espace abonnee */
function follow2() {
  let url = 'services/follow.php?target='+this.id.substring(7);
  fetchFromJson(url)
  .then(processAnswer)
  .then(switch3, errorBasic);
}

/* ne suit plus l'utilisateur via l'espace abonnee et abonnements */
function unfollow2() {
  let url = 'services/unfollow.php?target='+this.id.substring(7);
  fetchFromJson(url)
  .then(processAnswer)
  .then(switch3, errorBasic);
}

/* affichage du profil par le click dans la liste abonnes et abonnements */
function profileNotHidden4() {
  if (this.src.substr(0,4) == "blob") {
    let url = 'services/getProfile.php?userId='+currentUser.login;
    fetchFromJson(url)
    .then(processAnswer)
    .then(displayProfile, errorBasic);
  }
  else {
    let url = 'services/getProfile.php?userId='+this.src.split("=")[1];
    fetchFromJson(url)
    .then(processAnswer)
    .then(displayProfile, errorBasic);
  }
}