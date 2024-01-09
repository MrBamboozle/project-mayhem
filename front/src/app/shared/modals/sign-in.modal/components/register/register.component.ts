import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthenticationService } from '@app/services/authentication.service';
import { RegisterRequest } from '@app/shared/models/login';
import { passwordMatchingValidator } from '@app/shared/validators/password-matching.validator';
import { usernameValidator } from '@app/shared/validators/username.validator';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './register.component.html',
  styleUrl: './register.component.scss'
})
export class RegisterComponent {

  public readonly registerForm: FormGroup = this.formBuilder.group({
    email: ['', [Validators.email, Validators.required]],
    alias: ['', [Validators.maxLength(16), usernameValidator(), Validators.required]],
    password: ['', [Validators.required]],
    repeatPassword: ['', [Validators.required]],
  }, { validators: passwordMatchingValidator() })

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly authService: AuthenticationService,
  ) {}

  get controls(): { [p: string]: AbstractControl } {
    return this.registerForm.controls;
  }

  onSubmit() {
    if (this.registerForm.invalid) {
      return;
    }

    const request: RegisterRequest = {
      email: this.registerForm.value.email,
      alias: this.registerForm.value.alias,
      password: this.registerForm.value.password,
      repearPassword: this.registerForm.value.repeatPassword,
    }

    console.log('form is valis, submitting:', request);

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
