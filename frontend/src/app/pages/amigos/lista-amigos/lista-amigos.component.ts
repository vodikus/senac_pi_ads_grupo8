import { Component } from '@angular/core';
import { UsuarioService } from 'src/app/_service/usuario.service';

@Component({
  selector: 'app-lista-amigos',
  templateUrl: './lista-amigos.component.html',
  styleUrls: ['./lista-amigos.component.scss']
})
export class ListaAmigosComponent {
  amigos: any;

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.carregaAmigos();
  }

  carregaAmigos(): void {
    this.usuarioService.buscarListaAmigos().subscribe({
      next: data => {
        this.amigos = data;
      },
      error: err => {
        console.log(err);
      }
    });     
  }

}
