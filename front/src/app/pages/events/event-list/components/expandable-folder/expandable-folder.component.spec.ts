import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExpandableFolderComponent } from './expandable-folder.component';

describe('ExpandableFolderComponent', () => {
  let component: ExpandableFolderComponent;
  let fixture: ComponentFixture<ExpandableFolderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExpandableFolderComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ExpandableFolderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
