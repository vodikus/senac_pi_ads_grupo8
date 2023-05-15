import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { MeuPerfilComponent } from './meu-perfil/meu-perfil.component';
import { PerfilComponent } from './perfil/perfil.component';
import { UsuariosComponent } from './usuarios.component';
import { EnderecoComponent } from "./endereco/endereco.component";
import { AvatarComponent } from "./avatar/avatar.component";
import { InteressesComponent } from './interesses/interesses.component';

const routes: Routes = [
  {
    path: 'usuarios', component: UsuariosComponent, canActivate: [AuthGuard], children: [
      { path: '', component: MeuPerfilComponent },
      { path: 'perfil/:uid', component: PerfilComponent },
      { path: 'avatar', component: AvatarComponent },
      { path: 'endereco', component: EnderecoComponent },
      { path: 'interesses', component: InteressesComponent }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UsuariosRoutingModule { }
