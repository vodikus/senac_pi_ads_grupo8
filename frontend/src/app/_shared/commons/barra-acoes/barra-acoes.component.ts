import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-barra-acoes',
  templateUrl: './barra-acoes.component.html',
  styleUrls: ['./barra-acoes.component.scss']
})
export class BarraAcoesComponent implements OnInit {
  @Input('livroId') livroId: number = 0;
  @Input('usuarioId') usuarioId: number = 0;
  @Input('emprestimoId') emprestimoId: number = 0;
  @Input('mostrarDetalhes') mostrarDetalhes: boolean = true;
  @Input('mostrarSolicitar') mostrarSolicitar: boolean = true;

  constructor() { }

  ngOnInit(): void {
  }

}
