import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";

import { CadastroComponent } from "./cadastro.component";
import { DadosacessoComponent } from './dadosacesso/dadosacesso.component';
import { DadospessoaisComponent } from './dadospessoais/dadospessoais.component';
import { EnderecoComponent } from './endereco/endereco.component';
import { PerfilComponent } from './perfil/perfil.component';
import { CadastroRoutingModule } from "./cadastro.routing.module";

@NgModule({
    imports: [
        CommonModule,
        CadastroRoutingModule
    ],
    exports: [],
    declarations: [
        CadastroComponent, 
        DadosacessoComponent, 
        DadospessoaisComponent, 
        EnderecoComponent, 
        PerfilComponent
    ],
    providers: [],
})

export class CadastroModule {}

export { CadastroComponent };
