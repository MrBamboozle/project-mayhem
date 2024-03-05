import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { Notification } from '@app/shared/models/notification';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { Observable, tap, Subscription, filter, interval, startWith, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NotificationsService {
  private readonly notificationsUrl: string = `${config.API_URL}/user-notifications`;
  private readonly notificationsAllUrl: string = `${config.API_URL}/user-notifications/all`;

  public hasUnreadNotifications: boolean = false;
  public unreadNotificationsNumber: number = 0;

  private notificationSubscription!: Subscription;

  constructor(
    private readonly _http: HttpClient,
    private readonly userStore: UserStoreService
  ) { }

  public getNotifications(queryParams: string = ''): Observable<PaginatedResponse<Notification> & { total: number; totalUnread: number }> {
    return this._http
      .get<PaginatedResponse<Notification> & { total: number; totalUnread: number }>(`${this.notificationsUrl}${queryParams}`)
      .pipe(
        tap(data => {

          //TODO: This will come from the BE
          this.hasUnreadNotifications = data.totalUnread !== 0;
          this.unreadNotificationsNumber = data.totalUnread;
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

  public toggleNotificationSubscription(): void {
    if(this.notificationSubscription) {
      this.notificationSubscription.unsubscribe();
    }

    this.notificationSubscription = interval(60000) // Every 60000 milliseconds (1 minute)
      .pipe(
        startWith(0),
        filter(() => this.userStore.isSignedIn),
        switchMap(() => this.getNotifications())
      )
      .subscribe();
  }
}
