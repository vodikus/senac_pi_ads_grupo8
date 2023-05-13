import { Component, OnInit } from '@angular/core';
import { UsuarioService } from '../../../_service/usuario.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-meu-perfil',
  templateUrl: './meu-perfil.component.html',
  styleUrls: ['./meu-perfil.component.scss']
})
export class MeuPerfilComponent implements OnInit {
  perfil: any;
  IMG_SERVER = environment.backendUrl;

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.usuarioService.buscarMeuPerfil().subscribe({
      next: data => {
        this.perfil = data;
      },
      error: err => {
        console.log("Erro ao carregar perfil: " + err);
      }
    });
  }

}
