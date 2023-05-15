import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PadraoModule } from 'src/app/_shared/commons/padrao.module';

import { EmprestadosRoutingModule } from './emprestados-routing.module';
import { EmprestadosComponent } from './emprestados.component';
import { ListarEmprestadosComponent } from './listar-emprestados/listar-emprestados.component';
import { DetalheEmprestadoComponent } from './detalhe-emprestado/detalhe-emprestado.component';


@NgModule({
  declarations: [
    EmprestadosComponent,
    ListarEmprestadosComponent,
    DetalheEmprestadoComponent
  ],
  imports: [
    CommonModule,
    EmprestadosRoutingModule,
    PadraoModule
  ]
})
export class EmprestadosModule { }
