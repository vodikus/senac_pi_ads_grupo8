import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { UsuarioService } from '../../../_service/usuario.service';
import { Usuario } from 'src/app/_classes/usuario';
import { LivroService } from 'src/app/_service/livro.service';
import { Livro } from 'src/app/_classes/livro';
import { environment } from 'src/environments/environment';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';
import { Location } from '@angular/common';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.scss']
})
export class PerfilComponent implements OnInit {
  IMG_SERVER = environment.backendUrl;
  livros: Array<Livro> = new Array<Livro>();
  perfil: Usuario = new Usuario();
  usuarioId: number = 0;

  constructor(private livroService: LivroService, private usuarioService: UsuarioService, private route: ActivatedRoute, private location: Location) { }

  ngOnInit(): void {
    this.usuarioId = Number(this.route.snapshot.paramMap.get('uid'));
    this.usuarioService.buscarPerfil(this.usuarioId).subscribe({
      next: data => {
        this.perfil = data;
      },
      error: err => {
        console.log("Erro ao carregar perfil: " + err);
      }
    });
    this.livroService.buscarLivrosPorUsuario(this.usuarioId).subscribe({
      next: data => {
        this.livros = data;
      },
      error: err => {
        console.log("Erro ao carregar livros: " + err);
      }
    });
  }

  reloadPage(): void {
    window.location.reload();
  }

  adicionarAmigo(): void {
    this.usuarioService.adicionarAmigo(this.usuarioId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1900) {
          this.reloadPage();
        }
      },
      error: err => {
        console.log("Erro ao adicionar amigo: " + err);
      }
    });
  }

  excluirAmigo(): void {
    this.usuarioService.removerAmigo(this.usuarioId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1901) {
          this.reloadPage();
        }
      },
      error: err => {
        console.log("Erro ao remover amigo: " + err);
      }
    });
  }

  bloquearUsuario(): void {
    this.usuarioService.bloquearUsuario(this.usuarioId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1208) {
          this.reloadPage();
        }
      },
      error: err => {
        console.log("Erro ao adicionar amigo: " + err);
      }
    });
  }

  desbloqUsuario(): void {
    this.usuarioService.desbloquearUsuario(this.usuarioId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1209) {
          this.reloadPage();
        }
      },
      error: err => {
        console.log("Erro ao remover amigo: " + err);
      }
    });
  }
}
