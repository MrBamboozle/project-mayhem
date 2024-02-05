import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { config } from '@app/core/app-config';
import { Category } from '@app/shared/models/event';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CategoriesService {
  private readonly categoriesUrl: string = `${config.API_URL}/categories`;

  constructor(
    private readonly _http: HttpClient,
  ) { }

  public getCategories(): Observable<Category[]> {
    return this._http
      .get<Category[]>(this.categoriesUrl);
  }
}
