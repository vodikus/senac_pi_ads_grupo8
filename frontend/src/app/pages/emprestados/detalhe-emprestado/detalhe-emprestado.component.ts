import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { EmprestimoService } from 'src/app/_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';
import * as moment from "moment";
import { Previsao } from 'src/app/_classes/previsao';

@Component({
  selector: 'app-detalhe-emprestado',
  templateUrl: './detalhe-emprestado.component.html',
  styleUrls: ['./detalhe-emprestado.component.scss']
})
export class DetalheEmprestadoComponent implements OnInit {
  emprestimoId: number = 0;
  emprestimo: Emprestimo = new Emprestimo();
  emprestimoCarregado: boolean = false;
  erroCarregamento: boolean = false;
  mensagemErro: MensagemPadrao = new MensagemPadrao();
  hoje: Date = new Date();
  minDevolucao: Date = new Date();
  form: FormGroup = new FormGroup({
    retirada_prevista: new FormControl(''),
    devolucao_prevista: new FormControl('')
  });
  previsao: Previsao = new Previsao();
  enviado = false;

  constructor(private emprestimoService: EmprestimoService, private route: ActivatedRoute, private location: Location, private formBuilder: FormBuilder,) { }

  ngOnInit(): void {
    this.emprestimoId = Number(this.route.snapshot.paramMap.get('eid'));
    this.carregaEmprestimo(this.emprestimoId);
    this.form = this.formBuilder.group(
      {
        retirada_prevista: ['', Validators.required],
        devolucao_prevista: ['', Validators.required]
      }
    );
  }

  get f(): { [key: string]: AbstractControl } {
    return this.form.controls;
  }

  carregaEmprestimo(emprestimoId: number): void {
    this.emprestimoService.buscarEmprestimo(emprestimoId).subscribe({
      next: data => {
        this.emprestimo = data;
        this.emprestimoCarregado = true;
        this.minDevolucao = moment(new Date()).add(this.emprestimo.qtd_dias, 'days').toDate();
      },
      error: err => {
        console.log(err);
        this.mensagemErro = err.error;
        this.erroCarregamento = true;
      }
    });
  }

  agendaEntrega(): void {
    this.enviado = true;

    if (this.form.invalid) {
      return;
    }

    this.previsao = this.form.value;
    this.previsao.eid = this.emprestimoId;

    this.emprestimoService.previsaoEmprestimo(this.previsao).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1303) {
          this.reloadPage();
        } else {
          this.mensagemErro = mensagem;
          this.erroCarregamento = true;
        }
      },
      error: err => {
        console.log(err);
        this.mensagemErro = err.error;
        this.erroCarregamento = true;
      }
    });

  }

  
  confirmaEntrega(): void {
    this.emprestimoService.confirmaEmprestimo(this.emprestimoId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1304) {
          this.reloadPage();
        } else {
          this.mensagemErro = mensagem;
          this.erroCarregamento = true;
        }
      },
      error: err => {
        console.log(err);
        this.mensagemErro = err.error;
        this.erroCarregamento = true;
      }
    });
  }
  
  confirmaDevolucao(): void {
    this.emprestimoService.confirmaDevolucao(this.emprestimoId).subscribe({
      next: data => {
        let mensagem = data as MensagemPadrao;
        console.log(mensagem);
        if (mensagem.codigo == 1301) {
          this.reloadPage();
        } else {
          this.mensagemErro = mensagem;
          this.erroCarregamento = true;
        }
      },
      error: err => {
        console.log(err);
        this.mensagemErro = err.error;
        this.erroCarregamento = true;
      }
    });
  }

  calculaDevolucao(event: any): void {
    this.minDevolucao = moment(event.target.value).add(this.emprestimo.qtd_dias, 'days').toDate();
  }

  reloadPage(): void {
    window.location.reload();
  }

}
