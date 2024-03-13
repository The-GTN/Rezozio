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
  $action = $data->getFollowers($_SESSION['ident']->login);
  if(!$action) {
    produceError("erreur systeme : getFollowers");
    return;
  }
  $res = array();
  foreach ($action as $value) {
    $mutual = $data->followed($_SESSION['ident']->login,$value['follower']);
    $user = $data->getUser($value['follower']);
    $subres = array(['userId'=>$value['follower'],'pseudo'=>$user['pseudo'],'mutual'=>$mutual]);
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
