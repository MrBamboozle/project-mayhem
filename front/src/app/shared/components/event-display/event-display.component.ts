import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { config } from '@app/core/app-config';
import { EngagementType, Event } from '@app/shared/models/event';
import { User } from '@app/shared/models/user';
import { formatAddress, formatDateToLocale, formatTopSecretFontTitle } from '@app/shared/utils/formatters';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-event-display',
  standalone: true,
  imports: [CommonModule, NgbDropdownModule],
  templateUrl: './event-display.component.html',
  styleUrl: './event-display.component.scss'
})
export class EventDisplayComponent {
  @Input() event!: Omit<Event, 'creator' | 'createdAt' | 'updatedAt'>;

  private previewMap!: MapWrapper;

  ngAfterViewInit(): void {
    this.initMap();
  }

  private initMap(): void {
    this.previewMap = new MapWrapper(`preview-map-${this.event.id }`)
      .setView(this.event.location)
      .addUniqueMarker(this.event.location);
  }

  get formattedTitle(): string {
    return formatTopSecretFontTitle(this.event.title);
  }

  get formattedAddress(): string {
    return formatAddress(JSON.parse(this.event.address));
  }

  get formattedDateFrom(): string {
    return formatDateToLocale(this.event.startTime);
  }

  get formattedDateTo(): string {
    return formatDateToLocale(this.event.endTime);
  }

  get watchers(): User[] {
    return this.event.engagingUsersTypes
      .filter((engagingUserType) => engagingUserType.engagementType === EngagementType.watch)
      .map((engagingUserType) => engagingUserType.user);
  }

  get attendees(): User[] {
    return this.event.engagingUsersTypes
      .filter((engagingUserType) => engagingUserType.engagementType === EngagementType.attend)
      .map((engagingUserType) => engagingUserType.user);
  }

  public userAvatarSrc(user: User): string {
    return `${config.BACKEND_URL}${user.avatar.path}`;
  }

}
