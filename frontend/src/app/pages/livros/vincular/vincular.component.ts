import { Component } from '@angular/core';
import { LivroService } from 'src/app/_service/livro.service';
import { Router, ActivatedRoute } from '@angular/router';
import { Livro } from 'src/app/_classes/livro';
import { environment } from 'src/environments/environment';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';
import { UsuarioService } from 'src/app/_service/usuario.service';

@Component({
  selector: 'app-vincular',
  templateUrl: './vincular.component.html',
  styleUrls: ['./vincular.component.scss']
})
export class VincularComponent {
  IMG_SERVER = environment.backendUrl;
  livroId: number = 0;
  livro: Livro = new Livro;

  constructor(
    private livroService: LivroService,
    private usuarioService: UsuarioService,
    private router: Router,
    private route: ActivatedRoute) { }

  ngOnInit(): void {
    this.livroId = Number(this.route.snapshot.paramMap.get('lid'));
    this.buscaPorId(this.livroId);
  }

  buscaPorId(lid: number): void {
    this.livroService.buscarLivrosPorId(lid).subscribe({
      next: data => {
        this.livro = data;
      },
      error: err => {
        console.log(err);
      }
    });
  }

  cadastrarLivro(): void {
    this.usuarioService.vincularLivro(this.livroId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1205) {
          this.router.navigateByUrl('/usuarios');
        }
      },
      error: err => {
        console.log(err);
      }
    });
  }  
}
