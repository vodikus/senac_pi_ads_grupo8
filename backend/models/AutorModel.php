<?php
require_once "includes/BaseModel.php";

class AutorModel extends BaseModel
{
    public $campos = array (
        'aid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'nome_autor' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function validaAutor($aid) {
        if ( $this->query("SELECT 1 FROM autores WHERE aid=:aid",  ['aid' => $aid ]) <= 0  ) {
            throw new CLConstException('ERR_AUTOR_NAO_ENCONTRADO');
        }
        return true;
    }

    public function listarAutores()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos);
        return $this->select("SELECT $campos FROM autores a");
    }
    public function buscarAutor($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos);
        return $this->select("SELECT $campos FROM autores a WHERE aid=:aid",  [ 'aid' => $id ] );
    }

    public function buscarAutorPorNome($nome)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
            $autores = $this->select("SELECT $campos FROM autores a WHERE nome_autor LIKE :nome_autor", ['nome_autor' => "%$nome%"]);
            if (count($autores) > 0) {
                return $autores;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }    

    public function adicionarAutor($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->insert("INSERT INTO autores (nome_autor) VALUES " .
                                " (:nome_autor)",
                                $dados
                            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'nome_autor_uk')) {
                        throw new CLConstException('ERR_AUTOR_JA_EXISTENTE');
                    }
                    break;
            }           
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

    public function deletarAutor($aid = 0)
    {
        try {
            return $this->query("DELETE FROM autores WHERE aid=:aid", ['aid' => $aid]);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'fk_autores')) {
                        throw new CLConstException('ERR_AUTOR_DELETAR_FK');
                    }
                    break;
            }            
            throw new Exception ($e->getMessage(), $e->getCode() );
        }
    }

    public function atualizarAutor($aid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE autores SET $campos WHERE aid=:aid", array_merge(['aid' => $aid], $dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'nome_autor_uk')) {
                        throw new CLConstException('ERR_AUTOR_JA_EXISTENTE');
                    }
                    break;
            }           
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}