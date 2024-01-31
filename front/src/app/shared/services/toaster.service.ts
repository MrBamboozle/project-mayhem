import { HttpErrorResponse } from '@angular/common/http';
import { Injectable, TemplateRef } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ToastService {
  toasts: any[] = [];


  show(textOrTpl: string | TemplateRef<any>, options: any = {}) {
    this.toasts = [{ textOrTpl, ...options }];
  }

  showError(textOrTpl: string | TemplateRef<any>) {
    this.show(textOrTpl, { header: 'Error', classname: 'bg-danger text-light' });
  }

  remove(toast: any) {
    this.toasts = this.toasts.filter(t => t !== toast);
  }

  showHttpError(error: HttpErrorResponse): void {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // Client-side error
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // Server-side error
      errorMessage = `<b>${error.error.message}</b>`;
      const errors = error.error?.errors;
      if(errors) {
        for(let key in errors) {
          errorMessage += `<br>${errors[key]}`;
        }
      }
    }
    this.showError(errorMessage);
  }
}