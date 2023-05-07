import { Component, Input, OnInit } from '@angular/core';
import { Usuario } from '../../_classes/usuario'
import { UsuarioService } from 'src/app/_service/usuario.service';

@Component({
  selector: 'app-barra-usuario',
  templateUrl: './barra-usuario.component.html',
  styleUrls: ['./barra-usuario.component.css']
})
export class BarraUsuarioComponent implements OnInit {
  @Input('uid') uid!: number;
  usuario!: Usuario;

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.usuarioService.buscarPerfil(this.uid).subscribe({
      next: data => {
        this.usuario = data[0];
      },
      error: err => {
        console.log(err);
      }
    }); 
  }

}