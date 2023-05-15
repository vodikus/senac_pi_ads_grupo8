import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { DetalheEmprestadoComponent } from './detalhe-emprestado/detalhe-emprestado.component';
import { EmprestadosComponent } from './emprestados.component';
import { ListarEmprestadosComponent } from './listar-emprestados/listar-emprestados.component';

const routes: Routes = [
  {
    path: 'emprestados', component: EmprestadosComponent, canActivate: [AuthGuard], children: [
      { path: '', component: ListarEmprestadosComponent },
      { path: 'detalhe/:eid', component: DetalheEmprestadoComponent },
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmprestadosRoutingModule { }
