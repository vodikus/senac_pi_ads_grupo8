import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { EnderecoService } from 'src/app/_service/endereco.service';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';
import { Endereco } from 'src/app/_classes/endereco';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';

@Component({
  selector: 'app-endereco',
  templateUrl: './endereco.component.html',
  styleUrls: ['./endereco.component.scss']
})
export class EnderecoComponent implements OnInit {
  form: FormGroup = new FormGroup({
    cep: new FormControl(''),
    logradouro: new FormControl(''),
    numero: new FormControl(''),
    complemento: new FormControl(''),
    bairro: new FormControl(''),
    cidade: new FormControl(''),
    uf: new FormControl('')
  });
  enviado = false;
  endereco: Endereco = new Endereco();

  constructor(private formBuilder: FormBuilder, private enderecoService: EnderecoService, private router: Router) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group(
      {
        cep: ['', [Validators.required, Validators.maxLength(9)]],
        logradouro: ['', [Validators.required]],
        numero: ['', Validators.required],
        bairro: ['', Validators.required],
        complemento: [''],
        cidade: ['', Validators.required],
        uf: ['', Validators.required],
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

    this.endereco = this.form.value;

    console.log(JSON.stringify(this.form.value, null, 2));

    this.enderecoService.cadastrarEndereco(this.endereco).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1700) {
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

  buscaCep(event: any): void {
    let retorno: Endereco = this.enderecoService.buscarCepOnline(event.target.value, environment.cepProvider);
    this.form.controls['logradouro'].setValue(retorno.logradouro);
    this.form.controls['bairro'].setValue(retorno.bairro);
    this.form.controls['cidade'].setValue(retorno.cidade);
    this.form.controls['estado'].setValue(retorno.uf);
  }

}
