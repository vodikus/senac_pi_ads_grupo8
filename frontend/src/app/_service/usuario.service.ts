import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

const USER_API = 'http://clube-backend/api/usuarios/';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {

  constructor(private http: HttpClient) {}

  getUserProfile(): Observable<any> {
    return this.http.get(USER_API + 'meu-perfil');
  }
  

}
