import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { EventDisplayComponent } from '@app/shared/components/event-display/event-display.component';
import { EngagementType, EngagementTypeRequest, Event } from '@app/shared/models/event';
import { UserStoreService } from '@app/shared/stores/user.store.service';
import { BehaviorSubject } from 'rxjs';

@Component({
  selector: 'app-view-event',
  standalone: true,
  imports: [EventDisplayComponent, CommonModule],
  templateUrl: './view-event.component.html',
  styleUrl: './view-event.component.scss'
})
export class ViewEventComponent {

  public fetchedEvent: BehaviorSubject<boolean> = new BehaviorSubject(false);
  public isCurrentUserCreator: boolean = false;

  public uuid: string = '';
  public event!: Event;

  constructor(
    private readonly _route: ActivatedRoute,
    private readonly _router: Router,
    private readonly eventsService: EventsService,
    private readonly userStore: UserStoreService
  ) { }

  ngOnInit(): void {
    this._route.paramMap.subscribe(params => {
      if(params.get('uuid') !== null) {
        this.uuid = params.get('uuid') as string;
      }

      this.fetchEvent();
    });
  }

  private fetchEvent(): void {
    this.eventsService.getEvent(this.uuid).subscribe(
      (data) => {
        this.event = data;
        this.fetchedEvent.next(true);

        this.isCurrentUserCreator = this.userStore.currentUser.getValue().id === this.event.creator.id;
      }
    )
  }

  public editEvent(): void {
    this._router.navigate(['events', this.event.id, 'edit']);
  }

  get isWatching(): boolean {
    return this.event.engagingUsersTypes.some(
      (engagingUserType) => engagingUserType.engagementType === EngagementType.watch && this.userStore.currentUser.getValue().id === engagingUserType.user.id
    );
  }

  get isAttending(): boolean {
    return this.event.engagingUsersTypes.some(
      (engagingUserType) => engagingUserType.engagementType === EngagementType.attend && this.userStore.currentUser.getValue().id === engagingUserType.user.id
    );
  }

  public watchEvent(): void {
    const req: EngagementTypeRequest = {
      engagementType: EngagementType.watch
    };

    this.eventsService.engageEvent(this.event.id, req).subscribe(
      (data) => {
        this.event = data;
      }
    );
  }

  public attendEvent(): void {
    const req: EngagementTypeRequest = {
      engagementType: EngagementType.attend
    };

    this.eventsService.engageEvent(this.event.id, req).subscribe(
      (data) => {
        this.event = data;
      }
    );
  }

  public detachEvent(): void {
    const req: EngagementTypeRequest = {
      engagementType: EngagementType.detach
    };

    this.eventsService.engageEvent(this.event.id, req).subscribe(
      (data) => {
        this.event = data;
      }
    );
  }

}
