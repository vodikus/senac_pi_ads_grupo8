import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Endereco } from '../_classes/endereco';


const ENDERECO_API = environment.backendUrl + '/api/enderecos/';
const OPENCEP_API = "https://opencep.com/v1/";
const APICEP_API = "https://cdn.apicep.com/file/apicep/";
const REPUBLICA_VIRTUAL_CEP_API = "http://cep.republicavirtual.com.br/web_cep.php?formato=json&cep=";


@Injectable({
  providedIn: 'root'
})
export class EnderecoService {

  constructor(private http: HttpClient) { }

  buscarCepOnline(cep: string, provedor: string): Endereco {
    let retorno: Endereco = new Endereco();
    switch (provedor) {
      case 'OPENCEP':
        this.openCep(cep.replace('-', '')).subscribe({
          next: data => {
            retorno.cep = data.cep;
            retorno.logradouro = data.logradouro;
            retorno.bairro = data.bairro;
            retorno.cidade = data.localidade;
            retorno.uf = data.uf;
          },
          error: err => {
            console.log(err);
          }
        });
        break;

      case 'APICEP':
        this.apiCep(cep).subscribe({
          next: data => {
            retorno.cep = data.code;
            retorno.logradouro = data.address;
            retorno.bairro = data.district;
            retorno.cidade = data.city;
            retorno.uf = data.state;
          },
          error: err => {
            console.log(err);
          }
        });
        break;

      case 'REPUBLICA_VIRTUAL':
        this.republicaVirtualCep(cep.replace('-', '')).subscribe({
          next: data => {
            retorno.cep = cep;
            retorno.logradouro = `${data.tipo_logradouro} ${data.logradouro}`;
            retorno.bairro = data.bairro;
            retorno.cidade = data.cidade;
            retorno.uf = data.uf;
          },
          error: err => {
            console.log(err);
          }
        });
        break;
    }
    return retorno;
  }

  openCep(cep: string): Observable<any> {
    return this.http.get(OPENCEP_API + cep);
  }

  apiCep(cep: string): Observable<any> {
    return this.http.get(APICEP_API + cep + ".json");
  }

  republicaVirtualCep(cep: string): Observable<any> {
    return this.http.get(REPUBLICA_VIRTUAL_CEP_API + cep);
  }

  buscarMeusEnderecos(): Observable<any> {
    return this.http.get(ENDERECO_API + 'listar');
  }

  cadastrarEndereco(endereco: Endereco): Observable<any> {
    return this.http.post(ENDERECO_API + 'adicionar', endereco);
  }

  deletarEndereco(id: number): Observable<any> {
    return this.http.delete(ENDERECO_API + 'deletar/'+id);
  }


}
