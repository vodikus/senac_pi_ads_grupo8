import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../_service/auth.service';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.css']
})
export class MenuComponent implements OnInit {

  constructor(private auth: AuthService, private router: Router) { }

  autenticado: boolean = false;

  ngOnInit(): void {
    // this.autenticado = this.auth.isLoggedIn();
    this.autenticado = true;
  }

}
