import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { UsuarioService } from '../../../_service/usuario.service';
import { Usuario } from 'src/app/_classes/usuario';
import { LivroService } from 'src/app/_service/livro.service';
import { Livro } from 'src/app/_classes/livro';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.scss']
})
export class PerfilComponent implements OnInit  {
  IMG_SERVER = environment.backendUrl;
  livros: Array<Livro> = new Array<Livro>();
  perfil: Usuario = new Usuario();
  usuarioId: number = 0;

  constructor(private livroService: LivroService, private usuarioService: UsuarioService, private route: ActivatedRoute,) { }

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

}
