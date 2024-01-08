import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { config } from '@app/core/app-config';
import { Observable, catchError, map } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {
  private readonly loginUrl: string = `${config.API_URL}/login`;
  private readonly logoutUrl: string = `${config.API_URL}/logout`;
  private readonly currentUserUrl: string = `${config.API_URL}/me`;

  constructor(
    private readonly _http: HttpClient,
    private readonly _router: Router,
  ) { }

  public login(
    email: string,
    password: string,
    redirectUrl?: string
  ): Observable<Error> {
    return this._http
      .post<any>(this.loginUrl, { email, password })
      .pipe(map((res) => res.data));
  }

}
