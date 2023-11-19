import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { Livro } from 'src/app/_classes/livro';
import { LivroService } from 'src/app/_service/livro.service';

@Component({
  selector: 'app-buscar',
  templateUrl: './buscar.component.html',
  styleUrls: ['./buscar.component.scss']
})
export class BuscarComponent {
  temResultado = false;
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

  onSubmit(): void {
    this.enviado = true;

    if (this.form.invalid) {
      return;
    }

    this.buscar();

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
    // this.enviado = false;
  }

  buscaPorIsbn(termo: string): void {
    this.livroService.buscarLivrosPorIsbn(termo).subscribe({
      next: data => {
        this.livros = data;
        this.temResultado = (data.length > 0);
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
        this.temResultado = (data.length > 0);
      },
      error: err => {
        console.log(err);
      }
    });
  }

}
