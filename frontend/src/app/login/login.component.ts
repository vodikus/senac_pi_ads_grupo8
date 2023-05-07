import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../_service/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  form: any = {
    username: null,
    password: null
  };
  isLoggedIn = false;
  isLoginFailed = false;
  errorMessage = '';
  roles: string[] = [];

  constructor(private authService: AuthService, private router: Router) { }

  ngOnInit(): void {
    if (this.authService.isLoggedIn()) {
      this.isLoggedIn = true;
      this.router.navigate(['/meu-perfil']);
    }
  }

  onSubmit(): void {
    const { username, password } = this.form;

    this.authService.getToken(username, password).subscribe({
      next: data => {
        this.authService.saveData(data);

        this.isLoginFailed = false;
        this.isLoggedIn = true;

        this.router.navigateByUrl('/meu-perfil');
      },
      error: err => {
        console.log(err);
        this.errorMessage = err.error.mensagem;
        this.isLoginFailed = true;
      }
    });
  }

  reloadPage(): void {
    window.location.reload();
  }

}
