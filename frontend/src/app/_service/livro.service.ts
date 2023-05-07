import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const LIVRO_API = environment.apiUrl + '/livros/';

@Injectable({
  providedIn: 'root'
})
export class LivroService {
  
  constructor(private http: HttpClient) { }

  buscarUltimasAtualizacoes(): Observable<any> {
    return this.http.get(LIVRO_API + 'listar-disponiveis?ordem=dh_atualizacao,desc');
  }
}
