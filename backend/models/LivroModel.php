<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";
require_once "models/AutorModel.php";

class LivroModel extends BaseModel
{
    public $campos = array (
        'lid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'titulo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'descricao' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'avaliacao' => ['protected' => 'update', 'type' => 'float', 'visible' => true],
        'capa' => ['protected' => 'none', 'type'=>'varchar', 'visible' => true],
        'isbn' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function listarLivros()
    {
        $autorModel = new AutorModel();
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');
        $campos_autor = SQLHelper::montaCamposSelect($autorModel->getCampos(), 'a');

        return $this->select("SELECT $campos, $campos_autor FROM livros l" .
        " LEFT JOIN livros_autores la ON la.lid = l.lid " .
        " LEFT JOIN autores a ON a.aid = la.aid "
        );
    }

    public function buscarLivro($id = 0)
    {
        $autorModel = new AutorModel();
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');
        $campos_autor = SQLHelper::montaCamposSelect($autorModel->getCampos(), 'a');
        return $this->select("SELECT $campos, $campos_autor FROM livros l" .
        " LEFT JOIN livros_autores la ON la.lid = l.lid " .
        " LEFT JOIN autores a ON a.aid = la.aid " .
        " WHERE l.lid=:lid", 
            [ 'lid' => $id ]
        );
    }

    public function adicionarLivro($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO livros (titulo, descricao, capa, isbn, iid) VALUES " .
                                " (:titulo, :descricao, :capa, :isbn, :iid)",
                                $dados
                            );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarLivro($lid = 0)
    {
        try {
            return $this->query("DELETE FROM livros WHERE lid=:lid", [ 'lid' => $lid ] );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function atualizarLivro($lid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE livros SET $campos WHERE lid=:lid", array_merge(['lid' => $lid], $dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}