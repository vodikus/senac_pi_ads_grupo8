<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class LivroAssuntoModel extends BaseModel
{
    public $campos = array (
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true]
    );

    public function adicionarLivroAssunto($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( 
                $this->query("SELECT 1 FROM livros WHERE lid=:lid", ['lid' => $dados['lid'] ]) <= 0 || 
                $this->query("SELECT 1 FROM assuntos WHERE iid=:iid",  ['iid' => $dados['iid'] ]) <= 0  ) {
                    throw New Exception( "Assunto ou Livro não encontrado");
            }

            return $this->query("INSERT INTO livros_assuntos (lid, iid) VALUES " .
            " (:lid, :iid)",
            $dados
        );
    } catch (Exception $e) {
        throw New Exception( $e->getMessage(), $e->getCode() );
    }
}

public function deletarLivroAssunto($entrada)
{
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM livros_assuntos WHERE lid=:lid AND iid=:iid", ['lid' => $entrada['lid'], 'iid' => $entrada['iid']]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}