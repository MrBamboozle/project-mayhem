import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { config } from '@app/core/app-config';
import { UsersService } from '@app/services/users.service';
import { UserEditRequest } from '@app/shared/models/user';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { passwordMatchingValidator } from '@app/shared/validators/password-matching.validator';
import { usernameValidator } from '@app/shared/validators/username.validator';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.scss'
})
export class ProfileComponent {

  public isEditMode: boolean = false;

  public readonly profileForm: FormGroup = this.formBuilder.group({
    email: [this.userStore.currentUser.getValue()?.email, [Validators.email, Validators.required]],
    name: [this.userStore.currentUser.getValue()?.name, [Validators.maxLength(16), usernameValidator(), Validators.required]],
    password: ['', [Validators.required]],
    repeatPassword: ['', [Validators.required]],
  }, { validators: passwordMatchingValidator() })

  public avatarSrc: string = `${config.BACKEND_URL}${this.userStore.currentUser.getValue()?.avatar.path}`

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly userStore: UserStoreService,
    private readonly userService: UsersService,
  ) {}

  get controls(): { [p: string]: AbstractControl } {
    return this.profileForm.controls;
  }

  disableEditing(): void {
    this.isEditMode = false;
  }

  enableEditing(): void {
    this.isEditMode = true;
  }

  onSubmit() {
    if (this.profileForm.invalid) {
      return;
    }

    const request: UserEditRequest = {
      name: this.profileForm.value.name,
      password: this.profileForm.value.password,
      repeatPassword: this.profileForm.value.repeatPassword,
    }


    // this.authService
    //   .login(request)
    //   .subscribe({
    //     next: (data: LoginResponse) => {
    //       this.userStore.storeCurrentUser(data.user);
    //       this.authService.storeAccessToken(data.token)
    //       this.onSignedIn.emit(true);
    //     },
    //     error: (e) => console.error(e),
    //     complete: () => console.info('complete') 
    //   })
  }

}
