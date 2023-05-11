import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { UsuarioService } from '../../../_service/usuario.service';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.scss']
})
export class PerfilComponent implements OnInit  {
  perfil: any;

  constructor(private usuarioService: UsuarioService, private route: ActivatedRoute,) { }

  ngOnInit(): void {
    const uid = Number(this.route.snapshot.paramMap.get('uid'));
    this.usuarioService.buscarPerfil(uid).subscribe({
      next: data => {
        this.perfil = data[0];
      },
      error: err => {
        console.log(err);
      }
    });
  }

}
