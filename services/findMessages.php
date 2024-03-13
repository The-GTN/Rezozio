<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineString('author');
$args->defineInt('before',['default'=>0,'min_range'=>0]);
$args->defineInt('count',['default'=>15,'min_range'=>1]);


if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
}


try{
  $data = new DataLayer();
  $action = $data->findMessages($args->author,$args->before,$args->count);
  if(!$action) {
    produceError("utilisateur inexistant");
    return;
  }
  $res = array();
  foreach ($action as $value) {
    $user = $data->getUser($value['author']);
    $pseudo = $user['pseudo'];
    $subres = array(['messageId'=>$value['id'],'author'=>$value['author'],'pseudo'=>$pseudo,'content'=>$value['content'],'datetime'=>$value['datetime']]);
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
