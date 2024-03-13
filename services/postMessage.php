<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters("post");
$args->defineNonEmptyString('source');


if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

if(!isset($_SESSION['ident'])) {
  produceError("Non connecté");
  return;
}

if(strlen($args->source) > 280){
  produceError("Post trop long (plus de 280 caractères)");
  return;
}

try{
  $data = new DataLayer();
  $action = $data->postMessage($_SESSION['ident']->login,$args->source);
  if(!$action) {
    produceError("erreur systeme");
    return;
  }
  $res = $data->getLastMessageId();
  produceResult($res);
  return;
  }
catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
