<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";
require_once "models/LivroModel.php";

class EmprestimoModel extends BaseModel
{
    public $campos = array(
        'eid' => ['protected' => 'all', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid_dono' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid_tomador' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'qtd_dias' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'retirada_prevista' => ['protected' => 'none', 'type' => 'date', 'visible' => true],
        'devolucao_prevista' => ['protected' => 'none', 'type' => 'date', 'visible' => true],
        'retirada_efetiva' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'devolucao_efetiva' => ['protected' => 'none', 'type' => 'timestamp', 'visible' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_solicitacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public $statusDominio = array(
        'SOLI' => 'Solicitado',
        'CANC' => 'Cancelado',
        'DEVO' => 'Devolvido',
        'EMPR' => 'Emprestado',
        'EXTR' => 'Extraviado'
    );

    private function validaUsuarioLivro($dados)
    {
        (new LivroModel())->validaLivro($dados['lid']);
        (new UsuarioModel())->validaUsuario($dados['uid_dono']);

        return true;
    }

    private function validaDisponibilidadeLivro($dados)
    {
        try {
            if ($this->query("SELECT 1 FROM usuarios_livros WHERE lid=:lid AND uid=:uid AND status='D' ", ['uid' => $dados['uid_dono'], 'lid' => $dados['lid']]) <= 0) {
                throw new CLConstException('ERR_LIVRO_NAO_DISPONIVEL');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    private function validaStatusLivro($uid, $status, $dados)
    {
        try {
            if (is_array($status)) {
                $strStatus = implode(',', array_map(['StringHelper', 'addQuotes'], $status));
                $statusSql = " status IN ($strStatus) ";
            } else {
                $statusSql = " status='$status' ";
            }
            $sql = "SELECT 1 FROM emprestimos WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND $statusSql ";

            if (
                $this->query(
                    $sql,
                    [
                        'uid_dono' => $uid,
                        'uid_tomador' => $dados['uid_tomador'],
                        'lid' => $dados['lid']
                    ]
                ) <= 0
            ) {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    private function montaSelectEmprestimo($campos, $where = "", $groupBy = "")
    {
        if (!empty($campos)) {
            $sql =
                "SELECT $campos " .
                " FROM emprestimos e";

            if (!empty($where)) {
                $sql .= " WHERE $where ";
            }

            if (!empty($groupBy)) {
                $sql .= " GROUP BY $groupBy";
            }

            return $sql;
        }
        return 'SELECT * FROM livros l';
    }

    public function buscaEmprestimo($eid = 0)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'e');
            $emprestimo = $this->select($this->montaSelectEmprestimo($campos, "eid=:eid"), ['eid' => $eid]);
            if (count($emprestimo) > 0) {
                return $this->complementaEmprestimo($emprestimo[0]);
            } else {
                throw new CLConstException('ERR_EMPRESTIMO_NAO_LOCALIZADO');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function complementaEmprestimo($emprestimo) {
        $emprestimo["livro"] = (new LivroModel())->buscarLivroPorId($emprestimo["lid"]);
        $emprestimo["situacao"] = "";
        switch ($emprestimo["status"]) {
            case 'EMPR':
                if (strtotime("now") > strtotime($emprestimo["devolucao_prevista"]))
                    $emprestimo["situacao"] = "Atrasado";
                else
                    $emprestimo["situacao"] = "Em dia";
                break;
            case 'SOLI':
                if ($emprestimo["retirada_prevista"] != null && $emprestimo["devolucao_prevista"] != null)
                    $emprestimo["situacao"] = "Em espera";
                else
                    $emprestimo["situacao"] = "Solicitado";
                break;

            default:
                $emprestimo["situacao"] = $this->statusDominio[$emprestimo["status"]];
                break;
        }
        return $emprestimo;
    }

    public function listarEmprestimos($uid = 0, $tipo = 'TOMADOS', $status = "")
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'e');
            $sql = "";
            $dado = [];
            if (!empty($status)) {
                $sql = " AND status=:status";
                $dado = ["status" => $status];
            }
            switch ($tipo) {
                case "TOMADOS":
                    $emprestimos = $this->select($this->montaSelectEmprestimo($campos, "uid_tomador=:uid_tomador $sql"), array_merge(['uid_tomador' => $uid], $dado));
                    break;
                case "EMPRESTADOS":
                    $emprestimos = $this->select($this->montaSelectEmprestimo($campos, "uid_dono=:uid_dono $sql"), array_merge(['uid_dono' => $uid], $dado));
                    break;
                default:
                    $emprestimos = [];
                    break;
            }
            $saida = [];
            foreach ($emprestimos as $emprestimo) {
                $saida[] = $this->complementaEmprestimo($emprestimo);
            }
            return $saida;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarLivrosEmprestados($uid = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'e');
        return $this->select("SELECT $campos FROM emprestimos e WHERE uid_dono=:uid", ['uid' => $uid]);
    }

    public function listarEmprestimosTomados($uid = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'e');
        return $this->select("SELECT $campos FROM emprestimos e WHERE uid_tomador=:uid", ['uid' => $uid]);
    }

    public function solicitarEmprestimo($uid, $entrada)
    {
        try {
            $campos = array_filter($this->campos, ['SQLHelper', 'limpaCamposProtegidos']);

            $dados = SQLHelper::validaCampos($campos, $entrada, 'INSERT');
            if ($this->validaUsuarioLivro($dados) && $this->validaDisponibilidadeLivro($dados) && !$this->validaStatusLivro($uid, ['SOLI', 'EMPR'], $dados)) {
                return $this->insert(
                    "INSERT INTO emprestimos (uid_dono, lid, uid_tomador, qtd_dias) VALUES " .
                    " (:uid_dono, :lid, :uid_tomador, :qtd_dias)",
                    array_merge(['uid_tomador' => $uid], $dados)
                );
            } else {
                throw new CLConstException('ERR_LIVRO_NAO_DISPONIVEL');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function devolverEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $dados = $this->buscaEmprestimo($eid);
            if ($this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'EMPR', $dados)) {
                $sqlSt = $this->query(
                    "UPDATE emprestimos SET status='DEVO', devolucao_efetiva=CURRENT_TIMESTAMP, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='EMPR' ",
                    array_merge(['uid_tomador' => $uid], $dados)
                );
            }
            return ($sqlSt > 0);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function desistirEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $dados = $this->buscaEmprestimo($eid);
            if ($this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados)) {
                $sqlSt = $this->query(
                    "UPDATE emprestimos SET status='CANC', dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status='SOLI' ",
                    array_merge(['uid_tomador' => $uid], $dados)
                );
            }
            return ($sqlSt > 0);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function previsaoEmprestimo($uid, $entrada)
    {
        $sqlSt = 0;
        try {
            $emprestimo = $this->buscaEmprestimo($entrada['eid']);
            $campos = array_filter(SQLHelper::sobrescrevePropriedades($this->campos, [
                'qtd_dias' => ['required' => false],
                'retirada_prevista' => ['required' => true],
                'devolucao_prevista' => ['required' => true]
            ]), ['SQLHelper', 'limpaCamposProtegidos']);

            $dados = SQLHelper::limpaDados(
                $this->campos,
                array_replace_recursive($emprestimo, $entrada)
            );

            $dadosLimpos = SQLHelper::validaCampos($campos, $dados, 'UPDATE');

            if ($this->validaUsuarioLivro($dadosLimpos) && $this->validaStatusLivro($uid, 'SOLI', $dadosLimpos)) {
                $sqlSt = $this->query(
                    "UPDATE emprestimos SET " .
                    " retirada_prevista=:retirada_prevista, devolucao_prevista=:devolucao_prevista, dh_atualizacao=CURRENT_TIMESTAMP " .
                    " WHERE eid=:eid AND status = 'SOLI'",
                    array_merge(['eid' => $entrada['eid']], $dadosLimpos)
                );
            }
            return ($sqlSt > 0);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function retirarEmprestimo($uid, $eid)
    {
        $sqlSt = 0;
        try {
            $emprestimo = $this->buscaEmprestimo($eid);
            $campos = SQLHelper::sobrescrevePropriedades($this->campos, [
                'eid' => ['required' => false],
                'qtd_dias' => ['required' => false],
                'retirada_prevista' => ['required' => false],
                'devolucao_prevista' => ['required' => false],
                'dh_solicitacao' => ['protected' => 'none'],
                'dh_atualizacao' => ['protected' => 'none']
            ]);
            unset($emprestimo['eid']);
            unset($emprestimo['livro']);
            unset($emprestimo['situacao']);
            $dados = SQLHelper::validaCampos($campos, $emprestimo, 'UPDATE');
            if ($this->validaUsuarioLivro($dados) && $this->validaStatusLivro($uid, 'SOLI', $dados)) {
                if (is_null($dados['retirada_prevista']) || is_null($dados['devolucao_prevista'])) {
                    throw new CLConstException('ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA');
                } else {
                    $sqlSt = $this->query(
                        "UPDATE emprestimos SET " .
                        " retirada_efetiva=CURRENT_TIMESTAMP, status='EMPR', dh_atualizacao=CURRENT_TIMESTAMP " .
                        " WHERE eid=:eid AND uid_dono=:uid_dono AND lid=:lid AND uid_tomador=:uid_tomador AND status = 'SOLI'",
                        array_merge(['uid_tomador' => $uid, 'eid' => $eid], $dados)
                    );
                }
            }
            return ($sqlSt > 0);
        } catch (Exception $e) {
            throw $e;
        }
    }

}