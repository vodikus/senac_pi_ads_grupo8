import { Component, Input, OnInit } from '@angular/core';
import { Usuario } from 'src/app/_classes/usuario'
import { UsuarioService } from 'src/app/_service/usuario.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-barra-usuario',
  templateUrl: './barra-usuario.component.html',
  styleUrls: ['./barra-usuario.component.scss']
})
export class BarraUsuarioComponent implements OnInit {
  IMG_SERVER = environment.backendUrl;
  @Input('uid') uid!: number;
  @Input('mostraChat') mostraChat: boolean = true;
  @Input('mostraPerfil') mostraPerfil: boolean = true;
  @Input('tamanho') tamanho: number = 0;


  usuario: Usuario = new Usuario();

  constructor(private usuarioService: UsuarioService) { }

  ngOnInit(): void {
    this.usuarioService.buscarPerfil(this.uid).subscribe({
      next: data => {
        this.usuario = data;
      },
      error: err => {
        console.log(err);
      }
    }); 
  }

}
