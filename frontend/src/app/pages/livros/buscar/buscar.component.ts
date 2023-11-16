import { Component } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Livro } from 'src/app/_classes/livro';
import { LivroService } from 'src/app/_service/livro.service';

@Component({
  selector: 'app-buscar',
  templateUrl: './buscar.component.html',
  styleUrls: ['./buscar.component.scss']
})
export class BuscarComponent {
  hasResult: number = -1;
  livros: Array<Livro> = new Array<Livro>();
  form: FormGroup = new FormGroup({
    pesquisa: new FormControl(''),
    tipo_busca: new FormControl('assunto')
  });

  constructor(private livroService: LivroService) { }

  ngOnInit(): void {

  }

  buscar() {
    switch (this.form.controls['tipo_busca'].value) {
      case 'isbn':
        this.buscaPorIsbn(this.form.controls['pesquisa'].value);
        break;
      case 'titulo':
        this.buscaPorTitulo(this.form.controls['pesquisa'].value);
        break;
    }
  }  

  buscaPorIsbn(termo: string): void {
    this.livroService.buscarLivrosPorIsbn(termo).subscribe({
      next: data => {
        this.livros = data;
        this.hasResult = data.length;
      },
      error: err => {
        console.log(err);
      }
    });     
  }

  buscaPorTitulo(termo: string): void {
    this.livroService.buscarLivrosPorTitulo(termo).subscribe({
      next: data => {
        this.livros = data;
        this.hasResult = data.length;
      },
      error: err => {
        console.log(err);
      }
    });     
  }  

}
