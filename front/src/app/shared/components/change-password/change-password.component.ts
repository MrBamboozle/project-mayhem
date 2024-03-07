import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UsersService } from '@app/services/users.service';
import { PasswordChangeRequest, User, UserEditRequest } from '@app/shared/models/user';
import { passwordMatchingValidator } from '@app/shared/validators/password-matching.validator';

@Component({
  selector: 'app-change-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './change-password.component.html',
  styleUrl: './change-password.component.scss'
})
export class ChangePasswordComponent {

  @Input() user!: User;

  public readonly changePasswordForm: FormGroup = this.formBuilder.group({
    oldPassword: ['', [Validators.required]],
    newPassword: ['', [Validators.required]],
    repeatPassword: ['', [Validators.required]],
  }, { validators: passwordMatchingValidator('newPassword', 'repeatPassword') })

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

    const request: PasswordChangeRequest = {
      password: this.changePasswordForm.value.newPassword,
      passwordOld: this.changePasswordForm.value.oldPassword,
    }

    this.userService.changePassword(this.user.id, request).subscribe(
      () => {
        // Good job
      }
    )
  }

}
