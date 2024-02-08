import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { config } from '@app/core/app-config';
import { UsersService } from '@app/services/users.service';
import { ChangeAvatarComponent } from '@app/shared/components/change-avatar/change-avatar.component';
import { ChangePasswordComponent } from '@app/shared/components/change-password/change-password.component';
import { GeneralDataComponent } from '@app/shared/components/general-data/general-data.component';
import { User } from '@app/shared/models/user';
import { passwordMatchingValidator } from '@app/shared/validators/password-matching.validator';
import { usernameValidator } from '@app/shared/validators/username.validator';
import { NgbNavChangeEvent, NgbNavModule } from '@ng-bootstrap/ng-bootstrap';
import { BehaviorSubject } from 'rxjs';

@Component({
  selector: 'app-edit-user',
  standalone: true,
  imports: [CommonModule, NgbNavModule, GeneralDataComponent, ChangePasswordComponent, ChangeAvatarComponent],
  templateUrl: './edit-user.component.html',
  styleUrls: ['./edit-user.component.scss']
})
export class EditUserComponent implements OnInit {

  public fetchedUser: BehaviorSubject<boolean> = new BehaviorSubject(false);
  
  public active: number = 1;

  public uuid: string = '';
  public user!: User;

  constructor(
    private readonly _route: ActivatedRoute,
    private readonly usersService: UsersService,
  ) { }

  ngOnInit(): void {
    this._route.paramMap.subscribe(params => {
      if(params.get('uuid') !== null) {
        this.uuid = params.get('uuid') as string;
      }

      this.fetchUser();
    });
  }

  fetchUser(): void {
    this.usersService.getUser(this.uuid).subscribe(
      (data) => {
        this.user = data;
        this.fetchedUser.next(true);
      }
    )
  }

  avatarChanged($event: User) {
    this.user = $event;
  }

  userDataChanged($event: User) {
    this.user = $event;
  }

  onNavChange(event: NgbNavChangeEvent): void {
    console.log(event)
  }
}
