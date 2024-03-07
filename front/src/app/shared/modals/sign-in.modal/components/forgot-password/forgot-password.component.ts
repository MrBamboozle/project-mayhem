import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthenticationService } from '@app/services/authentication.service';
import { ForgotPasswordRequest } from '@app/shared/models/login';
import { MessageResponse } from '@app/shared/models/message';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './forgot-password.component.html',
  styleUrl: './forgot-password.component.scss'
})
export class ForgotPasswordComponent {
  public readonly forgotPasswordForm: FormGroup = this.formBuilder.group({
    email: ['', [Validators.email, Validators.required]],
  })

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly authService: AuthenticationService,
  ) {}

  get controls(): { [p: string]: AbstractControl } {
    return this.forgotPasswordForm.controls;
  }

  onSubmit() {
    if (this.forgotPasswordForm.invalid) {
      return;
    }

    const request: ForgotPasswordRequest = {
      email: this.forgotPasswordForm.value.email
    }

    this.authService
      .forgotPassword(request)
      .subscribe({
        next: (data: MessageResponse) => {
        },
        error: (e) => console.error(e),
        complete: () => console.info('complete') 
      })
  }

}
