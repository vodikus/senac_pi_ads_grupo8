import { Injectable } from '@angular/core';
import { HttpClient, HttpRequest, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Cadastro } from '../_classes/cadastro';

const USER_API = environment.backendUrl + '/api/usuarios/';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {

  constructor(private http: HttpClient) { }

  buscarMeuPerfil(): Observable<any> {
    return this.http.get(USER_API + 'meu-perfil');
  }

  buscarPerfil(id: Number): Observable<any> {
    return this.http.get(USER_API + 'buscar/' + id);
  }

  buscarListaAmigos(): Observable<any> {
    return this.http.get(USER_API + 'amigos');
  }

  validarEmail(email: string): Observable<any> {
    let body = { 'email': email };
    return this.http.post(USER_API + 'validar', body);
  }

  validarCpf(cpf: string): Observable<any> {
    let body = { 'cpf': cpf };
    return this.http.post(USER_API + 'validar', body);
  }

  adicionarUsuario(cadastro: Cadastro): Observable<any> {
    return this.http.post(USER_API + 'adicionar', cadastro);
  }

  adicionarAmigo(uid: number): Observable<any> {
    return this.http.post(USER_API + 'adicionarAmigo/' + uid, '');
  }


  removerAmigo(uid: number): Observable<any> {
    return this.http.delete(USER_API + 'removerAmigo/' + uid);
  }

  bloquearUsuario(uid: number): Observable<any> {
    return this.http.post(USER_API + 'bloquearUsuario/' + uid, '');
  }


  desbloquearUsuario(uid: number): Observable<any> {
    return this.http.delete(USER_API + 'desbloquearUsuario/' + uid);
  }

  buscarMeusAssuntos(): Observable<any> {
    return this.http.get(USER_API + 'listar-assuntos');
  }

  atualizarFoto(arquivo: File): Observable<HttpEvent<any>> {
    const formData: FormData = new FormData();

    formData.append('imagem', arquivo);

    const req = new HttpRequest('POST', USER_API + 'enviarFoto', formData, {
      reportProgress: true,
      responseType: 'json'
    });

    return this.http.request(req);
  }

  adicionarAssunto(iid: number): Observable<any> {
    let body = `[{"iid": "${iid}"}]`;
    return this.http.post(USER_API + 'vincularAssunto', body);
  }

  removerAssunto(iid: number): Observable<any> {
    let body = `[{"iid": "${iid}"}]`;
    return this.http.post(USER_API + 'desvincularAssunto', body);
  }

}
