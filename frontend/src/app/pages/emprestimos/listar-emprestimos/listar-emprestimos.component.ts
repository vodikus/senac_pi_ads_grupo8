import { Component, OnInit } from '@angular/core';
import { EmprestimoService } from 'src/app/_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';

@Component({
  selector: 'app-listar-emprestimos',
  templateUrl: './listar-emprestimos.component.html',
  styleUrls: ['./listar-emprestimos.component.scss']
})
export class ListarEmprestimosComponent implements OnInit {
  emprestimos: Array<Emprestimo> = new Array<Emprestimo>();

  constructor(private emprestimoService: EmprestimoService) { }

  ngOnInit(): void {
    this.carregaEmprestimos();
  }

  carregaEmprestimos(): void {
    this.emprestimoService.buscarEmprestimos().subscribe({
      next: data => {
        this.emprestimos = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }  

}
