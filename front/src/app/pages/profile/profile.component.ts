import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ChangeAvatarComponent } from '@app/shared/components/change-avatar/change-avatar.component';
import { ChangePasswordComponent } from '@app/shared/components/change-password/change-password.component';
import { GeneralDataComponent } from '@app/shared/components/general-data/general-data.component';
import { User } from '@app/shared/models/user';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { NgbNavChangeEvent, NgbNavModule } from '@ng-bootstrap/ng-bootstrap';
import { BehaviorSubject } from 'rxjs';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, NgbNavModule, GeneralDataComponent, ChangePasswordComponent, ChangeAvatarComponent],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.scss'
})
export class ProfileComponent {

  public fetchedUser: BehaviorSubject<boolean> = new BehaviorSubject(false);

  public active: number = 1;

  public currentUser: User = this.userStore.currentUser.getValue();

  constructor(
    private readonly userStore: UserStoreService,
  ) {
    this.currentUser = this.userStore.currentUser.getValue();
  } 

  ngOnInit(): void {
    if(this.currentUser) {
      this.fetchedUser.next(true);
    }
  }

  onNavChange(event: NgbNavChangeEvent): void {
    console.log(event)
  }

  avatarChanged($event: User) {
    this.userStore.storeCurrentUser($event);
    this.currentUser = this.userStore.currentUser.getValue();
  }
}
