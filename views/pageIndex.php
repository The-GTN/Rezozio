<?php // Nollet Antoine Groupe 3

  $dataPersonne ="";
  if (isset($personne))
     $dataPersonne = 'data-personne="'.htmlentities(json_encode($personne)).'"';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
 <meta charset="UTF-8" />
 <title>Rezozio</title>
 <link rel="stylesheet" href="style/util.css" />
 <link rel="stylesheet" href="style/form.css" />
 <link rel="stylesheet" href="style/log.css" />
 <link rel="stylesheet" href="style/search.css" />
 <link rel="stylesheet" href="style/messages.css" />
 <link rel="stylesheet" href="style/profil.css" />
 <link rel="stylesheet" href="style/subscriptions.css" />
 <link rel="icon" href="images/logo.png" />
 <script src="js/actions.js"></script>
 <script src="js/fetchUtils.js"></script>
 <script src="js/process.js"></script>
 <script src="js/setLoad.php"></script>
 <script src="js/setSendRequest.js"></script>
</head>
<?php
  echo "<body $dataPersonne>";
?>
  <h1>
    <img src="images/logo.png" alt="logo" id="logo" />
    <img id="avatar2" class="connecte" alt="mon avatar" src="images/avatar_def_48.png" />
  </h1>

  <form method="POST" action="services/findUsers.php" id="form_recherche">
    <fieldset id="fsearch">
      <input type="text" name="searchedString" placeholder="Rechercher un Utilisateur" id="searchedString" />
      <button type="submit" name="valid">
        <img src="images/recherche.png" alt="logo_recherche" id="logo_recherche" />
      </button>
      <output  for="searchedString" name="message"></output>
    </fieldset>
  </form>

<section id="espace_variable">
 <section class="deconnecte">
   <form method="POST" action="services/login.php"  id="form_login">
    <fieldset>
     <legend>Connexion</legend>
     <input type="text" name="login" id="login" required="" autofocus="" placeholder="Login"/><br /><br />
     <input type="password" name="password" id="password" required="required" placeholder="Mot de Passe" /><br /><br />
     <button type="submit" name="valid">OK</button><br />
     <output  for="login password" name="message"></output>
    </fieldset>
   </form>
   <br />
   <button id="gotoinscription">S'inscrire</button>
 </section>

 <section class="connecte">
  <h2 id="pseudo"></h2>
  <h2 id="at"></h2>

  <span id=switch> <b>Filtre Abonnements : </b></span>
  <label class="switch">
    <input id=filtre type="checkbox" checked="checked">
    <span class="slider round"></span>
  </label>

  <div id="titreAbonnement"><b>Abonnements</b></div>
  <div id="titreAbonnee"><b>Abonnées</b></div>
  <section id="listeAbonnement" ></section>
  <section id="listeAbonne" ></section>

 </section>

 <section id="inscription">
   <form method="POST" action="services/createUser.php"  id="form_createUser">
    <fieldset>
     <legend>Inscription</legend>
     <input type="text" name="userId" id="login2" pattern="\w{3,25}" required="" placeholder="Login" minlength="3"/><br /><br />
     <input type="text" name="pseudo" id="pseudo2" required="" placeholder="Pseudo" minlength="3"/><br /><br />
     <input type="password" name="password" id="password2" required="required" placeholder="Mot de Passe"/><br /><br />
     <button type="submit" name="valid">OK</button><br />
     <output  for="login password" name="message"></output>
    </fieldset>
   </form>
   <br />
   <button id="gotoconnection">Se connecter</button>
 </section>
</section>

<section id="messages"></section>

<section id="recherche">
  <section id="listeRecherche"></section>
  <button id="logout" class="connecte">Déconnexion</button>
</section>

<section id="profile">
  <div id="personalProfile">
    <button class="closeProfile">X</button>
    <img alt="option" src="images/option.png" id="option" />
    <img id="avatar" alt="avatar" src="images/avatar_def_256.png" />
    <br />
    <span id="profilePseudo"></span>
    <p id="profileDescription"></p>
    <button id="followOrNot"></button>
    <section id=profileMessages></section>
  </div>
  <div id="setProfile">
    <button class="closeProfile">X</button>
    <button id="returnButton">&lt;=</button>
    <form method = "POST" action="services/uploadAvatar.php" name="upload_image" enctype="multipart/form-data" id="form_img">
     <fieldset>
        <legend>Nouvel avatar</legend>
        <input type="file" name="image" required="required"/>
        <button type="submit" name="valid">Envoyer</button>
      </fieldset>
    </form>
    <br />
    <br />
    <form method="POST" action="services/setProfile.php" id="form_setProfile">
      <fieldset>
        <legend>Changer le Profil</legend>
        <br />
        <input type="text" name="pseudo" placeholder="Nouveau Pseudo ?" maxlength="25" id="setPseudo" /> <br /> <br />
        <input type="password" name="password" placeholder="Nouveau Mot de Passe ?" id="setPassword" /> <br /> <br />
        <textarea name="description" maxlength="1024" rows="7" cols="50" id="setDescription" placeholder="Nouvelle description ?"></textarea>
        <br /> <br />
        <button type="submit" name="valid">Envoyer</button>
        <output  for="text password description" name="message"></output>
      </fieldset>
    </form>
  </div>
</section>

<a id="credits" target="_blank" href="http://webtp.fil.univ-lille1.fr/~nollet/Projet_Rezozio/credits.html">C</a>

</body>
</html>
