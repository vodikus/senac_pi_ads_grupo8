<?php
require_once "includes/BaseModel.php";
require_once "models/LivroModel.php";
require_once "models/AutorModel.php";

class LivroAutorModel extends BaseModel
{
    public $campos = array(
        'aid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true]
    );

    public function adicionarLivroAutor($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');

            (new LivroModel())->validaLivro($dados['lid']);
            (new AutorModel())->validaAutor($dados['aid']);

            return $this->query(
                "INSERT INTO livros_autores (lid, aid) VALUES " .
                " (:lid, :aid)",
                $dados
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        throw new CLException('ERR_AUTOR_VINCULO_EXISTE');
                    }
                    break;
            }
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deletarLivroAutor($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');

            (new LivroModel())->validaLivro($dados['lid']);
            (new AutorModel())->validaAutor($dados['aid']);

            return $this->query("DELETE FROM livros_autores WHERE lid=:lid AND aid=:aid", ['lid' => $dados['lid'], 'aid' => $dados['aid']]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

}