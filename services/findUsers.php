<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineNonEmptyString('searchedString');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}

if (strlen($args->searchedString) < 3) {
  produceError("Trop petit pour permettre la recherche. Ne respecte pas la consigne.");
  return;
}

try{
  $data = new DataLayer();
  $action = $data->findUsers($args->searchedString);
  $res = array();
  foreach ($action as $value) {
    $subres = array(['userId'=>$value['login'],'pseudo'=>$value['pseudo']]);
    $res = array_merge($res,$subres);
  }
  produceResult($res);
  return;
  }
catch (PDOException $e){
  produceError($e->getMessage());
  return;
}
?>
