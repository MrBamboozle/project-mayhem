import { Injectable, Injector } from '@angular/core';
import { ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { UserStoreService } from '@app/shared/stores/user.store.service';

// Functional guard for user access
export function userGuard(injector: Injector) {
  return (route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree => {
    const userStore = injector.get(UserStoreService);
    const router = injector.get(Router);
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
export function adminGuard(injector: Injector) {
  return (route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree => {
    const userStore = injector.get(UserStoreService);
    const router = injector.get(Router);

    if (userStore.isSignedIn && userStore.isAdmin) {
      return true;
    }

    router.navigate(['/not-found']);
    return false;
  };
}
