import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { User, UserRequest } from '@app/shared/models/user';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UsersService {
  private readonly usersUrl: string = `${config.API_URL}/users`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getUsers(): Observable<User[]> {
    return this._http
      .get<User[]>(this.usersUrl);
  }

  public getUser(id: string): Observable<User> {
    return this._http
      .get<User>(`${this.usersUrl}/${id}`);
  }

  public postUser(userReq: UserRequest): Observable<User> {
    return this._http
      .post<User>(this.usersUrl, userReq);
  }

  public patchEvent(id: string, userReq: UserRequest): Observable<User> {
    return this._http
      .patch<User>(`${this.usersUrl}/${id}`, userReq);
  }

  //TODO: delete response?
  public deleteEvent(id: string): Observable<User> {
    return this._http
      .delete<User>(`${this.usersUrl}/${id}`);
  }
}