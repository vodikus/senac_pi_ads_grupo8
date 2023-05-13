import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import * as moment from "moment";
import { environment } from '../../environments/environment';

const AUTH_API = environment.backendUrl + '/api/auth/';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/json' })
};

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  constructor(private http: HttpClient) { }

  getToken(username: string, password: string): Observable<any> {
    let body = `{"username":"${username}","password":"${password}"}`;
    return this.http.post(AUTH_API + 'getToken', body, httpOptions);
  }

  clean(): void {
    localStorage.clear();
  }

  public saveData(data: any): void {
    localStorage.removeItem('auth-token');
    localStorage.removeItem('auth-expires-at');
    const expiresAt = moment().add(data.expires_in, 'second');

    localStorage.setItem('auth-token', data.access_token);
    localStorage.setItem("auth-expires-at", JSON.stringify(expiresAt.valueOf()));
  }

  logout() {
    localStorage.removeItem('auth-token');
    localStorage.removeItem('auth-expires-at');
  }

  public isLoggedIn() {
    return moment().isBefore(this.getExpiration());
  }

  isLoggedOut() {
    return !this.isLoggedIn();
  }

  getExpiration() {
    const expiration = localStorage.getItem("auth-expires-at") as string;
    const expiresAt = JSON.parse(expiration);
    return moment(expiresAt);
  }
}

