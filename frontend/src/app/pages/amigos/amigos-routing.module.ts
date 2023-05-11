import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from 'src/app/_shared/auth.guard';
import { AmigosComponent } from './amigos.component';
import { ChatComponent } from './chat/chat.component';
import { ListaAmigosComponent } from './lista-amigos/lista-amigos.component';

const routes: Routes = [
  {
    path: 'amigos', component: AmigosComponent, canActivate: [AuthGuard], children: [
      { path: '', component: ListaAmigosComponent },
      { path: 'chat/:uid', component: ChatComponent }
    ]  
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AmigosRoutingModule { }
