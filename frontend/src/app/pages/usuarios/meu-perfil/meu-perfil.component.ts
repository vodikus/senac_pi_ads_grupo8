import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UsuarioService } from 'src/app/_service/usuario.service';
import { EnderecoService } from 'src/app/_service/endereco.service';
import { LivroService } from 'src/app/_service/livro.service';
import { environment } from 'src/environments/environment';
import { Assunto } from 'src/app/_classes/assunto';
import { Endereco } from 'src/app/_classes/endereco';
import { Livro } from 'src/app/_classes/livro';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';

@Component({
  selector: 'app-meu-perfil',
  templateUrl: './meu-perfil.component.html',
  styleUrls: ['./meu-perfil.component.scss']
})
export class MeuPerfilComponent implements OnInit {
  perfil: any;
  assuntos: Array<Assunto> = new Array<Assunto>();
  enderecos: Array<Endereco> = new Array<Endereco>();
  livros: Array<Livro> = new Array<Livro>();
  agora: Date = new Date();

  IMG_SERVER = environment.backendUrl;

  constructor(private usuarioService: UsuarioService, private enderecoService: EnderecoService, private livroService: LivroService, private router: Router) { }

  ngOnInit(): void {
    this.usuarioService.buscarMeuPerfil().subscribe({
      next: data => {
        this.perfil = data;
      },
      error: err => {
        console.log("Erro ao carregar perfil: " + err);
      }
    });

    this.usuarioService.buscarMeusAssuntos().subscribe({
      next: data => {
        this.assuntos = data;
      },
      error: err => {
        console.log("Erro ao carregar assuntos: " + err);
      }
    });

    this.enderecoService.buscarMeusEnderecos().subscribe({
      next: data => {
        this.enderecos = data;
      },
      error: err => {
        console.log("Erro ao carregar enderecos: " + err);
      }
    });

    this.livroService.buscarMeusLivros().subscribe({
      next: data => {
        this.livros = data;
      },
      error: err => {
        console.log("Erro ao carregar livros: " + err);
      }
    });
  }

  deletarEndereco(enderecoId: number): void {
    this.enderecoService.deletarEndereco(enderecoId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1701) {
          this.reloadPage();
        }
      },
      error: err => {
        console.log(err);
      }
    });
  }

  reloadPage(): void {
    window.location.reload();
  }
}
