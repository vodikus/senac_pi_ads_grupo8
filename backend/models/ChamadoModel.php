<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";

class ChamadoModel extends BaseModel
{
    public $campos = array(
        'cid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'uid_origem' => ['protected' => 'update', 'type' => 'int', 'visible' => true],
        'uid_destino' => ['protected' => 'update', 'type' => 'int', 'visible' => true, 'required' => true],
        'lid' => ['protected' => 'update', 'type' => 'int', 'visible' => true, 'required' => true],
        'tipo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'assunto' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'motivo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'texto' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_inclusao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true]
    );

    public function adicionarChamado($uid, $entrada)
    {
        try {
            $campos = array_filter($this->campos, ['SQLHelper', 'limpaCamposProtegidos']);

            $dados = SQLHelper::validaCampos($campos, $entrada, 'INSERT');

            (new UsuarioModel())->validaUsuario($uid);

            return $this->insert(
                "INSERT INTO chamados (uid_origem, uid_destino, lid, tipo, assunto, motivo, texto) VALUES " .
                " (:uid_origem, :uid_destino, :lid, :tipo, :assunto, :motivo, :texto)",
                array_merge(['uid_origem' => $uid], $dados)
            );

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function buscarChamados($filtro=[], $admin=false)
    {
        try {
            
            $dados = [];
            $sql = "WHERE 1=1 ";
            foreach ($filtro as $key => $value) {
                switch ($key) {
                    case 'uid_origem':                        
                        (new UsuarioModel())->validaUsuario($value);
                        $sql .= " AND uid_origem=:uid_origem";
                        $dados['uid_origem'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'uid_destino':                        
                        (new UsuarioModel())->validaUsuario($value);
                        $sql .= " AND uid_destino=:uid_destino";
                        $dados['uid_destino'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'status':
                        $sql .= " AND status=:status";
                        $dados['status'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'tipo':
                        $sql .= " AND tipo=:tipo";
                        $dados['tipo'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                }
            }
            $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
            $assuntos = $this->select("SELECT $campos FROM chamados a $sql", $dados);
            return $assuntos;
        } catch (Exception $e) {
            throw $e;
        }
    }

}