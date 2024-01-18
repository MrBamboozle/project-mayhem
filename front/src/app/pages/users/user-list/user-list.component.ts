import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { UsersService } from '@app/services/users.service';
import { User } from '@app/shared/models/user';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';
import { NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';
import { Subject, debounceTime } from 'rxjs';

@Component({
  selector: 'app-user-list',
  standalone: true,
  imports: [CommonModule, FormsModule, NgbPaginationModule],
  templateUrl: './user-list.component.html',
  styleUrl: './user-list.component.scss'
})
export class UserListComponent {

  public users: User[] = [];

  public currentPage: number = 1;
  public pageSize: number = 10; // Set the number of items per page
  public collectionSize: number = 0; // Total number of items

  public searchTerm: string = '';
  private sortDirection: { [key: string]: 'asc' | 'desc' | '' } = {
    name: '',
    email: '',
    // Add other columns as needed
  };

  private searchSubject: Subject<string> = new Subject();

  constructor(
    private readonly _router: Router,
    private readonly usersService: UsersService,
    private readonly modalService: ModalHelperService,
  ) {}

  ngOnInit(): void {
    this.fetchUsers(this.currentPage);

    // Debounce search input
    this.searchSubject.pipe(debounceTime(500)).subscribe(() => {
      this.fetchUsers(this.currentPage);
    });
  }

  onSearchChange(): void {
    this.searchSubject.next(this.searchTerm);
  }

  sort(column: string): void {
    // Toggle sort direction
    this.sortDirection[column] = this.sortDirection[column] === 'asc' ? 'desc' : (this.sortDirection[column] === 'desc' ? '' : 'asc');
    this.fetchUsers(this.currentPage);
  }

  getSortIcon(column: string): string {
    if (this.sortDirection[column] === 'asc') {
      return 'rotate-asc'; // or your preferred 'ascending' icon class
    } else if (this.sortDirection[column] === 'desc') {
      return 'rotate-desc'; // or your preferred 'descending' icon class
    }
    return ''; // or your preferred 'default' icon class
  }

  fetchUsers(page: number): void {
    let queryParams = `?page=${page}`;
    if (this.searchTerm) queryParams += `&filter[all]=${encodeURIComponent(this.searchTerm)}`;

    // Construct sorting query parameters
    const sortingParams = Object.entries(this.sortDirection)
      .filter(([_, dir]) => dir)
      .map(([col, dir]) => `sort[${col}]=${dir}`)
      .join('&');
    if (sortingParams) queryParams += `&${sortingParams}`;

    this.usersService.getUsers(queryParams).subscribe((paginatedData) => {
      this.users = paginatedData.data;
      this.currentPage = paginatedData.current_page;
      this.pageSize = paginatedData.per_page;
      this.collectionSize = paginatedData.total;
    })
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.fetchUsers(page);
  }

  editUser(id: string): void {
    this._router.navigate(['users', 'edit', id]);
  }

  deleteUser(id: string): void {
    this.modalService.openCofirmModal(
      'Delete user?',
      'Are you sure you want to delete this user?',
      () => {
        this.usersService.deleteUser(id).subscribe(() => {
          this.fetchUsers(this.currentPage);
        });
      }
    )
  }
}
