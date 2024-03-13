<?php // Nollet Antoine Groupe 3
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


$args = new RequestParameters("get");
$args->defineString('light');

$cookie_name = "light";
$cookie_value = $args->light;
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

produceResult(['light'=>$args->light]);
return;

?>