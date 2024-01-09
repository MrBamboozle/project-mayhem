import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { Event, EventRequest } from '@app/shared/models/event';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EventsService {
  private readonly eventsUrl: string = `${config.API_URL}/events`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getEvents(): Observable<Event[]> {
    return this._http
      .get<Event[]>(this.eventsUrl);
  }

  public getEvent(id: string): Observable<Event> {
    return this._http
      .get<Event>(`${this.eventsUrl}/${id}`);
  }

  public postEvent(eventReq: EventRequest): Observable<Event> {
    return this._http
      .post<Event>(this.eventsUrl, eventReq);
  }

  public patchEvent(id: string, eventReq: EventRequest): Observable<Event> {
    return this._http
      .patch<Event>(`${this.eventsUrl}/${id}`, eventReq);
  }

  //TODO: delete response?
  public deleteEvent(id: string): Observable<Event> {
    return this._http
      .delete<Event>(`${this.eventsUrl}/${id}`);
  }
}
