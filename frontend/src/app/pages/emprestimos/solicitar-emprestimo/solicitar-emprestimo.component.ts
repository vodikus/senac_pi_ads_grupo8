import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';
import { Livro } from 'src/app/_classes/livro';
import { LivroService } from 'src/app/_service/livro.service';
import { EmprestimoService } from 'src/app/_service/emprestimo.service';
import { MensagemPadrao } from 'src/app/_classes/mensagem-padrao';

type Detalhe = {emprestimoId: number};

@Component({
  selector: 'app-solicitar-emprestimo',
  templateUrl: './solicitar-emprestimo.component.html',
  styleUrls: ['./solicitar-emprestimo.component.scss']
})
export class SolicitarEmprestimoComponent {

  livro: Livro = new Livro();
  usuarioId: number = 0;
  qtdDias: number = 0;
  retorno: MensagemPadrao = new MensagemPadrao();

  constructor(
    private livroService: LivroService,
    private emprestimoService: EmprestimoService,
    private route: ActivatedRoute,
    private router: Router,
    private location: Location
  ) { }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('lid'));
    this.usuarioId = Number(this.route.snapshot.paramMap.get('uid'));
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

  selecionaQtdDias(dias: number): void {
    this.qtdDias = dias;
  }

  voltar(): void {
    this.location.back();
  }

  solicitarEmprestimo(): void {
    if (this.qtdDias > 0) {
      this.emprestimoService.solicitarEmprestimo(this.usuarioId, this.livro.lid, this.qtdDias).subscribe({
        next: data => {
          this.retorno = data;
          if (this.retorno.codigo==1300) {
            let detalhe = this.retorno.detalhe as unknown as Detalhe;
            this.router.navigateByUrl('/emprestimos/detalhe/' + detalhe.emprestimoId);
          } else {
            
          }
        },
        error: err => {
          console.log(err);
        }
      });
    } else {
      console.error("Clique nos dias!")
    }
  }
}
