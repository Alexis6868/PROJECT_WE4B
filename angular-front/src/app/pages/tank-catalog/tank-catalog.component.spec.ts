import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TankCatalogComponent } from './tank-catalog.component';

describe('TankCatalogComponent', () => {
  let component: TankCatalogComponent;
  let fixture: ComponentFixture<TankCatalogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TankCatalogComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TankCatalogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
