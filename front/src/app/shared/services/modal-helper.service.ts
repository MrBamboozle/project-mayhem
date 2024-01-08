import { Component, Injectable, Type } from '@angular/core';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';

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
}