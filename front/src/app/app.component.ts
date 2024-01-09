import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { NgbDropdownModule, NgbModal, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { SignInModalComponent } from './shared/modals/sign-in.modal/sign-in.modal.component';
import { ModalHelperService } from './shared/services/modal-helper.service';
import { HttpClientModule } from '@angular/common/http';
import { UserStoreService } from './shared/stores/user.store.service';
import { AuthenticationService } from './services/authentication.service';
import { User } from './shared/models/user';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive, NgbDropdownModule, NgbModalModule, HttpClientModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  public isSidebarHidden: boolean = false;
  public isSignedIn: boolean = false;
  public title: string = 'PROJECT MAYHEM';

  constructor(
    private readonly modalService: ModalHelperService,
    private readonly authService: AuthenticationService,
    public readonly userStore: UserStoreService
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
