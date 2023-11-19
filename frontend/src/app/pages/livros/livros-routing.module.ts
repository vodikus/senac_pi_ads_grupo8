import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdicionarComponent } from './adicionar/adicionar.component';
import { VincularComponent } from './vincular/vincular.component';
import { LivrosComponent } from './livros.component';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { BuscarComponent } from './buscar/buscar.component';
import { DetalheComponent } from './detalhe/detalhe.component';

const routes: Routes = [
  {
    path: 'livros', component: LivrosComponent, canActivate: [AuthGuard], children: [
      { path: 'adicionar', component: AdicionarComponent },
      { path: 'vincular/:lid', component: VincularComponent },
      { path: 'buscar', component: BuscarComponent },
      { path: 'detalhe/:lid', component: DetalheComponent}
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LivrosRoutingModule { }
