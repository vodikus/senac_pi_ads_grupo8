import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const CHAT_API = environment.backendUrl + '/api/chat/';

@Injectable({
  providedIn: 'root'
})
export class ChatService {

  constructor(private http: HttpClient) { }

  buscarListaAmigos(id: Number): Observable<any> {
    return this.http.get(CHAT_API + 'listar/' + id);
  }

  enviarMensagem(id: Number, mensagem: string): Observable<any> {    
    let body = { 'uid_amigo': id, 'mensagem': mensagem};
    return this.http.post(CHAT_API + 'enviar', body);
  }

}
