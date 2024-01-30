import { Component, Input } from '@angular/core';
import { trigger, state, style, transition, animate } from '@angular/animations';
import { CommonModule } from '@angular/common';
import { Event } from '@app/shared/models/event';
import * as L from 'leaflet';

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

  private previewMap: any;
  private previewMarker: any;

  ngAfterViewInit(): void {
    this.initMap();
  }

  private initMap(): void {
    this.previewMap = L.map(`preview-map-${this.event.id}`);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(this.previewMap);

    const location = this.event.location.split(',');

    const coord: any = {
      lat: location[0],
      lng: location[1],
    }

    
    this.previewMap.setView(coord, 13); // Default location

    const customIcon = L.icon({
      iconUrl: 'assets/images/pin.png',
      iconSize: [56, 48], // size of the icon
      iconAnchor: [33, 30], // point of the icon which will correspond to marker's location
      popupAnchor: [1, -34] // point from which the popup should open relative to the iconAnchor
    });

    this.previewMarker = L.marker([coord.lat, coord.lng], { icon: customIcon }).addTo(this.previewMap);
      
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
