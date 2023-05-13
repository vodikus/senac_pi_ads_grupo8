import { Component, Input, OnInit } from '@angular/core';
import { Usuario } from 'src/app/_classes/usuario';
import { UsuarioService } from 'src/app/_service/usuario.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-caixa-usuario',
  templateUrl: './caixa-usuario.component.html',
  styleUrls: ['./caixa-usuario.component.scss']
})
export class CaixaUsuarioComponent implements OnInit {
  @Input('usuarioId') uid: number = 0;
  IMG_SERVER = environment.backendUrl;
  perfil: Usuario = new Usuario;  

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.carregarUsuario(this.uid);
  }

  carregarUsuario(uid: number): void {
    this.usuarioService.buscarPerfil(uid).subscribe({
      next: data => {
        this.perfil = data;
      },
      error: err => {
        console.log("Erro ao carregar perfil: " + err);
      }
    });    
  }

}
