import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { AuthGuard } from './_shared/auth.guard';
import { HomeComponent } from './pages/home/home.component';
import { LoginComponent } from './pages/login/login.component';
import { EmprestadosComponent } from './pages/emprestados/emprestados.component';
import { FavoritosComponent } from './pages/favoritos/favoritos.component';
import { PesquisaComponent } from './pages/pesquisa/pesquisa.component';
import { SuporteComponent } from './pages/suporte/suporte.component';
import { DenunciaComponent } from './pages/denuncia/denuncia.component'; 

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'emprestados', component: EmprestadosComponent, canActivate: [AuthGuard] },
  { path: 'favoritos', component: FavoritosComponent, canActivate: [AuthGuard] },
  { path: 'pesquisa', component: PesquisaComponent },
  { path: 'suporte', component: SuporteComponent, canActivate: [AuthGuard] },
  { path: 'denuncia/:id', component: DenunciaComponent, canActivate: [AuthGuard] },
  
  ];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
