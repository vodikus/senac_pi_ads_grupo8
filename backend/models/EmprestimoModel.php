<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class EmprestimoModel extends BaseModel
{
    public $campos = array (
        'uid_dono' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid_tomador' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'qtd_dias' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'retirada_prevista' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'devolucao_prevista' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'retirada_efetiva' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'devolucao_efetiva' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_solicitacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    private function validaUsuarioLivro($dados) {
        if ( $this->query("SELECT 1 FROM livros WHERE lid=:lid",  ['lid' => $dados['lid'] ]) <= 0  ) {
                throw New Exception( "Livro não encontrado");
        }
        if ( $this->query("SELECT 1 FROM usuarios WHERE uid=:uid",  ['uid' => $dados['uid_dono'] ]) <= 0  ) {
            throw New Exception( "Usuário não encontrado");
        }
        return true;
    }

    private function validaDisponibilidadeLivro($dados) {
        try {
            if ( $this->query("SELECT 1 FROM usuarios_livros WHERE lid=:lid AND uid=:uid AND status='D' ",  [ 'uid'=>$dados['uid_dono'], 'lid' => $dados['lid'] ]  ) <= 0  ) {
                    throw New Exception( "Livro não disponivel");
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
        return true;
    }

    private function validaStatusLivro($uid, $status, $dados) {
        try {
            if ( 
                $this->query("SELECT 1 FROM emprestimos WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status=:status ",  
                    [   'uid_dono' => $dados['uid_dono'], 
                        'uid_tomador' => $uid, 
                        'lid' => $dados['lid'], 
                        'status' => $status 
                    ]  
                ) <= 0  ) {
                    throw New Exception( "Empréstimo não disponivel");
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
        return true;
    }

    public function listarLivrosEmprestados($uid = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos,'e');
        return $this->select("SELECT $campos FROM emprestimos e WHERE uid_dono=:uid", ['uid'=>$uid]);
    }

    public function listarEmprestimosTomados($uid = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos,'e');
        return $this->select("SELECT $campos FROM emprestimos e WHERE uid_tomador=:uid", ['uid'=>$uid]);
    }

    public function solicitarEmprestimo($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->validaUsuarioLivro($dados) && $this->validaDisponibilidadeLivro($dados) ) {
                return $this->query("INSERT INTO emprestimos (uid_dono, lid, uid_tomador, qtd_dias) VALUES " .
                " (:uid_dono, :lid, :uid_tomador, :qtd_dias)",
                array_merge(['uid_tomador' => $uid], $dados)
                );
            } 
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function devolverEmprestimo($uid, $entrada)
    {
        try {
            $campos = SQLHelper::sobrescrevePropriedades( $this->campos, [
                'qtd_dias' => ['required' => false]
            ]);    
            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'EMPR', $dados) ) {
                return $this->query("UPDATE emprestimos SET status='DEVO', devolucao_efetiva=CURRENT_TIMESTAMP, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='EMPR' ", 
                    array_merge(['uid_tomador'=>$uid], $dados) );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function desistirEmprestimo($uid, $entrada)
    {
        try {
            $campos = SQLHelper::sobrescrevePropriedades( $this->campos, [
                'qtd_dias' => ['required' => false]
            ]);            
            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados) ) {
                return $this->query("UPDATE emprestimos SET status='CANC', dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='SOLI' ", 
                    array_merge(['uid_tomador'=>$uid],$dados) );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function previsaoEmprestimo($uid, $entrada)
    {
        try {
            $campos = SQLHelper::sobrescrevePropriedades( $this->campos, [
                'qtd_dias' => ['required' => false],
                'retirada_prevista' => ['required' => true],
                'devolucao_prevista' => ['required' => true]
            ]);
            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            if ( $this->validaUsuarioLivro($dados)  && $this->validaStatusLivro($uid, 'SOLI', $dados) ) {
                return $this->query("UPDATE emprestimos SET " .
                    " retirada_prevista=:retirada_prevista, devolucao_prevista=:devolucao_prevista, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status = 'SOLI'", 
                    array_merge(['uid_tomador'=>$uid], $dados ));
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function retirarEmprestimo($uid, $entrada)
    {
        try {
            $campos = SQLHelper::sobrescrevePropriedades( $this->campos, [
                'qtd_dias' => ['required' => false]
            ]);            
            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados) ) {
                return $this->query("UPDATE emprestimos SET " .
                    " retirada_efetiva=CURRENT_TIMESTAMP, status='EMPR', dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status = 'SOLI'", 
                    array_merge(['uid_tomador'=>$uid], $dados ) );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    // @TODO Validar estados antes de realizar os updates / inserts devido a retirada da chave primária

}