<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";
require_once "models/ChamadoModel.php";

class ChamadoDetalheModel extends BaseModel
{
    public $campos = array(
        'chid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'cid' => ['protected' => 'update', 'type' => 'int', 'visible' => false],
        'uid' => ['protected' => 'update', 'type' => 'int', 'visible' => true, 'required' => true],
        'mensagem' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'tipo' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function adicionarDetalhe($entrada, $tipo="MENSAGEM")
    {
        try {
            $campos = array_filter($this->campos, ['SQLHelper', 'limpaCamposProtegidos']);
            $entrada['tipo'] = $tipo;
            $dados = SQLHelper::validaCampos($campos, $entrada, 'INSERT');


            (new ChamadoModel())->validaChamado($dados['cid']);
            (new UsuarioModel())->validaUsuario($dados['uid']);

            $this->query("UPDATE chamados SET dh_atualizacao=CURRENT_TIMESTAMP WHERE cid=:cid", ['cid' => $dados['cid']]);
            return $this->insert(
                "INSERT INTO chamados_detalhe (cid, uid, mensagem, tipo) VALUES " .
                " (:cid, :uid, :mensagem, :tipo)",
                $dados
            );

        } catch (Exception $e) {   
            throw $e;
        }
    }

    public function listarDetalhes($cid=0)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'cd');
            $detalhes = $this->select("SELECT $campos FROM chamados_detalhe cd WHERE cid=:cid", ['cid' => $cid]);
            return $detalhes;
        } catch (Exception $e) {
            throw $e;
        }
    }

}