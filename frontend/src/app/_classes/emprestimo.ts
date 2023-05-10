import { Livro } from "./livro";

export class Emprestimo {
    eid: number = 0;
    uid_dono: number = 0;
    lid: number = 0;
    uid_tomador: number = 0;
    qtd_dias: number = 0;
    retirada_prevista: Date = new Date();
    devolucao_prevista: Date = new Date();
    retirada_efetiva: Date = new Date();
    devolucao_efetiva: Date = new Date();
    status: string = "";
    dh_solicitacao: Date = new Date();
    dh_atualizacao: Date = new Date();
    livro: Livro = new Livro();
    situacao: string = "";
}
