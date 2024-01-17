import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { config } from '@app/core/app-config';
import { AvatarsService } from '@app/services/avatars.service';
import { UsersService } from '@app/services/users.service';
import { Avatar, User } from '@app/shared/models/user';
import { ImageCroppedEvent, ImageCropperModule } from 'ngx-image-cropper';
import Compressor from 'compressorjs';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';

@Component({
  selector: 'app-change-avatar',
  standalone: true,
  imports: [CommonModule, ImageCropperModule],
  templateUrl: './change-avatar.component.html',
  styleUrl: './change-avatar.component.scss'
})
export class ChangeAvatarComponent {

  @Input() user!: User;

  public avatarId: string = '';

  public defaultAvatars: Avatar[] = [];

  @Output() avatarChanged = new EventEmitter<User>();

  constructor(
    private readonly avatarsService: AvatarsService,
    private readonly userService: UsersService,
    private readonly modalService: ModalHelperService
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

  selectedDefaultAvatar(id: string): void {
    if(!this.defaultAvatars.some((avatar: Avatar) => avatar.id === this.user.avatar.id)) {
      this.modalService.openCofirmModal(
        'Override custom avatar?',
        'Choosing a default avatar will override your custom avatar. Are you sure you want to do this?',
        () => {
          this.chooseDefaultAvatar(id);
        })
    } else {
      this.chooseDefaultAvatar(id);
    }
    
  }

  chooseDefaultAvatar(id: string): void {
    this.userService.chooseDefaultAvatar(this.user.id, id).subscribe(
      (data) => {
        this.avatarChanged.emit(data);
        this.avatarId = data.avatar.id;
      }
    )
  }

  fetchAvatars(): void {
    this.avatarsService.getAvatars().subscribe(
      (data) => {
        this.defaultAvatars = data;
      }
    )
  }

  openUploadImageModal(): void {
    this.modalService.openImageUploadModal(true, (uploadedImage) => {
      this.modalService.openImageCropAndCompressModal(
        uploadedImage,
        (croppedImage) => {

          const fileReader = new FileReader();
          fileReader.onload = (e) => {
            this.modalService.openImagePreviewModal(
              e.target?.result as string,
              () => {
                this.uploadImage(croppedImage);
              }
            );
          };
          fileReader.readAsDataURL(croppedImage);
        })
    })
  }

  uploadImage(image: Blob): void {

    this.userService.uploadCustomAvatar(this.user.id, image).subscribe(
      (data) => {
        this.avatarChanged.emit(data);
        this.avatarId = data.avatar.id;      }
    )
  }
}
