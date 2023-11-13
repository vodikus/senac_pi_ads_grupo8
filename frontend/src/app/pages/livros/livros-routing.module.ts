import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdicionarComponent } from './adicionar/adicionar.component';
import { LivrosComponent } from './livros.component';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { BuscarComponent } from './buscar/buscar.component';

const routes: Routes = [
  {
    path: 'livros', component: LivrosComponent, canActivate: [AuthGuard], children: [
      { path: 'adicionar', component: AdicionarComponent },
      { path: 'buscar', component: BuscarComponent }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LivrosRoutingModule { }
