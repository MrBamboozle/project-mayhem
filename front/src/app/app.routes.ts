import { Routes } from '@angular/router';

import { HomeComponent } from './pages/home/home.component';
import { EventsComponent } from './pages/events/events.component';
import { UsersComponent } from './pages/users/users.component';
import { EventListComponent } from './pages/events/event-list/event-list.component';
import { UserListComponent } from './pages/users/user-list/user-list.component';
import { NotFoundComponent } from './pages/not-found/not-found.component';
import { ProfileComponent } from './pages/profile/profile.component';

export const routes: Routes = [
  { path: '', component: HomeComponent, },
  {
    path: 'events', component: EventsComponent,
    children: [
      { path: '', component: EventListComponent },
      { path: 'list', component: EventListComponent },
    ],
  },
  {
    path: 'users', component: UsersComponent,
    children: [
      { path: '', component: UserListComponent },
      { path: 'list', component: UserListComponent },
    ],
  },
  { path: 'profile', component: ProfileComponent },
  { path: '**', component: NotFoundComponent }
];
