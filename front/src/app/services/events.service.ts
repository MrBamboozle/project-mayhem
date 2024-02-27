import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { CreateEventRequest, EngagementTypeRequest, Event } from '@app/shared/models/event';
import { MessageResponse } from '@app/shared/models/message';
import { PaginatedResponse } from '@app/shared/models/paginated-response';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EventsService {
  private readonly eventsUrl: string = `${config.API_URL}/events`;
  private readonly eventsAllUrl: string = `${config.API_URL}/events-all`;
  private readonly eventsEngageUrl: string = `${this.eventsUrl}/engage`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getEvents(queryParams: string = ''): Observable<PaginatedResponse<Event>> {
    return this._http
      .get<PaginatedResponse<Event>>(`${this.eventsUrl}${queryParams}`);
  }

  public getAllEvents(): Observable<Event[]> {
    return this._http
      .get<Event[]>(`${this.eventsAllUrl}`);
  }

  public getEvent(id: string): Observable<Event> {
    return this._http
      .get<Event>(`${this.eventsUrl}/${id}`);
  }

  public postEvent(eventReq: CreateEventRequest): Observable<Event> {
    return this._http
      .post<Event>(this.eventsUrl, eventReq);
  }

  public patchEvent(id: string, eventReq: CreateEventRequest): Observable<Event> {
    return this._http
      .patch<Event>(`${this.eventsUrl}/${id}`, eventReq);
  }

  public deleteEvent(id: string): Observable<MessageResponse> {
    return this._http
      .delete<MessageResponse>(`${this.eventsUrl}/${id}`);
  }

  public engageEvent(id: string, typeReq: EngagementTypeRequest): Observable<Event> {
    return this._http
      .post<Event>(`${this.eventsEngageUrl}/${id}`, typeReq);
  } 
}
