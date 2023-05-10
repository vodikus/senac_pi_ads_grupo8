import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { BarRatingModule } from "ngx-bar-rating";

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { CadastroModule } from './pages/cadastro/cadastro.module';
import { MeuPerfilComponent } from './meu-perfil/meu-perfil.component';
import { AuthInterceptor } from './_interceptor/auth.interceptor';
import { MenuComponent } from './menu/menu.component';
import { AmigosComponent } from './pages/amigos/amigos.component';
import { HomeComponent } from './pages/home/home.component';
import { EmprestimosComponent } from './pages/emprestimos/emprestimos.component';
import { EmprestadosComponent } from './pages/emprestados/emprestados.component';
import { PesquisaComponent } from './pages/pesquisa/pesquisa.component';
import { FavoritosComponent } from './pages/favoritos/favoritos.component';
import { SuporteComponent } from './pages/suporte/suporte.component';
import { ChatComponent } from './pages/amigos/chat/chat.component';
import { BarraUsuarioComponent } from './commons/barra-usuario/barra-usuario.component';
import { BarraAcoesComponent } from './commons/barra-acoes/barra-acoes.component';
import { BarraSocialComponent } from './commons/barra-social/barra-social.component';
import { CaixaLivroComponent } from './commons/caixa-livro/caixa-livro.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    MeuPerfilComponent,
    MenuComponent,
    AmigosComponent,
    HomeComponent,
    EmprestimosComponent,
    EmprestadosComponent,
    PesquisaComponent,
    FavoritosComponent,
    SuporteComponent,
    ChatComponent,
    BarraUsuarioComponent,
    BarraAcoesComponent,
    BarraSocialComponent,
    CaixaLivroComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    CadastroModule,
    FormsModule,
    HttpClientModule,
    BarRatingModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
