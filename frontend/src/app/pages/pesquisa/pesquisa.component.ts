import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { Livro } from 'src/app/_classes/livro';
import { LivroService } from 'src/app/_service/livro.service';

@Component({
  selector: 'app-pesquisa',
  templateUrl: './pesquisa.component.html',
  styleUrls: ['./pesquisa.component.scss']
})
export class PesquisaComponent implements OnInit {
  enviado = false;
  livros: Array<Livro> = new Array<Livro>();
  form: FormGroup = new FormGroup({
    pesquisa: new FormControl(''),
    tipo_busca: new FormControl('assunto')
  });

  constructor(private formBuilder: FormBuilder, private livroService: LivroService) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group(
      {
        pesquisa: ['', [Validators.required]],
        tipo_busca: ['', [Validators.required]],
      }
    );
  }

  get f(): { [key: string]: AbstractControl } {
    return this.form.controls;
  }

  buscar() {
    this.enviado = true;

    if (this.form.invalid) {
      return;
    }

    switch (this.form.controls['tipo_busca'].value) {
      case 'assunto':
        this.buscaPorAssunto(this.form.controls['pesquisa'].value);
        break;
      case 'autor':
        this.buscaPorAutor(this.form.controls['pesquisa'].value);
        break;
      case 'titulo':
        this.buscaPorTitulo(this.form.controls['pesquisa'].value);
        break;
    }
  }

  buscaPorAssunto(termo: string): void {
    this.livroService.buscarLivrosPorAssunto(termo).subscribe({
      next: data => {
        this.livros = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }

  buscaPorAutor(termo: string): void {
    this.livroService.buscarLivrosPorAutor(termo).subscribe({
      next: data => {
        this.livros = data;
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
      },
      error: err => {
        console.log(err);
      }
    });     
  }

}
