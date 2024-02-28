import { CommonModule } from '@angular/common';
import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CategoryPickerComponent } from '../category-picker/category-picker.component';
import { UserPickerComponent } from '../user-picker/user-picker.component';
import { User } from '@app/shared/models/user';

@Component({
  selector: 'app-events-filters',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, CategoryPickerComponent, UserPickerComponent],
  templateUrl: './events-filters.component.html',
  styleUrl: './events-filters.component.scss'
})
export class EventsFiltersComponent {
  @Output() onFilterChange = new EventEmitter<string>();
  
  public filterForm: FormGroup = this.fb.group({
    all: [''],
    creator: [''],
    categories: [[]],
    startDateFrom: [''],
    startDateTo: ['']
  });;

  constructor(private fb: FormBuilder) {}

  get controlsFilterForm(): { [p: string]: AbstractControl } {
    return this.filterForm.controls;
  }

  onSubmit(): void {
    const queryParams = this.buildQueryParams(this.filterForm.value);
    this.onFilterChange.emit(queryParams);
  }

  private buildQueryParams(filterValues: any): string {
    const params = Object.entries(filterValues)
      .filter(([_, value]) => value) // Remove entries with falsy values
      .map(([key, value]) => {
        if (key === 'categories' && Array.isArray(value)) { // Handle array values for categories
          return `filter[${key}]=${value.map(val => encodeURIComponent(val)).join(',')}`;
        }
        return `filter[${key}]=${encodeURIComponent(value as string)}`;
      })
      .join('&');
    return params ? `?${params}` : '';
  }

  onSelectedCategoryChange(selectedIds: string[]) {
    this.controlsFilterForm.categories.setValue(selectedIds);
  }

  onSelectedUserChange(user: User | null) {
    this.controlsFilterForm.creator.setValue(user ? user.id : '');
  }
}