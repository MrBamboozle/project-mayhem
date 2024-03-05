import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { EventsFiltersComponent } from '@app/shared/components/events-filters/events-filters.component';
import { SignInModalComponent } from '@app/shared/modals/sign-in.modal/sign-in.modal.component';
import { Event } from '@app/shared/models/event';
import { InitialEventFilters } from '@app/shared/models/filter';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { NgbCollapseModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [EventsFiltersComponent, NgbCollapseModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {
  public areFiltersCollapsed: boolean = true;
  private homepageMap!: MapWrapper;

  public events: Event[] = [];

  public initialEventFilters: InitialEventFilters = {};

  constructor(
    private readonly _router: Router,
    private readonly eventsService: EventsService,
    private readonly userStore: UserStoreService,
    private readonly modalService: ModalHelperService,
  ) {
    const currentDate = new Date();
    const oneWeekFromNow = new Date(currentDate.getTime() + 7 * 24 * 60 * 60 * 1000);

    this.initialEventFilters.startTime = currentDate.toISOString().slice(0, -1);
    this.initialEventFilters.startTimeTo = oneWeekFromNow.toISOString().slice(0, -1);
  }

  ngOnInit(): void {
    const queryParams = `?filter[startTime]=${this.initialEventFilters.startTime}&filter[startTimeTo]=${this.initialEventFilters.startTimeTo}`
    this.fetchHomepageEvents(queryParams);
  }

  ngAfterViewInit(): void {
    this.initMap();
  }

  public onCreateClick(): void {
    if(this.userStore.isSignedIn) {
      this._router.navigate(['events', 'create'])
    } else {
      this.modalService.openSignInModal(() => {
        this._router.navigate(['events', 'create']);
      });
    }
  }

  public onFilterChange(queryParams: string): void {
    this.fetchHomepageEvents(queryParams);
  }

  private fetchHomepageEvents(queryParams: string): void {
    this.eventsService.getAllEvents(queryParams).subscribe((data) => {
      this.events = data;

      this.homepageMap.clearTooltipsAndMarkers();

      this.events.forEach((event) => {
        this.homepageMap.addTooltip(event.location, event.title, true, () => {
          this._router.navigate(['events', event.id]);
        })
        this.homepageMap.addMarker(event.location);
      })
    })
  }

  private initMap(): void {
    this.homepageMap = new MapWrapper(`homepage-map`)
      .setView()
  }

}
