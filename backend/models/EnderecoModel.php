<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class EnderecoModel extends BaseModel
{
    private $campos = array (
        'eid' => ['protected' => 'all', 'type' => 'int'],
        'uid' => ['protected' => 'all', 'type' => 'int'],
        'cep' => ['protected' => 'none', 'type' => 'int'],
        'logradouro' => ['protected' => 'none', 'type' => 'varchar'],
        'numero' => ['protected' => 'none', 'type' => 'varchar'],
        'complemento' => ['protected' => 'none', 'type' => 'varchar'],
        'bairro' => ['protected' => 'none', 'type' => 'varchar'],
        'cidade' => ['protected' => 'none', 'type'=>'varchar'],
        'uf' => ['protected' => 'none', 'type' => 'varchar'],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always']
    );

    public function buscarEndereco($id = 0)
    {
        return $this->select("SELECT * FROM enderecos WHERE uid=:uid", ['uid'=>$id]);
    }
    public function deletarEndereco($eid = 0, $uid = 0)
    {
        return $this->query("DELETE enderecos WHERE eid=:eid and uid=:uid", ['eid' => $eid, 'uid' => $uid]);
    }
    public function adicionarEndereco($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO enderecos (uid, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES " .
                                " (:uid, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)", $dados
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
            return $this->query("UPDATE enderecos SET $campos WHERE uid=:uid", array_merge(['uid'=>$uid], $dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}