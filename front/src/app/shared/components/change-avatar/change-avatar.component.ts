import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, EventEmitter, Input, Output } from '@angular/core';
import { config } from '@app/core/app-config';
import { AvatarsService } from '@app/services/avatars.service';
import { UsersService } from '@app/services/users.service';
import { Avatar, User } from '@app/shared/models/user';
import { ImageCroppedEvent, ImageCropperModule } from 'ngx-image-cropper';
import Compressor from 'compressorjs';

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
    private readonly avatarService: AvatarsService,
    private readonly userService: UsersService,
    private cdr: ChangeDetectorRef
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

  imageChangedEvent: any = '';
  croppedImage: any = '';
  compressedImage: any = '';
  imageCroppedEvent!: ImageCroppedEvent;

  onFileChange(event: any): void {
    this.imageChangedEvent = event;
  }

  imageCropped(event: ImageCroppedEvent) {
    console.log(event)
    this.imageCroppedEvent = event;
  }

  onCropFinished() {
    // The user has finished cropping the image
    this.croppedImage = this.imageCroppedEvent.blob;
    this.compressImage(this.croppedImage, 0.8, 102400*3); // Initial quality and target size (100KB multiplied by a factor)
  }

  compressImage(imageSrc: any, quality: number, targetSize: number, minQuality = 0.4) {
    new Compressor(imageSrc, {
      quality: quality,
      success: (compressedImage) => {
        if (compressedImage.size > targetSize && quality > minQuality) {
          console.warn('bigger', compressedImage.size, targetSize, quality, compressedImage);
          const newQuality = quality - 0.1 > minQuality ? quality - 0.1 : minQuality;
          this.compressImage(imageSrc, newQuality, targetSize, minQuality);
        } else {
          // Handle the compressed image
          console.log('Final Image Size:', compressedImage.size);
          this.uploadImage(compressedImage); // Call your upload function
        }
      },
      error: (err) => {
        console.error(err.message);
      }
    });
  }

  uploadImage(image: Blob) {
    console.log('great success!!!', URL.createObjectURL(image))
    this.compressedImage = URL.createObjectURL(image);
    this.cdr.detectChanges();
    // Code to upload the image to your server
  }
}
