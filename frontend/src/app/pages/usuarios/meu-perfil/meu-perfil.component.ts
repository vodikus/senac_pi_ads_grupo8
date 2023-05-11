import { Component, OnInit } from '@angular/core';
import { UsuarioService } from '../../../_service/usuario.service';

@Component({
  selector: 'app-meu-perfil',
  templateUrl: './meu-perfil.component.html',
  styleUrls: ['./meu-perfil.component.scss']
})
export class MeuPerfilComponent implements OnInit {
  perfil: any;

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.usuarioService.buscarMeuPerfil().subscribe({
      next: data => {
        this.perfil = data[0];
      },
      error: err => {
        console.log(err);
      }
    });
  }

}
