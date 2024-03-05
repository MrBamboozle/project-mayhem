import { Component, Injectable, Type } from '@angular/core';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ConfirmDialogModalComponent } from '../modals/confirm-dialog.modal/confirm-dialog.modal.component';
import { ImageUploadModalComponent } from '../modals/image-upload.modal/image-upload.modal.component';
import { ImageCropCompressModalComponent } from '../modals/image-crop-compress.modal/image-crop-compress.modal.component';
import { ImagePreviewModalComponent } from '../modals/image-preview.modal/image-preview.modal.component';
import { NotificationsService } from '@app/services/notifications.service';
import { SignInModalComponent } from '../modals/sign-in.modal/sign-in.modal.component';

@Injectable({
  providedIn: 'root'
})
export class ModalHelperService {

  constructor(
    private readonly modalService: NgbModal,
    private readonly notificationsService: NotificationsService
  ) {}

  openModal(component: any): NgbModalRef {
    const modalRef = this.modalService.open(component, {size: 'lg', animation: false});

    modalRef.shown.subscribe(() => {
      modalRef.update({modalDialogClass: 'show-modal'})
    });

    return modalRef;
  }

  public openSignInModal(successFn: () => void = () => {}): void {
    const modalRef = this.openModal(SignInModalComponent);

    modalRef.result.then(
      () => {
      },
      () => {
        this.notificationsService.toggleNotificationSubscription();
        successFn();
      }
    );
  }

  public openCofirmModal(
    title: string,
    message: string,
    confirmFn: () => void,
    cancelFn: () => void = () => {},
  ): void {
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

  public openImageUploadModal(
    cropAndCompress: boolean,
    confirmFn: (file: File) => void,
  ): void {
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

  public openImageCropAndCompressModal(
    image: File,
    confirmFn: (file: File) => void,
    targetSize: number = 102400*3,
    aspectRatio: number = 1 / 1,
  ): void {
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

  public openImagePreviewModal(
    previewImage: string,
    confirmFn: () => void
  ): void {
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