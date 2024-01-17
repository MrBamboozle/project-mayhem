import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ImageCroppedEvent, ImageCropperModule } from 'ngx-image-cropper';
import Compressor from 'compressorjs';

@Component({
  selector: 'app-image-crop-compress.modal',
  standalone: true,
  imports: [ImageCropperModule],
  templateUrl: './image-crop-compress.modal.component.html',
  styleUrl: './image-crop-compress.modal.component.scss'
})
export class ImageCropCompressModalComponent {

  @Input() image: any = null;
  @Input() targetSize: number = 102400*3;
  @Input() aspectRatio: number = 1 / 1;

  private croppedImage: File | Blob | null | undefined = null;
  private imageCroppedEvent!: ImageCroppedEvent;
  public imageBase64: string | undefined = undefined;

  constructor(public activeModal: NgbActiveModal) {}

  ngAfterViewInit(): void {
    if (this.image) {
      this.readFile(this.image);
    }
  }

  private readFile(file: File): void {
    const fileReader = new FileReader();
    fileReader.onload = (event: any) => {
      this.imageBase64 = event.target.result;
    };
    fileReader.readAsDataURL(file);
  }
  
  imageCropped(event: ImageCroppedEvent): void {
    this.imageCroppedEvent = event;
  }

  onCropFinished(): void {
    // The user has finished cropping the image
    this.croppedImage = this.imageCroppedEvent.blob;
    this.compressImage(this.croppedImage as Blob, 0.8, this.targetSize); // Initial quality and target size (100KB multiplied by a factor)
  }

  compressImage(imageSrc: File | Blob , quality: number, targetSize: number, minQuality = 0.4): void {
    new Compressor(imageSrc, {
      quality: quality,
      success: (compressedImage) => {
        if (compressedImage.size > targetSize && quality > minQuality) {
          const newQuality = quality - 0.1 > minQuality ? quality - 0.1 : minQuality;
          this.compressImage(imageSrc, newQuality, targetSize, minQuality);
        } else {
          if(compressedImage.size > targetSize) {
            console.error('Unable to compress file sufficiently.');
          } else {
            // Handle the compressed image
            this.uploadImage(compressedImage); // Call your upload function
          }
        }
      },
      error: (err) => {
        console.error(err.message);
      }
    });
  }

  uploadImage(image: Blob) : void{
    this.activeModal.close(image);
  }

  dismiss(): void {
    this.activeModal.dismiss('cancel');
  }
}
