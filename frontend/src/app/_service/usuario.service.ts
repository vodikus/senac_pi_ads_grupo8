import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const USER_API = environment.apiUrl + '/usuarios/';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {

  constructor(private http: HttpClient) {}

  getUserProfile(): Observable<any> {
    return this.http.get(USER_API + 'meu-perfil');
  }

  buscarListaAmigos(): Observable<any> {
    return this.http.get(USER_API + 'amigos');
  }
  

}
