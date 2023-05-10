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
    console.log('af1: '+this.estadoFavorito);
    let retorno = this.chamarServico(!this.estadoFavorito);
    console.log('af2: '+retorno);
    if ( retorno ) {
      this.favorito = "assets/FavoritosRed.png"
    } else {
      this.favorito = "assets/Favoritos.png"
    }
    this.estadoFavorito = retorno;
  }

  chamarServico(estado: boolean): boolean {
    console.log('cs1: '+estado);
    if (estado) {
      console.log('cs2: '+!estado);
      this.livroService.adicionarFavorito(this.livroId, this.usuarioId).subscribe({
        next: data => {
          this.mensagem = <MensagemPadrao>data;
          if (this.mensagem.codigo == 1204) {
            return true;
          }
          return false;
        },
        error: err => {
          console.log(err);
          return false;
        }
      });
    } else {
      console.log('cs3: '+estado);
      this.livroService.removerFavorito(this.livroId, this.usuarioId).subscribe({
        next: data => {
          this.mensagem = <MensagemPadrao>data;
          if (this.mensagem.codigo == 1205) {
            return false;
          }
          return true;
        },
        error: err => {
          console.log(err);
          return true;
        }
      });
    }
    return true;
  }

}
