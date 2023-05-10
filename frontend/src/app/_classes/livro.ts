export class Livro {
    lid: number = 0;
    titulo: string = "";
    descricao: string = "";
    avaliacao: number = 0;
    capa: string = "/imagens/livros/sem-capa.png";
    isbn: string = "";
    status: string = "";
    dh_atualizacao: Date = new Date();
    autores: string = "";
    assuntos: string = "";
}
