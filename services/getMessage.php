<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineNonEmptyString('messageId');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

try{
  $data = new DataLayer();
  $action = $data->getMessage($args->messageId);
  if(!$action) {
    produceError("Il n'y a pas de message avec cet identifiant.");
    return;
  }
  else {

    try {
      $user = $data->getUser($action['author']);
      $pseudo = $user['pseudo'];
      $res = ['messageId'=>$action['id'],'author'=>$action['author'],'pseudo'=>$pseudo,'content'=>$action['content'],'datetime'=>$action['datetime']];
      produceResult($res);
      return;
    }
    catch (PDOException $e) {
      produceError($e->getMessage());
      return;
    }
  }
  }
catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
