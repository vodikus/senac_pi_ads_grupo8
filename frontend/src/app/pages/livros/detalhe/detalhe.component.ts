import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Livro } from 'src/app/_classes/livro';
import { LivroService } from 'src/app/_service/livro.service';

@Component({
  selector: 'app-detalhe',
  templateUrl: './detalhe.component.html',
  styleUrls: ['./detalhe.component.scss']
})
export class DetalheComponent {
  livro: Livro = new Livro();

  constructor(
    private livroService: LivroService,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('lid'));
    this.carregaLivro(id);
  }

  carregaLivro(id: number): void {
    this.livroService.buscarLivrosPorId(id).subscribe({
      next: data => {
        this.livro = data;
      },
      error: err => {
        console.log(err);
      }
    });
  }
}
