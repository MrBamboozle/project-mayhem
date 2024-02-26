import { Component, Injector } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { NgbDropdownModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { SignInModalComponent } from './shared/modals/sign-in.modal/sign-in.modal.component';
import { ModalHelperService } from './shared/services/modal-helper.service';
import { HttpClientModule } from '@angular/common/http';
import { UserStoreService } from './shared/stores/user.store.service';
import { AuthenticationService } from './services/authentication.service';
import { config } from './core/app-config';
import { ToastsContainer } from './core/components/toast-container.component';
import { AppInjector } from './core/service/app-injector.service';
import { Subscription, filter, interval, startWith, switchMap } from 'rxjs';
import { NotificationsService } from './services/notifications.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive, NgbDropdownModule, NgbModalModule, HttpClientModule, ToastsContainer],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  public isSidebarHidden: boolean = false;
  public title: string = 'PROJECT MAYHEM';

  private notificationSubscription!: Subscription;

  get avatarSrc(): string {
    return `${config.BACKEND_URL}${this.userStore.currentUser.getValue().avatar.path}`
  }

  constructor(
    private readonly injector: Injector,
    private readonly modalService: ModalHelperService,
    private readonly authService: AuthenticationService,
    public readonly notificationsService: NotificationsService,
    public readonly userStore: UserStoreService,
  ) {
    AppInjector.setInjector(injector);

    this.toggleNotificationSubscription();
  }

  private toggleNotificationSubscription(): void {
    if(this.notificationSubscription) {
      this.notificationSubscription.unsubscribe();
    }

    this.notificationSubscription = interval(60000) // Every 60000 milliseconds (1 minute)
      .pipe(
        startWith(0),
        filter(() => this.userStore.isSignedIn),
        switchMap(() => this.notificationsService.getNotifications())
      )
      .subscribe();
  }

  public toggleSidebar(): void {
    this.isSidebarHidden = !this.isSidebarHidden;
  }

  public openSignInModal(): void {
    const signInModalRef = this.modalService.openModal(SignInModalComponent);

    signInModalRef.result.then(
      () => {
      },
      () => {
        this.toggleNotificationSubscription();
      }
    );
  }

  public signOut(): void {
    this.authService.logout();
  }
}
