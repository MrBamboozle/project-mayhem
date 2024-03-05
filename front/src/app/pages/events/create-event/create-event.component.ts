import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, ElementRef, Renderer2, ViewChild } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { EventsService } from '@app/services/events.service';
import { Category, CreateEventRequest, Event } from '@app/shared/models/event';
import { dateTimeValidator } from '@app/shared/validators/date-time.validator';
import { MatStepper, MatStepperModule } from '@angular/material/stepper';
import { dateRangeValidator } from '@app/shared/validators/date-range.validator';
import * as L from 'leaflet';
import { LocationService } from '@app/services/location.service';
import { LocationRequest } from '@app/shared/models/location';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { formatAddress, formatDateToDateTimeLocal } from '@app/shared/utils/formatters';
import { CategoriesService } from '@app/services/categories.service';
import { EventDisplayComponent } from '@app/shared/components/event-display/event-display.component';
import { ActivatedRoute, Router } from '@angular/router';
import { BehaviorSubject } from 'rxjs';
import { CategoryPickerComponent } from '@app/shared/components/category-picker/category-picker.component';
import { UserStoreService } from '@app/shared/stores/user.store.service';

@Component({
  selector: 'app-create-event',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, MatStepperModule, EventDisplayComponent, CategoryPickerComponent],
  templateUrl: './create-event.component.html',
  styleUrl: './create-event.component.scss'
})
export class CreateEventComponent {
  @ViewChild(CategoryPickerComponent) categoryPicker!: CategoryPickerComponent;
  @ViewChild('stepper') stepper!: MatStepper;

  public fetchedEvent: BehaviorSubject<boolean> = new BehaviorSubject(false);

  public shouldDisplayPreview: boolean = false;

  public categories: Category[] = [];
  public initialSelectedCategoryIds: string[] = [];

  private map!: MapWrapper;

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

  private createEventFormGroups: FormGroup[] = [
    this.createEventFormFirst,
    this.createEventFormSecond,
    this.createEventFormThird
  ]

  public uuid: string = '';
  public event!: Event;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly locationService: LocationService,
    private readonly eventsService: EventsService,
    private readonly categoriesService: CategoriesService,
    private readonly el: ElementRef,
    private readonly renderer: Renderer2,
    private readonly _router: Router,
    private readonly _route: ActivatedRoute,
    private readonly _changeDetectorRef: ChangeDetectorRef,
    private readonly userStore: UserStoreService
  ) {}

  ngOnInit(): void {
    this.categoriesService.getCategories().subscribe(
      (data: Category[]) => {
        this.categories = data;
      }
    )

    this._route.paramMap.subscribe(params => {
      if(params.get('uuid') !== null) {
        this.uuid = params.get('uuid') as string;
        this.fetchEvent();
      } else {
        this.fetchedEvent.next(true);
        setTimeout(() => this.initMap());
      }

    });
  }

  ngAfterViewInit(): void {
    this.fetchedEvent.subscribe((fetched) => {
      if(fetched) {
        // Listen for form changes and adjust stepper mode
        this.createEventFormGroups.forEach((group: FormGroup) => {
          group.statusChanges.subscribe(status => {
            this.checkFormsValidity();
          });
        })
      }
    })
  }

  private checkFormsValidity() {
    const allValid = this.createEventFormFirst.valid && this.createEventFormSecond.valid && this.createEventFormThird.valid;
    if (!allValid) {
      // If any form is invalid, enforce linear mode and step completion based on validity
      this.stepper.linear = true;  
    } else {
      this.stepper.linear = false;  
    }
    for(let index = 0; index < this.stepper.steps.length; index++) {
      const step = this.stepper.steps.get(index)!;
      step.completed = this.createEventFormGroups[index] ? this.createEventFormGroups[index].valid : true;

      if(step.completed === false) {
        for(let i = index+1; i < this.stepper.steps.length; i++) {
          this.stepper.steps.get(i)!.completed = false;
        }
        break;
      }
    }
    this.stepper._stateChanged();    
    this._changeDetectorRef.detectChanges();     
  }

  private fetchEvent(): void {
    this.eventsService.getEvent(this.uuid).subscribe(
      (data) => {
        this.event = data;
        this.controlsFirst.title.setValue(this.event.title);
        this.controlsFirst.tagline.setValue(this.event.tagLine);
        this.controlsFirst.description.setValue(this.event.description);
        this.initialSelectedCategoryIds = this.event.categories.map((category: Category) => category.id);
        this.controlsFirst.categories.setValue(this.initialSelectedCategoryIds);
        this.controlsSecond.dateFrom.setValue(formatDateToDateTimeLocal(this.event.startTime));
        this.controlsSecond.dateTo.setValue(formatDateToDateTimeLocal(this.event.endTime));
        this.controlsThird.location.setValue(this.event.location);
        this.controlsThird.address.setValue(JSON.parse(this.event.address));

        this.fetchedEvent.next(true);
        setTimeout(() => {
          this.initMap(true)

          this.stepper.steps.forEach(step => {
            step.completed = true;
            // If using FormGroup within steps, you might also need to mark them as valid
            // e.g., if(step.stepControl) step.stepControl.setErrors(null);
          });
          // Optionally, if you want to update the visual state immediately
          this.stepper._stateChanged();         
        });
      }
    )
  }

  private initMap(existingEvent: boolean = false): void {
    if(existingEvent) {
      this.map = new MapWrapper('map')
        .setView(this.event.location)
        .addUniqueMarker(this.event.location);

      this.isLocationSelected = true;
    } else {
      this.map = new MapWrapper('map').setView();
    }

    this.map.onClick((e:L.LeafletMouseEvent) => {
      const coords = e.latlng;
      const locationString = `${coords.lat},${coords.lng}`;
      
      this.controlsThird.location.setValue(locationString);
      
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

  get formattedAddress(): string {
    return formatAddress(this.controlsThird.address.getRawValue());
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

  get previewEvent(): Omit<Event, 'createdAt' | 'updatedAt'> {

    const eventData = {
      ...this.createEventFormFirst.value,
      ...this.createEventFormSecond.value,
      ...this.createEventFormThird.value,
      // include other steps data if any
    };

    
    const event: Omit<Event, 'createdAt' | 'updatedAt'> = {
      title: eventData.title,
      description: eventData.description,
      startTime: eventData.dateFrom,
      endTime: eventData.dateTo,
      location: eventData.location,
      categories: this.selectedCategories,
      tagLine: eventData.tagline,
      address: JSON.stringify(eventData.address),
      id: 'preview',
      engagingUsersTypes: this.event ? this.event.engagingUsersTypes : [],
      creator: this.userStore.currentUser.getValue()
    }

    return event;
  }

  onNextStep(stepper: MatStepper, formGroup: FormGroup) {
    this.markAllAsTouched(formGroup);
    this.closeDropdown();

    if (formGroup.valid) {
      stepper.next();
    }

    if(stepper.selectedIndex === 3) {
      this.shouldDisplayPreview = true;
    }
  }

  private markAllAsTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();
    });
  }

  onSelectedCategoryChange(selectedIds: string[]) {
    this.controlsFirst.categories.setValue(selectedIds);
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

  closeDropdown() {
    this.categoryPicker.closeDropdown();
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

      if(this.event) {
        this.eventsService
          .patchEvent(this.event.id, request)
          .subscribe((data: any) => {
            this._router.navigate(['events', data.id]);
          })
      } else {
        this.eventsService
          .postEvent(request)
          .subscribe((data: any) => {
            this._router.navigate(['events', data.id]);
          })
      }
    }
  }

}
