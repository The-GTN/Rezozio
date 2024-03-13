<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');


$args = new RequestParameters();
$args->defineNonEmptyString('userId');
$values = ['large','small'];
$args->defineEnum('size',$values,['default'=>'small']);

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $descFile = $data->getAvatar($args->userId,$args->size);
  if ($descFile){
    if($args->size == 'small') $flux = is_null($descFile['data']) ? fopen('../images/avatar_def_48.png','r') : $descFile['data'];
    else $flux = is_null($descFile['data']) ? fopen('../images/avatar_def_256.png','r') : $descFile['data'];
    $mimeType = is_null($descFile['data']) ? 'image/png' : $descFile['mimetype'];

    header("Content-type: $mimeType");
    fpassthru($flux);
    exit();
  }
  else
    produceError('Utilisateur inexistant');
}
catch (PDOException $e){
  produceError($e->getMessage());
}

?>
