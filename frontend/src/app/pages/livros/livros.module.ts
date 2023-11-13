import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { LivrosRoutingModule } from './livros-routing.module';
import { AdicionarComponent } from './adicionar/adicionar.component';
import { LivrosComponent } from './livros.component';
import { PadraoModule } from 'src/app/_shared/commons/padrao.module';
import { BuscarComponent } from './buscar/buscar.component';


@NgModule({
  declarations: [
    AdicionarComponent,
    LivrosComponent,
    BuscarComponent
  ],
  imports: [
    CommonModule,
    LivrosRoutingModule,
    PadraoModule
  ]
})
export class LivrosModule { }
