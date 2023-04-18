<?php
include_once 'includes/Connection.php';
class BaseModel
{
    private $database;
    private $db;

    public $campos = [];

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
            error_log("SQL: $query");
            error_log("Parametros: ".var_export($parametros, true));
            return $sth->fetchAll();
        } catch (Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    function query($query = "", $parametros = []) {
        try {
            error_log("SQL: $query");
            error_log("Parametros: ".var_export($parametros, true));            
            $sth = $this->db->prepare($query);
            $stExec = $sth->execute($parametros);
            $rowCount = $sth->rowCount();
            // error_log("Status Exec: $stExec");
            // error_log("Result: $rowCount");
            return $rowCount;
        } catch (Exception $e) {
            error_log("Erro: " . $e->getMessage());
            throw New Exception( $e->getMessage(), $e->getCode());
        }
    }

    function pegarConexao() {
        return $this->db;
    }

    function getCampos() {
        return $this->campos;
    }
}