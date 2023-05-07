<?php
require_once "includes/BaseModel.php";

class LivroModel extends BaseModel
{
    public $campos = array(
        'lid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'titulo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'descricao' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'avaliacao' => ['protected' => 'update', 'type' => 'float', 'visible' => true],
        'capa' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'isbn' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    private function montaSelectLivros($campos, $where = "", $groupBy = "", $orderBy = "")
    {
        if (!empty($campos)) {
            $sql =
                "SELECT $campos, " .
                " group_concat(DISTINCT a.nome_autor ORDER BY a.nome_autor SEPARATOR ', ') autores, " .
                " group_concat(DISTINCT i.nome_assunto ORDER BY i.nome_assunto SEPARATOR ', ') assuntos " .
                " FROM livros l" .
                " LEFT JOIN livros_autores la ON la.lid = l.lid " .
                " LEFT JOIN autores a ON a.aid = la.aid " .
                " LEFT JOIN livros_assuntos li ON li.lid = l.lid " .
                " LEFT JOIN assuntos i ON i.iid = li.iid ";

            if (!empty($where)) {
                $sql .= " WHERE $where ";
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

    private function montaSelectUsuariosLivros($campos, $where = "", $groupBy = "", $orderBy = "")
    {
        if (!empty($campos)) {
            $sql =
                "SELECT $campos, " .
                " group_concat(DISTINCT a.nome_autor ORDER BY a.nome_autor SEPARATOR ', ') autores, " .
                " group_concat(DISTINCT i.nome_assunto ORDER BY i.nome_assunto SEPARATOR ', ') assuntos, " .
                " u.nome, u.apelido, u.avatar, u.uid " .
                " FROM usuarios_livros ul " .
                " INNER JOIN livros l ON l.lid = ul.lid " .
                " INNER JOIN usuarios u ON ul.uid = u.uid " .
                " LEFT JOIN livros_autores la ON la.lid = l.lid " .
                " LEFT JOIN autores a ON a.aid = la.aid " .
                " LEFT JOIN livros_assuntos li ON li.lid = l.lid " .
                " LEFT JOIN assuntos i ON i.iid = li.iid ";

            if (!empty($where)) {
                $sql .= " WHERE ul.status='D' AND $where ";
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

    public function validaLivro($lid)
    {
        if ($this->query("SELECT 1 FROM livros WHERE lid=:lid", ['lid' => $lid]) <= 0) {
            throw new CLConstException('ERR_LIVRO_NAO_ENCONTRADO', "lid: $lid");
        }
        return true;
    }

    public function listarLivros($ordenacao)
    {
        $ordem = str_replace(",", " ", $ordenacao);
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros($campos, '', $campos, $ordem)
        );
    }

    public function buscarLivroPorId($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros(
                $campos,
                ' l.lid=:lid ',
                $campos
            ),
            ['lid' => $id]
        );
    }

    public function buscarLivroPorIsbn($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros(
                $campos,
                ' l.isbn=:isbn ',
                $campos
            ),
            ['lid' => $id]
        );
    }

    public function buscarLivroPorAssunto($assunto = "")
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros(
                $campos,
                ' i.nome_assunto LIKE :assunto ',
                $campos
            ),
            ['assunto' => "%$assunto%"]
        );
    }

    public function buscarLivroPorAutor($autor = "")
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros(
                $campos,
                ' a.nome_autor LIKE :autor ',
                $campos
            ),
            ['autor' => "%$autor%"]
        );
    }

    public function buscarLivroPorUsuario($uid = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            "SELECT $campos FROM usuarios_livros ul " .
            "INNER JOIN usuarios u USING (uid) " .
            "INNER JOIN livros l USING (lid) " .
            "WHERE ul.uid = :uid",
            ['uid' => $uid]
        );
    }

    public function buscarLivroPorTitulo($titulo = "")
    {
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectLivros(
                $campos,
                ' l.titulo LIKE :titulo ',
                $campos
            ),
            ['titulo' => "%$titulo%"]
        );
    }

    public function adicionarLivro($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->insert(
                "INSERT INTO livros (titulo, descricao, capa, isbn) VALUES " .
                " (:titulo, :descricao, :capa, :isbn)",
                $dados
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'livro_isbn_uk')) {
                        throw new CLConstException('ERR_LIVRO_JA_EXISTENTE');
                    }
                    break;
            }
            throw $e;
        }
    }

    public function deletarLivro($lid = 0)
    {
        try {
            return $this->query("DELETE FROM livros WHERE lid=:lid", ['lid' => $lid]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function atualizarLivro($lid, $entrada)
    {
        try {

            $this->validaLivro($lid);

            $campos = SQLHelper::sobrescrevePropriedades($this->campos, [
                'isbn' => ['required' => false]
            ]);
            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            $camposUpdate = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE livros SET $camposUpdate WHERE lid=:lid", array_merge(['lid' => $lid], $dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'livro_isbn_uk')) {
                        throw new CLConstException('ERR_LIVRO_JA_EXISTENTE');
                    }
                    break;
            }
            throw $e;
        }
    }

    public function buscarLivrosDisponiveis($ordenacao)
    {
        $ordem = str_replace(",", " ", $ordenacao);
        $campos = SQLHelper::montaCamposSelect($this->campos, 'l');

        return $this->select(
            $this->montaSelectUsuariosLivros(
                $campos,
                '',
                $campos,
                $ordem
            )
        );
    }

}