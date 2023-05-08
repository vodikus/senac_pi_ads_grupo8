<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";

class ChatModel extends BaseModel
{
    public $campos = array(
        'mid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'uid' => ['protected' => 'update', 'type' => 'int', 'visible' => true],
        'uid_amigo' => ['protected' => 'update', 'type' => 'int', 'visible' => true],
        'mensagem' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_criacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true]
    );

    public function adicionarMensagem($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');

            if ($dados['uid'] == $dados['uid_amigo']) {
                throw new CLConstException('ERR_CHAT_ENVIO', "uid: {$dados['uid']} uid_amigo: {$dados['uid_amigo']}");
            }

            (new UsuarioModel())->validaBloqueio($dados['uid'], $dados['uid_amigo']);

            return $this->insert(
                "INSERT INTO chat (uid, uid_amigo, mensagem) VALUES " .
                " (:uid, :uid_amigo, :mensagem)",
                $dados
            );

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarMensagens($uid, $uid_amigo)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'c');
            $mensagens = $this->select("SELECT $campos, u.nome, u.apelido, u.avatar FROM chat c " . 
            "INNER JOIN usuarios u ON c.uid_amigo = u.uid " . 
            "WHERE (c.uid=:uid AND c.uid_amigo=:uid_amigo) OR (c.uid=:uid_amigo AND c.uid_amigo=:uid) " .
            "ORDER BY c.dh_criacao ASC", ['uid' => $uid, 'uid_amigo' => $uid_amigo]);
            return $mensagens;
        } catch (Exception $e) {
            throw $e;
        }
    }

}