<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class AssuntoModel extends BaseModel
{
    public $campos = array (
        'iid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'nome_assunto' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function listarAssuntos()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
        return $this->select("SELECT $campos FROM assuntos a");
    }
    public function buscarAssunto($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
        return $this->select("SELECT $campos FROM assuntos a WHERE iid=:iid",  [ 'iid' => $id ] );
    }

    public function adicionarAssunto($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO assuntos (nome_assunto) VALUES " .
                                " (:nome_assunto)",
                                $dados
                            );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarAssunto($iid = 0)
    {
        try {
            return $this->query("DELETE FROM assuntos WHERE iid=:iid", ['iid' => $iid]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function atualizarAssunto($iid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE assuntos SET $campos WHERE iid=:iid", array_merge(['iid' => $iid], $dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}