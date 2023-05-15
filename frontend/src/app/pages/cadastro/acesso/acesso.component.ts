import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { UsuarioService } from 'src/app/_service/usuario.service';
import Validation from 'src/app/_classes/validation';
import { Router } from '@angular/router';
import { Cadastro } from 'src/app/_classes/cadastro';
import { CadastroValidation } from 'src/app/_classes/cadastro-validation';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';
import { AuthService } from 'src/app/_service/auth.service';

type Detalhe = { usuarioId: number };

@Component({
  selector: 'app-acesso',
  templateUrl: './acesso.component.html',
  styleUrls: ['./acesso.component.scss']
})
export class AcessoComponent implements OnInit {
  form: FormGroup = new FormGroup({
    email: new FormControl(''),
    senha: new FormControl(''),
    confirmeSenha: new FormControl(''),
    cpf: new FormControl(''),
    nome: new FormControl(''),
    apelido: new FormControl(''),
    nascimento: new FormControl(''),
    sexo: new FormControl('')
  });
  enviado = false;
  cadastro: Cadastro = new Cadastro();

  constructor(private formBuilder: FormBuilder,
    private usuarioService: UsuarioService,
    private router: Router,
    private authService: AuthService) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group(
      {
        email: ['', [Validators.required, Validators.email], [CadastroValidation.emailJaExiste(this.usuarioService)]],
        senha: [
          '',
          [
            Validators.required,
            Validators.minLength(6),
            Validators.maxLength(40)
          ]
        ],
        confirmeSenha: ['', Validators.required],
        apelido: ['', Validators.required],
        cpf: ['', [Validators.required, Validators.maxLength(11)], [CadastroValidation.cpfJaExiste(this.usuarioService)]],
        nome: [
          '',
          [
            Validators.required,
            Validators.minLength(2)
          ]
        ],
        nascimento: ['', Validators.required],
        sexo: ['', Validators.required],
      },
      {
        validators: [Validation.match('senha', 'confirmeSenha')]
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

    this.cadastro = this.form.value;
    this.persistirCadastro(this.cadastro);

    this.router.navigateByUrl('/cadastro/confirmacao');
  }

  onReset(): void {
    this.enviado = false;
    this.form.reset();
  }

  persistirCadastro(cadastro: Cadastro): void {
    delete cadastro['confirmeSenha'];
    this.usuarioService.adicionarUsuario(cadastro).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1200) {
          this.autenticarUsuario(cadastro.email, cadastro.senha);
          localStorage.setItem('usuario-apelido', cadastro.apelido as unknown as string);
        }
      },
      error: err => {
        console.log(err);
      }
    });
  }

  private autenticarUsuario(usuario: string, senha: string): void {
    this.authService.getToken(usuario, senha).subscribe({
      next: data => {
        this.authService.saveData(data);
      },
      error: err => {
        console.log(err);
      }
    });
  }

}
