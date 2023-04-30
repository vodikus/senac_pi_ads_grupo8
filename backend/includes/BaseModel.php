<?php
include_once 'includes/Connection.php';
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";
require_once "helpers/StringHelper.php";

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
            $this->errorLog($query,$parametros);
            return $sth->fetchAll();
        } catch (Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    function query($query = "", $params = []) {
        try {
            $this->errorLog($query,$params);
            $sth = $this->db->prepare($query);
            $parametros = $this->sanitizeParams($query,$params);
            error_log("Clean: ".var_export($parametros, true));                        

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

    function insert($query = "", $params = []) {
        try {
            $this->errorLog($query,$params);
            $sth = $this->db->prepare($query);
            $parametros = $this->sanitizeParams($query,$params);
            error_log("Clean: ".var_export($parametros, true));                        
            
            $stExec = $sth->execute($parametros);
            $sqlId = $this->db->lastInsertId();
            error_log("ReturnId: $sqlId");

            return $sqlId;
        } catch (Exception $e) {
            error_log("Erro: " . $e->getMessage());
            throw New Exception( $e->getMessage(), $e->getCode());
        }
    }

    function sanitizeParams($query, $params) {
        $arrParams = [];
        preg_match_all( '/(\:[a-zA-Z0-9-_]+)/', $query, $tokens );
        error_log("Tokens: ".var_export($tokens[0], true)); 
        $arrTokens = array_diff_key($tokens[0], $params);
        foreach ( $arrTokens as $token ) {
            $chave = str_replace(':','',$token);
            // error_log("Chave: $chave");
            $arrParams[$chave] = $params[$chave];
        }
        return $arrParams;
    }

    function pegarConexao() {
        return $this->db;
    }

    function errorLog($query, $param) {
        error_log("SQL: $query");
        error_log("Parametros: ".var_export($param, true));
    }

}