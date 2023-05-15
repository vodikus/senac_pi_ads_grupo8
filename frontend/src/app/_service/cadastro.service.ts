import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CadastroService {
  private cadastro = new BehaviorSubject<any>({});
  cadastro$ = this.cadastro.asObservable();

  sincronizaCadastro(cadastro: any) {
    this.cadastro.next(cadastro);
  }

}
