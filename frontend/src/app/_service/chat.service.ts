import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const CHAT_API = environment.apiUrl + '/chat/';

@Injectable({
  providedIn: 'root'
})
export class ChatService {

  constructor(private http: HttpClient) { }

  buscarListaAmigos(id: Number): Observable<any> {
    return this.http.get(CHAT_API + 'listar/' + id);
  }
    
}
