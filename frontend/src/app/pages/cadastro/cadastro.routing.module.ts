import { NgModule } from "@angular/core";
import { RouterModule, Routes } from "@angular/router";

import { CadastroComponent } from "./cadastro.component";
import { AcessoComponent } from "./acesso/acesso.component";
import { ConfirmacaoComponent } from "./confirmacao/confirmacao.component";

const cadastroRoutes: Routes = [
    {path: 'cadastro', component: CadastroComponent, children: [
        {path: 'acesso', component: AcessoComponent},
        {path: 'confirmacao', component: ConfirmacaoComponent},
    ]}
];

@NgModule({
    imports: [RouterModule.forChild(cadastroRoutes)],
    exports: [RouterModule]
})

export class CadastroRoutingModule {}