import { Component, OnInit } from '@angular/core';
import { LivroService } from '../../_service/livro.service';
import { Livro } from 'src/app/_classes/livro';

@Component({
  selector: 'app-favoritos',
  templateUrl: './favoritos.component.html',
  styleUrls: ['./favoritos.component.scss']
})
export class FavoritosComponent implements OnInit {
  livros: Array<Livro> = new Array<Livro>();

  constructor(private livroService: LivroService) { }

  ngOnInit(): void {
    this.carregaLivros();
  }

  carregaLivros(): void {
    this.livroService.listarFavoritos().subscribe({
      next: data => {
        this.livros = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }
}
