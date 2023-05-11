import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

import { BarRatingModule } from "ngx-bar-rating";
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { BarraStatusComponent } from './barra-status/barra-status.component';
import { BarraUsuarioComponent } from './barra-usuario/barra-usuario.component';
import { BarraAcoesComponent } from './barra-acoes/barra-acoes.component';
import { BarraSocialComponent } from './barra-social/barra-social.component';
import { CaixaLivroComponent } from './caixa-livro/caixa-livro.component';
import { PopupComponent } from './popup/popup.component';

@NgModule({
  declarations: [
    BarraStatusComponent,
    BarraUsuarioComponent,
    BarraAcoesComponent,
    BarraSocialComponent,
    CaixaLivroComponent,
    PopupComponent
  ],
  imports: [
    CommonModule,
    RouterModule,
    BarRatingModule,
    ReactiveFormsModule, 
    FormsModule
  ],
  exports: [
    BarraStatusComponent,
    BarraUsuarioComponent,
    BarraAcoesComponent,
    BarraSocialComponent,
    CaixaLivroComponent,
    PopupComponent
  ]
})
export class PadraoModule { }
