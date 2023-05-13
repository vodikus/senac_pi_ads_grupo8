import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const EMPRESTIMO_API = environment.backendUrl + '/api/emprestimos/';
@Injectable({
  providedIn: 'root'
})
export class EmprestimoService {

  constructor(private http: HttpClient) { }

  buscarEmprestimo(id: number): Observable<any> {
    return this.http.get(EMPRESTIMO_API + 'buscar/'+id);
  }

  buscarEmprestimos(): Observable<any> {
    return this.http.get(EMPRESTIMO_API + 'meus-emprestimos');
  }

  buscarEmprestados(): Observable<any> {
    return this.http.get(EMPRESTIMO_API + 'meus-emprestados');
  }

  solicitarEmprestimo(usuarioId: number, livroId: number, qtdDias: number): Observable<any> {
    let body = { "uid_dono": usuarioId, "lid": livroId, "qtd_dias": qtdDias};
    return this.http.post(EMPRESTIMO_API + 'solicitar', body);
  }
}
