<?php
require_once "includes/BaseModel.php";
require_once "models/LivroModel.php";

class LivroAvaliacaoModel extends BaseModel
{
    public $campos = array (
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'nota' => ['protected' => 'none', 'type' => 'float', 'visible' => true, 'required' => true]
    );

    public function adicionarLivroAvaliacao($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            (new LivroModel())->validaLivro($dados['lid']);

            return $this->query("INSERT INTO livros_avaliacoes (lid, uid, nota) VALUES (:lid, :uid, :nota)", array_merge(['uid' => $uid],$dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        throw New CLException('ERR_LIVRO_JA_AVALIADO');
                    }
                    break;
            }            
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}