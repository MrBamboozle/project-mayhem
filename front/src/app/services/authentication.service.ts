import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { config } from '@app/core/app-config';
import { LoginRequest, LoginResponse, RefreshTokenResponse } from '@app/shared/models/login';
import { User } from '@app/shared/models/user';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { Observable, catchError, map, take, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {
  private readonly loginUrl: string = `${config.API_URL}/login`;
  private readonly logoutUrl: string = `${config.API_URL}/logout`;
  private readonly refreshTokenUrl: string = `${config.API_URL}/refresh-token`;
  private readonly currentUserUrl: string = `${config.API_URL}/me`;

  constructor(
    private readonly _http: HttpClient,
    private readonly _router: Router,
    private readonly userStore: UserStoreService
  ) { }

  public login(
    request: LoginRequest,
    redirectUrl?: string
  ): Observable<LoginResponse> {
    return this._http
      .post<LoginResponse>(this.loginUrl, request);
  }

  public logout() {
    this.clearTokens();
    this.userStore.storeCurrentUser(null);
    return this._http
      .post(this.logoutUrl, {})
  }

  public getRefreshToken(): Observable<RefreshTokenResponse> {
    return this._http
      .get<any>(this.refreshTokenUrl, {
        headers: {
          // eslint-disable-next-line prettier/prettier
          'refreshToken': this.refreshToken || '',
        },
      })
      .pipe(take(1), tap(console.log));
  }

  public getCurrentUser(): Observable<User> {
    return this._http
      .get<User>(this.currentUserUrl);
  }
  

  public storeAccessToken(token: string): void {
    localStorage.setItem('accessToken', token);
  }

  public storeRefreshToken(token: string): void {
    localStorage.setItem('refreshToken', token);
  }

  get accessToken(): string | null {
    return localStorage.getItem('accessToken');
  }

  get refreshToken(): string | null {
    return localStorage.getItem('refreshToken');
  }

  public clearTokens(): void {
    localStorage.removeItem('accessToken');
    localStorage.removeItem('refreshToken');
  }

}
