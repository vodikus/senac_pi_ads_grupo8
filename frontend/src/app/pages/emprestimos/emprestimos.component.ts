import { Component, OnInit } from '@angular/core';
import { EmprestimoService } from '../../_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';

@Component({
  selector: 'app-emprestimos',
  templateUrl: './emprestimos.component.html',
  styleUrls: ['./emprestimos.component.scss']
})
export class EmprestimosComponent implements OnInit {
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
