<?php // Nollet Antoine Groupe 3
require_once("lib/db_parms.php");

Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );
            $this->connexion->query("SET search_path = public,rezozio");


    }

    public function getUser($userId) {
      $sql = <<<EOD
      select login,pseudo from users where login=:login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$userId);
      try{
        $stmt->execute();
        return $stmt->fetch();
      }
      catch(PDOException $e){
        return false;
      }
    }


    public function getProfile($userId) {
      $sql = <<<EOD
      select login,pseudo,description from users where login=:login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$userId);
      try{
        $stmt->execute();
        return $stmt->fetch();
      }
      catch(PDOException $e){
        return false;
      }
    }


    public function getMessage($messageId) {
      $sql = <<<EOD
      select * from messages where id = :id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id',$messageId);
      try{
        $stmt->execute();
        return $stmt->fetch();
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function getAvatar($login,$size){
      if($size=='small') {
        $sql = <<<EOD
        select avatar_type, avatar_small
        from users
        where login=:login
EOD;
      }
      else {
        $sql = <<<EOD
        select avatar_type, avatar_large
        from users
        where login=:login
EOD;
      }
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->bindColumn('avatar_type', $mimeType);
      if($size=='small') $stmt->bindColumn('avatar_small', $flow, PDO::PARAM_LOB);
      else if($size=='large') $stmt->bindColumn('avatar_large', $flow, PDO::PARAM_LOB);
      $stmt->execute();
      $res = $stmt->fetch();
      if ($res)
      return ['mimetype'=>$mimeType,'data'=>$flow];
      else
      return false;
    }


    public function createUser($login,$pseudo,$password) {
      $sql = <<<EOD
      INSERT INTO users (login, pseudo, password) VALUES ( :login , :pseudo , :password)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$login);
      $stmt->bindValue(':password', password_hash($password,CRYPT_BLOWFISH));
      $stmt->bindValue(':pseudo',$pseudo);
      try{
        $stmt->execute();
        $this->follow($login,$login);
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function findUsers($searchedString) {
      if (strtolower($searchedString) == "all" || strtolower($searchedString) == "tous" ) {
        $sql = <<<EOD
        select login,pseudo from users order by pseudo
EOD;
        $stmt = $this->connexion->prepare($sql);
      }
      else {
        $sql = <<<EOD
        select login,pseudo from users where lower(login) like lower(:search) or lower(pseudo) like lower(:search) order by pseudo
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':search','%'.$searchedString.'%');
      }
      try{
        $stmt->execute();
        return $stmt->fetchAll();
      }
      catch(PDOException $e){
        return false;
      }
    }

   public function authentifier($login, $password){ // version password hash
        $sql = <<<EOD
        select
        login, pseudo, password
        from users
        where login = :login
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        $info = $stmt->fetch();
        if ($info && crypt($password, $info['password']) == $info['password'])
              return new Identite($info['login'], $info['pseudo']);
        else
          return NULL;
    }

    public function findMessages($author,$before,$count){
      if($author == '') {
        if($before == 0) {
          $sql = <<<EOD
          select * from messages order by id desc limit :count
EOD;
        }
        else {
          $sql = <<<EOD
          select * from messages where id < :before order by id desc limit :count
EOD;
        }
      }
      else {
        if($before == 0) {
          $sql = <<<EOD
          select * from messages where author = :author order by id desc limit :count
EOD;
        }
        else {
          $sql = <<<EOD
          select * from messages where author = :author and id < :before order by id desc limit :count
EOD;
        }
      }
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':count',$count);
      if($author!='') $stmt->bindValue(':author',$author);
      if($before!=0) $stmt->bindValue(':before',$before);
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function findFollowedMessages($connecte,$before,$count) {
      if($before==0) {
        $sql = <<<EOD
        select * from messages join subscriptions on follower=:connecte and target=author order by id desc limit :count
EOD;
      }
      else {
        $sql = <<<EOD
        select * from messages join subscriptions on follower=:connecte and target=author where id < :before order by id desc limit :count
EOD;
      }
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':count',$count);
      $stmt->bindValue(':connecte',$connecte);
      if($before!=0) $stmt->bindValue(':before',$before);
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function postMessage($connecte,$source) {
      $sql = <<<EOD
      insert into messages (author,content) values (:connecte,:source)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':connecte',$connecte);
      $stmt->bindValue(':source',$source);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function getLastMessageId() {
      $sql = <<<EOD
      select * from messages order by id desc limit 1
EOD;
      $stmt = $this->connexion->prepare($sql);
      try{
        $stmt->execute();
        $res = $stmt->fetch();
        return $res['id'];
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function setPassword($connecte,$password) {
      $sql = <<<EOD
      update users set password = :password where login = :connecte
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':password', password_hash($password,CRYPT_BLOWFISH));
      $stmt->bindValue(':connecte', $connecte);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function setPseudo($connecte,$pseudo) {
      $sql = <<<EOD
      update users set pseudo = :pseudo where login = :connecte
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':pseudo', $pseudo);
      $stmt->bindValue(':connecte', $connecte);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function setDescription($connecte,$description) {
      $sql = <<<EOD
      update users set description = :description where login = :connecte
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':description', $description);
      $stmt->bindValue(':connecte', $connecte);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function storeAvatar($connecte,$type,$small,$large) {
      $sql = <<<EOD
      update users set (avatar_type, avatar_small, avatar_large) = (:type, :small, :large) WHERE login = :connecte
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':connecte',$connecte);
      $stmt->bindValue(':type',$type);
      $stmt->bindValue(':small',$small, PDO::PARAM_LOB );
      $stmt->bindValue(':large',$large, PDO::PARAM_LOB );
      try{
        $stmt->execute();
        return $stmt->rowCount() ==1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function follow($connecte,$target) {
      $sql = <<<EOD
      insert into subscriptions (follower,target) values (:connecte,:target)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':target', $target);
      $stmt->bindValue(':connecte', $connecte);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function unfollow($connecte,$target) {
      $sql = <<<EOD
      delete from subscriptions where follower = :connecte and target = :target
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':target', $target);
      $stmt->bindValue(':connecte', $connecte);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }


    public function isFollower($connecte,$autre) {
      $sql = <<<EOD
      select * from subscriptions where follower=:autre and target=:connecte
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':connecte',$connecte);
      $stmt->bindValue(':autre',$autre);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function followed($connecte,$autre) {
      $sql = <<<EOD
      select * from subscriptions where follower=:connecte and target=:autre
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':connecte',$connecte);
      $stmt->bindValue(':autre',$autre);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      }
      catch(PDOException $e){
        return false;
      }
    }


    public function getFollowers($userId) {
      $sql = <<<EOD
      select * from subscriptions join users on login=follower where target=:userid and follower!=:userid order by pseudo
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userid',$userId);
      try{
        $stmt->execute();
        return $stmt->fetchAll();
      }
      catch(PDOException $e){
        return false;
      }
    }

    public function getSubscriptions($userId) {
      $sql = <<<EOD
      select * from subscriptions join users on login=target where follower=:userid and target!=:userid order by pseudo
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userid',$userId);
      try{
        $stmt->execute();
        return $stmt->fetchAll();
      }
      catch(PDOException $e) {
        return false;
      }
    }


}
?>
