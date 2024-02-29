import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Output, ViewChild } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UsersService } from '@app/services/users.service';
import { User } from '@app/shared/models/user';
import { userAvatarSrc } from '@app/shared/utils/avatar';
import { NgbDropdown, NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';
import { Subject, debounceTime } from 'rxjs';

@Component({
  selector: 'app-user-picker',
  standalone: true,
  imports: [CommonModule, FormsModule, NgbDropdownModule],
  templateUrl: './user-picker.component.html',
  styleUrl: './user-picker.component.scss'
})
export class UserPickerComponent {
  public userAvatarSrc = userAvatarSrc;

  @ViewChild('dropdown') dropdown!: NgbDropdown;

  @Output() onSelectedUserChange = new EventEmitter<User | null>();
  @Output() onDropdownToggle = new EventEmitter<boolean>();

  public users: User[] = [];
  public selectedUser: User | null = null;
  public searchTerm: string = '';

  private searchSubject: Subject<string> = new Subject();


  constructor(
    private readonly usersService: UsersService,
  ) {}

  ngOnInit(): void {
    this.fetchUsers();

    // Debounce search input
    this.searchSubject.pipe(debounceTime(500)).subscribe(() => {
      this.fetchUsers();
    });
  }

  fetchUsers(): void {
    let queryParams = ``;
    if (this.searchTerm) queryParams += `?filter[all]=${encodeURIComponent(this.searchTerm)}`;

    this.usersService.getAllUsers(queryParams).subscribe((data) => {
      this.users = data;
    });
  }

  onSearchChange(): void {
    this.searchSubject.next(this.searchTerm);
  }

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

  selectUser(user: User) {
    this.selectedUser = user;
    this.onSelectedUserChange.emit(this.selectedUser);
  }

  removeUser() {
    this.selectedUser = null;
    this.onSelectedUserChange.emit(this.selectedUser);
  }

  open(event: any) {
    if (!this.dropdown.isOpen()) {
      setTimeout(() => {
        this.dropdown.open();
      }, 10)
    } else {
      event.stopPropagation()
    }
  }

}
