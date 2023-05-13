import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../_service/auth.service';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {
  autenticado: boolean = false;
  rota: string = "";

  constructor(private auth: AuthService, private router: Router) { }

  ngOnInit(): void {
    // this.autenticado = this.auth.isLoggedIn();
    this.autenticado = true;
  }

  pegarNivelUrl(nivel: number): string {
    const arvoreUrl = this.router.parseUrl(this.router.url);
    arvoreUrl.queryParams = {};
    arvoreUrl.fragment = null;
    if ( this.router.url != '/' ) {
      return arvoreUrl.root.children['primary'].segments[nivel].toString();
    }
    return "";
  }

}
