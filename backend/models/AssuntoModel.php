<?php
require_once "includes/BaseModel.php";

class AssuntoModel extends BaseModel
{
    public $campos = array(
        'iid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'nome_assunto' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function validaAssunto($id) {
        if ( $this->query("SELECT 1 FROM assuntos WHERE iid=:iid",  ['iid' => $id ]) <= 0  ) {
            throw new CLConstException('ERR_ASSUNTO_NAO_ENCONTRADO', "iid: $id");
        }
        return true;
    }

    public function listarAssuntos()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
        return $this->select("SELECT $campos FROM assuntos a");
    }
    public function buscarAssuntoPorId($id = 0)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
            $assunto = $this->select("SELECT $campos FROM assuntos a WHERE iid=:iid", ['iid' => $id]);
            if (count($assunto) > 0) {
                return $assunto[0];
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function buscarAssuntoPorNome($dados)
    {
        try {
            $nome_assunto = $dados['nome_assunto'];
            $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
            $assuntos = $this->select("SELECT $campos FROM assuntos a WHERE nome_assunto LIKE :nome_assunto", ['nome_assunto' => "%$nome_assunto%"]);
            if (count($assuntos) > 0) {
                return $assuntos;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function adicionarAssunto($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->insert(
                "INSERT INTO assuntos (nome_assunto) VALUES " .
                " (:nome_assunto)",
                $dados
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'nome_assunto_uk')) {
                        throw new CLConstException('ERR_ASSUNTO_JA_EXISTENTE');
                    }
                    break;
            }
            throw $e;
        }
    }

    public function deletarAssunto($iid = 0)
    {
        try {
            return $this->query("DELETE FROM assuntos WHERE iid=:iid", ['iid' => $iid]);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'fk_la_assuntos')) {
                        throw new CLConstException('ERR_ASSUNTO_DELETAR_FK');
                    }
                    break;
            }            
            throw $e;
        }
    }

    public function atualizarAssunto($iid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE assuntos SET $campos WHERE iid=:iid", array_merge(['iid' => $iid], $dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'nome_assunto_uk')) {
                        throw new CLConstException('ERR_ASSUNTO_JA_EXISTENTE');
                    }
                    break;
            }
            throw $e;
        }
    }

}