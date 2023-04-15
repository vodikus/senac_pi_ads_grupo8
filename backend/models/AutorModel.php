<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";
require_once "models/AutorModel.php";

class AutorModel extends BaseModel
{
    public $campos = array (
        'aid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'nome_autor' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function listarAutores()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos);
        return $this->select("SELECT $campos FROM autores a");
    }
    public function buscarAutor($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos);
        return $this->select("SELECT $campos FROM autores a WHERE aid=:aid",  [ 'aid' => $id ] );
    }

    public function adicionarAutor($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO autores (nome_autor) VALUES " .
                                " (:nome_autor)",
                                $dados
                            );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarAutor($aid = 0)
    {
        try {
            return $this->query("DELETE FROM autores WHERE aid=:aid", ['aid' => $aid]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function atualizarAutor($aid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE autores SET $campos WHERE aid=:aid", array_merge(['aid' => $aid], $dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}