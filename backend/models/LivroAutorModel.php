<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";
require_once "models/AutorModel.php";

class LivroAutorModel extends BaseModel
{
    public $campos = array (
        'aid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true]
    );

    public function adicionarLivroAutor($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( 
                $this->query("SELECT 1 FROM livros WHERE lid=:lid", ['lid' => $dados['lid'] ]) <= 0 || 
                $this->query("SELECT 1 FROM autores WHERE aid=:aid",  ['aid' => $dados['aid'] ]) <= 0  ) {
                    throw New Exception( "Autor ou Livro não encontrado");
            }

            return $this->query("INSERT INTO livros_autores (lid, aid) VALUES " .
            " (:lid, :aid)",
            $dados
        );
    } catch (Exception $e) {
        throw New Exception( $e->getMessage(), $e->getCode() );
    }
}

public function deletarLivroAutor($entrada)
{
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM livros_autores WHERE lid=:lid AND aid=:aid", ['lid' => $entrada['lid'], 'aid' => $entrada['aid']]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}