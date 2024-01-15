import { Component, Injectable, Type } from '@angular/core';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ConfirmDialogModalComponent } from '../modals/confirm-dialog.modal/confirm-dialog.modal.component';

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

    modalRef.result.then((result) => {
      if (result === 'confirm') {
        // Logic to execute on confirmation
        confirmFn();
      }
    }, (reason) => {
      if (reason === 'cancel') {
        // Logic to execute on dismissal
        cancelFn();
      }
    });
  }
}