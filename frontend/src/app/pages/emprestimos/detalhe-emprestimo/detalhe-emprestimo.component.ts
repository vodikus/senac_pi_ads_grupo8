import { Component, OnInit } from '@angular/core';
import { EmprestimoService } from 'src/app/_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-detalhe-emprestimo',
  templateUrl: './detalhe-emprestimo.component.html',
  styleUrls: ['./detalhe-emprestimo.component.scss']
})
export class DetalheEmprestimoComponent implements OnInit {
  emprestimo: Emprestimo = new Emprestimo();
  emprestimoCarregado: boolean = false;

  constructor(private emprestimoService: EmprestimoService, private route: ActivatedRoute) { }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('eid'));
    this.carregaEmprestimo(id);
  }

  carregaEmprestimo(emprestimoId: number): void {
    this.emprestimoService.buscarEmprestimo(emprestimoId).subscribe({
      next: data => {
        this.emprestimo = data;
        this.emprestimoCarregado = true;
      },
      error: err => {
        console.log(err);
      }
    });
  }

}
