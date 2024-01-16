import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, EventEmitter, Input, Output } from '@angular/core';
import { config } from '@app/core/app-config';
import { AvatarsService } from '@app/services/avatars.service';
import { UsersService } from '@app/services/users.service';
import { Avatar, User } from '@app/shared/models/user';

@Component({
  selector: 'app-change-avatar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './change-avatar.component.html',
  styleUrl: './change-avatar.component.scss'
})
export class ChangeAvatarComponent {

  @Input() user!: User;

  public avatarId: string = '';

  public defaultAvatars: Avatar[] = [];

  @Output() avatarChanged = new EventEmitter<User>();

  constructor(
    private readonly avatarService: AvatarsService,
    private readonly userService: UsersService,
  ) {}

  ngOnInit(): void {
    this.avatarId = this.user.avatar.id;

    this.fetchAvatars();
  }

  fullAvatarSrc(partialSrc: string): string {
    return `${config.BACKEND_URL}${partialSrc}`;
  }

  isChosenAvatar(id: string): boolean {
    return id === this.avatarId;
  }

  chooseAvatar(id: string): void {
    this.userService.chooseDefaultAvatar(this.user.id, id).subscribe(
      (data) => {
        this.avatarChanged.emit(data);
        this.avatarId = data.avatar.id;
      }
    )
  }

  fetchAvatars() {
    this.avatarService.getAvatars().subscribe(
      (data) => {
        this.defaultAvatars = data;
      }
    )
  }
}
