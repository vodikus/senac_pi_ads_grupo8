<?php
Class Connection {
  private  $server = "mysql:host=localhost;dbname=clube_livros";
  private  $user = "clube_livros";
  private  $pass = "senha";
  private $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
  protected $con;
  
  public function openConnection() {
    try {
      $this->con = new PDO($this->server, $this->user,$this->pass,$this->options);
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