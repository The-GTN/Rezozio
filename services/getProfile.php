<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineNonEmptyString('userId');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

if ( isset($_SESSION['ident'])) {
  try{
    $data = new DataLayer();
    $action = $data->getProfile($args->userId);
    $isFollower = $data->isFollower($_SESSION['ident']->login,$args->userId);
    $followed = $data->followed($_SESSION['ident']->login,$args->userId);
    if(!$action) {
      produceError("Cet Utilisateur n'existe pas");
      return;
    }
    else {
      $res = ['userId'=>$action['login'],'pseudo'=>$action['pseudo'],'description'=>$action['description'],'isFollower'=>$isFollower,'followed'=>$followed];
      produceResult($res);
      return;
    }
    }
  catch (PDOException $e){
    produceError($e->getMessage());
    return;
  }
}
else {
  try{
    $data = new DataLayer();
    $action = $data->getProfile($args->userId);
    if(!$action) {
      produceError("Cet Utilisateur n'existe pas");
      return;
    }
    else {
      $res = ['userId'=>$action['login'],'pseudo'=>$action['pseudo'],'description'=>$action['description']];
      produceResult($res);
      return;
    }
    }
  catch (PDOException $e){
    produceError($e->getMessage());
    return;
  }
}
