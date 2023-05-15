import { Component, OnInit } from '@angular/core';
import { EmprestimoService } from 'src/app/_service/emprestimo.service';
import { Emprestimo } from 'src/app/_classes/emprestimo';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';

@Component({
  selector: 'app-detalhe-emprestimo',
  templateUrl: './detalhe-emprestimo.component.html',
  styleUrls: ['./detalhe-emprestimo.component.scss']
})
export class DetalheEmprestimoComponent implements OnInit {
  emprestimoId: number = 0;
  emprestimo: Emprestimo = new Emprestimo();
  emprestimoCarregado: boolean = false;

  constructor(private emprestimoService: EmprestimoService, private route: ActivatedRoute, private location: Location) { }

  ngOnInit(): void {
    this.emprestimoId = Number(this.route.snapshot.paramMap.get('eid'));
    this.carregaEmprestimo(this.emprestimoId);
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

  cancelarReserva(evento: string) {
    if (evento == "confirmar") {
      this.emprestimoService.desistirEmprestimo(this.emprestimoId).subscribe({
        next: data => {
          this.emprestimo = data;
          this.emprestimoCarregado = true;
        },
        error: err => {
          console.log(err);
        }
      });
      this.reloadPage();
    }
  }

  reloadPage(): void {
    window.location.reload();
  }  

}
