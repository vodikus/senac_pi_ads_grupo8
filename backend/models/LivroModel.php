<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class LivroModel extends BaseModel
{
    public $campos = array (
        'lid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'titulo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'descricao' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'avaliacao' => ['protected' => 'update', 'type' => 'float', 'visible' => true],
        'capa' => ['protected' => 'none', 'type'=>'varchar', 'visible' => true],
        'isbn' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function listarLivros()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select("SELECT $campos, " .
            " group_concat(DISTINCT a.nome_autor ORDER BY a.nome_autor SEPARATOR ', ') autores, " .
            " group_concat(DISTINCT i.nome_assunto ORDER BY i.nome_assunto SEPARATOR ', ') assuntos " .
            " FROM livros l" .
            " LEFT JOIN livros_autores la ON la.lid = l.lid " .
            " LEFT JOIN autores a ON a.aid = la.aid " .
            " LEFT JOIN livros_assuntos li ON li.lid = l.lid " .
            " LEFT JOIN assuntos i ON i.iid = li.iid "  .
            " GROUP BY $campos"
        );
    }

    public function buscarLivro($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select("SELECT $campos, " .
            " group_concat(DISTINCT a.nome_autor ORDER BY a.nome_autor SEPARATOR ', ') autores, " .
            " group_concat(DISTINCT i.nome_assunto ORDER BY i.nome_assunto SEPARATOR ', ') assuntos " .
            " FROM livros l" .
            " LEFT JOIN livros_autores la ON la.lid = l.lid " .
            " LEFT JOIN autores a ON a.aid = la.aid " .
            " LEFT JOIN livros_assuntos li ON li.lid = l.lid " .
            " LEFT JOIN assuntos i ON i.iid = li.iid " .
            " WHERE l.lid=:lid " .
            " GROUP BY $campos", 
                [ 'lid' => $id ]
        );
    }

    public function adicionarLivro($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO livros (titulo, descricao, capa, isbn) VALUES " .
                                " (:titulo, :descricao, :capa, :isbn)",
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