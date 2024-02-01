import { Component, Input } from '@angular/core';
import { trigger, state, style, transition, animate } from '@angular/animations';
import { CommonModule } from '@angular/common';
import { Event } from '@app/shared/models/event';
import { MapWrapper } from '@app/shared/wrappers/map-wrapper';
import { formatAddress, formatDateToLocale, formatTopSecretFontTitle } from '@app/shared/utils/formatters';

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
}
