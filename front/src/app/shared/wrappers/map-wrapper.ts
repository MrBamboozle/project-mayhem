import * as L from 'leaflet';
import { ToastService } from '../services/toaster.service';
import { AppInjector } from '@app/core/service/app-injector.service';
import { DomSanitizer } from '@angular/platform-browser';
import { SecurityContext } from '@angular/core';

export function coordsFromString(location: string): L.LatLng {
  const splitLocation = location.split(',').map((segment: string) => Number(segment));

  return L.latLng(splitLocation[0], splitLocation[1]);
}
export class MapWrapper {

  protected toasterService: ToastService;
  protected sanitizer: DomSanitizer;


  private readonly map: L.Map;
  private marker: L.Marker;

  private markers: L.Marker[] = [];
  private tooltips: L.Marker[] = [];

  private readonly pinIcon: L.Icon = L.icon({
    iconUrl: 'assets/images/pin.png',
    iconSize: [56, 48], // size of the icon
    iconAnchor: [33, 30], // point of the icon which will correspond to marker's location
    popupAnchor: [1, -34] // point from which the popup should open relative to the iconAnchor
  });

  constructor(
    id: string,
    maxZoom: number = 19
  ) {
    this.toasterService = AppInjector.getInjector().get(ToastService);
    this.sanitizer = AppInjector.getInjector().get(DomSanitizer)
    this.map = L.map(id);
    this.marker = L.marker([51.505, -0.09], { icon: this.pinIcon })

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: maxZoom,
    }).addTo(this.map);
  }

  public setView(location: string | L.LatLng = '', zoom: number = 13): this {
    if(location) {
      let coords: L.LatLng = typeof location === 'string' ? coordsFromString(location) : location;

      this.map.setView(coords, zoom);

      return this;
    }

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        const coords = position.coords;
        const latLng = L.latLng(coords.latitude, coords.longitude);
        return this.map.setView(latLng, 13);
      }, () => {
        this.toasterService.showError('Unable to retrieve your location.');
        this.map.setView([51.505, -0.09], 13); // Default location
      });
    } else {
      this.toasterService.showError('Geolocation is not supported by this browser.');
    }
    
    this.map.setView([51.505, -0.09], 13); // Default location

    return this;
  }

  public onClick(callback: (e: L.LeafletMouseEvent) => any, shouldAddMarker: boolean = true): this {
    this.map.on('click', (e: L.LeafletMouseEvent) => {
      callback(e);

      if(shouldAddMarker) {
        const coord = e.latlng;
        this.addUniqueMarker(coord);
      }    
    });

    return this;
  }

  public addUniqueMarker(location: string | L.LatLng): this {
    let coords: L.LatLng = typeof location === 'string' ? coordsFromString(location) : location;

    if (this.marker) {
      this.map.removeLayer(this.marker);
    }
    this.marker = L.marker([coords.lat, coords.lng], { icon: this.pinIcon }).addTo(this.map);

    return this;
  }

  public addMarker(location: string | L.LatLng): this {
    let coords: L.LatLng = typeof location === 'string' ? coordsFromString(location) : location;

    const marker = L.marker([coords.lat, coords.lng], { icon: this.pinIcon }).addTo(this.map);
    this.markers.push(marker);

    return this;
  }

  public addTooltip(location: string | L.LatLng, text: string, interactive: boolean, action: () => void) {
    let coords: L.LatLng = typeof location === 'string' ? coordsFromString(location) : location;

    // Define the HTML content for your custom divIcon
    const htmlContent = `
      <div class="custom-tooltip-container">
        <div class="custom-tooltip">${this.sanitizer.sanitize(SecurityContext.HTML, text)}</div>
      </div>
    `;

    // Create a divIcon with your custom HTML content
    const customIcon = L.divIcon({
      className: 'custom-tooltip-icon', // Use this class to style your tooltip
      html: htmlContent,
      iconSize: L.point(200, 30), // Adjust the size as needed
    });

    // Create a marker with the divIcon
    const marker = L.marker(coords, { icon: customIcon }).addTo(this.map);

    // If interactive, attach the click event to the marker
    if (interactive) {
      marker.on('click', action);
    }

    this.tooltips.push(marker);
  }

  public clearTooltipsAndMarkers(): void {
    this.markers.forEach(marker => marker.remove());
    this.markers = []; // Reset the markers array
    this.tooltips.forEach(tooltip => tooltip.remove());
    this.tooltips = []; // Reset the markers array
  }

}