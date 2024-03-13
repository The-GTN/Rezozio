<?php // Nollet Antoine Groupe 3
    set_include_path('..'.PATH_SEPARATOR);
    require('lib/watchdog_service.php');
    $login = $_SESSION['ident']->login;
    session_destroy();
    produceResult($login);

?>
