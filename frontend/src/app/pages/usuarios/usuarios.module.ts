import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UsuariosRoutingModule } from './usuarios-routing.module';
import { MeuPerfilComponent } from './meu-perfil/meu-perfil.component';
import { UsuariosComponent } from './usuarios.component';
import { PerfilComponent } from './perfil/perfil.component';
import { PadraoModule } from 'src/app/_shared/commons/padrao.module';

@NgModule({
  declarations: [
    MeuPerfilComponent,
    UsuariosComponent,
    PerfilComponent
  ],
  imports: [
    CommonModule,
    UsuariosRoutingModule,
    PadraoModule
  ]
})
export class UsuariosModule { }
