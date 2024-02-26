import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { NotificationsService } from '@app/services/notifications.service';
import { Notification } from '@app/shared/models/notification';
import { ModalHelperService } from '@app/shared/services/modal-helper.service';
import { NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-profile-notifications',
  standalone: true,
  imports: [CommonModule, NgbPaginationModule],
  templateUrl: './profile-notifications.component.html',
  styleUrl: './profile-notifications.component.scss'
})
export class ProfileNotificationsComponent {

  public notifications: Notification[] = [];

  public currentPage: number = 1;
  public pageSize: number = 10; // Set the number of items per page
  public collectionSize: number = 0; // Total number of items

  constructor(
    public readonly notificationsService: NotificationsService,
    private readonly modalService: ModalHelperService,
  ) {}

  ngOnInit(): void {
    this.fetchNotifications(this.currentPage);
  }

  fetchNotifications(page: number): void {
    let queryParams = `?page=${page}`;

    this.notificationsService.getNotifications(queryParams).subscribe((paginatedData) => {
      this.notifications = paginatedData.data;
      this.currentPage = paginatedData.current_page;
      this.pageSize = paginatedData.per_page;
      this.collectionSize = paginatedData.total;
    });
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.fetchNotifications(page);
  }

  public markAllAsRead(): void {
    this.notificationsService.patchNotifications().subscribe(() => {
      this.fetchNotifications(this.currentPage);
    });
  }

  public deleteAll(): void {
    this.modalService.openCofirmModal(
      'Delete all notifications?', 
      'Are you sure you want to delete all your notifications?', 
      () => {
        this.notificationsService.deleteNotifications().subscribe(() => {
          this.currentPage = 1;
          this.fetchNotifications(this.currentPage);
        });
      });
  }
}
