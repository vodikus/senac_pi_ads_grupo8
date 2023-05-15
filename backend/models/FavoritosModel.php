<?php
require_once "includes/BaseModel.php";
require_once "models/LivroModel.php";
require_once "models/UsuarioModel.php";
require_once "models/UsuarioLivroModel.php";

class FavoritosModel extends BaseModel
{
    public $campos = array(
        'uid_usuario' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid_dono' => ['protected' => 'none', 'type' => 'int', 'visible' => true]
    );

    private function montaSelectFavoritos($campos, $where = "", $groupBy = "", $orderBy = "")
    {
        if (!empty($campos)) {
            $sql =
                "SELECT $campos, " .
                " group_concat(DISTINCT a.nome_autor ORDER BY a.nome_autor SEPARATOR ', ') autores, " .
                " group_concat(DISTINCT i.nome_assunto ORDER BY i.nome_assunto SEPARATOR ', ') assuntos, " .
                " u.nome, u.apelido, u.avatar, u.uid, ul.status as status_livro " .
                " FROM usuarios_livros ul " .
                " INNER JOIN livros l ON l.lid = ul.lid " .
                " INNER JOIN usuarios u ON ul.uid = u.uid " .
                " INNER JOIN favoritos f ON ul.uid = f.uid_dono AND f.lid = ul.lid " .
                " LEFT JOIN livros_autores la ON la.lid = l.lid " .
                " LEFT JOIN autores a ON a.aid = la.aid " .
                " LEFT JOIN livros_assuntos li ON li.lid = l.lid " .
                " LEFT JOIN assuntos i ON i.iid = li.iid " .
                " WHERE ul.status='D' AND f.uid_usuario = :uid_usuario ";

            if (!empty($where)) {
                $sql .= " AND $where ";
            }

            if (!empty($groupBy)) {
                $sql .= " GROUP BY $groupBy";
            }

            if (!empty($orderBy)) {
                $sql .= " ORDER BY $orderBy";
            }

            return $sql;
        }
        return 'SELECT * FROM livros l';
    }

    public function adicionarFavorito($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');

            (new LivroModel())->validaLivro($dados['lid']);
            (new UsuarioModel())->validaUsuario($dados['uid_dono']);
            (new UsuarioLivroModel())->validaUsuarioLivro($dados['uid_dono'], $dados['lid']);

            return $this->query(
                "INSERT INTO favoritos (uid_usuario, lid, uid_dono) VALUES " .
                " (:uid, :lid, :uid_dono)",
                array_merge(['uid' => $uid], $dados)
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        throw new CLConstException('ERR_LIVRO_JA_FAVORITO');
                    }
                    break;
            }
            throw $e;
        }
    }

    public function removerFavorito($uid, $entrada)
    {
        SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');
        try {
            return $this->query(
                "DELETE FROM favoritos WHERE uid_usuario=:uid AND lid=:lid and uid_dono=:uid_dono",
                ['uid' => $uid, 'lid' => $entrada['lid'], 'uid_dono' => $entrada['uid_dono']]
            );
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function listarFavoritos($uid, $ordenacao)
    {
        $ordem = str_replace(",", " ", $ordenacao);
        $campos = SQLHelper::montaCamposSelect((new LivroModel())->campos, 'l');

        return $this->select(
            $this->montaSelectFavoritos($campos, '', $campos, $ordem), ['uid_usuario' => $uid]
        );
    }

}