import { Component } from '@angular/core';
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

  constructor(
    private readonly modalService: ModalHelperService,
    private readonly authService: AuthenticationService,
    public readonly userStore: UserStoreService,
  ) {}

  public toggleSidebar(): void {
    this.isSidebarHidden = !this.isSidebarHidden;
  }

  public openSignInModal(): void {
    const signInModalRef = this.modalService.openModal(SignInModalComponent);
  }

  public signOut(): void {
    this.authService.logout();
  }
}
