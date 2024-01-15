import { Component, Input } from '@angular/core';
import { User } from '@app/shared/models/user';

@Component({
  selector: 'app-change-avatar',
  standalone: true,
  imports: [],
  templateUrl: './change-avatar.component.html',
  styleUrl: './change-avatar.component.scss'
})
export class ChangeAvatarComponent {

  @Input() user!: User;

}
