import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-confirmacao',
  templateUrl: './confirmacao.component.html',
  styleUrls: ['./confirmacao.component.scss']
})
export class ConfirmacaoComponent implements OnInit {
  nome: string = "";
  apelido: string = "";
  
  constructor(private router: Router) { }

  ngOnInit(): void {
    this.nome = localStorage.getItem("usuario-nome") as unknown as string;
    this.apelido = localStorage.getItem("usuario-apelido") as unknown as string;
  }

  onSubmit(): void {
    this.router.navigateByUrl('/cadastro/endereco');
  }



}
