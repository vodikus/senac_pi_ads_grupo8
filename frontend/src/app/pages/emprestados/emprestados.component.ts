import { Component, OnInit } from '@angular/core';
import { EmprestimoService } from '../../_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';

@Component({
  selector: 'app-emprestados',
  templateUrl: './emprestados.component.html',
  styleUrls: ['./emprestados.component.scss']
})
export class EmprestadosComponent implements OnInit {
  emprestimos: Array<Emprestimo> = new Array<Emprestimo>();

  constructor(private emprestimoService: EmprestimoService) { }

  ngOnInit(): void {
    this.carregaEmprestimos();
  }

  carregaEmprestimos(): void {
    this.emprestimoService.buscarEmprestados().subscribe({
      next: data => {
        this.emprestimos = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }  

}
