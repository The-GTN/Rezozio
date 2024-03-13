/* Nollet Antoine Groupe 3 */

/* On charge la page, des fonctions sont lancées */
window.addEventListener('load',setLoad);


var currentUser = null; //objet "personne" de l'utilisateiur connecté

/* Lance les différentes fonctions pour initialiser le site */
function setLoad() {
  initState();
  initEvents();
}

/* initialise l'état de la page */
function initState(){
  document.getElementById("filtre").checked = true;
  document.getElementById("profile").hidden = true;
  document.getElementById("setProfile").hidden = true;
  document.getElementById("inscription").hidden=true;
  let personne = document.body.dataset.personne;
  if (typeof personne == "undefined") etatDeconnecte();
  else etatConnecte(JSON.parse(personne));
}

/* initialise les différents events */
function initEvents() {
  document.forms.form_login.addEventListener('submit',sendLogin);
  document.forms.form_login.addEventListener('input',function(){this.message.value='';});
  document.forms.form_createUser.addEventListener('submit',sendCreateUser);
  document.forms.form_createUser.addEventListener('input',removeP);
  document.forms.form_img.addEventListener('submit',sendUpdateAvatar);
  document.forms.form_recherche.addEventListener('submit',sendResearch);
  document.forms.form_recherche.addEventListener('keyup',sendResearch);
  document.forms.form_setProfile.addEventListener('submit',sendSetProfile);
  document.querySelector('#logout').addEventListener('click',sendLogout);
  document.querySelector('#gotoinscription').addEventListener('click',gotoinscription);
  document.querySelector('#gotoconnection').addEventListener('click',gotoconnection);
  document.getElementById("filtre").addEventListener("click",filtrerMessages);
  document.getElementById("avatar2").addEventListener("click",myProfileNotHidden);
  document.getElementById("pseudo").addEventListener("click",myProfileNotHidden);
  document.getElementById("at").addEventListener("click",myProfileNotHidden);
  document.getElementById("option").addEventListener("click",gotoSetProfile);
  document.getElementById("returnButton").addEventListener("click",returnProfil);
  let lescroix = document.getElementsByClassName("closeProfile");
  for(i=0;i!=lescroix.length;i++){
    lescroix[i].addEventListener("click",profileHidden);
  }
  document.getElementById("logo").addEventListener("click",changeLight);
  <?php
  session_name('MonAuthentification');
  session_start();
  if(isset($_COOKIE["light"])) {
    if($_COOKIE["light"] == "black") echo "changeLight();changeLight();changeLight();";
    else if ($_COOKIE["light"] == "gray") echo "changeLight();changeLight();";
    else if ($_COOKIE["light"] == "lightgray") echo "changeLight();";
  }
  ?>
}
