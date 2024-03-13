<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters("post");
$args->defineString('password');
$args->defineString('pseudo');
$args->defineString('description');


if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

if(!isset($_SESSION['ident'])) {
  produceError("Non connecté");
  return;
}

if(strlen($args->pseudo) > 25){
  produceError("pseudo trop long (plus de 25 caractères)");
  return;
}

if(strlen($args->description) > 1024){
  produceError("description trop longue (plus de 1024 caractères)");
  return;
}

try{
  $data = new DataLayer();

  if(strlen($args->password)!=0){
    $action = $data->setPassword($_SESSION['ident']->login,$args->password);
    if(!$action){
      produceError("erreur système : setPassword");
      return;
    }
  }

  if(strlen($args->pseudo)!=0){
    $action = $data->setPseudo($_SESSION['ident']->login,$args->pseudo);
    if(!$action){
      produceError("erreur système : setPseudo");
      return;
    }
  }

  if(strlen($args->description)!=0){
    $action = $data->setDescription($_SESSION['ident']->login,$args->description);
    if(!$action){
      produceError("erreur système : setDescription");
      return;
    }
  }

  $user = $data->getUser($_SESSION['ident']->login);
  if(!$user) {
    produceError("erreur système : getUser");
    return;
  }

  $res = ['userId'=>$user['login'],'pseudo'=>$user['pseudo']];
  produceResult($res);
  return;
  }

catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
