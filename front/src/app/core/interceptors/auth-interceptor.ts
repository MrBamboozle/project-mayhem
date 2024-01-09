import { Injectable, Injector } from '@angular/core';
import {
  HttpInterceptor,
  HttpHandler,
  HttpRequest,
  HttpHeaders,
  HttpErrorResponse,
} from '@angular/common/http';
import { catchError, tap, switchMap } from 'rxjs/operators';
import { Observable, of, Subject, throwError } from 'rxjs';
import { AuthenticationService } from '@app/services/authentication.service';
import { RefreshTokenResponse } from '@app/shared/models/login';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  private tokenRefreshedSource = new Subject();
  private tokenRefreshed$ = this.tokenRefreshedSource.asObservable();
  private refreshTokenInProgress: boolean = false;

  constructor(
    private readonly authService: AuthenticationService,
  ) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<any> {
    if (request.url.endsWith('/login') || request.url.endsWith('/refresh-token')) {
      return next.handle(request);
    }
    console.log('caught by interceptor')

    // Handle request
    request = this.addAuthHeader(request);
    // Handle response
    return next.handle(request).pipe(
      catchError((error: HttpErrorResponse) => {
        return this.handleResponseError(error, request, next);
      })
    );
  }

  
  addAuthHeader(request: HttpRequest<any>): HttpRequest<any> {
    const accessToken: string | null = this.authService.accessToken;

    
    // Clone the request and set the new header in one step.
    const authReq = request.clone({
      headers: request.headers.set('Authorization', `Bearer ${accessToken}`)
    });

    return authReq;
  }

  refreshToken(): Observable<any> {
    if (this.refreshTokenInProgress) {
      return new Observable((observer) => {
        this.tokenRefreshed$.subscribe(() => {
          observer.next();
          observer.complete();
        });
      });
    } else {
      this.refreshTokenInProgress = true;

      return this.authService.getRefreshToken().pipe(
        tap((data: any) => {
          this.authService.storeAccessToken(data?.token);
          this.authService.storeRefreshToken(data?.refresh_token);
          this.refreshTokenInProgress = false;
          this.tokenRefreshedSource.next(true);
        }),
        catchError((err) => {
          this.refreshTokenInProgress = false;
          this.authService.logout();
          return of(err);
        })
      );
    }
  }

  handleResponseError(error: HttpErrorResponse, request?: HttpRequest<any>, next?: any): any {
    // Business error
    if (error.status === 400) {
      // Show message
    }

    // Invalid token error
    else if (error.status === 401) {
      return this.refreshToken().pipe(
        switchMap(() => {
          if(request) {
            request = this.addAuthHeader(request);
          }
          return next.handle(request);
        }),
        catchError((e) => {
          if (e.status !== 401) {
            return this.handleResponseError(e);
          } else {
            // Show message
            this.authService.logout();
            return of(e);
          }
        })
      );
    }

    // Access denied error
    // We no longer log the user out after a 403 response, just send a message about insufficient rights.
    else if (error.status === 403) {
      // Show message
      // this.toastrService.showToastr(
      //   'Insufficient user rights to access this screen or action.',
      //   ToastrTypeEnum.ERROR
      // );
    }

    // Server error
    else if (error.status === 500) {
      // Show message
    }

    // Maintenance error
    else if (error.status === 503) {
      // Show message
      // Redirect to the maintenance page
    }

    return throwError(() => error);
  }

}
