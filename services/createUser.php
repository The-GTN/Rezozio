<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


if ( ! isset($_SESSION['ident'])) {
  $args = new RequestParameters("post");
  $args->defineNonEmptyString('userId');
  $args->defineNonEmptyString('pseudo');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  }

  if(strlen($args->userId) > 25){
    produceError("id trop long (plus de 25 caractères)");
    return;
  }

  if(strlen($args->userId) < 3) {
    produceError("id trop court (moins de 3 caractères)");
    return;
  }

  if(strlen($args->pseudo) > 25){
    produceError("pseudo trop long (plus de 25 caractères)");
    return;
  }

  try{
    $data = new DataLayer();
    $action = $data->createUser($args->userId,$args->pseudo,$args->password);
    if($action) {
      $res = ['userId'=>$args->userId,'pseudo'=>$args->pseudo];
      produceResult($res);
      return;
    }
    else {
      produceError("utilisateur déjà existant");
      return;
    }
    }
  catch (PDOException $e){
    produceError($e->getMessage());
    return;
  }

} else {
   produceError("déjà authentifié");
   return;
}
?>
