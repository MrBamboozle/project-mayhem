import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Router } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { EventsFiltersComponent } from '@app/shared/components/events-filters/events-filters.component';
import { Event } from '@app/shared/models/event';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [EventsFiltersComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {

  private homepageMap!: MapWrapper;

  public events: Event[] = [];

  constructor(
    private readonly _router: Router,
    private readonly eventsService: EventsService,
  ) {}

  ngOnInit(): void {
    this.fetchHomepageEvents();
  }

  ngAfterViewInit(): void {
    this.initMap();
  }

  private fetchHomepageEvents(): void {
    this.eventsService.getAllEvents().subscribe((data) => {
      this.events = data;

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
