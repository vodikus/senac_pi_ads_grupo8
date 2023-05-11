import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { AmigosRoutingModule } from './amigos-routing.module';
import { AmigosComponent } from './amigos.component';
import { ChatComponent } from './chat/chat.component';
import { ListaAmigosComponent } from './lista-amigos/lista-amigos.component';


@NgModule({
  declarations: [
    AmigosComponent,
    ChatComponent,
    ListaAmigosComponent
  ],
  imports: [
    CommonModule,
    AmigosRoutingModule,
    FormsModule
  ]
})
export class AmigosModule { }

export { AmigosComponent };