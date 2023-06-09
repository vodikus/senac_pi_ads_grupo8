import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InteressesComponent } from './interesses.component';

describe('InteressesComponent', () => {
  let component: InteressesComponent;
  let fixture: ComponentFixture<InteressesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ InteressesComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(InteressesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
