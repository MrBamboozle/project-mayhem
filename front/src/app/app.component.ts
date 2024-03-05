import { Component, Injector } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { NgbDropdownModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalHelperService } from './shared/services/modal-helper.service';
import { HttpClientModule } from '@angular/common/http';
import { UserStoreService } from './shared/stores/user.store.service';
import { AuthenticationService } from './services/authentication.service';
import { config } from './core/app-config';
import { ToastsContainer } from './core/components/toast-container.component';
import { AppInjector } from './core/service/app-injector.service';
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

  get avatarSrc(): string {
    return `${config.BACKEND_URL}${this.userStore.currentUser.getValue().avatar.path}`
  }
  get pencilSrc(): string {
    return `assets/images/pencil.png`
  }
  get rulerSrc(): string {
    return `assets/images/ruler.png`
  }

  constructor(
    private readonly injector: Injector,
    private readonly modalService: ModalHelperService,
    private readonly authService: AuthenticationService,
    public readonly notificationsService: NotificationsService,
    public readonly userStore: UserStoreService,
  ) {
    AppInjector.setInjector(injector);

    this.notificationsService.toggleNotificationSubscription();
  }

  public toggleSidebar(): void {
    this.isSidebarHidden = !this.isSidebarHidden;
  }

  public openSignInModal(): void {
    this.modalService.openSignInModal();
  }

  public signOut(): void {
    this.authService.logout();
  }
}
