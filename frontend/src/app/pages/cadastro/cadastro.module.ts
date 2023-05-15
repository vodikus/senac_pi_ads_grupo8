import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";

import { CadastroComponent } from "./cadastro.component";
import { AcessoComponent } from './acesso/acesso.component';
import { ConfirmacaoComponent } from './confirmacao/confirmacao.component';
import { EnderecoComponent } from '../usuarios/endereco/endereco.component';
import { CadastroRoutingModule } from "./cadastro.routing.module";
import { PadraoModule } from "src/app/_shared/commons/padrao.module";

@NgModule({
    imports: [
        CommonModule,
        CadastroRoutingModule,
        PadraoModule
    ],
    exports: [],
    declarations: [
        CadastroComponent, 
        AcessoComponent, 
        ConfirmacaoComponent, 
        EnderecoComponent, 
    ],
    providers: [],
})

export class CadastroModule {}

export { CadastroComponent };
