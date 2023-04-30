import { NgModule } from "@angular/core";
import { RouterModule, Routes } from "@angular/router";

import { CadastroComponent } from "./cadastro.component";
import { DadosacessoComponent } from "./dadosacesso/dadosacesso.component";
import { DadospessoaisComponent } from "./dadospessoais/dadospessoais.component";
import { EnderecoComponent } from "./endereco/endereco.component";
import { PerfilComponent } from "./perfil/perfil.component";

const cadastroRoutes: Routes = [
    {path: 'cadastro', component: CadastroComponent, children: [
        {path: 'dadosacesso', component: DadosacessoComponent},
        {path: 'dadospessoais', component: DadospessoaisComponent},
        {path: 'endereco', component: EnderecoComponent},
        {path: 'perfil', component: PerfilComponent},
    ]}
];

@NgModule({
    imports: [RouterModule.forChild(cadastroRoutes)],
    exports: [RouterModule]
})

export class CadastroRoutingModule {}