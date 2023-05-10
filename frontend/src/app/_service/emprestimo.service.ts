import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const EMPRESTIMO_API = environment.apiUrl + '/emprestimos/';
@Injectable({
  providedIn: 'root'
})
export class EmprestimoService {

  constructor(private http: HttpClient) { }

  buscarEmprestimos(): Observable<any> {
    return this.http.get(EMPRESTIMO_API + 'meus-emprestimos');
  }
}
