<?php
include_once 'includes/Connection.php';
class BaseModel
{
    private $database;
    private $db;

    function __construct() {
        try {
            $this->database = new Connection();
            $this->db = $this->database->openConnection();
        } catch (PDOException $e) {
            echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        }
    }

    function __destruct() {
        try {
            $this->database->closeConnection();
        } catch (PDOException $e) {
            echo "Erro ao desconectar do banco de dados: " . $e->getMessage();
        }
    }

    function select($query = "", $parametros = []) {
        try {
            $sth = $this->db->prepare($query, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $sth->execute($parametros);
            return $sth->fetchAll();
        } catch (Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    function query($query = "", $parametros = []) {
        try {
            $sth = $this->db->prepare($query);
            $sth->execute($parametros);
            return $sth->rowCount();
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode());
        }
    }

    function pegarConexao() {
        return $this->db;
    }

}