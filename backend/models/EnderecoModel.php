<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class EnderecoModel extends BaseModel
{
    public $campos = array (
        'eid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'uid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'cep' => ['protected' => 'update', 'type' => 'int', 'visible' => true, 'required' => true],
        'logradouro' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'numero' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'complemento' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'bairro' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'cidade' => ['protected' => 'none', 'type'=>'varchar', 'visible' => true, 'required' => true],
        'uf' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function buscarEnderecos($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos,'e');
        return $this->select("SELECT $campos FROM enderecos e WHERE uid=:uid", ['uid'=>$id]);
    }

    public function deletarEndereco($eid = 0, $uid = 0)
    {
        return $this->query("DELETE FROM enderecos WHERE eid=:eid and uid=:uid", ['eid' => $eid, 'uid' => $uid]);
    }
    public function adicionarEndereco($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO enderecos (uid, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES " .
                                " (:uid, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)",
                                array_merge(['uid'=>$uid], $dados)
                            );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
    public function atualizarEndereco($eid, $uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE enderecos SET $campos WHERE eid=:eid and uid=:uid", array_merge(['eid' => $eid, 'uid'=>$uid], $dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}