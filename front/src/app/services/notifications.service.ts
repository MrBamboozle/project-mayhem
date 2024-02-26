import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { Notification } from '@app/shared/models/notification';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NotificationsService {
  private readonly notificationsUrl: string = `${config.API_URL}/user-notifications`;
  private readonly notificationsAllUrl: string = `${config.API_URL}/user-notifications/all`;

  public hasUnreadNotifications: boolean = false;
  public unreadNotificationsNumber: number = 0;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getNotifications(queryParams: string = ''): Observable<PaginatedResponse<Notification>> {
    return this._http
      .get<PaginatedResponse<Notification>>(`${this.notificationsUrl}${queryParams}`)
      .pipe(
        tap(data => {

          //TODO: This will come from the BE
          this.hasUnreadNotifications = data.data.some((notification) => notification.read === 0);
          this.unreadNotificationsNumber = data.data.filter((notification) => notification.read === 0).length;
        })
      );
  }

  public patchNotifications(): Observable<PaginatedResponse<Notification>> {
    return this._http
      .patch<PaginatedResponse<Notification>>(this.notificationsAllUrl, {});
  }

  public deleteNotifications(): Observable<PaginatedResponse<Notification>> {
    return this._http
      .delete<PaginatedResponse<Notification>>(this.notificationsAllUrl);
  }
}
