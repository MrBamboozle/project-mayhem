import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output, ViewChild } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CategoriesService } from '@app/services/categories.service';
import { Category } from '@app/shared/models/event';
import { NgbDropdown, NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-category-picker',
  standalone: true,
  imports: [CommonModule, FormsModule, NgbDropdownModule],
  templateUrl: './category-picker.component.html',
  styleUrl: './category-picker.component.scss'
})
export class CategoryPickerComponent {
  @ViewChild('dropdown') dropdown!: NgbDropdown;

  @Output() onSelectedCategoryChange = new EventEmitter<string[]>();
  @Output() onDropdownToggle = new EventEmitter<boolean>();

  @Input() initialCategoryIds: string[] = [];


  public categories: Category[] = [];
  public filteredCategories: Category[] = this.categories;
  public selectedCategoryIds: string[] = [];
  public searchTerm: string = '';

  constructor(
    private readonly categoriesService: CategoriesService,
  ) {}

  ngOnInit(): void {
    this.categoriesService.getCategories().subscribe(
      (data: Category[]) => {
        this.categories = data;
        this.filteredCategories = this.categories;
      }
    )
  }

  ngAfterViewInit(): void {
    this.selectedCategoryIds = this.initialCategoryIds;
  }

  // Category picking
  public closeDropdown() {
    if (this.dropdown.isOpen()) {
      this.dropdown.close();
    }
  }

  preventClose(event: MouseEvent): void {
    // Check if the dropdown is already open; if so, stop propagation
    if (this.dropdown.isOpen()) {
      event.stopPropagation();
    }
  }

  onDropdownOpenChange(isOpen: boolean): void {
    this.onDropdownToggle.emit(isOpen);
  }

  get selectedCategories(): Category[] {
    return this.categories.filter(category => this.selectedCategoryIds.some((selectedId: string) => selectedId === category.id));
  }

  filterCategories() {
    this.filteredCategories = this.categories.filter(category => 
      category.name.toLowerCase().includes(this.searchTerm.toLowerCase()) &&
      !this.selectedCategoryIds.some((selectedId: string) => selectedId === category.id));
  }

  selectCategory(category: Category) {
    if (!this.selectedCategoryIds.includes(category.id)) {
      this.selectedCategoryIds = [...this.selectedCategoryIds, category.id];
    }
    this.onSelectedCategoryChange.emit(this.selectedCategoryIds);
  }

  removeCategory(categoryToRemove: Category) {
    this.selectedCategoryIds = this.selectedCategoryIds.filter((id: string) => id !== categoryToRemove.id);
    this.onSelectedCategoryChange.emit(this.selectedCategoryIds);
  }

  open(event: any) {
    this.filterCategories(); // Ensure dropdown opens with all items
    if (!this.dropdown.isOpen()) {
      setTimeout(() => {
        this.dropdown.open();
      }, 10)
    } else {
      event.stopPropagation()
    }
  }
}
