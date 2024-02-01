import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { EventsService } from '@app/services/events.service';
import { CreateEventRequest } from '@app/shared/models/event';
import { dateTimeValidator } from '@app/shared/validators/date-time.validator';
import { MatStepper, MatStepperModule } from '@angular/material/stepper';
import { dateRangeValidator } from '@app/shared/validators/date-range.validator';
import * as L from 'leaflet';
import { LocationService } from '@app/services/location.service';
import { LocationRequest } from '@app/shared/models/location';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { formatAddress, formatDateToLocale, formatTopSecretFontTitle } from '@app/shared/utils/formatters';

@Component({
  selector: 'app-create-event',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatStepperModule],
  templateUrl: './create-event.component.html',
  styleUrl: './create-event.component.scss'
})
export class CreateEventComponent {
  private map!: MapWrapper;
  private reviewMap!: MapWrapper;

  public isLocationSelected: boolean = false;
  public isFetchingAddress: boolean = false;

  public createEventFormFirst: FormGroup  = this.formBuilder.group({
    title: ['', [Validators.required, Validators.maxLength(40)]],
    tagline: ['', [Validators.maxLength(140)]],
    description: ['', [Validators.required]],
    categories: [[]],
  })

  public createEventFormSecond: FormGroup  = this.formBuilder.group({
    dateFrom: ['', [Validators.required, dateTimeValidator()]],
    dateTo: ['', [Validators.required, dateTimeValidator()]],
  }, { validators: dateRangeValidator() });

  public createEventFormThird: FormGroup  = this.formBuilder.group({
    location: ['', [Validators.required]],
    address: [''],
  });

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly locationService: LocationService,
    private readonly eventsService: EventsService,
  ) {}

  ngAfterViewInit(): void {
    this.initMaps();
  }

  private initMaps(): void {
    this.map = new MapWrapper('map').setView();
    this.reviewMap = new MapWrapper('review-map');

    this.map.onClick((e:L.LeafletMouseEvent) => {
      const coords = e.latlng;
      const locationString = `${coords.lat},${coords.lng}`;
      
      this.controlsThird.location.setValue(locationString);
      
      this.reviewMap.setView(coords, 15).addMarker(coords);
      
      const request: LocationRequest = {
        location: locationString
      };
      
      this.isLocationSelected = true;
      this.isFetchingAddress = true;
      this.locationService.postLocation(request).subscribe(
        (data) => {
          this.isFetchingAddress = false;
          
          this.controlsThird.address.setValue(data)
        }
      )
    })
  }

  get formattedTitle(): string {
    return formatTopSecretFontTitle(this.createEventFormFirst.get('title')?.value);
  }

  get formattedAddress(): string {
    return formatAddress(this.controlsThird.address.getRawValue());
  }

  get formattedDateFrom(): string {
    return formatDateToLocale(this.createEventFormSecond.get('dateFrom')?.value);
  }

  get formattedDateTo(): string {
    return formatDateToLocale(this.createEventFormSecond.get('dateTo')?.value);
  }

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

  submitEvent() {
    if (this.createEventFormFirst.valid && this.createEventFormSecond.valid && this.createEventFormThird.valid) {
      // combine the data from the form groups
      const eventData = {
        ...this.createEventFormFirst.value,
        ...this.createEventFormSecond.value,
        ...this.createEventFormThird.value,
        // include other steps data if any
      };

      
      const request: CreateEventRequest = {
        title: eventData.title,
        description: eventData.description,
        startTime: eventData.dateFrom,
        endTime: eventData.dateTo,
        location: eventData.location,
      }
  
      if(eventData.tagline) {
        request.tagLine = eventData.tagline;
      }
      if(eventData.address) {
        request.address = eventData.address;
      }
  
      this.eventsService
        .postEvent(request)
        .subscribe((data: any) => {
          console.log('event created!', data)
        })

    }
  }

}
