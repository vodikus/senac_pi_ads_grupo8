import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { Livro } from 'src/app/_classes/livro';
import { Autor } from 'src/app/_classes/autor';
import { LivroService } from 'src/app/_service/livro.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AutorService } from 'src/app/_service/autor.service';
import { concat, of, Observable, Subject } from 'rxjs';
import { catchError, debounceTime, distinctUntilChanged, switchMap, tap } from 'rxjs/operators';
import { Assunto } from 'src/app/_classes/assunto';
import { AssuntoService } from 'src/app/_service/assunto.service';
import { OpenLibraryService } from 'src/app/_service/open-library.service';
import { OlBook } from 'src/app/_classes/ol-book';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';

@Component({
  selector: 'app-adicionar',
  templateUrl: './adicionar.component.html',
  styleUrls: ['./adicionar.component.scss']
})
export class AdicionarComponent {
  livroId: number = 0;
  isbn: string = "";

  isbnExiste = false;
  olLivro: OlBook = new OlBook();
  livro: Livro = new Livro();
  livros: Array<Livro> = new Array<Livro>();
  form: FormGroup = new FormGroup({
    titulo: new FormControl(''),
    descricao: new FormControl(''),
    isbn: new FormControl(''),
    autores: new FormControl(''),
    assuntos: new FormControl(''),
  });
  enviado = false;

  autores: Autor[] = [];
  autoresLista?: Observable<Autor[]>;
  autoresLoad = false;
  autoresInput = new Subject<string>();

  assuntos: Autor[] = [];
  assuntosLista?: Observable<Assunto[]>;
  assuntosLoad = false;
  assuntosInput = new Subject<string>();

  constructor(
    private formBuilder: FormBuilder,
    private livroService: LivroService,
    private router: Router,
    private route: ActivatedRoute,
    private autorService: AutorService,
    private assuntoService: AssuntoService,
    private openLibraryService: OpenLibraryService) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group(
      {
        titulo: ['', [Validators.required, Validators.maxLength(255)]],
        descricao: ['', [Validators.required, Validators.maxLength(1000)]],
        isbn: ['', [Validators.required, Validators.minLength(1), Validators.maxLength(13)]],
        autores: ['', [Validators.required]],
        assuntos: [''],
      }
    );

    this.livroId = Number(this.route.snapshot.paramMap.get('lid'));
    this.buscarAutores();
    this.buscarAssuntos();
  }

  get f(): { [key: string]: AbstractControl } {
    return this.form.controls;
  }

  onSubmit(): void {
    this.enviado = true;

    if (this.form.invalid) {
      return;
    }

    this.livro = this.form.value;

    console.log(JSON.stringify(this.form.value, null, 2));

    this.livroService.cadastrarLivro(this.livro).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1200) {
          this.router.navigateByUrl('/usuarios');
        }
      },
      error: err => {
        console.log(err);
      }
    });
  }

  onReset(): void {
    this.enviado = false;
    this.form.reset();
  }

  buscaPorIsbn(event: any): void {
    this.livroService.buscarLivrosPorIsbn(event.target.value).subscribe({
      next: data => {
        this.livros = data;
        this.isbnExiste = (data.length > 0);
        this.enviado = (data.length > 0);
        if (this.isbnExiste) {
          this.form.controls['isbn'].setErrors({ 'existe': true })
        } else {
          this.openLibraryService.buscarLivroPorISBN(event.target.value).subscribe({
            next: data => {
              this.olLivro = <OlBook>Object.values(data)[0];
              this.form.controls['titulo'].setValue(this.olLivro.title);
            },
            error: err => {
              console.log(err);
            }
          });
        }
      },
      error: err => {
        console.log(err);
      }
    });

  }

  trackByFn(item: Autor) {
    return item.aid;
  }

  private buscarAutores() {
    this.autoresLista = concat(
      of([]), // default items
      this.autoresInput.pipe(
        distinctUntilChanged(),
        tap(() => this.autoresLoad = true),
        switchMap(term => this.autorService.buscarAutorPorNome(term).pipe(
          catchError(() => of([])), // empty list on error
          tap(() => this.autoresLoad = false)
        ))
      )
    );
    this.autoresLoad = false;
  }

  private buscarAssuntos() {
    this.assuntosLista = concat(
      of([]), // default items
      this.assuntosInput.pipe(
        distinctUntilChanged(),
        tap(() => this.assuntosLoad = true),
        switchMap(term => this.assuntoService.buscarPorNome(term).pipe(
          catchError(() => of([])), // empty list on error
          tap(() => this.assuntosLoad = false)
        ))
      )
    );
    this.assuntosLoad = false;
  }

}