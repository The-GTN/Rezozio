/* Nollet Antoine Groupe 3 */

/* passe à l'inscription, l'utilisateur est donc toujour deconnecte */
function gotoinscription() {
  for (let elt of document.querySelectorAll('.deconnecte'))
     elt.hidden=true;
  for (let elt of document.querySelectorAll('.connecte'))
     elt.hidden=true;
  document.getElementById("login").value="";
  document.getElementById("password").value="";
  document.getElementById("inscription").hidden=false;
}

/* passe de l'inscription à l'affichage du formulaire de connection */
function gotoconnection() {
  etatDeconnecte();
  var ins = document.getElementById("inscription");
  if (typeof ins.getElementsByTagName("p")[0] == "object") removeP();
  ins.hidden = true;
  document.getElementById("login2").value="";
  document.getElementById("pseudo2").value="";
  document.getElementById("password2").value="";
}

/* action du bouton de filtrage des messages, filtre ou non les messages selon si il est oui ou non coché */
function filtrerMessages() {
	cleanMessages();
	displayPostMessage();
	if(this.checked == true) filtreMessages();
	else TousMessages();
}

/* affiche le bloc où on peut écrire les messages */
function displayPostMessage() {
	let bloc = document.getElementById("messages");
	let formulaire = document.createElement("form");
	formulaire.id="form_postmessage";
	formulaire.action="services/postMessage.php"
	formulaire.method="post";
	bloc.appendChild(formulaire);
	let blocForm = document.createElement("div");
	blocForm.id = "blocPostMessage";
	formulaire.appendChild(blocForm);
	let textarea = document.createElement("textarea");
	textarea.maxlength = "280";
	textarea.name ="source";
	textarea.id = "postMessage";
	textarea.placeholder="Quoi de neuf ..?";
	textarea.rows = "7";
	textarea.cols = "57";
	textarea.style.margin = "5px 0 0 5px";
	textarea.style.resize = "none";
	textarea.required = "required";
	blocForm.appendChild(textarea);
  let br = document.createElement("br");
  blocForm.appendChild(br);
	let button = document.createElement("button");
	button.type = "submit";
	button.name = "valid";
	let textButton = document.createTextNode("Publier");
	button.appendChild(textButton);
	blocForm.appendChild(button);
	formulaire.addEventListener("submit",post);
}

/* vide le contenu du fil des messages */
function cleanMessages() {
	let messages = document.getElementById("messages");
	messages.innerHTML = "";
}

/* action du bouton before du fil des messages */
function beforeMessages() {
  let messages = document.getElementById("messages").getElementsByClassName("message");
  let n = messages[messages.length - 1].id;
  if (document.getElementById("avatar2").hidden==true || (document.getElementById("filtre").checked == false && document.getElementById("avatar2").hidden==false) ) addPublic(n);
  else addFiltre(n);
}

/* Affecte l'event click d'affichage de profil à tous les noms d'auteurs de messages */
function lesGens() {
  let lesGens = document.getElementsByClassName("auteur");
  for(i=0;i!=lesGens.length;i++){
    lesGens[i].addEventListener("click",profileNotHidden);
    lesGens[i].parentNode.parentNode.getElementsByTagName("img")[0].addEventListener("click",profileNotHidden4);
  }
}

/* se déclenche lorsqu'on clique sur la croix de sortie de profil, cache l'espace de profil */
function profileHidden() {
  document.getElementById("filtre").checked = true;
  cleanMessages();
  document.getElementById("personalProfile").hidden = false;
  document.getElementById("setProfile").hidden = true;
  document.getElementById("profile").hidden = true;
  if(document.getElementById("avatar2").hidden==false){
    displayPostMessage();
    filtreMessages();
    sendListeAbonnee();
    sendListeAbonnement();
  }
  else TousMessages();
}

/* action lorsqu'on clique sur la roue denté dans l'espace de profil, envoie vers l'espace de changement de profil */
function gotoSetProfile() {
  document.getElementById("personalProfile").hidden = true;
  document.getElementById("setProfile").hidden = false;
}

/* action du bouton before du fil des messages d'un profil */
function oneGuyBeforeMessages() {
  let messages = document.getElementById("profileMessages").getElementsByClassName("message");
  let n = messages[messages.length - 1].id.substring(3);
  let user = messages[0].getElementsByClassName("auteur")[0].getElementsByTagName("em")[0].innerText.substring(2)
  sendProfileMessages(n,user);
}

/* action du bouton de retour au profil, cache l'espace de changement de profil et affiche l'espace de profil */
function returnProfil() {
  document.getElementById("setProfile").hidden = true;
  myProfileNotHidden();
  document.getElementById("personalProfile").hidden = false;
}

/* enleve le texte en dessous du formulaire d'inscription */
function removeP() {
  var ins = document.getElementById("inscription");
  var p = ins.getElementsByTagName("p")[0];
  if(p != null) ins.removeChild(p);
}

function changeLight() {
  let entete = document.getElementsByTagName("h1")[0];
  let droite = document.getElementById("recherche");
  let gauche = document.getElementById("espace_variable");
  let mess = document.getElementById("messages");
  let change = [entete,gauche,droite,mess];
  if (entete.style.backgroundColor == "lightgray") grayLight(change);
  else if (entete.style.backgroundColor == "gray") blackLight(change);
  else if (entete.style.backgroundColor == "black") normalLight(change);
  else lightgrayLight(change);
}

function grayLight(change) {
  let url = 'services/light.php?light=gray';
  fetchFromJson(url)
  .then(processAnswer)
  .then(rien, rien);
  document.getElementById("logo_recherche").src = "images/recherche2.png"
  for(elem of document.getElementsByClassName("deconnecte")) elem.style.color = "white";
  for(elem of document.getElementsByClassName("connecte")) elem.style.color = "white";
  document.getElementById("logout").style.color = "";
  document.getElementById("listeRecherche").style.backgroundColor = "rgba(255,255,255,0.7)";
  change[1].style.borderRightColor = "white";
  change[1].style.borderTopColor = "white";
  change[2].style.borderLeftColor = "white";
  change[2].style.borderTopColor = "white";
  change[3].style.borderTopColor = "white";
  for(elem of change)
    elem.style.backgroundColor = "gray";
}

function blackLight(change) {
  for(elem of change)
    elem.style.backgroundColor = "black";
  let url = 'services/light.php?light=black';
  fetchFromJson(url)
  .then(processAnswer)
  .then(rien, rien);
}

function normalLight(change) {
  document.getElementById("logo_recherche").src = "images/recherche.png"
  for(elem of document.getElementsByClassName("deconnecte")) elem.style.color = "";
  for(elem of document.getElementsByClassName("connecte")) elem.style.color = "";
  document.getElementById("listeRecherche").style.backgroundColor = "";
  for(elem of change)
    elem.style.backgroundColor = "";
  change[1].style.borderRightColor = "";
  change[1].style.borderTopColor = "";
  change[2].style.borderLeftColor = "";
  change[2].style.borderTopColor = "";
  change[3].style.borderTopColor = "";
  let url = 'services/light.php?light=normal';
  fetchFromJson(url)
  .then(processAnswer)
  .then(rien, rien);
}

function lightgrayLight(change) {
  for(elem of change)
    elem.style.backgroundColor = "lightgray";
  let url = 'services/light.php?light=lightgray';
  fetchFromJson(url)
  .then(processAnswer)
  .then(rien, rien);
}

function rien(res) {}