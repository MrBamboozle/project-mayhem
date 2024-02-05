import { CommonModule } from '@angular/common';
import { Component, ElementRef, Renderer2, ViewChild } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { EventsService } from '@app/services/events.service';
import { Category, CreateEventRequest } from '@app/shared/models/event';
import { dateTimeValidator } from '@app/shared/validators/date-time.validator';
import { MatStepper, MatStepperModule } from '@angular/material/stepper';
import { dateRangeValidator } from '@app/shared/validators/date-range.validator';
import * as L from 'leaflet';
import { LocationService } from '@app/services/location.service';
import { LocationRequest } from '@app/shared/models/location';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { formatAddress, formatDateToLocale, formatTopSecretFontTitle } from '@app/shared/utils/formatters';
import { CategoriesService } from '@app/services/categories.service';
import { NgbDropdown, NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-create-event',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, MatStepperModule, NgbDropdownModule],
  templateUrl: './create-event.component.html',
  styleUrl: './create-event.component.scss'
})
export class CreateEventComponent {
  @ViewChild('dropdown') dropdown!: NgbDropdown;

  public categories: Category[] = [];
  public filteredCategories: Category[] = this.categories;
  public searchTerm: string = '';

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
    private readonly categoriesService: CategoriesService,
    private readonly el: ElementRef,
    private readonly renderer: Renderer2
  ) {}

  ngOnInit(): void {
    this.categoriesService.getCategories().subscribe(
      (data: Category[]) => {
        console.log(data)
        this.categories = data;
        this.filteredCategories = this.categories;
      }
    )
  }

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
    this.closeDropdown();

    if (formGroup.valid) {
      stepper.next();
    }
  }

  private markAllAsTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();
    });
  }

  // Category picking
  closeDropdown() {
    if (this.dropdown.isOpen()) {
      this.dropdown.close();
    }
  }

  preventClose(event: MouseEvent): void {
    // Check if the dropdown is already open; if so, stop propagation
    if (this.dropdown.isOpen()) {
      event.stopPropagation();
    }
  }

  onDropdownOpenChange(isOpen: boolean): void {
    const elements = this.el.nativeElement.querySelectorAll('.mat-horizontal-content-container');
    elements.forEach((element: any) => {
      this.renderer.setStyle(element, 'overflow', isOpen ? 'visible' : 'hidden');
    });
  }

  get selectedCategories(): Category[] {
    return this.categories.filter(category => this.controlsFirst.categories.value.some((selectedId: string) => selectedId === category.id));
  }

  filterCategories() {
    this.filteredCategories = this.categories.filter(category => 
      category.name.toLowerCase().includes(this.searchTerm.toLowerCase()) &&
      !this.controlsFirst.categories.value.some((selectedId: string) => selectedId === category.id));
  }

  selectCategory(category: Category) {
    // Add category ID to the form control
    const categories = this.controlsFirst.categories.value;
    if (!categories.includes(category.id)) {
      this.controlsFirst.categories.setValue([...categories, category.id]);
    }
  }

  removeCategory(categoryToRemove: Category) {
    // Remove category ID from the form control
    const categories = this.controlsFirst.categories.value;
    this.controlsFirst.categories.setValue(categories.filter((id: string) => id !== categoryToRemove.id));
  }

  open(event: Event) {
    this.filterCategories(); // Ensure dropdown opens with all items
    if (!this.dropdown.isOpen()) {
      setTimeout(() => {
        this.dropdown.open();
      }, 10)
    } else {
      event.stopPropagation()
    }
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
  
      if(eventData.categories) {
        request.categories = eventData.categories;
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
