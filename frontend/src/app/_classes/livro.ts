export class Livro {
    lid: number = 0;
    titulo: string = "";
    descricao: string = "";
    avaliacao: number = 0;
    capa: string = "/assets/sem-capa.png";
    isbn: string = "";
    status: string = "";
    dh_atualizacao: Date = new Date();
    autores: any;
    assuntos: any;
    uid: number = 0;
    status_livro: string = "";
}
