import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-image-upload.modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './image-upload.modal.component.html',
  styleUrl: './image-upload.modal.component.scss'
})
export class ImageUploadModalComponent {

  @Input() cropAndCompress: boolean = false;
  
  public displayImage: string | null = null;
  public uploadedFile: File | null = null;

  constructor(
    public activeModal: NgbActiveModal,
  ) {}

  onFileChange(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.processFile(file);
    }
  }

  onDrop(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();

    const files = event.dataTransfer?.files;
    if (files && files.length) {
      const file = files[0];
      if (this.isFileImage(file)) {
        this.processFile(file);
      } else {
        // Handle the error or notify the user
        console.log("Only image files are allowed.");
      }
    }
  }
  
  private isFileImage(file: File): boolean {
    return file && file.type.startsWith('image/');
  }
  

  onDragOver(event: DragEvent): void {
    event.stopPropagation();
    event.preventDefault();
  }

  onDragLeave(event: DragEvent): void {
    event.stopPropagation();
    event.preventDefault();
  }

  private processFile(file: File): void {
    this.uploadedFile = file;
    // Process the file (e.g., display, compress, upload)
    const fileReader = new FileReader();
    fileReader.onload = (e) => {
      this.displayImage = e.target?.result as string;
    };
    fileReader.readAsDataURL(file);
  }

  confirmUpload(): void {
    this.activeModal.close(this.uploadedFile);
  }

  dismiss(): void {
    this.activeModal.dismiss('cancel');
  }
}
