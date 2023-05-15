import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AmigosModule } from './pages/amigos/amigos.module';
import { CadastroModule } from './pages/cadastro/cadastro.module';
import { EmprestimosModule } from './pages/emprestimos/emprestimos.module';
import { EmprestadosModule } from './pages/emprestados/emprestados.module';
import { PadraoModule } from './_shared/commons/padrao.module';
import { UsuariosModule } from './pages/usuarios/usuarios.module';

import { AuthInterceptor } from './_interceptor/auth.interceptor';

import { LoginComponent } from './pages/login/login.component';
import { MenuComponent } from './menu/menu.component';
import { HomeComponent } from './pages/home/home.component';
import { PesquisaComponent } from './pages/pesquisa/pesquisa.component';
import { FavoritosComponent } from './pages/favoritos/favoritos.component';
import { SuporteComponent } from './pages/suporte/suporte.component';
import { DenunciaComponent } from './pages/denuncia/denuncia.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    MenuComponent,
    HomeComponent,
    PesquisaComponent,
    FavoritosComponent,
    SuporteComponent,
    DenunciaComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    CadastroModule,
    FormsModule,
    HttpClientModule,
    EmprestadosModule,
    EmprestimosModule,
    PadraoModule,
    AmigosModule,
    UsuariosModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
