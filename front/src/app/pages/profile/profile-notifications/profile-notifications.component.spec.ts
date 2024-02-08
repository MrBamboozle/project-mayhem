import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProfileNotificationsComponent } from './profile-notifications.component';

describe('ProfileNotificationsComponent', () => {
  let component: ProfileNotificationsComponent;
  let fixture: ComponentFixture<ProfileNotificationsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ProfileNotificationsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ProfileNotificationsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
