import { Component } from '@angular/core';
import * as L from 'leaflet';

@Component({
  selector: 'app-event-list',
  standalone: true,
  imports: [],
  templateUrl: './event-list.component.html',
  styleUrl: './event-list.component.scss'
})
export class EventListComponent {
  private map: any;
  private marker: any;

  constructor() {}

  ngAfterViewInit(): void {
    this.initMap();
  }

  private initMap(): void {
    this.map = L.map('map');

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(this.map);

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      const coords = position.coords;
      const latLng = L.latLng(coords.latitude, coords.longitude);
      this.map.setView(latLng, 13);
      this.addMarker(latLng);
    }, () => {
      console.log('Unable to retrieve your location');
      this.map.setView([51.505, -0.09], 13); // Default location
    });
  } else {
    console.log('Geolocation is not supported by this browser.');
    this.map.setView([51.505, -0.09], 13); // Default location
  }

  this.map.on('click', (e: L.LeafletMouseEvent) => {
    const coord = e.latlng;
    this.displayCoordinates(coord.lat, coord.lng);
    this.addMarker(coord);
  });
  }

  private displayCoordinates(lat: number, lng: number): void {
    const coordElement = document.getElementById('coordinates');
    coordElement!.innerHTML = `Latitude: ${lat.toFixed(5)}, Longitude: ${lng.toFixed(5)}`;
  }

  private addMarker(coord: L.LatLng): void {
    const customIcon = L.icon({
      iconUrl: 'assets/images/pin.png',
      iconSize: [56, 48], // size of the icon
      iconAnchor: [33, 30], // point of the icon which will correspond to marker's location
      popupAnchor: [1, -34] // point from which the popup should open relative to the iconAnchor
    });

    if (this.marker) {
      this.map.removeLayer(this.marker);
    }
    this.marker = L.marker([coord.lat, coord.lng], { icon: customIcon }).addTo(this.map);
  }
}
