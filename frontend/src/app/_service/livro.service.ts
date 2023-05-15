import { Injectable, LOCALE_ID } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const LIVRO_API = environment.backendUrl + '/api/livros/';

@Injectable({
  providedIn: 'root'
})
export class LivroService {
  
  constructor(private http: HttpClient) { }

  buscarUltimasAtualizacoes(): Observable<any> {
    return this.http.get(LIVRO_API + 'listar-disponiveis?ordem=dh_atualizacao,desc');
  }

  buscarLivrosPorUsuario(uid: number): Observable<any> {
    return this.http.get(LIVRO_API + 'buscar-por-usuario?uid='+uid);
  }

  buscarLivrosPorAssunto(termo: string): Observable<any> {
    return this.http.get(LIVRO_API + 'buscar-por-assunto?nome_assunto='+termo);
  }

  buscarLivrosPorTitulo(termo: string): Observable<any> {
    return this.http.get(LIVRO_API + 'buscar-por-titulo?titulo='+termo);
  }

  buscarLivrosPorAutor(termo: string): Observable<any> {
    return this.http.get(LIVRO_API + 'buscar-por-autor?nome_autor='+termo);
  }

  buscarMeusLivros(): Observable<any> {
    return this.http.get(LIVRO_API + 'meus-livros');
  }

  buscarLivrosPorId(id: number): Observable<any> {
    return this.http.get(LIVRO_API + 'buscar-por-id?id='+id);
  }

  listarFavoritos(): Observable<any> {
    return this.http.get(LIVRO_API + 'favoritos');
  }
  
  adicionarFavorito(lid: number, uid_dono: number) {
    let body = {'lid': lid, 'uid_dono': uid_dono };
    return this.http.post(LIVRO_API + 'adicionarFavorito', body);
  }
  
  removerFavorito(lid: number, uid_dono: number) {
    let body = {'lid': lid, 'uid_dono': uid_dono };
    return this.http.post(LIVRO_API + 'removerFavorito', body);
  }
}
