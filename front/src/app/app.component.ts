import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive, NgbDropdownModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  public isSidebarHidden: boolean = false;
  public isSignedIn: boolean = false;
  public title: string = 'Project Mayhem';

  public toggleSidebar(): void {
    this.isSidebarHidden = !this.isSidebarHidden;
  }

  public signIn(): void {
    this.isSignedIn = true;
  }

  public signOut(): void {
    this.isSignedIn = false;
  }
}
