import { APP_INITIALIZER, ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { HTTP_INTERCEPTORS, HttpClient, provideHttpClient, withFetch, withInterceptors, withInterceptorsFromDi } from '@angular/common/http';
import { AuthInterceptor } from './core/interceptors/auth-interceptor';
import { UserStoreService } from './shared/stores/user.store.service';
import { Observable, catchError, of, tap } from 'rxjs';
import { config } from './core/app-config';
import { ToastInterceptor } from './core/interceptors/toast-interceptor';
import { provideAnimations } from '@angular/platform-browser/animations';
import { APP_BASE_HREF } from '@angular/common';

function initializeAppFactory(httpClient: HttpClient, userStore: UserStoreService): () => Observable<any> {
  return () => httpClient.get(`${config.API_URL}/me`)
    .pipe(
      tap({
        next: (user: any) => {
          userStore.storeCurrentUser(user);
        },
        error: (e) => console.error(e),
        complete: () => console.info('complete')
      }),
      catchError((error) => {
        console.log('error caught on initialization', error)
        // Handle or log error
        // Return an observable to allow the app to continue initializing
        return of(null);
      })
    )
 }

export const appConfig: ApplicationConfig = {
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: ToastInterceptor,
      multi: true
    },
    {
      provide: APP_BASE_HREF, 
      useValue: '/'
    },
    provideRouter(routes),
    provideHttpClient(withFetch(), withInterceptorsFromDi()),
    {
      provide: APP_INITIALIZER,
      useFactory: initializeAppFactory,
      multi: true,
      deps: [HttpClient, UserStoreService],
    },
    provideAnimations()
  ]
};

//TODO: Functional interceptors
