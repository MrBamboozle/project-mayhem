import { ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { AppInjector } from '../service/app-injector.service';

// Functional guard for user access
export function userGuard() {
  return (route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree => {
    const userStore = AppInjector.getInjector().get(UserStoreService);
    const router = AppInjector.getInjector().get(Router);
    const url: string = state.url;

    if (userStore.isSignedIn) {
      return true;
    }

    if (url === '/' || url.startsWith('/events/list') || url.startsWith('/events/view')) {
      return true;
    }

    router.navigate(['/']);
    return false;
  };
}

// Functional guard for admin access
export function adminGuard() {
  return (route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree => {
    const userStore = AppInjector.getInjector().get(UserStoreService);
    const router = AppInjector.getInjector().get(Router);

    if (userStore.isSignedIn && (userStore.isAdmin || userStore.isGodMode)) {
      return true;
    }

    router.navigate(['/']);
    return false;
  };
}
