import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Assunto } from '../_classes/assunto';

const ASSUNTO_API = environment.backendUrl + '/api/assuntos/';

@Injectable({
  providedIn: 'root'
})
export class AssuntoService {

  constructor(private http: HttpClient) { }

  // buscarPorNome(nome: string): Observable<any> {
  //   return this.http.get(ASSUNTO_API + 'buscar-por-nome?nome_assunto=' + nome);
  // }
  buscarPorNome(nome: string = ""): Observable<Assunto[]> {
    let resultado = new Observable<Assunto[]>;
    if ( nome )
      resultado = this.http.get<Assunto[]>(ASSUNTO_API + 'buscar-por-nome?nome_assunto=' + nome + '&ordem=nome_assunto,asc');
    return resultado;
  }

}
