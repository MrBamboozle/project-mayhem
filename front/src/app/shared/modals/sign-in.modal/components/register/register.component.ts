import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Output } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthenticationService } from '@app/services/authentication.service';
import { LoginResponse, RegisterRequest } from '@app/shared/models/login';
import { UserStoreService } from '@app/shared/stores/user.store.service';
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

  @Output() onRegistered = new EventEmitter<boolean>();

  public readonly registerForm: FormGroup = this.formBuilder.group({
    email: ['', [Validators.email, Validators.required]],
    name: ['', [Validators.maxLength(16), usernameValidator(), Validators.required]],
    password: ['', [Validators.required]],
    repeatPassword: ['', [Validators.required]],
  }, { validators: passwordMatchingValidator() })

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly authService: AuthenticationService,
    private readonly userStore: UserStoreService,
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
      name: this.registerForm.value.name,
      password: this.registerForm.value.password,
      repeatPassword: this.registerForm.value.repeatPassword,
    }

    this.authService
      .register(request)
      .subscribe({
        next: (data: LoginResponse) => {
          this.userStore.storeCurrentUser(data.user);
          this.authService.storeAccessToken(data.token);
          this.authService.storeRefreshToken(data.refreshToken);
          this.onRegistered.emit(true);
        },
        error: (e) => console.error(e),
        complete: () => console.info('complete') 
      })
  }

}
