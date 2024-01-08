import { APP_INITIALIZER, ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { HTTP_INTERCEPTORS, HttpClient, provideHttpClient, withFetch, withInterceptors, withInterceptorsFromDi } from '@angular/common/http';
import { AuthInterceptor } from './core/interceptors/auth-interceptor';
import { UserStoreService } from './shared/stores/user.store.service';
import { Observable, tap } from 'rxjs';
import { config } from './core/app-config';

function initializeAppFactory(httpClient: HttpClient, userStore: UserStoreService): () => Observable<any> {
  return () => httpClient.get(`${config.API_URL}/me`)
    .pipe(
      tap({
        next: (user: any) => {
          userStore.storeCurrentUser(user);
        },
        error: (e) => console.error(e),
        complete: () => console.info('complete')
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
    provideRouter(routes),
    provideHttpClient(withFetch(), withInterceptorsFromDi()),
    {
      provide: APP_INITIALIZER,
      useFactory: initializeAppFactory,
      multi: true,
      deps: [HttpClient, UserStoreService],
    },
  ]
};

//TODO: Functional interceptors
