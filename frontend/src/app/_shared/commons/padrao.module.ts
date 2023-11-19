import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

import { BarRatingModule } from "ngx-bar-rating";
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { TagInputModule } from 'ngx-chips';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MomentModule } from 'ngx-moment';
import { NgSelectModule } from '@ng-select/ng-select';

import { BarraStatusComponent } from './barra-status/barra-status.component';
import { BarraUsuarioComponent } from './barra-usuario/barra-usuario.component';
import { BarraAcoesComponent } from './barra-acoes/barra-acoes.component';
import { BarraSocialComponent } from './barra-social/barra-social.component';
import { CaixaLivroComponent } from './caixa-livro/caixa-livro.component';
import { PopupComponent } from './popup/popup.component';
import { CaixaUsuarioComponent } from './caixa-usuario/caixa-usuario.component';
import { DetalheLivroComponent } from './detalhe-livro/detalhe-livro.component';

@NgModule({
  declarations: [
    BarraStatusComponent,
    BarraUsuarioComponent,
    BarraAcoesComponent,
    BarraSocialComponent,
    CaixaLivroComponent,
    PopupComponent,
    CaixaUsuarioComponent,
    DetalheLivroComponent
  ],
  imports: [
    CommonModule,
    RouterModule,
    BarRatingModule,
    ReactiveFormsModule, 
    FormsModule,
    TagInputModule, 
    BrowserAnimationsModule,
    MomentModule,
    NgSelectModule
  ],
  exports: [
    BarraStatusComponent,
    BarraUsuarioComponent,
    BarraAcoesComponent,
    BarraSocialComponent,
    CaixaLivroComponent,
    PopupComponent,
    CaixaUsuarioComponent,
    DetalheLivroComponent,
    ReactiveFormsModule, 
    FormsModule,
    TagInputModule, 
    BrowserAnimationsModule,
    MomentModule,
    NgSelectModule
  ],
  providers: [
     
  ]
})
export class PadraoModule { }
