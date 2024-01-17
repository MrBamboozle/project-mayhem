import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ImageCropCompressModalComponent } from './image-crop-compress.modal.component';

describe('ImageCropCompressModalComponent', () => {
  let component: ImageCropCompressModalComponent;
  let fixture: ComponentFixture<ImageCropCompressModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ImageCropCompressModalComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ImageCropCompressModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
