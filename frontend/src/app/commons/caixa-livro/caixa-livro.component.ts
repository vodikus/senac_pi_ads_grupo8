import { Component, Input } from '@angular/core';
import { Livro } from 'src/app/_classes/livro';

@Component({
  selector: 'app-caixa-livro',
  templateUrl: './caixa-livro.component.html',
  styleUrls: ['./caixa-livro.component.scss']
})
export class CaixaLivroComponent {
  @Input('livro') livro!: Livro; 
  @Input('uid') uid!: number;
    
  @Input('mostrarDadosEmprestimo') mostrarDadosEmprestimo: boolean = true;
  @Input('mostrarEmprestador') mostrarEmprestador: boolean = true;
  @Input('mostrarLivroDescricao') mostrarLivroDescricao: boolean = true;
  @Input('mostrarBarraAvaliacao') mostrarBarraAvaliacao: boolean = true;
  @Input('mostrarBarraUsuario') mostrarBarraUsuario: boolean = true;
  @Input('mostrarBarraSocial') mostrarBarraSocial: boolean = true;
  @Input('mostrarBarraAcao') mostrarBarraAcao: boolean = true;
}
