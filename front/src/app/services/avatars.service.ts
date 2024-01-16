import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { Avatar } from '@app/shared/models/user';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AvatarsService {
  private readonly avatarsUrl: string = `${config.API_URL}/avatars`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getAvatars(): Observable<Avatar[]> {
    return this._http
      .get<Avatar[]>(this.avatarsUrl);
  }
}
