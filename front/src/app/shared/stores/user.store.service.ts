import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, ReplaySubject, Subject, isEmpty } from 'rxjs';
import { User } from '../models/user';
import { RoleEnum } from '../enums/roles';

@Injectable({
  providedIn: 'root'
})
export class UserStoreService {
  public readonly currentUser: BehaviorSubject<User> = new BehaviorSubject<User>(new class {} as User);
  public readonly isStored: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);

  public storeCurrentUser(user: User): void {
    this.currentUser.next(user);
    this.isStored.next(true);
  }

  public get isSignedIn(): boolean {
    return this.isStored.getValue();
  }

  public get isAdmin(): boolean {
    return this.isSignedIn && this.currentUser.getValue().role.name === RoleEnum.ADMIN;
  }

  public get isGodMode(): boolean {
    return this.isSignedIn && this.currentUser.getValue().role.name === RoleEnum.GODMODE;
  }
}
