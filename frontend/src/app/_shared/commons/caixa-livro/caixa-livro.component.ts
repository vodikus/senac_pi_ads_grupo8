import { Component, Input, OnInit } from '@angular/core';
import { Emprestimo } from 'src/app/_classes/emprestimo';
import { Livro } from 'src/app/_classes/livro';

@Component({
  selector: 'app-caixa-livro',
  templateUrl: './caixa-livro.component.html',
  styleUrls: ['./caixa-livro.component.scss']
})
export class CaixaLivroComponent implements OnInit {
  @Input('livro') livro: Livro = new Livro(); 
  @Input('emprestimo') emprestimo!: Emprestimo; 
  @Input('uid') uid: number = 0;
  @Input('origem') origem: string = "";
    
  @Input('mostrarCapa') mostrarCapa: boolean = true;
  @Input('mostrarDadosEmprestimo') mostrarDadosEmprestimo: boolean = false;
  @Input('mostrarEmprestador') mostrarEmprestador: boolean = false;
  @Input('mostrarLivroDescricao') mostrarLivroDescricao: boolean = true;
  @Input('mostrarLivroStatus') mostrarLivroStatus: boolean = true;
  @Input('mostrarBarraAvaliacao') mostrarBarraAvaliacao: boolean = true;
  @Input('mostrarBarraUsuario') mostrarBarraUsuario: boolean = true;
  @Input('mostrarBarraSocial') mostrarBarraSocial: boolean = true;
  @Input('mostrarBarraAcao') mostrarBarraAcao: boolean = true;
  
  @Input('mostrarBarraAcao_Solicitar') mostrarBarraAcao_Solicitar: boolean = true;
  @Input('mostrarBarraAcao_Detalhes') mostrarBarraAcao_Detalhes: boolean = true;
  @Input('mostrarBarraAcao_Gerenciar') mostrarBarraAcao_Gerenciar: boolean = false;
  @Input('mostrarBarraSocial_Favoritos') mostrarBarraSocial_Favoritos: boolean = true;
  @Input('mostrarBarraSocial_FlagFavoritos') mostrarBarraSocial_FlagFavoritos: boolean = false;

  dominioStatus: {[key: string]: string} = {
    SOLI: 'Solicitado',
    CANC: 'Cancelado',
    DEVO: 'Devolvido',
    EMPR: 'Emprestado',
    EXTR: 'Extraviado'
  }

  ngOnInit(): void {
    if (this.emprestimo) {
      this.livro = this.emprestimo.livro;
      this.uid = this.emprestimo.uid_dono;
    } else {
      this.emprestimo = new Emprestimo();
    }
  }
}
