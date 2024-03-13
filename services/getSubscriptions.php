<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

if(!isset($_SESSION['ident'])) {
  produceError("Non connecté");
  return;
}

try{
  $data = new DataLayer();
  $action = $data->getSubscriptions($_SESSION['ident']->login);
  if(!$action) {
    produceError("erreur systeme : getSubscriptions");
    return;
  }
  $res = array();
  foreach ($action as $value) {
    $user = $data->getUser($value['target']);
    $subres = array(['userId'=>$value['target'],'pseudo'=>$user['pseudo']]);
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
