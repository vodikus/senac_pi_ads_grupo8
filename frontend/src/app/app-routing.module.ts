import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { AuthGuard } from './_shared/auth.guard';
import { HomeComponent } from './pages/home/home.component';
import { LoginComponent } from './login/login.component';
import { MeuPerfilComponent } from './meu-perfil/meu-perfil.component';
import { AmigosComponent } from './pages/amigos/amigos.component';
import { ChatComponent } from './pages/amigos/chat/chat.component';
import { EmprestadosComponent } from './pages/emprestados/emprestados.component';
import { EmprestimosComponent } from './pages/emprestimos/emprestimos.component';
import { FavoritosComponent } from './pages/favoritos/favoritos.component';
import { PesquisaComponent } from './pages/pesquisa/pesquisa.component';
import { SuporteComponent } from './pages/suporte/suporte.component';
import { DenunciaComponent } from './pages/denuncia/denuncia.component'; 

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'meu-perfil', component: MeuPerfilComponent, canActivate: [AuthGuard] },
  { path: 'amigos', component: AmigosComponent, canActivate: [AuthGuard] },
  { path: 'chat/:id', component: ChatComponent, canActivate: [AuthGuard] },
  // { path: 'amigos', component: AmigosComponent, canActivate: [AuthGuard],children: [
  //   {path: 'chat/:id', component: ChatComponent}
  // ] },
  { path: 'emprestados', component: EmprestadosComponent, canActivate: [AuthGuard] },
  { path: 'emprestimos', component: EmprestimosComponent, canActivate: [AuthGuard] },
  { path: 'favoritos', component: FavoritosComponent, canActivate: [AuthGuard] },
  { path: 'pesquisa', component: PesquisaComponent },
  { path: 'suporte', component: SuporteComponent, canActivate: [AuthGuard] },
  { path: 'denuncia', component: DenunciaComponent, canActivate: [AuthGuard] },
  
  ];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
