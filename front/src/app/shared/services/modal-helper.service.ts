import { Component, Injectable, Type } from '@angular/core';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ConfirmDialogModalComponent } from '../modals/confirm-dialog.modal/confirm-dialog.modal.component';
import { ImageUploadModalComponent } from '../modals/image-upload.modal/image-upload.modal.component';
import { ImageCropCompressModalComponent } from '../modals/image-crop-compress.modal/image-crop-compress.modal.component';
import { ImagePreviewModalComponent } from '../modals/image-preview.modal/image-preview.modal.component';

@Injectable({
  providedIn: 'root'
})
export class ModalHelperService {

  constructor(
    private readonly modalService: NgbModal
  ) {}

  openModal(component: any): NgbModalRef {
    const modalRef = this.modalService.open(component, {size: 'lg', animation: false});

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    return modalRef;
  }

  openCofirmModal(
    title: string,
    message: string,
    confirmFn: () => void,
    cancelFn: () => void = () => {},
  ) {
    const modalRef = this.modalService.open(ConfirmDialogModalComponent);
    modalRef.componentInstance.title = title; // Customize title
    modalRef.componentInstance.message = message; // Customize message

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    modalRef.result.then(
      (result) => {
        if (result === 'confirm') {
          // Logic to execute on confirmation
          confirmFn();
        }
      }, 
      (reason) => {
        if (reason === 'cancel') {
          // Logic to execute on dismissal
          cancelFn();
        }
      }
    );
  }

  openImageUploadModal(
    cropAndCompress: boolean,
    confirmFn: (file: File) => void,
  ) {
    const modalRef = this.modalService.open(ImageUploadModalComponent, {size: 'xl', animation: false});
    modalRef.componentInstance.cropAndCompress = cropAndCompress;

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    modalRef.result.then(
        (result) => {
        // Logic to execute on confirmation
        confirmFn(result);
      }, 
      (reason) => {}
    );
  }

  openImageCropAndCompressModal(
    image: File,
    confirmFn: (file: File) => void,
    targetSize: number = 102400*3,
    aspectRatio: number = 1 / 1,
  ) {
    const modalRef = this.modalService.open(ImageCropCompressModalComponent, {size: 'xl', animation: false});
    modalRef.componentInstance.image = image;
    modalRef.componentInstance.targetSize = targetSize;
    modalRef.componentInstance.aspectRatio = aspectRatio;

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    modalRef.result.then(
        (result) => {
        // Logic to execute on confirmation
        confirmFn(result);
      }, 
      (reason) => {}
    );
  }

  openImagePreviewModal(
    previewImage: string,
    confirmFn: () => void
  ) {
    const modalRef = this.modalService.open(ImagePreviewModalComponent, {size: 'xl', animation: false});
    modalRef.componentInstance.previewImage = previewImage;

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    modalRef.result.then(
        (result) => {
        // Logic to execute on confirmation
        confirmFn();
      }, 
      (reason) => {}
    );
  }
}