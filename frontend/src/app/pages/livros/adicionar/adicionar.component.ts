import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { LivroService } from 'src/app/_service/livro.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-adicionar',
  templateUrl: './adicionar.component.html',
  styleUrls: ['./adicionar.component.scss']
})
export class AdicionarComponent {
  
  form: FormGroup = new FormGroup({
    titulo: new FormControl(''),
    descricao: new FormControl(''),
    isbn: new FormControl(''),
  });
  enviado = false;

  constructor(private formBuilder: FormBuilder, private livroService: LivroService, private router: Router) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group(
      {
        titulo: ['', [Validators.required, Validators.maxLength(255)]],
        descricao: ['', [Validators.required, Validators.maxLength(1000)]],
        isbn: ['', [Validators.required, Validators.maxLength(20)]],
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

    // this.endereco = this.form.value;

    // console.log(JSON.stringify(this.form.value, null, 2));

    // this.enderecoService.cadastrarEndereco(this.endereco).subscribe({
    //   next: data => {
    //     let mensagem = data as MensagemPadrao;
    //     console.log(mensagem);
    //     if (mensagem.codigo == 1700) {
    //       this.router.navigateByUrl('/usuarios');
    //     }
    //   },
    //   error: err => {
    //     console.log(err);
    //   }
    // });
  }

  onReset(): void {
    this.enviado = false;
    this.form.reset();
  }  
}