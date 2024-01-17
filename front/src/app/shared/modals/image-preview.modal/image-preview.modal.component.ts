import { Component, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-image-preview.modal',
  standalone: true,
  imports: [],
  templateUrl: './image-preview.modal.component.html',
  styleUrl: './image-preview.modal.component.scss'
})
export class ImagePreviewModalComponent {
  @Input() previewImage: string = 'Confirm Action';

  constructor(public activeModal: NgbActiveModal) {}

  confirm(): void {
    this.activeModal.close('confirm');
  }

  dismiss(): void {
    this.activeModal.dismiss('cancel');
  }
}
