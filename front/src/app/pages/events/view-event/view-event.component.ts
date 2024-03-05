import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EventsService } from '@app/services/events.service';
import { EventDisplayComponent } from '@app/shared/components/event-display/event-display.component';
import { EngagementType, EngagementTypeRequest, Event } from '@app/shared/models/event';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';
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
    public userStore: UserStoreService,
    private readonly modalService: ModalHelperService
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
        this.checkCreator();
      }
    )
  }

  public editEvent(): void {
    this._router.navigate(['events', this.event.id, 'edit']);
  }
  
  public deleteEvent(): void {
    this.modalService.openCofirmModal(
      'Delete event?',
      'Are you sure you want to delete this event?',
      () => {
        this.eventsService.deleteEvent(this.event.id).subscribe(() => {
          this._router.navigate(['events']);
        });
      }
    );
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
    if(this.userStore.isSignedIn) {
      const req: EngagementTypeRequest = {
        engagementType: EngagementType.watch
      };
  
      this.eventsService.engageEvent(this.event.id, req).subscribe(
        (data) => {
          this.event = data;
        }
      );
    } else {
      this.modalService.openSignInModal(() => {
        this.checkCreator();
      });
    }
  }

  public attendEvent(): void {
    if(this.userStore.isSignedIn) {
      const req: EngagementTypeRequest = {
        engagementType: EngagementType.attend
      };
  
      this.eventsService.engageEvent(this.event.id, req).subscribe(
        (data) => {
          this.event = data;
        }
      );
    } else {
      this.modalService.openSignInModal(() => {
        this.checkCreator();
      });
    }
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

  public checkCreator(): void {
    this.isCurrentUserCreator = this.userStore.currentUser.getValue().id === this.event.creator.id;
  }

}
