import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { EmprestimosComponent } from './emprestimos.component';
import { ListarEmprestimosComponent } from './listar-emprestimos/listar-emprestimos.component';
import { SolicitarEmprestimoComponent } from './solicitar-emprestimo/solicitar-emprestimo.component';
import { DetalheEmprestimoComponent } from './detalhe-emprestimo/detalhe-emprestimo.component';
import { GerenciarEmprestimoComponent } from './gerenciar-emprestimo/gerenciar-emprestimo.component';

const routes: Routes = [
  {
    path: 'emprestimos', component: EmprestimosComponent, canActivate: [AuthGuard], children: [
      { path: '', component: ListarEmprestimosComponent },
      { path: 'solicitar/:uid/:lid', component: SolicitarEmprestimoComponent },
      { path: 'detalhe/:eid', component: DetalheEmprestimoComponent },
      { path: 'gerenciar/:eid', component: GerenciarEmprestimoComponent }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmprestimosRoutingModule { }
