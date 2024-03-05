import { Router, Routes } from '@angular/router';

import { HomeComponent } from './pages/home/home.component';
import { EventsComponent } from './pages/events/events.component';
import { UsersComponent } from './pages/users/users.component';
import { EventListComponent } from './pages/events/event-list/event-list.component';
import { UserListComponent } from './pages/users/user-list/user-list.component';
import { NotFoundComponent } from './pages/not-found/not-found.component';
import { ProfileComponent } from './pages/profile/profile.component';
import { EditUserComponent } from './pages/users/edit-user/edit-user.component';
import { CreateEventComponent } from './pages/events/create-event/create-event.component';
import { ProfileDataComponent } from './pages/profile/profile-data/profile-data.component';
import { ProfileNotificationsComponent } from './pages/profile/profile-notifications/profile-notifications.component';
import { ViewEventComponent } from './pages/events/view-event/view-event.component';
import { userGuard, adminGuard } from './core/guards/auth-guard'; // Adjust the import path as needed
import { Injector } from '@angular/core';
import { UserStoreService } from './shared/stores/user.store.service';

export const routes: Routes = [
  { path: '', component: HomeComponent, },
  {
    path: 'events', component: EventsComponent,
    children: [
      { path: '', component: EventListComponent },
      { path: 'list', component: EventListComponent },
      { path: 'create', component: CreateEventComponent, canActivate: [userGuard()] },
      { path: ':uuid', component: ViewEventComponent },
      { path: ':uuid/edit', component: CreateEventComponent, canActivate: [userGuard()] },
    ],
  },
  {
    path: 'users', component: UsersComponent,
    children: [
      { path: '', component: UserListComponent },
      { path: 'list', component: UserListComponent },
      { path: 'edit/:uuid', component: EditUserComponent },
    ], canActivateChild: [adminGuard()]
  },
  { path: 'profile', component: ProfileComponent,
    children: [
      { path: '', component: ProfileDataComponent },
      { path: 'data', component: ProfileDataComponent },
      { path: 'events', component: EventListComponent, data: { profileEvents: true } },
      { path: 'notifications', component: ProfileNotificationsComponent },
    ], canActivate: [userGuard()]
  },
  { path: '**', component: NotFoundComponent }
];
