import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EmprestimosComponent } from './emprestimos.component';
import { EmprestimosRoutingModule } from './emprestimos-routing.module';
import { ListarEmprestimosComponent } from './listar-emprestimos/listar-emprestimos.component';
import { PadraoModule } from 'src/app/_shared/commons/padrao.module';
import { GerenciarEmprestimoComponent } from './gerenciar-emprestimo/gerenciar-emprestimo.component';
import { DetalheEmprestimoComponent } from './detalhe-emprestimo/detalhe-emprestimo.component';

@NgModule({
  declarations: [
    ListarEmprestimosComponent,
    EmprestimosComponent,
    GerenciarEmprestimoComponent,
    DetalheEmprestimoComponent
  ],
  imports: [
    CommonModule,
    EmprestimosRoutingModule,
    PadraoModule
  ]
})
export class EmprestimosModule { }

export { EmprestimosComponent };