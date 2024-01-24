import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { EventsService } from '@app/services/events.service';
import { CreateEventRequest } from '@app/shared/models/event';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { dateTimeValidator } from '@app/shared/validators/date-time.validator';
import { MatStepper, MatStepperModule } from '@angular/material/stepper';
import { dateRangeValidator } from '@app/shared/validators/date-range.validator';
import * as L from 'leaflet';
import { LocationService } from '@app/services/location.service';
import { LocationRequest } from '@app/shared/models/location';

@Component({
  selector: 'app-create-event',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatStepperModule],
  templateUrl: './create-event.component.html',
  styleUrl: './create-event.component.scss'
})
export class CreateEventComponent {
  private map: any;
  private marker: any;

  public fetchingAddress: boolean = false;

  public createEventFormFirst: FormGroup  = this.formBuilder.group({
    title: ['', [Validators.required]],
    tagline: [''],
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
    private readonly userStore: UserStoreService,
  ) {}

  ngAfterViewInit(): void {
    this.initMap();
  }

  private initMap(): void {
    this.map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(this.map);

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        const coords = position.coords;
        const latLng = L.latLng(coords.latitude, coords.longitude);
        this.map.setView(latLng, 13);
      }, () => {
        console.log('Unable to retrieve your location');
        this.map.setView([51.505, -0.09], 13); // Default location
      });
    } else {
      console.log('Geolocation is not supported by this browser.');
      this.map.setView([51.505, -0.09], 13); // Default location
    }

    this.map.on('click', (e: L.LeafletMouseEvent) => {
      const coord = e.latlng;
      const locationString = `${coord.lat},${coord.lng}`;
      this.fetchingAddress = true;
      
      const request: LocationRequest = {
        location: locationString
      };

      this.controlsThird.location.setValue(locationString);
      this.addMarker(coord);
      
      this.locationService.postLocation(request).subscribe(
        (data) => {
          this.fetchingAddress = false;
          
          this.controlsThird.address.setValue(data)
        }
      )
    });
  }

  get formattedAddress(): string {
    const address = this.controlsThird.address.getRawValue();
    return address ? `${address?.road} ${address?.houseNumber}, ${address?.city}` : '';
  }

  private addMarker(coord: L.LatLng): void {
    const customIcon = L.icon({
      iconUrl: 'assets/images/pin.png',
      iconSize: [56, 48], // size of the icon
      iconAnchor: [33, 30], // point of the icon which will correspond to marker's location
      popupAnchor: [1, -34] // point from which the popup should open relative to the iconAnchor
    });

    if (this.marker) {
      this.map.removeLayer(this.marker);
    }
    this.marker = L.marker([coord.lat, coord.lng], { icon: customIcon }).addTo(this.map);
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
