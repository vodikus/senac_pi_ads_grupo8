<?php
Class Connection {
  private $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
  protected $con;
  
  public function openConnection() {
    try {
      $dbHost = (getenv('DBHOST')) ? getenv('DBHOST') : 'localhost';
      $dbName = (getenv('DBNAME')) ? getenv('DBNAME') : 'clube_livros';
      $dbUser = (getenv('DBUSER')) ? getenv('DBUSER') : 'clube_livros';
      $dbPass = (getenv('DBPASS')) ? getenv('DBPASS') : 'senha';
      $server = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";

      $this->con = new PDO($server, $dbUser,$dbPass,$this->options);
      return $this->con;
    } catch (PDOException $e) {
      echo "Erro ao conectar com o banco de dados: " . $e->getMessage();
    }
  }

  public function closeConnection() {
    $this->con = null;
  }
}
?>