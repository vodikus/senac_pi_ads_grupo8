<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";

class ChamadoDetalheModel extends BaseModel
{
    public $campos = array(
        'chid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'cid' => ['protected' => 'update', 'type' => 'int', 'visible' => false],
        'uid' => ['protected' => 'update', 'type' => 'int', 'visible' => true, 'required' => true],
        'mensagem' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function adicionarDetalhe($entrada)
    {
        try {
            $campos = array_filter($this->campos, ['SQLHelper', 'limpaCamposProtegidos']);

            $dados = SQLHelper::validaCampos($campos, $entrada, 'INSERT');

            (new UsuarioModel())->validaUsuario($dados['uid']);

            return $this->insert(
                "INSERT INTO chamados_detalhe (cid, uid, mensagem) VALUES " .
                " (:cid, :uid, :mensagem)",
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