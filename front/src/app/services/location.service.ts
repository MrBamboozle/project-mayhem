import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { LocationRequest } from '@app/shared/models/location';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LocationService {
  private readonly locationUrl: string = `${config.API_URL}/location`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public postLocation(locationReq: LocationRequest): Observable<any> {
    return this._http
      .post<any>(this.locationUrl, locationReq);
  }
}
