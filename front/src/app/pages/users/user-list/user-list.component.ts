import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsersService } from '@app/services/users.service';
import { User } from '@app/shared/models/user';
import { NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-user-list',
  standalone: true,
  imports: [CommonModule, NgbPaginationModule],
  templateUrl: './user-list.component.html',
  styleUrl: './user-list.component.scss'
})
export class UserListComponent {

  public users: User[] = [];

  public currentPage: number = 1;
  public pageSize: number = 10; // Set the number of items per page
  public collectionSize: number = 0; // Total number of items

  constructor(
    private readonly _router: Router,
    private readonly usersService: UsersService,
  ) {}

  ngOnInit() {
    this.fetchUsers(this.currentPage);
  }

  fetchUsers(page: number): void {
    this.usersService.getUsers(page).subscribe((paginatedData) => {
      this.users = paginatedData.data;
      this.currentPage = paginatedData.current_page;
      this.pageSize = paginatedData.per_page;
      this.collectionSize = paginatedData.total;
    })
  }

  onPageChange(page: number) {
    this.currentPage = page;
    this.fetchUsers(page);
  }

  editUser(id: string) {
    this._router.navigate(['users', id]);
  }

  deleteUser(id: string) {
    this.usersService.deleteUser(id).subscribe(() => {
      this.fetchUsers(this.currentPage);
    });
  }
}
