import { Component, TemplateRef, inject } from '@angular/core';

import { CommonModule, NgTemplateOutlet } from '@angular/common';
import { NgbToastModule } from '@ng-bootstrap/ng-bootstrap';
import { ToastService } from '@app/shared/services/toaster.service';

@Component({
	selector: 'app-toasts',
	standalone: true,
	imports: [NgbToastModule, NgTemplateOutlet, CommonModule],
	template: `
		@for (toast of toastService.toasts; track toast) {
			<ngb-toast
        [header]="toast.header"
        [autohide]="true"
        [class]="toast.classname"
        [delay]="toast.delay || 5000"
        (hide)="toastService.remove(toast)">
        <ng-template [ngIf]="isTemplate(toast)" [ngIfElse]="text">
          <ng-template [ngTemplateOutlet]="toast.textOrTpl"></ng-template>
        </ng-template>
        <ng-template #text><span [innerHtml]="toast.textOrTpl"></span></ng-template>
      </ngb-toast>
		}
	`,
	host: { class: 'toast-container position-fixed bottom-0 end-0 p-3', style: 'z-index: 1200' },
})
export class ToastsContainer {
	toastService = inject(ToastService);

  isTemplate(toast: any) { return toast.textOrTpl instanceof TemplateRef; }
}