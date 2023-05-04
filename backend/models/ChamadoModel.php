<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";
require_once "models/ChamadoDetalheModel.php";

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

    public function buscarChamados($filtro = [], $admin = false)
    {
        try {

            $dados = [];
            $sql = "WHERE 1=1 ";
            $detalhe = FALSE;
            foreach ($filtro as $key => $value) {
                switch ($key) {
                    case 'uid_origem':
                        (new UsuarioModel())->validaUsuario($value);
                        $sql .= " AND c.uid_origem=:uid_origem";
                        $dados['uid_origem'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'uid_destino':
                        (new UsuarioModel())->validaUsuario($value);
                        $sql .= " AND c.uid_destino=:uid_destino";
                        $dados['uid_destino'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'status':
                        $sql .= " AND c.status=:status";
                        $dados['status'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'tipo':
                        $sql .= " AND c.tipo=:tipo";
                        $dados['tipo'] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'detalhe':
                        $detalhe = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                }
            }
            $campos = SQLHelper::montaCamposSelect($this->campos, 'c');
            $chamados = $this->select("SELECT $campos, '' as detalhes FROM chamados c $sql", $dados);
            if ($detalhe) {
                error_log(var_export($chamados, true));
                foreach ($chamados as $chave => $chamado) {
                    error_log("ID $chave: " . var_export($chamado, true));
                    $detalhes = (new ChamadoDetalheModel())->listarDetalhes($chamado["cid"]);
                    error_log("Detalhes: " .var_export($detalhes, true));
                    $chamados[$chave] = array_merge($chamado, ['detalhes' => $detalhes]);
                    error_log("Novo chamado: " .var_export($chamados[$chave], true));
                }
            }
            return $chamados;
        } catch (Exception $e) {
            throw $e;
        }
    }

}