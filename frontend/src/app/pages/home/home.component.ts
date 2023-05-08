import { Component, OnInit } from '@angular/core';
import { LivroService } from '../../_service/livro.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  livros: any;

  constructor(private livroService: LivroService) { }

  ngOnInit(): void {
    this.carregaLivros();
  }

  carregaLivros(): void {
    this.livroService.buscarUltimasAtualizacoes().subscribe({
      next: data => {
        this.livros = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }
}
