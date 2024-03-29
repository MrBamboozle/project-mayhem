import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { config } from '@app/core/app-config';
import { UsersService } from '@app/services/users.service';
import { User, UserEditRequest } from '@app/shared/models/user';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { usernameValidator } from '@app/shared/validators/username.validator';

@Component({
  selector: 'app-general-data',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './general-data.component.html',
  styleUrl: './general-data.component.scss'
})
export class GeneralDataComponent {

  @Input() user!: User;

  public isEditMode: boolean = false;

  public readonly profileForm: FormGroup = this.formBuilder.group({
    email: ['', [Validators.email, Validators.required]],
    name: ['', [Validators.maxLength(16), usernameValidator(), Validators.required]],
  });

  public avatarSrc: string = '';

  @Output() userDataChanged = new EventEmitter<User>();


  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly userService: UsersService,
  ) {}

  ngOnInit(): void {
    this.controls.email.setValue(this.user.email);
    this.controls.name.setValue(this.user.name);

    this.avatarSrc = `${config.BACKEND_URL}${this.user.avatar.path}`
  }

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
      name: this.profileForm.value.name
    }

    this.userService.patchUser(this.user.id, request).subscribe(
      (data) => {
        this.userDataChanged.emit(data);
        this.isEditMode = false;
      }
    )
  }

}
