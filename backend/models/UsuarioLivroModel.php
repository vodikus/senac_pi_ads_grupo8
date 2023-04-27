<?php
require_once "includes/BaseModel.php";
require_once "helpers/Constantes.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class UsuarioLivroModel extends BaseModel
{
    public $campos = array (
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_cadastro' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    private function validaUsuarioLivro($lid, $uid) {
        if ( $this->query("SELECT 1 FROM livros WHERE lid=:lid",  ['lid' => $lid ]) <= 0  ) {
                throw New Exception( helpers\Constantes::getMsg('ERR_LIVRO_NAO_ENCONTRADO'), helpers\Constantes::getCode('ERR_LIVRO_NAO_ENCONTRADO') );
        }
        if ( $this->query("SELECT 1 FROM usuarios WHERE uid=:uid",  ['uid' => $uid]) <= 0  ) {
            throw New Exception( helpers\Constantes::getMsg('ERR_USUARIO_NAO_ENCONTRADO'), helpers\Constantes::getCode('ERR_USUARIO_NAO_ENCONTRADO') );
        }
        return true;
    }

    public function adicionarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->validaUsuarioLivro($dados['lid'], $uid) ) {
                    $sqlSt = $this->query("INSERT INTO usuarios_livros (uid, lid) VALUES " .
                    " (:uid, :lid)",
                    array_merge(['uid' => $uid], $dados)
                   );
                   return ( $sqlSt > 0 );
            } 
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarUsuarioLivro($uid, $entrada)
    {
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM usuarios_livros WHERE uid=:uid AND lid=:lid", ['uid' => $uid, 'lid' => $entrada['lid']]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function atualizarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            if ( $this->validaUsuarioLivro($dados['lid'], $uid) ) {
                    $sqlSt = $this->query("UPDATE usuarios_livros SET status=:status, dh_atualizacao=CURRENT_TIMESTAMP  " .
                    " WHERE uid=:uid AND lid=:lid",
                    array_merge(['uid' => $uid], $dados)
                   );
                   return ( $sqlSt > 0 );
            } 
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case '01000':
                    if ( stripos($e->getMessage(),"1265 Data truncated for column 'status") > 0 ) {
                        throw New Exception( helpers\Constantes::getMsg('ERR_USUARIO_LIVRO_STATUS_INVALIDO'), helpers\Constantes::getCode('ERR_USUARIO_LIVRO_STATUS_INVALIDO') );
                    } 
                    break;

                default:
                    throw New Exception( $e->getMessage(), $e->getCode() );
            }            
        }
    }

}