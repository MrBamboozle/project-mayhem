import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { Event } from '@app/shared/models/event';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
import { NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';
import { Subject } from 'rxjs';

interface ExpandableEvent extends Event {
  isExpanded: boolean;
}

@Component({
  selector: 'app-event-list',
  standalone: true,
  imports: [RouterLink, CommonModule, NgbPaginationModule, FormsModule],
  templateUrl: './event-list.component.html',
  styleUrl: './event-list.component.scss'
})
export class EventListComponent {

  public eventList: ExpandableEvent[] = [];

  public currentPage: number = 1;
  public pageSize: number = 10; // Set the number of items per page
  public collectionSize: number = 0; // Total number of items

  public searchTerm: string = '';
  private sortDirection: { [key: string]: 'asc' | 'desc' | '' } = {
    name: '',
    email: '',
    // Add other columns as needed
  };

  private searchSubject: Subject<string> = new Subject();

  constructor(
    private readonly eventsService: EventsService
  ) {}

  ngOnInit(): void {
    this.fetchEvents(this.currentPage);
  }

  fetchEvents(page: number): void {
    let queryParams = `?page=${page}`;
    if (this.searchTerm) queryParams += `&filter[all]=${encodeURIComponent(this.searchTerm)}`;

    // Construct sorting query parameters
    // const sortingParams = Object.entries(this.sortDirection)
    //   .filter(([_, dir]) => dir)
    //   .map(([col, dir]) => `sort[${col}]=${dir}`)
    //   .join('&');
    // if (sortingParams) queryParams += `&${sortingParams}`;

    this.eventsService.getEvents(queryParams).subscribe(
      (paginatedData) => {
        this.eventList = paginatedData.data.map((event: Event) => {return {isExpanded: false, ...event}});
        this.currentPage = paginatedData.current_page;
        this.pageSize = paginatedData.per_page;
        this.collectionSize = paginatedData.total;
      }
    )
  }

  onSearchChange(): void {
    // Implement search logic
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.fetchEvents(page);
  }

  toggleExpand(event: any): void {
    event.isExpanded = !event.isExpanded;
  }


}
