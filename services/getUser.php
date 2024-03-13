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

try{
  $data = new DataLayer();
  $action = $data->getUser($args->userId);
  if(!$action) {
    produceError("Cet Utilisateur n'existe pas");
    return;
  }
  else {
    $res = ['userId'=>$action['login'],'pseudo'=>$action['pseudo']];
    produceResult($res);
    return;
  }
  }
catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
