import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const USER_API = environment.backendUrl + '/api/usuarios/';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {

  constructor(private http: HttpClient) {}

  buscarMeuPerfil(): Observable<any> {
    return this.http.get(USER_API + 'meu-perfil');
  }

  buscarPerfil(id: Number): Observable<any> {
    return this.http.get(USER_API + 'buscar/' + id);
  }

  buscarListaAmigos(): Observable<any> {
    return this.http.get(USER_API + 'amigos');
  }
  

}
