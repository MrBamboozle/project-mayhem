import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GeneralDataComponent } from './general-data.component';

describe('GeneralDataComponent', () => {
  let component: GeneralDataComponent;
  let fixture: ComponentFixture<GeneralDataComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GeneralDataComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(GeneralDataComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
