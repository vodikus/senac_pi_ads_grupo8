<?php
require_once "models/LivroModel.php";
require_once "models/AssuntoModel.php";

class LivroAssuntoModel extends BaseModel
{
    public $campos = array(
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true]
    );

    public function adicionarLivroAssunto($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            (new LivroModel())->validaLivro($dados['lid']);
            (new AssuntoModel())->validaAssunto($dados['iid']);

            return $this->query(
                "INSERT INTO livros_assuntos (lid, iid) VALUES " .
                " (:lid, :iid)",
                $dados
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deletarLivroAssunto($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');
            (new LivroModel())->validaLivro($dados['lid']);
            (new AssuntoModel())->validaAssunto($dados['iid']);

            return $this->query("DELETE FROM livros_assuntos WHERE lid=:lid AND iid=:iid", ['lid' => $dados['lid'], 'iid' => $dados['iid']]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

}