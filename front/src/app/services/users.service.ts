import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { MessageResponse } from '@app/shared/models/message';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
import { User, UserEditRequest, UserRequest } from '@app/shared/models/user';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UsersService {
  private readonly usersUrl: string = `${config.API_URL}/users`;
  private readonly usersAllUrl: string = `${config.API_URL}/users-all`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getUsers(queryParams: string = ''): Observable<PaginatedResponse<User>> {
    return this._http
      .get<PaginatedResponse<User>>(`${this.usersUrl}${queryParams}`);
  }

  public getAllUsers(queryParams: string = ''): Observable<User[]> {
    return this._http
      .get<User[]>(`${this.usersAllUrl}${queryParams}`);
  }

  public getUser(id: string): Observable<User> {
    return this._http
      .get<User>(`${this.usersUrl}/${id}`);
  }

  public postUser(userReq: UserRequest): Observable<User> {
    return this._http
      .post<User>(this.usersUrl, userReq);
  }

  public patchUser(id: string, userReq: UserEditRequest): Observable<User> {
    return this._http
      .patch<User>(`${this.usersUrl}/${id}`, userReq);
  }

  public deleteUser(id: string): Observable<MessageResponse> {
    return this._http
      .delete<MessageResponse>(`${this.usersUrl}/${id}`);
  }

  public chooseDefaultAvatar(userId: string, avatarId: string): Observable<User> {
    return this._http
      .post<User>(`${this.usersUrl}/${userId}/avatars/${avatarId}`, {});
  }

  public uploadCustomAvatar(userId: string, avatar: Blob): Observable<User> {
    const formData = new FormData();
    formData.append('avatar', avatar);

    return this._http
      .post<User>(`${this.usersUrl}/${userId}/avatars`, formData);
  }
}