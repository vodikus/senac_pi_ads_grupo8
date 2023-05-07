import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';
import { ChatService } from '../../../_service/chat.service'
import { UsuarioService } from '../../../_service/usuario.service'

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.scss']
})
export class ChatComponent implements OnInit {
  mensagens: any | undefined;
  usuario: any | undefined;

  constructor(
    private route: ActivatedRoute,
    private chatService: ChatService,
    private usuarioService: UsuarioService,
    private location: Location
  ) { }

  ngOnInit(): void {
    this.pegarMensagens();
  }

  pegarMensagens(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.usuarioService.buscarPerfil(id).subscribe(data => this.usuario = data[0]);
    this.chatService.buscarListaAmigos(id)
      .subscribe(data => this.mensagens = data);
  }

  voltar(): void {
    this.location.back();
  }
}
