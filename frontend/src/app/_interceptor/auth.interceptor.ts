import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

  constructor() { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const authToken = localStorage.getItem("auth-token");

    if (authToken) {
      if ( 
        request.url.startsWith('https://openlibrary.org') || 
        request.url.startsWith('https://opencep.org') || 
        request.url.startsWith('https://cdn.apicep.com') || 
        request.url.startsWith('http://cep.republicavirtual.com.br') || 
        request.url.startsWith('https://brasilapi.com.br') 
      ) {
        return next.handle(request);  
      }

      const cloned = request.clone({        
        headers: request.headers.set("Authorization",
          "Bearer " + authToken)
      });

      return next.handle(cloned);
    }
    else {
      return next.handle(request);
    }
  }
}
