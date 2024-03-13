<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


if ( ! isset($_SESSION['ident'])) {
  $args = new RequestParameters("post");
  $args->defineNonEmptyString('login');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  }

  try{
    $data = new DataLayer();
    $_SESSION['ident'] = $data->authentifier($args->login,$args->password);
    if($_SESSION['ident'] !== NULL) {
      produceResult($args->login);
      return;
    }
    else {
      produceError("Login et/ou password invalide");
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
