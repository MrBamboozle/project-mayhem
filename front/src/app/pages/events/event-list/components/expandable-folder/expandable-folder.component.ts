import { Component, Input } from '@angular/core';
import { trigger, state, style, transition, animate } from '@angular/animations';
import { CommonModule } from '@angular/common';
import { Event } from '@app/shared/models/event';
import { RouterLink } from '@angular/router';
import { formatTopSecretFontTitle } from '@app/shared/utils/formatters';
import { EventDisplayComponent } from '@app/shared/components/event-display/event-display.component';

@Component({
  selector: 'app-expandable-folder',
  templateUrl: './expandable-folder.component.html',
  styleUrls: ['./expandable-folder.component.scss'],
  standalone: true,
  imports: [CommonModule, RouterLink, EventDisplayComponent],
  animations: [
    trigger('folderState', [
      state('closed', style({
        transform: 'rotateX(0deg)'
      })),
      state('open', style({
        transform: 'rotateX(-180deg)'
      })),
      transition('closed => open', animate('500ms ease-out')),
      transition('open => closed', animate('500ms 500ms ease-out')),
    ]),
    trigger('contentHeight', [
      state('closed', style({
        'max-height': '130px'
      })),
      state('open', style({
        'max-height': '1500px'
      })),
      transition('closed => open', animate('500ms 200ms ease-out')),
      transition('open => closed', animate('500ms ease-out')),
    ]),
  ]
})
export class ExpandableFolderComponent {
  @Input() event!: Event;

  folderState = 'closed';
  overflowStyle = 'hidden';
  closeButtonStyle = 'back';

  toggleFolder() {
    this.folderState = (this.folderState === 'closed') ? 'open' : 'closed';
    if (this.folderState === 'open') {
      this.overflowStyle = 'hidden';
      setTimeout(() => this.overflowStyle = 'visible', 200);
      this.closeButtonStyle = 'front';
    } else {
      setTimeout(() => this.overflowStyle = 'hidden', 500);
      setTimeout(() => this.closeButtonStyle = 'back', 1000);
    }
  }

  get formattedTitle(): string {
    return formatTopSecretFontTitle(this.event.title);
  }
}
