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
  form: any = {
    mensagem: null
  };

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
    const id = Number(this.route.snapshot.paramMap.get('uid'));
    this.usuarioService.buscarPerfil(id).subscribe(data => this.usuario = data[0]);
    this.chatService.buscarListaAmigos(id)
      .subscribe(data => this.mensagens = data);
  }

  enviarMensagem(): void {
    const { mensagem } = this.form;
    const id = Number(this.route.snapshot.paramMap.get('uid'));
    console.log(mensagem)
    console.log(id)
    this.chatService.enviarMensagem(id, mensagem).subscribe({
      next: data => {
        console.log(data);
        this.reloadPage();
      },
      error: err => {
        console.log(err);
      }
    });
  }

  voltar(): void {
    this.location.back();
  }

    
  reloadPage(): void {
    window.location.reload();
  }  
}
