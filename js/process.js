/* Nollet Antoine Groupe 3 */

/* traitement de la réponse, commun à tous les envoies de requêtes */
function processAnswer(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}

/* erreur de base, avec affichage du probleme, si tous est en ordre, cette fonction ne devrait jamais être lancé */
function errorBasic(error) {
  window.alert(error);
}

/* erreur prévue, rien ne se passe */
function errorNone(error) {}

/* output valide de sendLogin, passe dans l'état 'connecté' */
function etatConnecte(personne) {
    currentUser = personne;
    if(typeof currentUser.login == "undefined") currentUser.login = currentUser.userId;
    // cache ou montre les éléments
    for (let elt of document.querySelectorAll('.deconnecte'))
       elt.hidden=true;
    for (let elt of document.querySelectorAll('.connecte'))
       elt.hidden=false;

    // personnalise le contenu
    document.querySelector('#pseudo').innerHTML = `<b>${currentUser.pseudo}</b>`;
    document.querySelector('#at').innerHTML = `@<em>${currentUser.login}</em>`;

    updateAvatar();
    cleanMessages();
    displayPostMessage();
    filtreMessages();
    document.getElementById("filtre").checked = true;
    sendListeAbonnee();
    sendListeAbonnement();

    document.getElementById("login").value="";
    document.getElementById("password").value="";
}


/* output valide de sendLogout, passe dans l'état 'déconnecté' */
function etatDeconnecte() {
    // cache ou montre les éléments
    for (let elt of document.querySelectorAll('.connecte'))
       elt.hidden=true;
    for (let elt of document.querySelectorAll('.deconnecte'))
       elt.hidden=false;
    // nettoie la partie personnalisée :
    currentUser = null;
    delete(document.body.dataset.personne);
    document.querySelector('#pseudo').textContent='';
    document.querySelector('#at').textContent='';
    document.querySelector('#avatar').src='';
    document.querySelector('#avatar2').src='';
    document.querySelector('#avatar2').hidden=true;
    cleanMessages();
    TousMessages();
}

/* output erreur de sendLogin et sendLogout */
function errorLogin(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_login.message.value = 'échec : ' + error.message;
}

/* output valide de sendCreateUser */
function inscris() {
  document.getElementById("inscription").innerHTML += "<p>Vous êtes bien inscris</p>";
  document.querySelector('#gotoconnection').addEventListener('click',gotoconnection);
}

/* ouput erreur de sendCreateUser */
function errorCreate(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_createUser.message.value = 'échec : ' + error.message;
}

/* output valide de sendUpdateAvatar, change les avatars de l'utilisateur directement sur le site */
function updateAvatar() {
  updateSmallAvatar();
  updateLargeAvatar();
  updateMessagesAvatar();
}

/* output valide de TousMessages et de filtreMessages, affiche les différents messages reçus */
function displayMessages(messages) {
	let bloc = document.getElementById("messages");
	for (let message of messages){
		let newDiv = document.createElement("div");
		newDiv.className = "message";
    newDiv.id=message['messageId'];
		bloc.appendChild(newDiv);

    let avatar = document.createElement("img");
    avatar.src= "services/getAvatar.php?userId="+message['author'];
    newDiv.appendChild(avatar);

		let blocTexte = document.createElement("div");
		newDiv.appendChild(blocTexte);

		let auteur = document.createElement("span");
		auteur.className = "auteur";
    let pseudo = document.createElement("b");
		pseudo.insertAdjacentHTML('beforeend',message['pseudo']);
		let login = document.createElement("em");
		login.insertAdjacentHTML('beforeend'," @"+message['author']);
		auteur.appendChild(pseudo);
		auteur.appendChild(login);
		blocTexte.appendChild(auteur);

		let annee = message['datetime'].substring(0,4);
		let mois = message['datetime'].substring(5,7);
		let jour = message['datetime'].substring(8,10);
		let heure = message['datetime'].substring(11,19);
		let texteDate = document.createTextNode("Publié le "+jour+"/"+mois+"/"+annee+" à "+heure);
		let date = document.createElement("span");
		date.className = "date";
		date.appendChild(texteDate);
		blocTexte.appendChild(date);

		let contenu = document.createElement("p");
		blocTexte.appendChild(contenu);
		contenu.insertAdjacentHTML('beforeend',message['content']);
	}
	lesGens();
	let before = document.createElement("button");
	before.insertAdjacentHTML('beforeend',"Before");
	before.id="before";
	before.style.margin = "1.5% 2.5% 1% 45.5%";
	before.addEventListener("click",beforeMessages);
	bloc.appendChild(before);
}

/* output erreur de TousMessages et de filtreMessages */
function displayErrorMessages(error) {
	cleanMessages();
	displayPostMessage();
}

/* output valide de post */
function poster() {
	cleanMessages();
	displayPostMessage();
  if(document.getElementById("filtre").checked == true) filtreMessages();
	else TousMessages();
}

/* output erreur de post */
function nonPoster(erreur) {
	cleanMessages();
	displayPostMessage();
	filtreMessages();
  errorBasic(erreur);
}

/* output valide de addFiltre et addPublic */
function addMessages(messages) {
  document.getElementById("before").parentNode.removeChild(document.getElementById("before"));
  displayMessages(messages);
}

/* output erreur de addFiltre et addPublic et sendProfileMessages */
function plusdemessageAvant(){
  window.alert("Plus de messages avant");
}

/* output valide de profileNotHidden et de myProfileNotHidden, affiche le profil */
function displayProfile(profile){
  document.getElementById("profilePseudo").innerHTML="";
  document.getElementById("profileDescription").innerHTML="";
  document.getElementById("profilePseudo").insertAdjacentHTML('beforeend',"<b>"+profile['pseudo']+"</b><em>@"+profile['userId']+"</em>");
  document.getElementById("avatar").src = "services/getAvatar.php?size=large&userId="+profile['userId'];
  document.getElementById("profileDescription").insertAdjacentHTML('beforeend',"<p>"+profile['description']+"</p>");
  document.getElementById("profileMessages").innerHTML = "";
  profileMessages(profile['userId']);
  document.getElementById("profile").hidden = false;
  if(document.getElementsByClassName("connecte")[0].hidden == false){
    if(profile['userId']==currentUser.login){
      document.getElementById("option").hidden = false;
      document.getElementById("followOrNot").hidden = true;
    }
    else{
      document.getElementById("option").hidden = true;
      document.getElementById("followOrNot").hidden = false;
      if(profile['followed']) {
        document.getElementById("followOrNot").innerHTML = "";
        document.getElementById("followOrNot").insertAdjacentHTML('beforeend',"Unfollow");
        document.getElementById("followOrNot").addEventListener("click",unfollow);
        document.getElementById("followOrNot").removeEventListener("click",follow);
      }
      else{
        document.getElementById("followOrNot").innerHTML = "";
        document.getElementById("followOrNot").insertAdjacentHTML('beforeend',"Follow");
        document.getElementById("followOrNot").addEventListener("click",follow);
        document.getElementById("followOrNot").removeEventListener("click",unfollow);
      }
    }
  }
  else {
    document.getElementById("option").hidden = true;
    document.getElementById("followOrNot").hidden = true;
  }
}

/* output valide de follow */
function switch1() {
  document.getElementById("followOrNot").innerHTML = "";
  document.getElementById("followOrNot").insertAdjacentHTML('beforeend',"Unfollow");
  document.getElementById("followOrNot").addEventListener("click",unfollow);
  document.getElementById("followOrNot").removeEventListener("click",follow);
}

/* output valide de unfollow */
function switch2() {
  document.getElementById("followOrNot").innerHTML = "";
  document.getElementById("followOrNot").insertAdjacentHTML('beforeend',"Follow");
  document.getElementById("followOrNot").addEventListener("click",follow);
  document.getElementById("followOrNot").removeEventListener("click",unfollow);
}

/* output valide de profileMessages */
function displayProfileMessages(messages) {
	let bloc = document.getElementById("profileMessages");
	for (let message of messages){
		let newDiv = document.createElement("div");
		newDiv.className = "message";
    newDiv.id = "bis"+message['messageId'];
		bloc.appendChild(newDiv);

    let avatar = document.createElement("img");
    avatar.src= "services/getAvatar.php?userId="+message['author'];
    newDiv.appendChild(avatar);

		let blocTexte = document.createElement("div");
		newDiv.appendChild(blocTexte);

		let auteur = document.createElement("span");
		auteur.className = "auteur";
    let pseudo = document.createElement("b");
		pseudo.insertAdjacentHTML('beforeend',message['pseudo']);
		let login = document.createElement("em");
		login.insertAdjacentHTML('beforeend'," @"+message['author']);
		auteur.appendChild(pseudo);
		auteur.appendChild(login);
		blocTexte.appendChild(auteur);

		let annee = message['datetime'].substring(0,4);
		let mois = message['datetime'].substring(5,7);
		let jour = message['datetime'].substring(8,10);
		let heure = message['datetime'].substring(11,19);
		let texteDate = document.createTextNode("Publié le "+jour+"/"+mois+"/"+annee+" à "+heure);
		let date = document.createElement("span");
		date.className = "date";
		date.appendChild(texteDate);
		blocTexte.appendChild(date);

		let contenu = document.createElement("p");
		blocTexte.appendChild(contenu);
		contenu.insertAdjacentHTML('beforeend',message['content']);
	}
  lesGens();
	let before = document.createElement("button");
	before.insertAdjacentHTML('beforeend',"Before");
	before.id="before2";
	before.style.margin = "0.3% 0 0.25% 0";
	before.addEventListener("click",oneGuyBeforeMessages);
	bloc.appendChild(before);
}

/* output valide de sendProfileMessages */
function addProfileMessages(messages) {
  document.getElementById("before2").parentNode.removeChild(document.getElementById("before2"));
  displayProfileMessages(messages);
}

/* output valide de sendSetProfile */
function changePseudo(change) {
  document.getElementById("setPseudo").value = "";
  document.getElementById("setPassword").value = "";
  document.getElementById("setDescription").value = "";
  let pseudo = document.getElementById("pseudo");
  pseudo.innerHTML = "";
  pseudo.insertAdjacentHTML("beforeend","<b>"+change['pseudo']+"</b>");
  window.alert("Profil modifié");
}

/* output valide de sendResearch */
function displayResearch(users) {
  let bloc = document.getElementById("listeRecherche");
  bloc.innerHTML = "";
  for(i=0;i!=users.length;i++) {
    let user = document.createElement("div");
    let img = document.createElement("img");
    img.src = "services/getAvatar.php?userId="+users[i]['userId'];
    img.style.borderRadius = "20px";
    user.appendChild(img);
    user.appendChild(document.createElement("br"));
    user.style.color = "black";
    user.style.backgroundColor = "white";
    user.id = users[i]['userId'];
    user.insertAdjacentHTML('beforeend',"<span><b>"+users[i]['pseudo']+"</b> <em>@"+users[i]['userId']+"</em>");
    user.addEventListener("click",profileNotHidden2);
    bloc.appendChild(user);
  }
}

/* output erreur de sendResearch */
function errorResearch(error) {
  document.getElementById("listeRecherche").innerHTML = "";
}

/* output valide de sendListeAbonnee */
function displayListeAbonnee(abonnes) {
  let bloc = document.getElementById("listeAbonne");
  bloc.innerHTML = "";
  for(i=0;i!=abonnes.length;i++) {
    let user = document.createElement("div");
    let img = document.createElement("img");
    img.src = "services/getAvatar.php?userId="+abonnes[i]['userId'];
    img.style.borderRadius = "20px";
    user.appendChild(img);
    user.appendChild(document.createElement("br"));
    user.style.color = "black";
    user.style.backgroundColor = "white";
    user.id = abonnes[i]['userId'];
    let span = document.createElement("span");
    let b = document.createElement("b");
    span.appendChild(b);
    b.insertAdjacentHTML('beforeend',abonnes[i]['pseudo']);
    user.appendChild(span);
    span.id= "span"+abonnes[i]['userId'];
    span.addEventListener("click",profileNotHidden3);
    img.id = "img_"+abonnes[i]['userId'];
    img.addEventListener("click",profileNotHidden3);
    user.appendChild(document.createElement("br"));
    let button = document.createElement("button");
    button.id="button_"+abonnes[i]['userId'];
    button.style.margin= "3px 0 5px 0";
    if(abonnes[i]['mutual']) {
      button.innerText = "Unfollow";
      button.addEventListener("click",unfollow2);
    }
    else{
      button.innerText = "Follow";
      button.addEventListener("click",follow2);
    }
    user.appendChild(button);
    bloc.appendChild(user);
  }
}

/* output erreur de sendListeAbonnee */
function errorListe1(error) {
  document.getElementById("listeAbonne").innerHTML = "";
}

/* output valide de sendListeAbonnement */
function displayListeAbonnement(abonnes) {
  let bloc = document.getElementById("listeAbonnement");
  bloc.innerHTML = "";
  for(i=0;i!=abonnes.length;i++) {
    let user = document.createElement("div");
    let img = document.createElement("img");
    img.src = "services/getAvatar.php?userId="+abonnes[i]['userId'];
    img.style.borderRadius = "20px";
    user.appendChild(img);
    user.appendChild(document.createElement("br"));
    user.style.color = "black";
    user.style.backgroundColor = "white";
    user.id = abonnes[i]['userId'];
    let span = document.createElement("span");
    let b = document.createElement("b");
    span.appendChild(b);
    b.insertAdjacentHTML('beforeend',abonnes[i]['pseudo']);
    user.appendChild(span);
    span.id= "Span"+abonnes[i]['userId'];
    span.addEventListener("click",profileNotHidden3);
    img.id = "Img_"+abonnes[i]['userId'];
    img.addEventListener("click",profileNotHidden3);
    user.appendChild(document.createElement("br"));
    let button = document.createElement("button");
    button.style.margin= "3px 0 5px 0";
    button.id="Button_"+abonnes[i]['userId'];
    button.innerText = "Unfollow";
    button.addEventListener("click",unfollow2);
    user.appendChild(button);
    bloc.appendChild(user);
  }
}

/* output erreur de sendListeAbonnement */
function errorListe2(error) {
  document.getElementById("listeAbonnement").innerHTML = "";
}

/* output valide de follow2 et unfollow2 */
function switch3() {
  cleanMessages();
	displayPostMessage();
	filtreMessages();
  sendListeAbonnee();
  sendListeAbonnement();
}
