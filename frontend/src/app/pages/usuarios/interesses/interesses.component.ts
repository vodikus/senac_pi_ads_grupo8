import { Component } from '@angular/core';
import { TagModel } from 'ngx-chips/core/tag-model';
import { Observable, map } from 'rxjs';
import { Assunto } from 'src/app/_classes/assunto';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';
import { AssuntoService } from 'src/app/_service/assunto.service';
import { UsuarioService } from 'src/app/_service/usuario.service';

@Component({
  selector: 'app-interesses',
  templateUrl: './interesses.component.html',
  styleUrls: ['./interesses.component.scss']
})
export class InteressesComponent {
  assuntos: Array<Assunto> = new Array<Assunto>();

  constructor(private assuntoService: AssuntoService, private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.usuarioService.buscarMeusAssuntos().subscribe({
      next: data => {
        this.assuntos = data;
      },
      error: err => {
        console.log("Erro ao carregar assuntos: " + err);
      }
    });
  }

  public buscaAssunto = (nome: string): Observable<any> => {
    return this.assuntoService.buscarPorNome(nome);
  }

  public adicionarAssunto(event: any) {
    let assunto: Assunto = event as Assunto;
    console.log(assunto);
    this.usuarioService.adicionarAssunto(assunto.iid).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1203) {
          
        }        
      },
      error: err => {
        console.log("Erro ao adicionar assuntos: " + err);
      }
    });
  }

  public removerAssunto(event: any) {
    let assunto: Assunto = event as Assunto;
    console.log(assunto);
    this.usuarioService.removerAssunto(assunto.iid).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1204) {
          
        }        
      },
      error: err => {
        console.log("Erro ao remover assuntos: " + err);
      }
    });
  }


}
