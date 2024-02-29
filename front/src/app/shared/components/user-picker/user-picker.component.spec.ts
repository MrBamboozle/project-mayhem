import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserPickerComponent } from './user-picker.component';

describe('UserPickerComponent', () => {
  let component: UserPickerComponent;
  let fixture: ComponentFixture<UserPickerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UserPickerComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UserPickerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
