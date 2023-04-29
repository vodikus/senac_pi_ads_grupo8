<?php
require_once "includes/BaseModel.php";

class EnderecoModel extends BaseModel
{
    public $campos = array (
        'enid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
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
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos,'e');
            return $this->select("SELECT $campos FROM enderecos e WHERE uid=:uid", ['uid'=>$id]);
        } catch (Exception $e) {        
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarEndereco($enid = 0, $uid = 0)
    {
        try {
            return $this->query("DELETE FROM enderecos WHERE enid=:enid and uid=:uid", ['enid' => $enid, 'uid' => $uid]);
        } catch (Exception $e) {        
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
    public function adicionarEndereco($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->insert("INSERT INTO enderecos (uid, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES " .
                                " (:uid, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)",
                                array_merge(['uid'=>$uid], $dados)
                            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'usu_ende_cep_uk')) {
                        throw New CLException('ERR_ENDERECO_JA_EXISTENTE');
                    }
                    break;
            }            
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
    public function atualizarEndereco($enid, $uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE enderecos SET $campos WHERE enid=:enid and uid=:uid", array_merge(['enid' => $enid, 'uid'=>$uid], $dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'usu_ende_cep_uk')) {
                        throw New CLException('ERR_ENDERECO_JA_EXISTENTE');
                    }
                    break;
            }               
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}