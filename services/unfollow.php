<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineNonEmptyString('target');


if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

if(!isset($_SESSION['ident'])) {
  produceError("Non connecté");
  return;
}

try{
  $data = new DataLayer();
  $action = $data->unfollow($_SESSION['ident']->login,$args->target);
  if(!$action) {
    produceError("utilisateur inexistant ou utilisateur non suivi");
    return;
  }
  produceResult(true);
  return;
  }
catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
