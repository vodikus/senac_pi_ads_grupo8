import { Component, OnInit, Input } from '@angular/core';
import { LivroService } from 'src/app/_service/livro.service';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';

@Component({
  selector: 'app-barra-social',
  templateUrl: './barra-social.component.html',
  styleUrls: ['./barra-social.component.scss']
})
export class BarraSocialComponent implements OnInit {
  estadoFavorito: boolean = false;
  favorito: string = "assets/Favoritos.png";
  mensagem: MensagemPadrao = new MensagemPadrao();

  @Input('livroId') livroId: number = 0;
  @Input('usuarioId') usuarioId: number = 0;

  constructor(private livroService: LivroService) { }

  ngOnInit(): void {
  }

  alternarFavorito(): void {
    let retorno = this.chamarServico(!this.estadoFavorito);
    if ( retorno ) {
      this.favorito = "assets/FavoritosRed.png"
    } else {
      this.favorito = "assets/Favoritos.png"
    }
    this.estadoFavorito = retorno;
  }

  chamarServico(estado: boolean): boolean {
    let retorno = estado;
    if (estado) {
      this.livroService.adicionarFavorito(this.livroId, this.usuarioId).subscribe({
        next: data => {
          this.mensagem = <MensagemPadrao>data;
          if (this.mensagem.codigo == 1204) {
            retorno = true;
          }
        },
        error: err => {
          console.log(err);
        }
      });
    } else {
      this.livroService.removerFavorito(this.livroId, this.usuarioId).subscribe({
        next: data => {
          this.mensagem = <MensagemPadrao>data;
          if (this.mensagem.codigo == 1205) {
            retorno = false;
          }
        },
        error: err => {
          console.log(err);
        }
      });
    }
    return retorno;
  }

}
