import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { Event } from '@app/shared/models/event';
import { NgbCollapseModule, NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';
import { ExpandableFolderComponent } from './components/expandable-folder/expandable-folder.component';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { EventsFiltersComponent } from '@app/shared/components/events-filters/events-filters.component';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';

@Component({
  selector: 'app-event-list',
  standalone: true,
  imports: [RouterLink, CommonModule, NgbPaginationModule, FormsModule, ExpandableFolderComponent, EventsFiltersComponent, NgbCollapseModule],
  templateUrl: './event-list.component.html',
  styleUrl: './event-list.component.scss',
})
export class EventListComponent {
  public areFiltersCollapsed: boolean = true;
  public profileEvents: boolean = false;

  public eventList: Event[] = [];

  public currentPage: number = 1;
  public pageSize: number = 10; // Set the number of items per page
  public collectionSize: number = 0; // Total number of items

  constructor(
    private readonly _router: Router,
    private readonly _route: ActivatedRoute,
    private readonly eventsService: EventsService,
    private readonly userStore: UserStoreService,
    private readonly modalService: ModalHelperService,
  ) {}

  ngOnInit(): void {
    this._route.data.subscribe(data => {
      if (data.profileEvents) {
        this.profileEvents = true;
      }
      this.fetchEvents(this.currentPage);
    });
  }

  fetchEvents(page: number, filterQueryParams?: string): void {
    let queryParams = `${filterQueryParams ? filterQueryParams + '&' : '?'}page=${page}`;
    if (this.profileEvents) queryParams += `&filter[userId]=${this.userStore.currentUser.getValue().id}`;

    this.eventsService.getEvents(queryParams).subscribe(
      (paginatedData) => {
        this.eventList = paginatedData.data;
        this.currentPage = paginatedData.current_page;
        this.pageSize = paginatedData.per_page;
        this.collectionSize = paginatedData.total;
      }
    )
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

  onFilterChange(queryParams: string): void {
    this.fetchEvents(1, queryParams);
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.fetchEvents(page);
  }

}
