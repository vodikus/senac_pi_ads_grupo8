import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UsuariosRoutingModule } from './usuarios-routing.module';
import { MeuPerfilComponent } from './meu-perfil/meu-perfil.component';
import { UsuariosComponent } from './usuarios.component';
import { PerfilComponent } from './perfil/perfil.component';
import { PadraoModule } from 'src/app/_shared/commons/padrao.module';
import { AvatarComponent } from './avatar/avatar.component';
import { InteressesComponent } from './interesses/interesses.component';

@NgModule({
  declarations: [
    MeuPerfilComponent,
    UsuariosComponent,
    PerfilComponent,
    AvatarComponent,
    InteressesComponent
  ],
  imports: [
    CommonModule,
    UsuariosRoutingModule,
    PadraoModule
  ]
})
export class UsuariosModule { }
