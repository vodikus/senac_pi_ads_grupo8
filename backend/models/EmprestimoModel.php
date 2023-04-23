<?php
require_once "includes/BaseModel.php";
require_once "includes/Constantes.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/StringHelper.php";
require_once "helpers/TimeDateHelper.php";

class EmprestimoModel extends BaseModel
{
    public $campos = array (
        'eid' => ['protected' => 'all', 'type' => 'int', 'visible' => true, 'required' => true],
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
                throw New Exception( Constantes::getMsg('ERR_LIVRO_NAO_ENCONTRADO'), Constantes::getCode('ERR_LIVRO_NAO_ENCONTRADO') );
        }
        if ( $this->query("SELECT 1 FROM usuarios WHERE uid=:uid",  ['uid' => $dados['uid_dono'] ]) <= 0  ) {
            throw New Exception( Constantes::getMsg('ERR_USUARIO_NAO_ENCONTRADO'), Constantes::getCode('ERR_USUARIO_NAO_ENCONTRADO') );
        }
        return true;
    }

    private function validaDisponibilidadeLivro($dados) {
        try {
            if ( $this->query("SELECT 1 FROM usuarios_livros WHERE lid=:lid AND uid=:uid AND status='D' ",  [ 'uid'=>$dados['uid_dono'], 'lid' => $dados['lid'] ]  ) <= 0  ) {
                    throw New Exception( Constantes::getMsg('ERR_LIVRO_NAO_DISPONIVEL'), Constantes::getCode('ERR_LIVRO_NAO_DISPONIVEL') );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
        return true;
    }

    private function validaStatusLivro($uid, $status, $dados) {
        try {
            if ( is_array($status) ) {
                $strStatus = implode(',', array_map(['StringHelper','addQuotes'],$status));
                $statusSql = " status IN ($strStatus) ";
            } else {
                $statusSql = " status='$status' ";
            }
            $sql = "SELECT 1 FROM emprestimos WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND $statusSql ";

            if ( 
                $this->query( $sql,  
                    [   'uid_dono' => $dados['uid_dono'], 
                        'uid_tomador' => $uid, 
                        'lid' => $dados['lid']
                    ]  
                ) <= 0  ) {
                    error_log("vsl not ahoy");
                    return false;
            } 
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
        error_log("vsl ahoy");
        return true;
    }

    public function buscaEmprestimo($uid = 0, $eid = 0) {
        try {
            $emprestimo = $this->select("SELECT uid_dono, lid, uid_tomador, qtd_dias, retirada_prevista, retirada_efetiva, devolucao_prevista, devolucao_efetiva, status, dh_solicitacao, dh_atualizacao FROM emprestimos WHERE eid=:eid AND uid_tomador=:uid_tomador", ['uid_tomador'=>$uid, 'eid'=>$eid]);
            if ( count($emprestimo) > 0 ) {
                return $emprestimo[0];
            } else {
                throw New Exception( Constantes::getMsg('ERR_EMPRESTIMO_NAO_LOCALIZADO'), Constantes::getCode('ERR_EMPRESTIMO_NAO_LOCALIZADO') );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function listarEmprestimos($uid = 0, $tipo = 'TOMADOS') {
        try {
            switch ($tipo) {
                case "TOMADOS":
                    $emprestimos = $this->select("SELECT uid_dono, lid, uid_tomador, qtd_dias, retirada_prevista, retirada_efetiva, devolucao_prevista, devolucao_efetiva, status, dh_solicitacao, dh_atualizacao FROM emprestimos WHERE uid_tomador=:uid_tomador", ['uid_tomador'=>$uid]);
                    break;
                case "EMPRESTADOS":
                    $emprestimos = $this->select("SELECT uid_dono, lid, uid_tomador, qtd_dias, retirada_prevista, retirada_efetiva, devolucao_prevista, devolucao_efetiva, status, dh_solicitacao, dh_atualizacao FROM emprestimos WHERE uid_dono=:uid_dono", ['uid_dono'=>$uid]);
                    break;
                default:
                    $emprestimos = [];
                    break;
            }
            return $emprestimos;
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
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
        $sqlSt = 0;
        try {
            $campos = array_filter($this->campos, ['SQLHelper','limpaCamposProtegidos']);

            $dados = SQLHelper::validaCampos($campos, $entrada, 'INSERT');
            if ( $this->validaUsuarioLivro($dados) && $this->validaDisponibilidadeLivro($dados) && !$this->validaStatusLivro($uid, ['SOLI','EMPR'], $dados) ) {
                $sqlSt = $this->query("INSERT INTO emprestimos (uid_dono, lid, uid_tomador, qtd_dias) VALUES " .
                " (:uid_dono, :lid, :uid_tomador, :qtd_dias)",
                array_merge(['uid_tomador' => $uid], $dados)
                );
                return ( $sqlSt > 0 );
            } else {
                throw New Exception( Constantes::getMsg('ERR_LIVRO_NAO_DISPONIVEL'), Constantes::getCode('ERR_LIVRO_NAO_DISPONIVEL') );
            }
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function devolverEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $dados = $this->buscaEmprestimo($uid, $eid);
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'EMPR', $dados) ) {
                $sqlSt = $this->query("UPDATE emprestimos SET status='DEVO', devolucao_efetiva=CURRENT_TIMESTAMP, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='EMPR' ", 
                    array_merge(['uid_tomador'=>$uid], $dados) );
            }
            return ( $sqlSt > 0 );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function desistirEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $dados = $this->buscaEmprestimo($uid, $eid);
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados) ) {
                $sqlSt = $this->query("UPDATE emprestimos SET status='CANC', dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='SOLI' ", 
                    array_merge(['uid_tomador'=>$uid],$dados) );
            }
            return ( $sqlSt > 0 );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function previsaoEmprestimo($uid, $entrada)
    {
        $sqlSt = 0;
        try {
            $emprestimo = $this->buscaEmprestimo($uid, $entrada['eid']);
            $campos = array_filter(SQLHelper::sobrescrevePropriedades( $this->campos, [
                'qtd_dias' => ['required' => false],
                'retirada_prevista' => ['required' => true],
                'devolucao_prevista' => ['required' => true]
            ]), ['SQLHelper','limpaCamposProtegidos']);
            
            $dados = SQLHelper::limpaDados(
                $this->campos, 
                array_replace_recursive($emprestimo, $entrada)
            );

            $dadosLimpos = SQLHelper::validaCampos( $campos, $dados, 'UPDATE');

            if ( $this->validaUsuarioLivro($dadosLimpos)  && $this->validaStatusLivro($uid, 'SOLI', $dadosLimpos) ) {
                $sqlSt = $this->query("UPDATE emprestimos SET " .
                    " retirada_prevista=:retirada_prevista, devolucao_prevista=:devolucao_prevista, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status = 'SOLI'", 
                    array_merge(['uid_tomador'=>$uid], $dadosLimpos ));
            }
            return ( $sqlSt > 0 );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function retirarEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $emprestimo = $this->buscaEmprestimo($uid, $eid);
            $campos = SQLHelper::sobrescrevePropriedades( $this->campos, [
                'eid' => ['required' => false],
                'qtd_dias' => ['required' => false],
                'dh_solicitacao' => ['protected' => 'none'],
                'dh_atualizacao' => ['protected' => 'none']                
            ]);
            $dados = SQLHelper::validaCampos($campos, $emprestimo, 'UPDATE');            
            if ( $this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados) ) {
                if ( is_null($dados['retirada_prevista']) || is_null($dados['devolucao_prevista']) ) {
                    throw New Exception( Constantes::getMsg('ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA'), Constantes::getCode('ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA') );
                } else {
                    $sqlSt = $this->query("UPDATE emprestimos SET " .
                        " retirada_efetiva=CURRENT_TIMESTAMP, status='EMPR', dh_atualizacao=CURRENT_TIMESTAMP " .
                        " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status = 'SOLI'", 
                        array_merge(['uid_tomador'=>$uid], $dados ) );
                }
            }
            return ( $sqlSt > 0 );
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    // @TODO Validar estados antes de realizar os updates / inserts devido a retirada da chave primária

}