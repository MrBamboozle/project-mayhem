import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UsersService } from '@app/services/users.service';
import { UserEditRequest } from '@app/shared/models/user';
import { passwordMatchingValidator } from '@app/shared/validators/password-matching.validator';

@Component({
  selector: 'app-change-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './change-password.component.html',
  styleUrl: './change-password.component.scss'
})
export class ChangePasswordComponent {

  public readonly changePasswordForm: FormGroup = this.formBuilder.group({
    password: ['', [Validators.required]],
    repeatPassword: ['', [Validators.required]],
  }, { validators: passwordMatchingValidator() })

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly userService: UsersService,
  ) {}

  get controls(): { [p: string]: AbstractControl } {
    return this.changePasswordForm.controls;
  }

  onSubmit() {
    if (this.changePasswordForm.invalid) {
      return;
    }

    const request: UserEditRequest = {
      password: this.changePasswordForm.value.password,
      repeatPassword: this.changePasswordForm.value.repeatPassword,
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
