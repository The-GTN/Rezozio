<?php // Nollet Antoine Groupe 3
class Identite {
  public $login;
  public $pseudo;
  public function __construct($login,$pseudo)
  {
    $this->login = $login;
    $this->pseudo = $pseudo;
  }
}
?>
