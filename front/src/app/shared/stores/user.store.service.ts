import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, ReplaySubject, Subject, isEmpty } from 'rxjs';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class UserStoreService {
  public readonly currentUser: BehaviorSubject<User|null> = new BehaviorSubject<User|null>(null);

  public storeCurrentUser(user: User|null): void {
    this.currentUser.next(user);
  }

  public get isSignedIn(): User|null {
    return this.currentUser.getValue();
  }
}
