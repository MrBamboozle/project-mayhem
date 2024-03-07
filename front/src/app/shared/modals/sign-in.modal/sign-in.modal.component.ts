import { Component } from '@angular/core';
import { NgbActiveModal, NgbNavChangeEvent, NgbNavModule } from '@ng-bootstrap/ng-bootstrap';
import { SignInComponent } from './components/sign-in/sign-in.component';
import { RegisterComponent } from './components/register/register.component';
import { ForgotPasswordComponent } from './components/forgot-password/forgot-password.component';

@Component({
  selector: 'app-sign-in.modal',
  standalone: true,
  imports: [NgbNavModule, SignInComponent, RegisterComponent, ForgotPasswordComponent],
  templateUrl: './sign-in.modal.component.html',
  styleUrl: './sign-in.modal.component.scss'
})
export class SignInModalComponent {

  public active: number = 1;

  constructor(
    public readonly activeModal: NgbActiveModal
  ) {} 

  onNavChange(event: NgbNavChangeEvent): void {
    console.log(event)
  }

}
