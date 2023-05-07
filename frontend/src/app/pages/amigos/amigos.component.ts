import { Component, OnInit } from '@angular/core';
import { UsuarioService } from '../../_service/usuario.service';

@Component({
  selector: 'app-amigos',
  templateUrl: './amigos.component.html',
  styleUrls: ['./amigos.component.css']
})
export class AmigosComponent implements OnInit {
  amigos: any;

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
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
