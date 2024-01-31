import { Component, Input } from '@angular/core';
import { trigger, state, style, transition, animate } from '@angular/animations';
import { CommonModule } from '@angular/common';
import { Event } from '@app/shared/models/event';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';

@Component({
  selector: 'app-expandable-folder',
  templateUrl: './expandable-folder.component.html',
  styleUrls: ['./expandable-folder.component.scss'],
  standalone: true,
  imports: [CommonModule],
  animations: [
    trigger('folderState', [
      state('closed', style({
        transform: 'rotateX(0deg)'
      })),
      state('open', style({
        transform: 'rotateX(-180deg)'
      })),
      transition('closed => open', animate('500ms ease-out')),
      transition('open => closed', animate('500ms 500ms ease-out')),
    ]),
    trigger('contentHeight', [
      state('closed', style({
        'max-height': '130px'
      })),
      state('open', style({
        'max-height': '800px'
      })),
      transition('closed => open', animate('500ms 200ms ease-out')),
      transition('open => closed', animate('500ms ease-out')),
    ]),
  ]
})
export class ExpandableFolderComponent {
  @Input() event!: Event;

  folderState = 'closed';
  overflowStyle = 'hidden';
  closeButtonStyle = 'back';

  private previewMap!: MapWrapper;

  ngAfterViewInit(): void {
    this.initMap();
  }

  private initMap(): void {
    this.previewMap = new MapWrapper(`preview-map-${this.event.id}`)
      .setView(this.event.location)
      .addMarker(this.event.location);
  }

  toggleFolder() {
    this.folderState = (this.folderState === 'closed') ? 'open' : 'closed';
    if (this.folderState === 'open') {
      this.overflowStyle = 'hidden';
      setTimeout(() => this.overflowStyle = 'visible', 200);
      this.closeButtonStyle = 'front';
    } else {
      setTimeout(() => this.overflowStyle = 'hidden', 500);
      setTimeout(() => this.closeButtonStyle = 'back', 1000);
    }
  }

  get formattedTitle(): string {
    return this.event.title.split(' ').map(word => `{${word}}`).join('');
  }

  get formattedAddress(): string {
    const address = JSON.parse(this.event.address);
    return address ? `${address?.road} ${address?.houseNumber}, ${address?.city}` : ''; //TODO: Format address display better
  }

  get formattedDates(): string {
    const startDate = new Date(this.event.startTime);
    const endDate = new Date(this.event.endTime);
    return `From: ${startDate.toLocaleString()}, To: ${endDate.toLocaleString()}`;
  }
}
