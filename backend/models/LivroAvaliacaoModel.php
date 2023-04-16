<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class LivroAvaliacaoModel extends BaseModel
{
    public $campos = array (
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'nota' => ['protected' => 'none', 'type' => 'float', 'visible' => true]
    );

    public function adicionarLivroAvaliacao($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->query("SELECT 1 FROM livros WHERE lid=:lid", ['lid' => $dados['lid'] ]) <= 0 ) {
                    throw New Exception( "Livro não encontrado");
            }
            return $this->query("INSERT INTO livros_avaliacoes (lid, uid, nota) VALUES (:lid, :uid, :nota)", array_merge(['uid' => $uid],$dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
}