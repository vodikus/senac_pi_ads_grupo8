import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const ASSUNTO_API = environment.backendUrl + '/api/assuntos/';

@Injectable({
  providedIn: 'root'
})
export class AssuntoService {

  constructor(private http: HttpClient) { }

  buscarPorNome(nome: string): Observable<any> {
    return this.http.get(ASSUNTO_API + 'buscar-por-nome?nome_assunto=' + nome);
  }

}
