import { Injectable, LOCALE_ID } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Autor } from '../_classes/autor';

const AUTOR_API = environment.backendUrl + '/api/autores/';

@Injectable({
  providedIn: 'root'
})
export class AutorService {
  constructor(private http: HttpClient) { }

  buscarAutorPorNome(nome: string = ""): Observable<Autor[]> {
    let resultado = new Observable<Autor[]>;
    if ( nome )
      resultado = this.http.get<Autor[]>(AUTOR_API + 'buscar-por-nome?nome_autor=' + nome + '&ordem=nome_autor,asc');
    return resultado;
  }

}
