import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { EventsService } from '@app/services/events.service';
import { CreateEventRequest } from '@app/shared/models/event';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { dateTimeValidator } from '@app/shared/validators/date-time.validator';
import { MatStepper, MatStepperModule } from '@angular/material/stepper';
import { dateRangeValidator } from '@app/shared/validators/date-range.validator';

@Component({
  selector: 'app-create-event',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatStepperModule],
  templateUrl: './create-event.component.html',
  styleUrl: './create-event.component.scss'
})
export class CreateEventComponent {

  createEventFormFirst: FormGroup  = this.formBuilder.group({
    title: ['', [Validators.required]],
    tagline: [''],
    description: ['', [Validators.required]],
    categories: [[]],
  })

  createEventFormSecond: FormGroup  = this.formBuilder.group({
    dateFrom: ['', [Validators.required, dateTimeValidator()]],
    dateTo: ['', [Validators.required, dateTimeValidator()]],
  }, { validators: dateRangeValidator() });

  createEventFormThird: FormGroup  = this.formBuilder.group({
    location: ['', [Validators.required]],
    address: [''],
  });

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly eventsService: EventsService,
    private readonly userStore: UserStoreService,
  ) {}

  get controlsFirst(): { [p: string]: AbstractControl } {
    return this.createEventFormFirst.controls;
  }
  get controlsSecond(): { [p: string]: AbstractControl } {
    return this.createEventFormSecond.controls;
  }
  get controlsThird(): { [p: string]: AbstractControl } {
    return this.createEventFormThird.controls;
  }

  onNextStep(stepper: MatStepper, formGroup: FormGroup) {
    this.markAllAsTouched(formGroup);

    if (formGroup.valid) {
      stepper.next();
    }
  }

  private markAllAsTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();
    });
  }


  // onSubmit() {
  //   if (this.createEventForm.invalid) {
  //     return;
  //   }

  //   const request: CreateEventRequest = {
  //     title: this.createEventForm.value.title,
  //     description: this.createEventForm.value.description,
  //     dateFrom: this.createEventForm.value.dateFrom,
  //     dateTo: this.createEventForm.value.dateTo,
  //     location: this.createEventForm.value.location,
  //   }

  //   if(this.createEventForm.value.tagline) {
  //     request.tagline = this.createEventForm.value.tagline;
  //   }
  //   if(this.createEventForm.value.address) {
  //     request.address = this.createEventForm.value.address;
  //   }

  //   this.eventsService
  //     .postEvent(request)
  //     .subscribe((data: any) => {
  //       console.log('event created!', data)
  //     })
  // }

  submitEvent() {
    if (this.createEventFormFirst.valid && this.createEventFormSecond.valid && this.createEventFormThird) {
      // combine the data from the form groups
      const eventData = {
        ...this.createEventFormFirst.value,
        ...this.createEventFormSecond.value,
        ...this.createEventFormThird.value,
        // include other steps data if any
      };
  
      // Call service to submit data or handle the submission logic
      console.log(eventData);
    }
  }

}
