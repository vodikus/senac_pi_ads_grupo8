import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { of, Observable } from 'rxjs';
import { OlBook } from '../_classes/ol-book';

const OPENLIBRARY_API ="https://openlibrary.org/api/books";

@Injectable({
  providedIn: 'root'
})
export class OpenLibraryService {
  constructor(private http: HttpClient) { }

  buscarLivroPorISBN(isbn: string = ""): Observable<OlBook> {
    let resultado = new Observable<OlBook>;
    if ( isbn ) {
      resultado = this.http.get<OlBook>(OPENLIBRARY_API + '?bibkeys=ISBN:' + isbn + '&format=json&jscmd=data');           
    }     
    return resultado;
  }
}
