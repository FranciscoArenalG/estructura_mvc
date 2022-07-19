<?php
/**
 *
 */
class Database{
  private $host;
  public $db;
  private $user;
  private $password;
  private $charset;

  public function __construct(){
    $this->host = constant("HOST");
    $this->db = constant("DB");
    $this->user = constant("USER");
    $this->password = constant("PASSWORD");
    $this->charset = constant("CHARSET");
  }

  function connect(){
    try {
      $connection = "mysql:host=".$this->host.";dbname=".$this->db.";charset=".$this->charset;//Mysql
      /* $connection = "pgsql:host=".$this->host.";port=5432;dbname=".$this->db.";";//Postgres */
      $options = [
        PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];
      $pdo = new PDO($connection, $this->user, $this->password, $options);

      return $pdo;
    } catch (PDOException $e) {
      print_r("Error de conexión: " . $e->getMessage());
    }

  }
}

 ?>
