import { Injectable } from '@angular/core';
import {
  HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpResponse, HttpErrorResponse
} from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';
import { ToastService } from '@app/shared/services/toaster.service';

@Injectable()
export class ToastInterceptor implements HttpInterceptor {
  constructor(private toastService: ToastService) {}

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(req).pipe(
      tap(evt => {
        // Show success toast only for POST, PATCH, DELETE requests
        if (evt instanceof HttpResponse && ['POST', 'PATCH', 'DELETE'].includes(req.method)) {
          this.toastService.show('You did it!', { header: 'Success', classname: 'bg-success text-light' });
        }
      }),
      catchError((error: HttpErrorResponse) => {
        console.log(error)
        this.toastService.showError(error);
        return throwError(() => error);
      })
    );
  }
}
