import { CommonModule } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { Component, EventEmitter, Output } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthenticationService } from '@app/services/authentication.service';
import { LoginRequest, LoginResponse } from '@app/shared/models/login';
import { User, UserRequest } from '@app/shared/models/user';
import { UserStoreService } from '@app/shared/stores/user.store.service';

@Component({
  selector: 'app-sign-in',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './sign-in.component.html',
  styleUrl: './sign-in.component.scss'
})
export class SignInComponent {

  @Output() onSignedIn = new EventEmitter<boolean>();

  public readonly loginForm: FormGroup = this.formBuilder.group({
    email: ['', [Validators.email, Validators.required]],
    password: ['', [Validators.required]]
  })

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly authService: AuthenticationService,
    private readonly userStore: UserStoreService
  ) {}

  get controls(): { [p: string]: AbstractControl } {
    return this.loginForm.controls;
  }

  onSubmit() {
    if (this.loginForm.invalid) {
      return;
    }

    const request: LoginRequest = {
      email: this.loginForm.value.email,
      password: this.loginForm.value.password,
    }

    this.authService
      .login(request)
      .subscribe({
        next: (data: LoginResponse) => {
          this.userStore.storeCurrentUser(data.user);
          this.authService.storeAccessToken(data.token)
          this.authService.storeRefreshToken(data.refresh_token)
          this.onSignedIn.emit(true);
        },
        error: (e) => console.error(e),
        complete: () => console.info('complete') 
      })
  }

}
