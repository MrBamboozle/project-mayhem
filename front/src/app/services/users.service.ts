import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
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

  public getUsers(page?: number): Observable<PaginatedResponse<User>> {
    return this._http
      .get<PaginatedResponse<User>>(`${this.usersUrl}${page ? '?page=' + page : ''}`);
  }

  public getUser(id: string): Observable<{user: User}> {
    return this._http
      .get<{user: User}>(`${this.usersUrl}/${id}`);
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
  public deleteUser(id: string): Observable<User> {
    return this._http
      .delete<User>(`${this.usersUrl}/${id}`);
  }
}